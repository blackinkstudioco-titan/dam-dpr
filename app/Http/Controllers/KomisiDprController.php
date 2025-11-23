<?php

namespace App\Http\Controllers;

use App\Models\KomisiDpr;
use Illuminate\Http\Request;

class KomisiDprController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $komisiDpr = KomisiDpr::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_komisi', 'like', "%{$search}%")
                            ->orWhere('bidang', 'like', "%{$search}%");
            })
            ->orderBy('nama_komisi', 'asc')
            ->paginate(10);

        return view('komisi-dpr.index', compact('komisiDpr', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('komisi-dpr.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_komisi' => 'required|string|max:255|unique:komisi_dpr,nama_komisi',
            'bidang' => 'nullable|string',
        ], [
            'nama_komisi.required' => 'Nama komisi harus diisi',
            'nama_komisi.unique' => 'Nama komisi sudah digunakan',
            'nama_komisi.max' => 'Nama komisi maksimal 255 karakter',
        ]);

        KomisiDpr::create($validated);

        return redirect()->route('komisi-dpr.index')
            ->with('success', 'Komisi DPR berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(KomisiDpr $komisiDpr)
    {
        return view('komisi-dpr.show', compact('komisiDpr'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KomisiDpr $komisiDpr)
    {
        return view('komisi-dpr.edit', compact('komisiDpr'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KomisiDpr $komisiDpr)
    {
        $validated = $request->validate([
            'nama_komisi' => 'required|string|max:255|unique:komisi_dpr,nama_komisi,' . $komisiDpr->id,
            'bidang' => 'nullable|string',
        ], [
            'nama_komisi.required' => 'Nama komisi harus diisi',
            'nama_komisi.unique' => 'Nama komisi sudah digunakan',
            'nama_komisi.max' => 'Nama komisi maksimal 255 karakter',
        ]);

        $komisiDpr->update($validated);

        return redirect()->route('komisi-dpr.index')
            ->with('success', 'Komisi DPR berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KomisiDpr $komisiDpr)
    {
        $komisiDpr->delete();

        return redirect()->route('komisi-dpr.index')
            ->with('success', 'Komisi DPR berhasil dihapus');
    }
}