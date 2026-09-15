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

    /**
     * SECURITY FIX (AUTHZ-VULN-11/14): admin/editor can manage any album;
     * everyone else (uploader, or any other non-staff role) is restricted
     * to albums they created. The previous checks only ever tested for the
     * 'uploader' role specifically, so a non-staff account with a
     * different role (e.g. a bare "guest") wasn't restricted at all.
     */
    private function ensureOwnsAlbumOrStaff(AlbumFoto $album): void
    {
        $user = Auth::user();
        if ($user && $user->hasAnyRole(['admin', 'editor'])) {
            return;
        }
        if (!$user || $album->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }
    }

    public function index()
    {
        
        $query = AlbumFoto::with(['latestPhoto', 'creator', 'event:id,nama_event'])
            ->withCount('fotos')
            ->latest();

        if (auth()->user() && !auth()->user()->hasAnyRole(['admin', 'editor'])) {
            $query->where('created_by', Auth::id());
        }

        $albums = $query->paginate(12);

        return view('albums.index', compact('albums'));
    }
    public function show(AlbumFoto $album, Request $request)
    {
        $this->ensureOwnsAlbumOrStaff($album);

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
            $this->ensureOwnsAlbumOrStaff($album);

            $komisi = KomisiDpr::all();
            $event = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
            return view('albums.edit', compact('album','komisi','event'));
        }

    public function update(Request $request, AlbumFoto $album)
        {
            $this->ensureOwnsAlbumOrStaff($album);

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
            $this->ensureOwnsAlbumOrStaff($album);

            $album->delete();

            return redirect()->route('albums.index')->with('success', 'Album berhasil dihapus.');
        }
}