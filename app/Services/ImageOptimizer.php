<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Resizes and compresses images with GD.
 * - store(): used on upload, saves a resized WebP (falls back to the plain upload if GD/WebP is missing).
 * - optimizeFile(): recompresses an existing file in place (same name and format), so DB paths stay valid.
 */
class ImageOptimizer
{
    public function store(UploadedFile $file, string $dir, int $maxSide = 1200, int $quality = 82, string $disk = 'public'): string
    {
        if (! $this->canOptimize() || ! function_exists('imagewebp')) {
            return $file->store($dir, $disk);
        }

        $image = $this->load($file->getRealPath());
        if (! $image) {
            return $file->store($dir, $disk);
        }

        $image = $this->orient($image, $file->getRealPath());
        $image = $this->resize($image, $maxSide);

        $path = trim($dir, '/') . '/' . Str::random(40) . '.webp';
        $tmp = tempnam(sys_get_temp_dir(), 'img');
        imagewebp($image, $tmp, $quality);
        imagedestroy($image);

        Storage::disk($disk)->put($path, file_get_contents($tmp));
        @unlink($tmp);

        return $path;
    }

    /**
     * Recompress an existing image in place. Returns [bytesBefore, bytesAfter]; the file is only replaced if it got smaller.
     *
     * @return array{0:int,1:int}
     */
    public function optimizeFile(string $absPath, int $maxSide = 1200, int $quality = 82): array
    {
        $before = filesize($absPath);
        if (! $this->canOptimize()) {
            return [$before, $before];
        }

        $info = @getimagesize($absPath);
        $image = $this->load($absPath);
        if (! $info || ! $image) {
            return [$before, $before];
        }

        $image = $this->orient($image, $absPath);
        $image = $this->resize($image, $maxSide);

        $tmp = tempnam(sys_get_temp_dir(), 'img');
        match ($info[2]) {
            IMAGETYPE_PNG => imagepng($image, $tmp, 8),
            IMAGETYPE_WEBP => imagewebp($image, $tmp, $quality),
            default => imagejpeg($image, $tmp, $quality),
        };
        imagedestroy($image);

        $after = filesize($tmp);
        if ($after > 0 && $after < $before) {
            copy($tmp, $absPath);
        } else {
            $after = $before;
        }
        @unlink($tmp);

        return [$before, $after];
    }

    private function canOptimize(): bool
    {
        return extension_loaded('gd');
    }

    /** @return \GdImage|null */
    private function load(string $path)
    {
        $info = @getimagesize($path);
        if (! $info) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $image) {
            return null;
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function orient($image, string $path)
    {
        if (! function_exists('exif_read_data') || @exif_imagetype($path) !== IMAGETYPE_JPEG) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $rotate = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($rotate !== 0) {
            $rotated = imagerotate($image, $rotate, 0);
            if ($rotated) {
                imagedestroy($image);

                return $rotated;
            }
        }

        return $image;
    }

    private function resize($image, int $maxSide)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $longest = max($w, $h);

        if ($longest <= $maxSide) {
            return $image;
        }

        $ratio = $maxSide / $longest;
        $resized = imagescale($image, (int) round($w * $ratio), (int) round($h * $ratio), IMG_BICUBIC);
        if (! $resized) {
            return $image;
        }
        imagedestroy($image);
        imagealphablending($resized, true);
        imagesavealpha($resized, true);

        return $resized;
    }
}
