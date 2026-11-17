<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Build public URLs for stored media. Rewrites absolute URLs that point at /storage/... on any host
 * to the current APP_URL so local dev does not load production (avoids CORB when HTML/error is returned).
 */
final class MediaUrl
{
    public static function publicStorageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            if (is_string($parsedPath) && $parsedPath !== '') {
                $parsedPath = str_replace('\\', '/', $parsedPath);
                $relative = self::pathAfterStorage($parsedPath);
                if ($relative !== null) {
                    return url('/storage/'.$relative);
                }
            }

            return $path;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/storage/')) {
            $path = substr($path, strlen('public/storage/'));
        } elseif (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        } elseif (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return url('/storage/'.ltrim($path, '/'));
    }

    /**
     * @return non-empty-string|null Relative path inside storage disk (e.g. categories/foo.jpg)
     */
    private static function pathAfterStorage(string $urlPath): ?string
    {
        $normalized = ltrim($urlPath, '/');

        if (Str::startsWith($normalized, 'public/storage/')) {
            $rest = substr($normalized, strlen('public/storage/'));

            return $rest !== '' ? $rest : null;
        }

        if (Str::startsWith($normalized, 'storage/')) {
            $rest = substr($normalized, strlen('storage/'));

            return $rest !== '' ? $rest : null;
        }

        // e.g. /index.php/storage/..., nested paths, or odd hosting layouts
        if (preg_match('#(?:^|/)(?:public/)?storage/(.+)$#i', $urlPath, $m)) {
            $rest = ltrim(str_replace('\\', '/', $m[1]), '/');

            return $rest !== '' ? $rest : null;
        }

        return null;
    }
}
