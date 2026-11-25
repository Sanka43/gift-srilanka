<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Mail\WelcomeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleController extends Controller
{
    public function redirect(): SymfonyRedirect
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(CartService $cartService): RedirectResponse
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google sign-in failed', ['message' => $e->getMessage()]);

            return redirect()->route('login')->withErrors(['email' => 'Google sign-in failed. Please try again.']);
        }

        $email = $google->getEmail();
        $verified = (bool) ($google->user['email_verified'] ?? false);

        if (! $email || ! $verified) {
            return redirect()->route('login')->withErrors(['email' => 'Your Google account email is not verified.']);
        }

        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $email)->first();

        $isNew = false;

        if ($user) {
            // Never auto-link privileged (non-customer) accounts by email.
            if (! $user->google_id && $user->role?->name !== 'customer') {
                return redirect()->route('login')->withErrors(['email' => 'This account cannot use Google sign-in. Please log in with your password.']);
            }
        } else {
            $isNew = true;
            $customerRole = Role::firstOrCreate(['name' => 'customer']);
            $user = new User([
                'name' => $google->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Str::random(40),
            ]);
            $user->role_id = $customerRole->id;
        }

        if ($user->exists && $user->isBlocked()) {
            return redirect()->route('login')->withErrors(['email' => 'Your account has been blocked. Please contact support.']);
        }

        $user->google_id = $google->getId();
        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        Auth::login($user, true);
        request()->session()->regenerate();

        $cartService->mergeSessionCartIntoDatabase();

        if ($isNew) {
            try {
                Mail::to($user->email)->queue(new WelcomeMail($user));
            } catch (\Throwable $e) {
                Log::warning('Welcome email failed for Google sign-up', ['user_id' => $user->id, 'message' => $e->getMessage()]);
            }
        }

        return redirect()->intended(route('home', absolute: false));
    }
}
