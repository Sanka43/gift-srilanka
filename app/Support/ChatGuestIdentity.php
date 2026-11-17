<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ChatGuestIdentity
{
    public const COOKIE_NAME = 'guest_chat_key';

    public function isEnabled(): bool
    {
        return (bool) config('contact.chat_guest_enabled', false);
    }

    public function resolveToken(Request $request): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $token = trim((string) $request->cookie(self::COOKIE_NAME, ''));
        $token = Str::isUuid($token) ? $token : (string) Str::uuid();

        $this->queueCookie($token);

        return $token;
    }

    public function queueCookie(string $token): void
    {
        $minutes = max(1, (int) config('contact.chat_guest_cookie_days', 7) * 24 * 60);

        Cookie::queue(
            Cookie::make(
                self::COOKIE_NAME,
                $token,
                $minutes,
                '/',
                null,
                true,
                true,
                false,
                'lax'
            )
        );
    }
}
