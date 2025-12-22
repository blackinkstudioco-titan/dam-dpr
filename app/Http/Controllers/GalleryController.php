<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DataFoto;

class GalleryController extends Controller
{
    public function getImages()
    {
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
        $foto = DataFoto::select('id', 'original_foto_url as url', 'judul')->get();

        // Map ke struktur yang sama
        $images = $foto->map(function ($item) {
            $path = $item->url; // ini adalah path relatif di disk 'public', contoh: 'uploads/galeri/2025/12/img-001.jpg'

            // Bangun URL publik; pakai fallback jika file tidak ada
            $publicUrl = ($path && Storage::disk('public')->exists($path))
                ? Storage::url($path)
                : asset('images/no-image.png');

            return [
                'url'   => $publicUrl,                  // URL publik untuk ditampilkan/insert di editor
                'name'  => $item->judul ?: basename($path ?? ''), // nama/label (judul jika ada, atau nama file)
                'path'  => $path,                       // path relatif (dari DB), berguna jika perlu operasi file
                'id'    => $item->id,                   // (opsional) simpan id kalau kamu butuh
            ];
        });

        return response()->json($images);
        
    }
}