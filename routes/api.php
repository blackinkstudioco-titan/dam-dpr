<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeywordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Keyword API Routes (read-only; safe to leave public — it's just search
// suggestions for the article editor's keyword field).
//
// SECURITY FIX (AUTHZ-VULN-19): POST keywords/store used to live here too.
// Routes in this file run under the stateless 'api' middleware group (no
// session, no CSRF), so "requiring login" isn't meaningful here — the
// front-end's session cookie is never even read for these routes. Since
// this app authenticates everything else via the session-based 'web' guard
// (not Sanctum tokens), the write endpoint was moved to routes/web.php
// under the existing 'auth' middleware group, where 'auth' actually means
// something. Same URL (/api/keywords/store), just registered from web.php.
Route::prefix('keywords')->group(function () {
    Route::get('search', [KeywordController::class, 'search']);
    Route::get('popular', [KeywordController::class, 'popular']);
});

// BUG FIX: this duplicated the 'api' prefix RouteServiceProvider already
// applies to this whole file, so it registered at /api/api/anggota-dpr/search
// — dead code nothing ever called (the real, working route is
// /api/anggota-dpr/search, defined in routes/web.php).


