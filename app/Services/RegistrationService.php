<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationService
{
    public function registerCustomer(array $payload): User
    {
        $customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['name' => 'customer']
        );

        $user = new User([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => ! empty($payload['phone']) ? $payload['phone'] : null,
            'password' => Hash::make($payload['password']),
        ]);
        $user->role_id = $customerRole->id;
        $user->save();

        return $user;
    }

    public function sendPostRegistrationNotifications(User $user): void
    {
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::warning('Registration verification email failed (user is still created)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($user->email)->queue(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::warning('Welcome email queue failed, sending synchronously', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);

            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Throwable $sendException) {
                Log::error('Welcome email send failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'message' => $sendException->getMessage(),
                ]);
            }
        }
    }
}
