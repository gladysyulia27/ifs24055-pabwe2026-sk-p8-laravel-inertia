<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve static build assets with immutable 1-year cache headers (for Lighthouse cache policy audit)
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (str_starts_with($requestUri, '/assets-cached/')) {
    $relativePath = substr($requestUri, strlen('/assets-cached'));
    $targetFile = realpath(__DIR__.$relativePath);
    $buildDir = realpath(__DIR__.'/build');

    if ($targetFile && $buildDir && str_starts_with($targetFile, $buildDir) && is_file($targetFile)) {
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $mimeTypes = [
            'js' => 'application/javascript; charset=utf-8',
            'mjs' => 'application/javascript; charset=utf-8',
            'css' => 'text/css; charset=utf-8',
            'map' => 'application/json; charset=utf-8',
            'json' => 'application/json; charset=utf-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
        ];

        $etag = '"'.md5_file($targetFile).'"';
        header('Content-Type: '.($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=31536000, immutable');
        header('ETag: '.$etag);
        header('Vary: Accept-Encoding');

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            exit;
        }

        $content = file_get_contents($targetFile);
        $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        if ($content !== false && str_contains($acceptEncoding, 'gzip') && function_exists('gzencode') && in_array($ext, ['js', 'mjs', 'css', 'map', 'json', 'svg'], true)) {
            $compressed = gzencode($content, 6);
            if ($compressed !== false) {
                header('Content-Encoding: gzip');
                header('Content-Length: '.strlen($compressed));
                echo $compressed;
                exit;
            }
        }

        header('Content-Length: '.filesize($targetFile));
        readfile($targetFile);
        exit;
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
