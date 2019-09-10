<?php

namespace App\Http\Middleware;

use Closure;

class UserStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($request->user()->store)) {
            return response()->json([
                'error' => 1,
                'message' => 'Sorry, you not have a store.'
            ], 400);
        }

        return $next($request);
    }
}
