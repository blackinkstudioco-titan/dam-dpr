<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaDpr;
use App\Models\Fraksi;
use App\Models\KomisiDpr;

class AnggotaDprController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $anggota = AnggotaDpr::query()
            ->where('nama', 'LIKE', "%{$term}%")
            ->limit(20)
            ->get();

        $results = $anggota->map(function ($a) {
            return [
                'id' => $a->id,
                'text' => "{$a->nama} - {$a->fraksi} ({$a->dapil})"
            ];
        });

        return response()->json($results);
    }
    public function index()
    {
        $anggota = AnggotaDpr::with(['fraksi', 'komisi'])->paginate(10);
        return view('anggota_dpr.index', compact('anggota'));
    }

    public function create()
    {
        //$fraksi = Fraksi::all();
        //$komisi = KomisiDpr::all();
        return view('anggota_dpr.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'dapil' => 'required|string|max:255',
            'periode_terpilih' => 'required|string|max:100',
            'fraksi' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:50',
            //'dapil' => 'nullable|string|max:150',
            //'jabatan' => 'nullable|string|max:100',
            //'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        /*
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('anggota_dpr', 'public');
        }
        */
        //dd($validated);
        AnggotaDpr::create($validated);
        return redirect()->route('anggota-dpr.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(AnggotaDpr $anggota_dpr)
    {
        $fraksi = Fraksi::all();
        $komisi = KomisiDpr::all();
        return view('anggota_dpr.edit', compact('anggota_dpr', 'fraksi', 'komisi'));
    }

    public function update(Request $request, AnggotaDpr $anggota_dpr)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'dapil' => 'required|string|max:255',
            'periode_terpilih' => 'required|string|max:100',
            'fraksi' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:50',
            //'dapil' => 'nullable|string|max:150',
            //'jabatan' => 'nullable|string|max:100',
            //'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        /*
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('anggota_dpr', 'public');
        }
        */

        $anggota_dpr->update($validated);
        return redirect()->route('anggota-dpr.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(AnggotaDpr $anggota_dpr)
    {
        if ($anggota_dpr->foto && file_exists(storage_path('app/public/' . $anggota_dpr->foto))) {
            unlink(storage_path('app/public/' . $anggota_dpr->foto));
        }

        $anggota_dpr->delete();
        return redirect()->route('anggota-dpr.index')->with('success', 'Data berhasil dihapus!');
    }

}
