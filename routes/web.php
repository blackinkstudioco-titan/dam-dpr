<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataFotoController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\AnggotaDprController;
use App\Http\Controllers\ArtikelPublishController;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AddPhotosController;
use App\Http\Controllers\ReportFotoController;
use App\Http\Controllers\ReportArtikelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KategoriFotoController;
use App\Http\Controllers\KomisiDprController;
use App\Http\Controllers\PhotoScheduleController;
use App\Http\Controllers\ArtikelScheduleController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ReportController;


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


Route::get('/api/anggota-dpr/search', [AnggotaDprController::class, 'search'])->name('anggota-dpr.search');

Route::get('/', [FrontEndController::class, 'index'])->name('home');
Route::get('/search', [FrontEndController::class, 'search'])->name('search');
Route::get('/list-artikel', [FrontEndController::class, 'artikel'])->name('list-artikel');
Route::get('/read-artikel/{slug}/{artikel_publish}', [FrontEndController::class, 'read_artikel'])
    ->name('read-artikel');

Route::get('/foto', [FrontEndController::class, 'foto'])->name('foto');
Route::get('/foto-detail/{slug}/{dataFoto}', [FrontEndController::class, 'show'])->name('foto-detail');



Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ... route lainnya
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth'])->group(function () {
  Route::resource('artikel', ArtikelController::class);
  Route::resource('artikel_publish', ArtikelPublishController::class);
  Route::get('editor', [ArtikelController::class, 'editor'])->name('artikel.editor');

  Route::prefix('foto/bulk-upload')->name('foto.bulk-upload')->group(function () {
        Route::get('/', [BulkUploadController::class, 'index'])->name('index');
        Route::post('/album', [BulkUploadController::class, 'storeAlbum'])->name('store-album');
        Route::post('/upload', [BulkUploadController::class, 'uploadFiles'])->name('upload-files');
        Route::post('/save-metadata', [BulkUploadController::class, 'saveMetadata'])->name('save-metadata');
        Route::get('/form-data', [BulkUploadController::class, 'getFormData'])->name('form-data');
        Route::post('/delete-file', [BulkUploadController::class, 'deleteFile'])->name('delete-file');
    });
  Route::resource('albums', AlbumController::class);

});


