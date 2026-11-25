<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateProfile(User $user, array $data): void
    {
        $update = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'district' => $data['district'] ?? null,
        ];

        if (! empty($data['remove_avatar']) && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $update['avatar_path'] = null;
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = app(ImageOptimizer::class)->store($data['avatar'], 'avatars', 400, 82);
            $update['avatar_path'] = str_replace('\\', '/', $path);
        }

        $user->update($update);
    }
}
