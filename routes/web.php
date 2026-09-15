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
use App\Http\Controllers\KeywordController;


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

  // SECURITY FIX (AUTHZ-VULN-08/17/18/28/55): artikel_publish (the
  // editorial queue that controls what actually goes live on the public
  // site) and the /editor listing had no role restriction at all — any
  // authenticated user could read, edit, or force-publish (active=1) any
  // record. ArtikelController::update() is the legitimate way a
  // non-editor pushes a draft to this queue (action=kirim_editor), so
  // gating this controller to admin/editor doesn't block that workflow.
  Route::middleware(['role:admin,editor'])->group(function () {
      Route::resource('artikel_publish', ArtikelPublishController::class);
      Route::get('editor', [ArtikelController::class, 'editor'])->name('artikel.editor');
  });

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
    // SECURITY FIX (AUTHZ-VULN-04/16): bulk-delete and toggle-publish used
    // to only require 'auth' (any logged-in user, any role) with no
    // ownership check in the controller either — now role-gated the same
    // way as create/edit/destroy, with ownership enforced in the
    // controller for the 'uploader' role (see ensureCanModify()).
    Route::middleware(['role:admin,editor,uploader'])->group(function () {
        // Additional routes HARUS di atas resource routes
        Route::post('data-foto/bulk-delete', [DataFotoController::class, 'bulkDelete'])
            ->name('data-foto.bulk-delete');
        Route::post('data-foto/{dataFoto}/toggle-publish', [DataFotoController::class, 'togglePublish'])
            ->name('data-foto.toggle-publish');
    });

    Route::get('data-foto/{dataFoto}/download', [DataFotoController::class, 'download'])
        ->name('data-foto.download');
    Route::post('data-foto/{dataFoto}/increment-view', [DataFotoController::class, 'incrementView'])
        ->name('data-foto.increment-view');
});

// BUG FIX: this block used to duplicate the route names below
// (add-photos.index/upload) while pointing at methods that don't exist on
// AddPhotosController (uploadFiles/saveMetadata/getFormData — those belong
// to BulkUploadController). It was dead code that nothing links to; if it
// were ever hit it would 500. Removed rather than left as a landmine.

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

// summernote gallery routes
// SECURITY FIX: this lists every photo in the DAM (including unpublished
// ones) for the article editor's image picker. It was previously public
// (no middleware at all), letting anyone on the internet enumerate every
// photo's title/description/URL. It's only ever used from inside the
// authenticated article editor, so it now requires login.
Route::middleware(['auth'])->group(function () {
    Route::get('/gallery/images', [GalleryController::class, 'getImages'])->name('gallery.images');

    // SECURITY FIX (AUTHZ-VULN-19): moved from routes/api.php, which runs
    // under the stateless 'api' middleware group (no session), so 'auth'
    // there never actually checked anything — this endpoint was reachable
    // with no cookie/token at all. Same URL, now actually behind login.
    Route::post('/api/keywords/store', [KeywordController::class, 'store'])->name('keywords.store');
});

require __DIR__.'/auth.php';
