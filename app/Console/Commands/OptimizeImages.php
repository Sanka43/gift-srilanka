<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--max=1200 : Max width/height in px} {--quality=82} {--dir=* : Subfolders of storage/app/public (default: products, categories, avatars)} {--dry-run} {--root= : Base folder (default storage/app/public; use public/storage if the disk root is public)}';

    protected $description = 'Resize and recompress existing uploaded images in place (file names stay the same)';

    public function handle(ImageOptimizer $optimizer): int
    {
        $dirs = $this->option('dir') ?: ['products', 'categories', 'avatars'];
        $max = (int) $this->option('max');
        $quality = (int) $this->option('quality');
        $totalBefore = $totalAfter = $count = 0;

        foreach ($dirs as $dir) {
            $root = $this->option('root') ?: storage_path('app/public');
            $base = rtrim($root, '/\\') . '/' . trim($dir, '/');
            if (! is_dir($base)) {
                continue;
            }

            foreach (File::allFiles($base) as $file) {
                if (! in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }
                $size = $dir === 'avatars' ? min($max, 400) : $max;

                if ($this->option('dry-run')) {
                    $before = $after = $file->getSize();
                } else {
                    [$before, $after] = $optimizer->optimizeFile($file->getRealPath(), $size, $quality);
                }

                $totalBefore += $before;
                $totalAfter += $after;
                $count++;
            }
        }

        $this->info(sprintf(
            '%d images: %.1f MB -> %.1f MB (saved %.1f MB)',
            $count,
            $totalBefore / 1048576,
            $totalAfter / 1048576,
            ($totalBefore - $totalAfter) / 1048576
        ));

        return self::SUCCESS;
    }
}
