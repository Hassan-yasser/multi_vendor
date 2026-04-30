<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserCanManageProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || (! $user->is_admin && $user->store_id === null)) {
            abort(403, __('Only administrators and store users can access profile.'));
        }

        return $next($request);
    }
}
