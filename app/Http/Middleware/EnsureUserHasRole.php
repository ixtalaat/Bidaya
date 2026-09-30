<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $request->expectsJson()
                ? abort(403)
                : redirect()->route('login');
        }

        $allowedRoles = collect($roles)
            ->map(fn (string $role) => Role::tryFrom($role))
            ->filter()
            ->all();

        if (! $user->hasRole(...$allowedRoles)) {
            abort(403);
        }

        return $next($request);
    }
}
