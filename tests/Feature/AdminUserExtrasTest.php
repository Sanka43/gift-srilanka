<?php

namespace Tests\Feature;

use App\Mail\AccountBlockedMail;
use App\Models\AdminAuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUserExtrasTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, bool $verified = true): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);
        $u = new User();
        $u->forceFill([
            'name' => ucfirst($role) . ' ' . $email, 'email' => $email, 'password' => 'secret123',
            'role_id' => Role::where('name', $role)->first()->id,
            'email_verified_at' => $verified ? now() : null,
        ]);
        $u->save();

        return $u;
    }

    public function test_non_admin_cannot_use_new_endpoints(): void
    {
        $c = $this->user('customer', 'c@x.com');
        $t = $this->user('customer', 't@x.com');
        $note = UserNote::create(['user_id' => $t->id, 'admin_id' => null, 'body' => 'x']);

        $this->actingAs($c)->post('/admin/users/bulk', ['action' => 'block', 'ids' => [$t->id]])->assertForbidden();
        $this->actingAs($c)->post("/admin/users/{$t->id}/notes", ['body' => 'hi'])->assertForbidden();
        $this->actingAs($c)->delete("/admin/users/{$t->id}/notes/{$note->id}")->assertForbidden();
        $this->actingAs($c)->post("/admin/users/{$t->id}/password-reset")->assertForbidden();
        $this->actingAs($c)->post("/admin/users/{$t->id}/resend-verification")->assertForbidden();
        $this->assertFalse($t->fresh()->isBlocked());
        $this->assertDatabaseCount('user_notes', 1);
    }

    public function test_bulk_block_skips_self_and_admins_and_logs(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $other = $this->user('admin', 'b@x.com');
        $t1 = $this->user('customer', 't1@x.com');
        $t2 = $this->user('customer', 't2@x.com');

        $this->actingAs($admin)->post('/admin/users/bulk', ['action' => 'block', 'ids' => [$t1->id, $t2->id, $admin->id, $other->id], 'reason' => 'spam'])
            ->assertSessionHas('success');

        $this->assertTrue($t1->fresh()->isBlocked());
        $this->assertTrue($t2->fresh()->isBlocked());
        $this->assertFalse($admin->fresh()->isBlocked());
        $this->assertFalse($other->fresh()->isBlocked());
        $this->assertDatabaseHas('admin_audit_logs', ['user_id' => $t1->id, 'action' => 'blocked', 'admin_id' => $admin->id]);

        $this->actingAs($admin)->post('/admin/users/bulk', ['action' => 'unblock', 'ids' => [$t1->id]])->assertSessionHas('success');
        $this->assertFalse($t1->fresh()->isBlocked());

        $this->actingAs($admin)->post('/admin/users/bulk', ['action' => 'nuke', 'ids' => [$t1->id]])->assertSessionHasErrors('action');
    }

    public function test_notes_add_delete_and_scoped_to_user(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $t = $this->user('customer', 't@x.com');
        $o = $this->user('customer', 'o@x.com');

        $this->actingAs($admin)->post("/admin/users/{$t->id}/notes", ['body' => 'VIP customer'])->assertSessionHas('success');
        $this->actingAs($admin)->get("/admin/users/{$t->id}")->assertOk()->assertSee('VIP customer');

        $note = UserNote::first();
        $this->actingAs($admin)->delete("/admin/users/{$o->id}/notes/{$note->id}")->assertNotFound();
        $this->actingAs($admin)->delete("/admin/users/{$t->id}/notes/{$note->id}")->assertSessionHas('success');
        $this->assertDatabaseCount('user_notes', 0);
    }

    public function test_block_email_only_when_requested(): void
    {
        Mail::fake();
        $admin = $this->user('admin', 'a@x.com');
        $t1 = $this->user('customer', 't1@x.com');
        $t2 = $this->user('customer', 't2@x.com');

        $this->actingAs($admin)->post("/admin/users/{$t1->id}/block")->assertSessionHas('success');
        Mail::assertNothingSent();

        $this->actingAs($admin)->post("/admin/users/{$t2->id}/block", ['notify' => 1])->assertSessionHas('success');
        Mail::assertSent(AccountBlockedMail::class, fn ($m) => $m->hasTo('t2@x.com'));
    }

    public function test_password_reset_and_verification_actions(): void
    {
        Notification::fake();
        $admin = $this->user('admin', 'a@x.com');
        $t = $this->user('customer', 't@x.com', false);

        $this->actingAs($admin)->post("/admin/users/{$t->id}/password-reset")->assertSessionHas('success');
        Notification::assertSentTo($t, ResetPassword::class);

        $this->actingAs($admin)->post("/admin/users/{$t->id}/resend-verification")->assertSessionHas('success');
        Notification::assertSentTo($t, \Illuminate\Auth\Notifications\VerifyEmail::class);

        $v = $this->user('customer', 'v@x.com', true);
        $this->actingAs($admin)->post("/admin/users/{$v->id}/resend-verification")->assertSessionHas('error');
    }

    public function test_filters_sorting_last_login_and_role_audit(): void
    {
        $admin = $this->user('admin', 'a@x.com');
        $t = $this->user('customer', 'noorders@x.com');

        $this->actingAs($admin)->get('/admin/users?orders=none&sort=spent')->assertOk()->assertSee('noorders@x.com');
        $this->actingAs($admin)->get('/admin/users?orders=with')->assertOk()->assertDontSee('noorders@x.com');
        $this->actingAs($admin)->get('/admin/users?sort=bogus')->assertOk();

        $this->actingAs($admin)->patch("/admin/users/{$t->id}/role", ['role' => 'admin']);
        $this->assertTrue(AdminAuditLog::where('user_id', $t->id)->where('action', 'promoted')->exists());

        $this->post('/logout');
        $this->post('/login', ['email' => 'noorders@x.com', 'password' => 'secret123']);
        $this->assertNotNull($t->fresh()->last_login_at);
    }
}
