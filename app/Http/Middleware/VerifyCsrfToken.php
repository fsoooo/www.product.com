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
        //
        'api/ins/qx/call_back',
        'api/ins/wk/call_back/check',
        'api/ins/wk/call_back/pay',
        'question/*'
    ];
}
