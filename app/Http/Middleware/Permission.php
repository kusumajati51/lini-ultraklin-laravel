<?php

namespace App\Http\Middleware;

use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $name)
    {
        if (!auth('officer')->user()->hasPermission($name)) {
            return redirect('/admin/dashboard');
        }

        return $next($request);
    }
}
