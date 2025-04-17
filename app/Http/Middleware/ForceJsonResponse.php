<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        if ($request->is('api/*')) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            
        }
        return $next($request);
    }
}


