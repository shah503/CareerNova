<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsParent
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isParent()) {
            abort(403, 'Unauthorized - Parent access required');
        }

        return $next($request);
    }
}