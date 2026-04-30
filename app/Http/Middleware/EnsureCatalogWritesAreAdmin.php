<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets any authenticated user list/view catalog entities; only admins may create/update/delete.
 */
final class EnsureCatalogWritesAreAdmin
{
    /** @var list<string> */
    private const READ_ONLY_ACTIONS = ['index', 'show'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        $action = $request->route()?->getActionMethod();
        if ($action !== null && in_array($action, self::READ_ONLY_ACTIONS, true)) {
            return $next($request);
        }

        if (! $user->is_admin) {
            abort(403, __('Only administrators can manage categories.'));
        }

        return $next($request);
    }
}
