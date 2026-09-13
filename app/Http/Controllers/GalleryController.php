<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DataFoto;

class GalleryController extends Controller
{
    public function getImages()
    {
        // SECURITY FIX (AUTHZ-VULN-13): this listed every photo in the DAM,
        // published or not, to whoever could call it. Route access is now
        // gated by 'auth' (see routes/web.php), but that alone still lets
        // any authenticated role (including the low-privilege "guest" role,
        // who can create their own articles) enumerate unpublished/internal
        // photos through this picker. Non-staff callers now only see
        // published photos, matching what they're allowed to view directly
        // via DataFotoController::show()/download(); admin/editor keep
        // seeing everything, since they legitimately need to insert
        // not-yet-published photos while drafting.
        $user = Auth::user();
        $onlyPublished = !($user && $user->hasAnyRole(['admin', 'editor']));
        // Opsi 1: Dari database
        // $images = Image::select('id', 'path as url', 'name')->get();
        
        // Opsi 2: Dari storage folder
        /*
        $files = Storage::disk('public')->allFiles();
        
        $images = collect($files)->map(function($file) {
            return [
                'url' => Storage::url($file),
                'name' => basename($file),
                'path' => $file
            ];
        });
        */
        
       

        // Ambil dari DB: id, url (alias), judul
        $foto = DataFoto::select('id', 'thumbnail_foto_url as url', 'judul', 'deskrp', 'publish')
            ->when($onlyPublished, fn ($q) => $q->where('publish', 1))
            ->get();

        // Map ke struktur yang sama
        $images = $foto->map(function ($item) {
            //$path = $item->url; // ini adalah path relatif di disk 'public', contoh: 'uploads/galeri/2025/12/img-001.jpg'
            $path = $item->url ? ltrim($item->url, '/') : null;
            // Bangun URL publik; pakai fallback jika file tidak ada
            $publicUrl = ($path && Storage::disk('public')->exists($path))
                ? Storage::url($path)
                : asset('images/no-image.png');

            return [
                'url'   => $publicUrl,                  // URL publik untuk ditampilkan/insert di editor
                'name'  => $item->judul ?: basename($path ?? ''), // nama/label (judul jika ada, atau nama file)
                'path'  => $path,                       // path relatif (dari DB), berguna jika perlu operasi file
                'id'    => $item->id,                   // (opsional) simpan id kalau kamu butuh
                'deskrp'=> $item->deskrp                // deskripsi tambahan
            ];
        });

        return response()->json($images);
        
    }
}