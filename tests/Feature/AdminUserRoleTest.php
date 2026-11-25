<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserRoleTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email): User
    {
        Role::firstOrCreate(['name' => 'admin']); Role::firstOrCreate(['name' => 'customer']);
        $r = Role::where('name', $role)->first();

        $u = new User(); $u->forceFill(['name' => ucfirst($role) . ' ' . $email, 'email' => $email, 'password' => 'secret123', 'role_id' => $r->id]); $u->save();

        return $u;
    }

    public function test_guest_and_customer_cannot_access(): void
    {
        $target = $this->user('customer', 't@x.com');

        $this->get('/admin/users')->assertRedirect();
        $this->patch("/admin/users/{$target->id}/role", ['role' => 'admin'])->assertRedirect();

        $customer = $this->user('customer', 'c@x.com');
        $this->actingAs($customer)->get('/admin/users')->assertForbidden();
        $this->actingAs($customer)->patch("/admin/users/{$target->id}/role", ['role' => 'admin'])->assertForbidden();
        $this->actingAs($customer)->patch("/admin/users/{$customer->id}/role", ['role' => 'admin'])->assertForbidden();
        $this->assertFalse($customer->fresh()->isAdmin());
        $this->assertFalse($target->fresh()->isAdmin());
    }

    public function test_admin_can_list_search_promote_and_demote(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $this->user('admin', 'b@x.com');
        $target = $this->user('customer', 'findme@x.com');

        $this->actingAs($admin)->get('/admin/users?search=findme')->assertOk()->assertSee('findme@x.com')->assertDontSee('b@x.com');

        $this->actingAs($admin)->patch("/admin/users/{$target->id}/role", ['role' => 'admin'])->assertSessionHas('success');
        $this->assertTrue($target->fresh()->isAdmin());

        $this->actingAs($admin)->patch("/admin/users/{$target->id}/role", ['role' => 'customer'])->assertSessionHas('success');
        $this->assertFalse($target->fresh()->isAdmin());
    }

    public function test_cannot_demote_self_and_invalid_role_rejected(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $this->user('admin', 'b@x.com');

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/role", ['role' => 'customer'])->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/role", ['role' => 'superuser'])->assertSessionHasErrors('role');
    }

    public function test_noop_role_change_reports_error_and_keeps_sole_admin(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $other = $this->user('admin', 'b@x.com');
        $this->actingAs($admin)->patch("/admin/users/{$other->id}/role", ['role' => 'customer'])->assertSessionHas('success');

        $third = $this->user('customer', 'c@x.com');
        $this->actingAs($admin)->patch("/admin/users/{$third->id}/role", ['role' => 'customer'])->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());
    }
}
