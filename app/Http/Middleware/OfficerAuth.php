<?php

namespace App\Http\Middleware;

use Closure;
use Config;

class OfficerAuth
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
        Config::set('auth.guards.api.provider', 'officers');

        return $next($request);
    }
}
