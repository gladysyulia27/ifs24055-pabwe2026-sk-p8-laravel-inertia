<?php

namespace App\Providers;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\Services\OpenAiPostGeneratorService;
use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Models\Post;
use App\Features\Post\Policies\PostPolicy;
use App\Features\Post\Repositories\PostRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use PDO;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(AiClientInterface::class, OpenAiPostGeneratorService::class);
    }

    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);

        if (! $this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            $request = request();
            $host = $request->getHost();
            if (
                $request->isSecure()
                || $request->header('X-Forwarded-Proto') === 'https'
                || str_ends_with($host, '.s1if.cloud')
                || str_starts_with((string) config('app.url'), 'https://')
            ) {
                URL::forceScheme('https');
            }
        }

        $this->ensureDatabaseReady();
    }

    private function ensureDatabaseReady(): void
    {
        if ($this->app->runningUnitTests()) {
            return;
        }

        $default = config('database.default');

        if ($default === 'sqlite') {
            $this->ensureSqliteFile();

            return;
        }

        if ($default === 'pgsql') {
            if (extension_loaded('pdo_pgsql')) {
                $cfg = config('database.connections.pgsql');
                $host = $cfg['host'] ?? '127.0.0.1';
                $port = $cfg['port'] ?? '5432';
                $db = $cfg['database'] ?? 'pabwe_p8_blog';
                $user = $cfg['username'] ?? 'postgres';
                $pass = $cfg['password'] ?? '';

                // 1. Coba koneksi langsung ke database target (penting untuk server deployment seperti Delcom)
                try {
                    new PDO(
                        "pgsql:host={$host};port={$port};dbname={$db}",
                        $user,
                        $pass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 3,
                        ]
                    );

                    return;
                } catch (Throwable) {
                    // Jika database belum ada di lokal, coba buat melalui maintenance db 'postgres'
                }

                // 2. Coba buat database jika belum ada (untuk environment lokal)
                try {
                    $pdo = new PDO(
                        "pgsql:host={$host};port={$port};dbname=postgres",
                        $user,
                        $pass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 2,
                        ]
                    );

                    $stmt = $pdo->prepare('SELECT 1 FROM pg_database WHERE datname = ?');
                    $stmt->execute([$db]);

                    if (! $stmt->fetchColumn()) {
                        $safeDb = str_replace('"', '""', $db);
                        $pdo->exec("CREATE DATABASE \"{$safeDb}\"");
                    }

                    return;
                } catch (Throwable) {
                    // Fall through ke SQLite hanya jika PostgreSQL benar-benar tidak dapat diakses
                }
            }

            if (extension_loaded('pdo_sqlite')) {
                $sqlitePath = $this->ensureSqliteFile();
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $sqlitePath,
                    'queue.batching.database' => 'sqlite',
                    'queue.failed.database' => 'sqlite',
                ]);
            }
        }
    }

    private function ensureSqliteFile(): string
    {
        $path = config('database.connections.sqlite.database', database_path('database.sqlite'));

        if ($path !== ':memory:') {
            $dir = dirname($path);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            if (! file_exists($path)) {
                @touch($path);
            }
        }

        return $path;
    }
}
