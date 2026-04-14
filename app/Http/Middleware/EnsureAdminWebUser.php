<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminWebUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if (! $u || ! method_exists($u, 'isAdmin') || ! $u->isAdmin()) {
            abort(403, 'Chỉ dành cho quản trị viên.');
        }

        return $next($request);
    }
}
