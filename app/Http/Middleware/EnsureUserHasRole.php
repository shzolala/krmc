<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Support\PanelRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        $allowed = collect($roles)
            ->map(fn (string $role): UserRole => UserRole::from($role))
            ->all();

        if (! $user->hasRole(...$allowed)) {
            return redirect()
                ->route(PanelRegistry::homeRouteFor($user))
                ->with('status', 'به پنل مربوط به نقش‌های خودتان هدایت شدید.');
        }

        return $next($request);
    }
}
