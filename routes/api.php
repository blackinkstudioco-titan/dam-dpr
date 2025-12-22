<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\AnggotaDprController;

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

// Keyword API Routes
Route::prefix('keywords')->group(function () {
    Route::get('search', [KeywordController::class, 'search']);
    Route::get('popular', [KeywordController::class, 'popular']);
    Route::post('store', [KeywordController::class, 'store']);
});
// routes/web.php atau routes/api.php
Route::prefix('api/anggota-dpr')->group(function () {
    Route::get('search', [AnggotaDprController::class, 'search']); // ✅ Gunakan function search
});


