<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataFotoController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\ArtikelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test-artikel-create', function() {
    return 'Route artikel create works!';
});

Route::get('/', [FrontEndController::class, 'index'])->name('home');
Route::get('/search', [FrontEndController::class, 'search'])->name('search');
Route::get('/list-artikel', [FrontEndController::class, 'artikel'])->name('list-artikel');
Route::get('/read-artikel/{slug}/{artikel}', [FrontEndController::class, 'read_artikel'])->name('read-artikel');
Route::get('/foto', [FrontEndController::class, 'foto'])->name('foto');
Route::get('/foto-detail/{slug}/{dataFoto}', [FrontEndController::class, 'show'])->name('foto-detail');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



});


Route::middleware(['auth'])->group(function () {
  Route::resource('artikel', ArtikelController::class);
});
/*
// Admin, Editor, Uploader bisa create
Route::middleware(['role:admin,editor,uploader'])->group(function () {
  Route::get('/artikel/create', [ArtikelController::class, 'create'])->name('artikel.create');
  Route::post('/artikel', [ArtikelController::class, 'store'])->name('artikel.store');
  //Route::resource('artikel', ArtikelController::class)->only(['create', 'store']);
});
//editor
Route::middleware(['role:admin,editor,uploader'])->group(function () {
  Route::get('/artikel/{id}/edit', [ArtikelController::class, 'edit'])->name('artikel.edit');
  Route::put('/artikel/{id}', [ArtikelController::class, 'update'])->name('artikel.update');
  Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy'])->name('artikel.destroy');
});
*/

Route::middleware(['auth'])->group(function () {
  Route::middleware(['role:admin'])->group(function () {
      Route::resource('users', UserController::class);
  });

  // Admin, Editor, Uploader bisa create
  Route::middleware(['role:admin,editor,uploader'])->group(function () {
        Route::resource('data-foto', DataFotoController::class)->only(['create', 'store']);

  });
  // Admin dan Editor bisa edit dan delete
   Route::middleware(['role:admin,editor'])->group(function () {
       Route::resource('data-foto', DataFotoController::class)->only(['edit', 'update', 'destroy']);
       //Route::get('/artikel/{id}/edit', [ArtikelController::class, 'edit'])->name('artikel.edit');
       //Route::put('/artikel/{id}', [ArtikelController::class, 'update'])->name('artikel.update');
       //Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy'])->name('artikel.destroy');
   });
  // Semua user bisa lihat (index dan show)
  Route::resource('data-foto', DataFotoController::class)->only(['index', 'show']);
  //Route::get('/artikel', [ArtikelController::class, 'index'])->name('artikel.index');
  //Route::get('/artikel/{id}', [ArtikelController::class, 'show'])->name('artikel.show');
});



Route::middleware(['auth'])->group(function () {
    // Additional routes HARUS di atas resource routes
    Route::post('data-foto/bulk-delete', [DataFotoController::class, 'bulkDelete'])
        ->name('data-foto.bulk-delete');
    Route::get('data-foto/{dataFoto}/download', [DataFotoController::class, 'download'])
        ->name('data-foto.download');
    Route::post('data-foto/{dataFoto}/toggle-publish', [DataFotoController::class, 'togglePublish'])
        ->name('data-foto.toggle-publish');
    Route::post('data-foto/{dataFoto}/increment-view', [DataFotoController::class, 'incrementView'])
        ->name('data-foto.increment-view');

    //Route::middleware(['role:admin'])->group(function () {
      // Resource routes di paling bawah
    //  Route::resource('data-foto', DataFotoController::class);
    //});
});



require __DIR__.'/auth.php';
