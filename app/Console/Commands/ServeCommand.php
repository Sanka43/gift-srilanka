<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

use function Illuminate\Support\php_binary;

class ServeCommand extends BaseServeCommand
{
    /**
     * Get the full server command.
     * Use project root server.php if it exists; otherwise use a router in storage/app/
     * so we never depend on the framework's missing server.php.
     *
     * @return array<int, string>
     */
    protected function serverCommand(): array
    {
        $rootRouter = base_path('server.php');
        $storageRouter = storage_path('app/server.php');

        if (is_file($rootRouter)) {
            $server = realpath($rootRouter);
        } elseif (is_file($storageRouter)) {
            $server = realpath($storageRouter);
        } else {
            $this->ensureServerPhpExists($rootRouter, $storageRouter);
            $server = is_file($storageRouter) ? realpath($storageRouter) : null;
        }

        if ($server === false || $server === null) {
            $this->error('Could not create or find a server router. Ensure storage/app/ is writable or create server.php in the project root.');
            exit(1);
        }

        return [
            php_binary(),
            '-S',
            $this->host().':'.$this->port(),
            $server,
        ];
    }

    /**
     * Create server router: try project root first, then storage/app/server.php.
     */
    protected function ensureServerPhpExists(string $rootPath, string $storagePath): void
    {
        $stubRoot = <<<'PHP'
<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}
require_once __DIR__.'/public/index.php';
PHP;
        $stubStorage = <<<'PHP'
<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__.'/../../public'.$uri)) {
    return false;
}
require_once __DIR__.'/../../public/index.php';
PHP;

        if (@file_put_contents($rootPath, $stubRoot) !== false) {
            return;
        }
        $dir = dirname($storagePath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (@file_put_contents($storagePath, $stubStorage) === false) {
            $this->warn("Could not write router to project root or storage/app. Permission denied.");
        }
    }
}
