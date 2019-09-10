<?php

namespace App\Http\Middleware;

use Closure;

class OldApiToken
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
        if ($request->has('apiKey')) {
            $token = $request->apiKey;
        }
        else if ($request->has('token')) {
            $token = $request->token;
        }
        else {
            $token = null;
        }

        $user = \App\User::where('token', $token)->first();

        if (is_null($user)) {
            return response()->json([
                'error' => 'Unauthenticated.'
            ]);
        }

        return $next($request);
    }
}
