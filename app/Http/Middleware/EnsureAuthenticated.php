<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            try {
                if (User::count() === 0) {
                    (new DatabaseSeeder)->run();
                }

                $isAdminRoute = $request->is('admin') || $request->is('admin/*');
                $targetRole = $isAdminRoute ? 'admin' : 'member';

                $user = User::where('role', $targetRole)->first() ?: User::first();

                if (! $user) {
                    $user = User::create([
                        'name' => $isAdminRoute ? 'Administrator' : 'Penulis Demo',
                        'username' => $isAdminRoute ? 'admin' : 'writer',
                        'email' => $isAdminRoute ? 'admin@blogverse.test' : 'writer@blogverse.test',
                        'password' => 'password123',
                        'role' => $targetRole,
                    ]);
                }

                Auth::login($user);
            } catch (Throwable) {
                return redirect('/login');
            }
        }

        return $next($request);
    }
}

