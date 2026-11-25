<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountBlockedMail;
use App\Models\AdminAuditLog;
use App\Models\Conversation;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNote;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    private const SORTS = [
        'newest' => ['created_at', 'desc'],
        'name' => ['name', 'asc'],
        'orders' => ['orders_count', 'desc'],
        'spent' => ['spent', 'desc'],
        'last_login' => ['last_login_at', 'desc'],
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = $request->query('role');
        $sort = array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'name';
        [$sortCol, $sortDir] = self::SORTS[$sort];

        $users = User::with('role')
            ->withCount('orders')
            ->withSum(['orders as spent' => fn ($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled')], 'total_amount')
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->when($request->query('status') === 'blocked', fn ($q) => $q->whereNotNull('blocked_at'))
            ->when($request->query('status') === 'active', fn ($q) => $q->whereNull('blocked_at'))
            ->when($request->query('orders') === 'with', fn ($q) => $q->has('orders'))
            ->when($request->query('orders') === 'none', fn ($q) => $q->doesntHave('orders'))
            ->when(in_array($roleFilter, ['admin', 'customer'], true), fn ($q) => $q->whereHas('role', fn ($r) => $r->where('name', $roleFilter)))
            ->orderBy($sortCol, $sortDir)
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $adminCount = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->count();
        $blockedCount = User::whereNotNull('blocked_at')->count();

        return view('admin.users.index', compact('users', 'adminCount', 'blockedCount', 'search', 'roleFilter', 'sort'));
    }

    public function show(User $user): View
    {
        $user->load('role');

        $orders = $user->orders()->latest()->paginate(10);

        $stats = [
            'orders' => $user->orders()->count(),
            'spent' => (float) $user->orders()->where('payment_status', 'paid')->where('status', '!=', 'cancelled')->sum('total_amount'),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
            'last_order' => $user->orders()->latest()->value('created_at'),
        ];

        $notes = UserNote::with('admin')->where('user_id', $user->id)->latest()->get();
        $logs = AdminAuditLog::with('admin')->where('user_id', $user->id)->latest()->limit(30)->get();
        $wishlist = Wishlist::with('product')->where('user_id', $user->id)->latest()->limit(20)->get();
        $reviews = Review::with('product')->where('user_id', $user->id)->latest()->limit(20)->get();
        $conversation = Conversation::withCount('messages')->where('customer_id', $user->id)->first();

        return view('admin.users.show', compact('user', 'orders', 'stats', 'notes', 'logs', 'wishlist', 'reviews', 'conversation'));
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'notify' => ['nullable', 'boolean'],
        ]);

        if ($error = $this->blockError($request->user(), $user)) {
            return back()->with('error', $error);
        }

        $this->applyBlock($user, $data['reason'] ?? null, (bool) ($data['notify'] ?? false));

        return back()->with('success', "{$user->name} has been blocked.");
    }

    public function unblock(Request $request, User $user): RedirectResponse
    {
        if (! $user->isBlocked()) {
            return back()->with('error', "{$user->name} is not blocked.");
        }

        $this->applyUnblock($user);

        return back()->with('success', "{$user->name} has been unblocked.");
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:block,unblock'],
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $done = 0;
        $skipped = 0;

        foreach (User::with('role')->whereIn('id', $data['ids'])->get() as $target) {
            if ($data['action'] === 'block') {
                if ($this->blockError($request->user(), $target)) {
                    $skipped++;
                    continue;
                }
                $this->applyBlock($target, $data['reason'] ?? null, false, true);
            } else {
                if (! $target->isBlocked()) {
                    $skipped++;
                    continue;
                }
                $this->applyUnblock($target, true);
            }
            $done++;
        }

        $this->audit(null, 'bulk_' . $data['action'], "{$done} done, {$skipped} skipped");

        $verb = $data['action'] === 'block' ? 'blocked' : 'unblocked';

        return back()->with($done > 0 ? 'success' : 'error', "{$done} user(s) {$verb}" . ($skipped ? ", {$skipped} skipped (yourself, admins or no change needed)." : '.'));
    }

    public function storeNote(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        UserNote::create(['user_id' => $user->id, 'admin_id' => $request->user()->id, 'body' => $data['body']]);
        $this->audit($user, 'note_added');

        return back()->with('success', 'Note added.');
    }

    public function destroyNote(User $user, UserNote $note): RedirectResponse
    {
        abort_unless($note->user_id === $user->id, 404);

        $note->delete();
        $this->audit($user, 'note_deleted');

        return back()->with('success', 'Note deleted.');
    }

    public function sendPasswordReset(User $user): RedirectResponse
    {
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->with('error', 'Could not send the reset link. Please try again shortly.');
        }

        $this->audit($user, 'password_reset_sent');

        return back()->with('success', "Password reset link sent to {$user->email}.");
    }

    public function resendVerification(User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'This email is already verified.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::warning('Admin resend verification failed', ['user_id' => $user->id, 'message' => $e->getMessage()]);

            return back()->with('error', 'Could not send the verification email.');
        }

        $this->audit($user, 'verification_sent');

        return back()->with('success', "Verification email sent to {$user->email}.");
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->stream(function () {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Orders', 'Status', 'Last login', 'Joined']);
            User::with('role')->withCount('orders')->orderBy('id')->chunk(200, function ($users) use ($h) {
                foreach ($users as $u) {
                    // Prefix formula-leading characters to avoid CSV injection in spreadsheets.
                    $safe = fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
                    fputcsv($h, [$u->id, $safe($u->name), $safe($u->email), $safe($u->phone), $u->role?->name, $u->orders_count, $u->isBlocked() ? 'blocked' : 'active', $u->last_login_at?->toDateTimeString(), $u->created_at?->toDateTimeString()]);
                }
            });
            fclose($h);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-' . date('Y-m-d-His') . '.csv"',
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
        ]);

        $newRole = $data['role'];

        if ($newRole === 'customer' && $request->user()->is($user)) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        $role = Role::where('name', $newRole)->first();
        if (! $role) {
            return back()->with('error', "The '{$newRole}' role does not exist.");
        }

        $result = DB::transaction(function () use ($user, $newRole, $role) {
            $user = User::with('role')->lockForUpdate()->findOrFail($user->id);

            if ($user->role?->name === $newRole) {
                return ['error', "{$user->name} is already " . ($newRole === 'admin' ? 'an admin' : 'a customer') . '.'];
            }

            if ($newRole === 'customer') {
                $admins = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->lockForUpdate()->count();
                if ($admins <= 1) {
                    return ['error', 'The last remaining admin cannot be demoted.'];
                }
            }

            $user->role_id = $role->id;
            $user->save();

            return ['success', $newRole === 'admin' ? "{$user->name} is now an admin." : "{$user->name} is now a customer."];
        });

        if ($result[0] === 'success') {
            $this->audit($user, $newRole === 'admin' ? 'promoted' : 'demoted');
        }

        return back()->with($result[0], $result[1]);
    }

    private function blockError(User $actor, User $target): ?string
    {
        if ($actor->is($target)) {
            return 'You cannot block yourself.';
        }
        if ($target->isAdmin()) {
            return 'Admins cannot be blocked. Revoke admin access first.';
        }
        if ($target->isBlocked()) {
            return "{$target->name} is already blocked.";
        }

        return null;
    }

    private function applyBlock(User $user, ?string $reason, bool $notify, bool $quiet = false): void
    {
        $user->blocked_at = now();
        $user->blocked_reason = $reason;
        $user->save();

        // Kill any existing sessions for this user (database session driver).
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        if ($notify) {
            try {
                Mail::to($user->email)->send(new AccountBlockedMail($user));
            } catch (\Throwable $e) {
                Log::warning('Account blocked email failed', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            }
        }

        if (! $quiet) {
            $this->audit($user, 'blocked', $reason);
        } else {
            $this->audit($user, 'blocked', trim('bulk ' . $reason));
        }
    }

    private function applyUnblock(User $user, bool $quiet = false): void
    {
        $user->blocked_at = null;
        $user->blocked_reason = null;
        $user->save();

        $this->audit($user, 'unblocked', $quiet ? 'bulk' : null);
    }

    private function audit(?User $target, string $action, ?string $details = null): void
    {
        AdminAuditLog::create([
            'admin_id' => auth()->id(),
            'user_id' => $target?->id,
            'action' => $action,
            'details' => $details ? mb_substr($details, 0, 500) : null,
            'ip' => request()->ip(),
        ]);
    }
}
