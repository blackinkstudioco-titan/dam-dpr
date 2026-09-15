<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // SECURITY FIX: the previous config used paths => [..., '*'] (every route
    // in the app, not just the API) together with allowed_origins => ['*']
    // and supports_credentials => true. That combination makes the browser
    // mirror the request's Origin header back with credentials allowed,
    // which lets ANY external website read authenticated, cookie-based
    // responses from this app via cross-origin fetch/XHR — effectively a
    // full CORS bypass of the session cookie for every route, not just the
    // API. CORS is now scoped to just the stateless API/Sanctum endpoints,
    // and cross-origin origins must be explicitly allow-listed via env.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Comma-separated list of trusted origins, e.g. in .env:
    // CORS_ALLOWED_ORIGINS=https://dam-dpr.example.go.id
    // Leave unset to allow no cross-origin access (same-origin only).
    'allowed_origins' => array_filter(explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
