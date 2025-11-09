<?php

namespace App\Http\Controllers;

use App\Models\ArtikelPublish;
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
          $artikel = ArtikelPublish::findOrFail($id);
          return view('data-artikel.edit-editor', compact('artikel'));
    }

    public function update(Request $request, $id)
    {
        $artikel = ArtikelPublish::findOrFail($id);

        $validated = $request->validate([
          'judul' => 'required|string|max:255',
          'tanggal' => 'required|date',
          'isi' => 'required|string',
          'penulis' => 'required|string',
          'sumber' => 'required|string',
          'keyword' => 'required|string',
          'subyek' => 'required|string',
          'foto' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            if ($artikel->foto) {
                Storage::disk('public')->delete($artikel->foto);
            }
            $validated['foto'] = $request->file('foto')->store('artikel', 'public');
        }
        else{
            $validated['foto']=$request->input('old_foto');

        }

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
