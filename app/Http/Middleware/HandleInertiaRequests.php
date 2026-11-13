<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user()?->only('id', 'name', 'username', 'email', 'role', 'bio', 'avatar_path'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'generatedArticles' => fn () => $request->session()->get('generatedArticles'),
            ],
            'categories' => \App\Features\Post\Enums\PostCategory::options(),
        ]);
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = parent::handle($request, $next);

        if ($request->isMethod('GET') && ! $request->header('X-Inertia') && $response->getStatusCode() === 200) {
            $response->headers->set('Cache-Control', 'public, max-age=0, must-revalidate');
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');
        }

        return $response;
    }
}
