<?php

namespace App\Providers;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\Services\OpenAiPostGeneratorService;
use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Models\Post;
use App\Features\Post\Policies\PostPolicy;
use App\Features\Post\Repositories\PostRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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

        Vite::createAssetPathsUsing(fn (string $path) => '/assets-cached/'.ltrim($path, '/'));

        // @codeCoverageIgnoreStart
        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            $this->ensureLocalDatabaseReady();
        }
        // @codeCoverageIgnoreEnd
    }

    // @codeCoverageIgnoreStart
    private function ensureLocalDatabaseReady(): void
    {
        $default = config('database.default');

        if ($default === 'sqlite') {
            $this->ensureSqliteFile();

            return;
        }

        if ($default === 'pgsql') {
            $cfg = config('database.connections.pgsql');
            $host = $cfg['host'] ?? '127.0.0.1';

            // Jangan ubah konfigurasi database pada server deployment (mis. delcom-deployment-postgres)
            if (! in_array($host, ['127.0.0.1', 'localhost'], true)) {
                return;
            }

            if (extension_loaded('pdo_pgsql')) {
                $port = $cfg['port'] ?? '5432';
                $db = $cfg['database'] ?? 'pabwe_p8_blog';
                $user = $cfg['username'] ?? 'postgres';
                $pass = $cfg['password'] ?? '';

                try {
                    new PDO(
                        "pgsql:host={$host};port={$port};dbname={$db}",
                        $user,
                        $pass,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 2,
                        ]
                    );

                    return;
                } catch (Throwable) {
                    // Coba buat database lokal jika belum ada
                }

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
                    // Abaikan jika gagal
                }
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
    // @codeCoverageIgnoreEnd
}
