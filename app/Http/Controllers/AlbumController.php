<?php
namespace App\Http\Controllers;

use App\Models\AlbumFoto;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = AlbumFoto::with('latestPhoto', 'creator')
            ->withCount('fotos')
            ->latest()
            ->paginate(12);

        return view('albums.index', compact('albums'));
    }
    public function show(AlbumFoto $album, Request $request)
    {
        $query = $album->fotos()
            ->with(['komisiDpr', 'anggotaDpr'])
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $search = '%' . $request->search . '%';
                    $query->where('judul', 'like', $search)
                          ->orWhere('deskrp', 'like', $search)
                          ->orWhere('k_word', 'like', $search);
                });
            })
            ->when($request->start_date, function($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->orderBy('created_at', 'desc');

        $photos = $query->paginate(20)->withQueryString();

        return view('albums.show', compact('album', 'photos'));
    }
}