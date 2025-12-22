<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaDpr;
use App\Models\Fraksi;
use App\Models\KomisiDpr;
use Illuminate\Http\JsonResponse;

class AnggotaDprController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('term', ''));
        
        // Log detail
        \Log::info('=== SEARCH DPR DEBUG ===');
        \Log::info('Raw term: ' . var_export($term, true));
        \Log::info('Term length: ' . mb_strlen($term));
        \Log::info('Request all: ' . json_encode($request->all()));
        
        if (mb_strlen($term) < 2) {
            \Log::info('Term too short, returning empty');
            return response()->json([]);
        }
        
        if (mb_strlen($term) > 100) {
            \Log::info('Term too long, returning empty');
            return response()->json([]);
        }

        $likePattern = "%{$term}%";
        \Log::info('LIKE pattern: ' . $likePattern);

        $results = \App\Models\AnggotaDpr::where('nama', 'like', $likePattern)
            ->orderBy('nama', 'asc')
            ->limit(10)
            ->pluck('nama')
            ->filter()
            ->values()
            ->toArray();
        
        \Log::info('Results count: ' . count($results));
        \Log::info('Results: ' . json_encode($results));
        \Log::info('========================');

        return response()->json($results);
    }
    public function index(Request $request)
    {
            $search = $request->get('search');
            
            $anggota = AnggotaDpr::query()
                ->with(['fraksi', 'komisi'])
                ->when($search, function ($query, $search) {
                    return $query->where('nama', 'like', "%{$search}%")
                                ->orWhere('dapil', 'like', "%{$search}%")
                                ->orWhere('jenis_kelamin', 'like', "%{$search}%")
                                ->orWhereHas('fraksi', function ($q) use ($search) {
                                    $q->where('nama_fraksi', 'like', "%{$search}%");
                                })
                                ->orWhereHas('komisi', function ($q) use ($search) {
                                    $q->where('nama_komisi', 'like', "%{$search}%");
                                });
                })
                ->orderBy('nama', 'asc')
                ->paginate(10)
                ->withQueryString(); // Agar parameter search tetap ada saat pagination
            
            return view('anggota_dpr.index', compact('anggota', 'search'));
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
    /**
     * Search keywords for autocomplete.
     */
   public function searchDpr(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('term', ''));
        
        // Log detail
        \Log::info('=== SEARCH DPR DEBUG ===');
        \Log::info('Raw term: ' . var_export($term, true));
        \Log::info('Term length: ' . mb_strlen($term));
        \Log::info('Request all: ' . json_encode($request->all()));
        
        if (mb_strlen($term) < 2) {
            \Log::info('Term too short, returning empty');
            return response()->json([]);
        }
        
        if (mb_strlen($term) > 100) {
            \Log::info('Term too long, returning empty');
            return response()->json([]);
        }

        $likePattern = "%{$term}%";
        \Log::info('LIKE pattern: ' . $likePattern);

        $results = \App\Models\AnggotaDpr::where('nama', 'like', $likePattern)
            ->orderBy('nama', 'asc')
            ->limit(10)
            ->pluck('nama')
            ->filter()
            ->values()
            ->toArray();
        
        \Log::info('Results count: ' . count($results));
        \Log::info('Results: ' . json_encode($results));
        \Log::info('========================');

        return response()->json($results);
    }

}
