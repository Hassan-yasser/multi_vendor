<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the user is linked to a store (store owner / staff) — not administrators-only panel users.
 */
final class EnsureUserBelongsToStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null || $user->store_id === null) {
            abort(403, __('You must belong to a store to manage products.'));
        }

        return $next($request);
    }
}
