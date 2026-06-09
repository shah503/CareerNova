<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsTeacher
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized - Teacher access required');
        }

        return $next($request);
    }
}