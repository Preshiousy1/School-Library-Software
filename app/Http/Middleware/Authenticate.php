<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

use Closure;

class Authenticate extends Middleware
{

    public function handle($request, Closure $next, $type, $type2 = null, $type3 = null)
    {
        if ($type2 && $type3) {
            if (auth($type)->check() || auth($type2)->check() || auth($type3)->check()) {
                return $next($request);
            }
        } elseif ($type2) {
            if (auth($type)->check() || auth($type2)->check()) {
                return $next($request);
            }
        } else {
            if (auth($type)->check()) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
