<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    // SECURITY FIX: 'artikel' and 'artikel/*' used to be exempted from CSRF
    // verification entirely, which allowed any external site to forge
    // requests (as a logged-in editor/admin) that create, edit, or delete
    // articles. All the artikel forms already send a valid @csrf token and
    // the only fetch() calls under these routes are GETs (which Laravel's
    // CSRF middleware never checks anyway), so no exemption is needed.
    protected $except = [
        //
    ];
}
