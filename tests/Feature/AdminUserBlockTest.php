<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserBlockTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);
        $u = new User();
        $u->forceFill(['name' => ucfirst($role) . ' ' . $email, 'email' => $email, 'password' => 'secret123', 'role_id' => Role::where('name', $role)->first()->id]);
        $u->save();

        return $u;
    }

    public function test_non_admin_cannot_view_block_or_unblock(): void
    {
        $customer = $this->user('customer', 'c@x.com');
        $target = $this->user('customer', 't@x.com');

        $this->actingAs($customer)->get("/admin/users/{$target->id}")->assertForbidden();
        $this->actingAs($customer)->get('/admin/users/export')->assertForbidden();
        $this->actingAs($customer)->post("/admin/users/{$target->id}/block")->assertForbidden();
        $this->actingAs($customer)->post("/admin/users/{$target->id}/unblock")->assertForbidden();
        $this->assertFalse($target->fresh()->isBlocked());
    }

    public function test_admin_sees_profile_with_orders(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $target = $this->user('customer', 't@x.com');
        Order::create(['user_id' => $target->id, 'full_name' => 'T', 'phone' => '1', 'address' => 'x', 'district' => 'Colombo', 'total_amount' => 1234.5, 'payment_method' => 'cash_on_delivery']);

        $this->actingAs($admin)->get("/admin/users/{$target->id}")->assertOk()->assertSee('t@x.com')->assertSee('1,234.50');
        $this->actingAs($admin)->get('/admin/users?status=blocked')->assertOk()->assertDontSee('t@x.com');
    }

    public function test_block_and_unblock_with_safeguards(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $other = $this->user('admin', 'b@x.com');
        $target = $this->user('customer', 't@x.com');

        $this->actingAs($admin)->post("/admin/users/{$target->id}/block", ['reason' => 'fraud'])->assertSessionHas('success');
        $this->assertTrue($target->fresh()->isBlocked());

        $this->actingAs($admin)->post("/admin/users/{$admin->id}/block")->assertSessionHas('error');
        $this->actingAs($admin)->post("/admin/users/{$other->id}/block")->assertSessionHas('error');
        $this->assertFalse($admin->fresh()->isBlocked());
        $this->assertFalse($other->fresh()->isBlocked());

        $this->actingAs($admin)->post("/admin/users/{$target->id}/unblock")->assertSessionHas('success');
        $this->assertFalse($target->fresh()->isBlocked());
    }

    public function test_blocked_user_cannot_login_or_use_existing_session(): void
    {
        $target = $this->user('customer', 't@x.com');
        $target->forceFill(['blocked_at' => now()])->save();

        $this->post('/login', ['email' => 't@x.com', 'password' => 'secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($target)->get('/')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
