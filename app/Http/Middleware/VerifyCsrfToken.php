<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/*',

		// handle old app
		'auth',
        'Auth',
        'Login',
        'login',
        'promo/beta',
        'v2/*',
        'Order/*'
    ];
}
