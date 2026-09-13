<?php
namespace App\Http\Controllers;

use App\Models\AlbumFoto;
use App\Models\KomisiDpr;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AlbumController extends Controller
{
     public function __construct(
        
    ) {
        $this->middleware('auth');
    }
    public function index()
    {
        
        $query = AlbumFoto::with(['latestPhoto', 'creator', 'event:id,nama_event'])
            ->withCount('fotos')
            ->latest();

        if (auth()->user()?->hasAnyRole(['uploader'])) {
            $query->where('created_by', Auth::id());
        }

        $albums = $query->paginate(12);

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
    public function edit(AlbumFoto $album)
        {
            $komisi = KomisiDpr::all();
            $event = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
            return view('albums.edit', compact('album','komisi','event'));
        }

    public function update(Request $request, AlbumFoto $album)
        {
            // SECURITY FIX: uploaders only see their own albums in index(),
            // but update()/destroy() had no equivalent check, so any
            // uploader could edit/delete another user's album just by
            // guessing/enumerating the album id in the URL.
            if (auth()->user()?->hasAnyRole(['uploader']) && $album->created_by !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah album ini.');
            }

            $validated = $request->validate([
                'nama_album' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'event_id' => 'nullable|exists:events,id',
                'komisi_dpr_id' => 'nullable|exists:komisi_dpr,id',
            ]);

            $album->update($validated);

            return redirect()->route('albums.index')->with('success', 'Album berhasil diperbarui.');
        }

    public function destroy(AlbumFoto $album)
        {
            // SECURITY FIX: same missing ownership check as update() above.
            if (auth()->user()?->hasAnyRole(['uploader']) && $album->created_by !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus album ini.');
            }

            $album->delete();

            return redirect()->route('albums.index')->with('success', 'Album berhasil dihapus.');
        }
}