<?php

namespace App\Http\Middleware;

use Closure;

class Role
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
        if (auth('officer')->user()->role->name == $name) {
            return $next($request);
        }

        return redirect('/admin/dashboard');
    }
}
