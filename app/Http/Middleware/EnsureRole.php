<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        abort_unless(
            $user && ($user->role === $role || $user->role === 'admin'),
            403,
            'Akses tidak diizinkan.'
        );

        return $next($request);
    }
}