Route::middleware(['auth'])->group(function () {
  Route::middleware(['role:admin,editor'])->group(function () {
      Route::resource('users', UserController::class);
      Route::resource('anggota-dpr', AnggotaDprController::class);
  });

  // Admin, Editor, Uploader bisa create
  Route::middleware(['role:admin,editor,uploader'])->group(function () {
      Route::resource('data-foto', DataFotoController::class)->only(['create', 'store']);

  });
  // Admin dan Editor bisa edit dan delete
   Route::middleware(['role:admin,editor,uploader'])->group(function () {
       Route::resource('data-foto', DataFotoController::class)->only(['edit', 'update', 'destroy']);

   });
  // Semua user bisa lihat (index dan show)
  Route::resource('data-foto', DataFotoController::class)->only(['index', 'show']);

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

Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('albums.show');


Route::prefix('albums/{album}/add-photos')->name('add-photos.')->group(function () {
    Route::get('/', [AddPhotosController::class, 'index'])->name('index');
    Route::post('/upload', [AddPhotosController::class, 'uploadFiles'])->name('upload');
    Route::post('/save-metadata', [AddPhotosController::class, 'saveMetadata'])->name('save-metadata');
    Route::get('/form-data', [AddPhotosController::class, 'getFormData'])->name('form-data');
});


Route::prefix('foto')->group(function () {
    Route::prefix('add-photos')->name('add-photos.')->group(function () {
        Route::get('{album}', [AddPhotosController::class, 'index'])->name('index');
        Route::post('upload/{album}', [AddPhotosController::class, 'upload'])->name('upload');
        Route::post('store/{album}', [AddPhotosController::class, 'store'])->name('store');
    });
});

Route::middleware(['auth','role:admin,editor'])->group(function () {
    // Report Routes foto
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/foto', [ReportFotoController::class, 'index'])->name('foto.index');
        Route::get('/foto/export-excel', [ReportFotoController::class, 'exportExcel'])->name('foto.export.excel');
        Route::get('/foto/export-pdf', [ReportFotoController::class, 'exportPdf'])->name('foto.export.pdf');
    });
      // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        // Report Artikel
        Route::get('/artikel', [ReportArtikelController::class, 'index'])->name('artikel.index');
        Route::get('/artikel/export-excel', [ReportArtikelController::class, 'exportExcel'])->name('artikel.export.excel');
        Route::get('/artikel/export-pdf', [ReportArtikelController::class, 'exportPdf'])->name('artikel.export.pdf');
    });
    //Report AKD
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/akd/foto_akd', [ReportController::class, 'foto_akd'])->name('akd.foto_akd');
        Route::get('/akd/artikel_akd', [ReportController::class, 'artikel_akd'])->name('akd.artikel_akd');
        Route::get('/kegiatan/foto_kegiatan', [ReportController::class, 'foto_kegiatan'])->name('kegiatan.foto_kegiatan');
        Route::get('/kegiatan/artikel_kegiatan', [ReportController::class, 'artikel_kegiatan'])->name('kegiatan.artikel_kegiatan');
        Route::get('/anggota_dpr/foto_dpr', [ReportController::class, 'foto_dpr'])->name('anggota_dpr.foto_dpr');
        Route::get('/anggota_dpr/artikel_dpr', [ReportController::class, 'artikel_dpr'])->name('anggota_dpr.artikel_dpr');
    });
});



Route::middleware(['auth','role:admin,editor'])->group(function () {
    Route::resource('kategori-foto', KategoriFotoController::class);
});

Route::middleware(['auth','role:admin,editor'])->group(function () {
    Route::resource('events', EventController::class);
});

Route::middleware(['auth','role:admin,editor'])->group(function () {
    Route::resource('komisi-dpr', KomisiDprController::class);
});


Route::middleware(['auth','role:admin,editor'])->group(function () {
    Route::prefix('photo-schedule')->name('photo-schedule.')->group(function () {
        Route::get('/', [PhotoScheduleController::class, 'index'])->name('index');
        Route::get('/create', [PhotoScheduleController::class, 'create'])->name('create');
        Route::post('/', [PhotoScheduleController::class, 'store'])->name('store');
        Route::get('/{foto}/edit', [PhotoScheduleController::class, 'edit'])->name('edit');
        Route::put('/{foto}', [PhotoScheduleController::class, 'update'])->name('update');
        Route::post('/{foto}/cancel', [PhotoScheduleController::class, 'cancel'])->name('cancel');
        Route::delete('/{foto}', [PhotoScheduleController::class, 'destroy'])->name('destroy');
    });
});



Route::middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::prefix('artikel-schedule')->name('artikel-schedule.')->group(function () {
        Route::get('/', [ArtikelScheduleController::class, 'index'])->name('index');
        Route::get('/create', [ArtikelScheduleController::class, 'create'])->name('create');
        Route::post('/', [ArtikelScheduleController::class, 'store'])->name('store');
        Route::get('/{artikel}/edit', [ArtikelScheduleController::class, 'edit'])->name('edit');
        Route::put('/{artikel}', [ArtikelScheduleController::class, 'update'])->name('update');
        Route::post('/{artikel}/cancel', [ArtikelScheduleController::class, 'cancel'])->name('cancel');
        Route::delete('/{artikel}', [ArtikelScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/batch-cancel', [ArtikelScheduleController::class, 'batchCancel'])->name('batch-cancel');
    });
});

//summernote gallery routes
Route::get('/gallery/images', [GalleryController::class, 'getImages'])->name('gallery.images');

require __DIR__.'/auth.php';
