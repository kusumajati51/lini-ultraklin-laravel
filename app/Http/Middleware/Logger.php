<?php

namespace App\Http\Middleware;

use Closure;

use App\Utils\LogUtil;

class Logger {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $name = 'api')
    {
        $response = $next($request);

        $log = new LogUtil('api');

        $log->createWithResponse($request, $response);

        return $response;
    }
}
