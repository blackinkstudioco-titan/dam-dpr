<?php

namespace App\Http\Controllers;

use App\Models\ArtikelPublish;
use App\Models\Event;
use App\Models\KomisiDpr;
use App\Models\AnggotaDpr;
use App\Models\KategoriFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtikelPublishController extends Controller
{
    public function index(Request $request)
    {
      $search = $request->get('q');
      $query = ArtikelPublish::query()->orderByDesc('id');

      if ($search) {
          $query->where('judul', 'like', "%$search%")
                ->orWhere('penulis', 'like', "%$search%")
                ->orWhere('keyword', 'like', "%$search%");
      }

      $artikels = $query->paginate(10);
      return view('data-artikel.index-artikel-editor', compact('artikels', 'search'));
    }

    public function create()
    {
        return view('artikel_publish.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'artikel_draft_id' => 'nullable|integer',
            'tanggal' => 'nullable|date',
            'rubrik' => 'nullable|string|max:7',
            'penulis' => 'nullable|string',
            'sumber' => 'nullable|string',
            'keyword' => 'nullable|string|max:250',
            'subyek' => 'nullable|string',
            'judul' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'deskripsi' => 'nullable|string',
            'isi' => 'nullable|string',
            'active' => 'boolean',
            'del' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('artikel_publish', 'public');
        }

        // SECURITY FIX: sanitize rich-text body before storing — see
        // ArtikelController for the full rationale (stored XSS via {!! !!}).
        if (isset($validated['isi'])) {
            $validated['isi'] = sanitize_rich_text($validated['isi']);
        }

        $validated['add_by'] = Auth::id();
        $validated['add_date'] = now();

        ArtikelPublish::create($validated);

        return redirect()->route('artikel_publish.index')->with('success', 'Artikel berhasil dipublikasikan.');
    }

    public function show($id)
    {
      $artikel = ArtikelPublish::findOrFail($id);
      return view('data-artikel.show-editor', compact('artikel'));
    }

    public function edit($id)
    {
          $komisi = KomisiDpr::all();
           //DB::enableQueryLog();
          $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
          $artikel = ArtikelPublish::findOrFail($id);
          $kategori = KategoriFoto::pluck('k_name', 'id');
          return view('data-artikel.edit-editor', compact('artikel','penugasan','komisi','kategori'));
    }

    public function update(Request $request, $id)
    {
        $artikel = ArtikelPublish::findOrFail($id);

        $validated = $request->validate([
          'judul' => 'required|string|max:255',
          'tanggal' => 'required|date',
          'isi' => 'required|string',
          'penulis' => 'required|string',
          'sumber' => ' nullable|string',
          'keyword' => 'required|string',
          'subyek' => 'nullable|string',
          'foto' => 'nullable|image|max:2048',
          'event_id' => 'nullable|integer',
          'komisi_dpr_id' => 'nullable|integer',
          'anggota_dpr_id' => 'nullable|integer',
          'anggota_dpr' => 'nullable|string',
          'kategori_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('foto')) {
            if ($artikel->foto) {
                Storage::disk('public')->delete($artikel->foto);
            }
            $validated['foto'] = $request->file('foto')->store('artikel', 'public');
        }
        // SECURITY FIX (INJ-VULN-10): see ArtikelController::update() — do
        // not trust a client-supplied `old_foto` field; leaving 'foto'
        // untouched when no new file is uploaded keeps the DB value as-is
        // and closes the path-poisoning-then-delete attack chain.

        // SECURITY FIX: same rich-text sanitization as store() above.
        $validated['isi'] = sanitize_rich_text($validated['isi']);

        $tanggalWaktu = date('Y-m-d H:i:s', strtotime($request->tanggal . ' ' . $request->waktu));
        $validated['tanggal'] = $tanggalWaktu;
        $validated['edit_by'] = Auth::id();
        $validated['edit_date'] = now();
        $validated['active']=$request->input('active');
        $artikel->update($validated);

        return redirect()->route('artikel_publish.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(ArtikelPublish $artikel_publish)
    {
        if ($artikel_publish->foto) {
            Storage::disk('public')->delete($artikel_publish->foto);
        }

        $artikel_publish->delete();

        return redirect()->route('artikel_publish.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
