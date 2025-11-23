<?php

namespace App\Http\Controllers;

use App\Models\KategoriFoto;
use Illuminate\Http\Request;

class KategoriFotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $kategoriFoto = KategoriFoto::query()
            ->search($search)
            ->withCount('dataFoto')
            ->orderBy('k_name', 'asc')
            ->paginate(10);

        return view('kategori-foto.index', compact('kategoriFoto', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori-foto.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'k_name' => 'required|string|max:255|unique:kategori_foto,k_name',
        ], [
            'k_name.required' => 'Nama kategori harus diisi',
            'k_name.unique' => 'Nama kategori sudah digunakan',
            'k_name.max' => 'Nama kategori maksimal 255 karakter',
        ]);

        KategoriFoto::create($validated);

        return redirect()->route('kategori-foto.index')
            ->with('success', 'Kategori foto berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriFoto $kategoriFoto)
    {
        $kategoriFoto->loadCount('dataFoto');
        $photos = $kategoriFoto->dataFoto()->paginate(12);

        return view('kategori-foto.show', compact('kategoriFoto', 'photos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriFoto $kategoriFoto)
    {
        return view('kategori-foto.edit', compact('kategoriFoto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriFoto $kategoriFoto)
    {
        $validated = $request->validate([
            'k_name' => 'required|string|max:255|unique:kategori_foto,k_name,' . $kategoriFoto->id,
        ], [
            'k_name.required' => 'Nama kategori harus diisi',
            'k_name.unique' => 'Nama kategori sudah digunakan',
            'k_name.max' => 'Nama kategori maksimal 255 karakter',
        ]);

        $kategoriFoto->update($validated);

        return redirect()->route('kategori-foto.index')
            ->with('success', 'Kategori foto berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriFoto $kategoriFoto)
    {
        // Check if category has photos
        if ($kategoriFoto->dataFoto()->count() > 0) {
            return redirect()->route('kategori-foto.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki ' . $kategoriFoto->dataFoto()->count() . ' foto');
        }

        $kategoriFoto->delete();

        return redirect()->route('kategori-foto.index')
            ->with('success', 'Kategori foto berhasil dihapus');
    }
}