<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\ArtikelPublish;
use App\Models\KomisiDpr;
use App\Models\Event;
use App\Models\AnggotaDpr;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    public function __construct(protected ImageService $imageService) {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $search = $request->get('q');
        //$query = Artikel::query()->orderByDesc('id');
        
        if (auth()->user()?->hasAnyRole(['admin', 'editor'])){
          $query = Artikel::with('event')
            ->leftJoin('artikel_publish', 'artikel.id', '=', 'artikel_publish.artikel_draft_id')
            ->select('artikel.*')
            ->selectRaw('IF(artikel_publish.id IS NULL, 0, 1) as is_published')
            ->orderByDesc('artikel.id');
        }
        else
        {
            $query = Artikel::with('event')
            ->leftJoin('artikel_publish', 'artikel.id', '=', 'artikel_publish.artikel_draft_id')
            ->select('artikel.*')
            ->where('artikel.add_by', Auth::id())
            ->selectRaw('IF(artikel_publish.id IS NULL, 0, 1) as is_published')
            ->orderByDesc('artikel.id');
        }
        if ($search) {
            $query->where('artikel.judul', 'like', "%$search%");
                  //->Where('artikel.penulis', 'like', "%$search%")
                  //->Where('artikel.keyword', 'like', "%$search%");
        }
     

        
        //dd($query->toSql());

        $artikels = $query->paginate(10);
        return view('data-artikel.index', compact('artikels', 'search'));
    }

    public function create()
    {

        $komisi = KomisiDpr::all();
        //DB::enableQueryLog();
        $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
        return view('data-artikel.create',compact('penugasan','komisi'));
    }

    public function store(Request $request)
    {
    \Log::info('Store method called');
    \Log::info('Request data:', $request->all());
    //exit();
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'isi' => 'required|string',
            'penulis' => 'required|string',
            'sumber' => 'required|string',
            'keyword' => 'required|string',
            'subyek' => 'required|string',
            'foto' => 'nullable|image|max:2048',
            'event_id' => 'nullable|integer',
            'komisi_dpr_id' => 'nullable|integer',
            'anggota_dpr_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('foto')) {
            //$validated['foto'] = $request->file('foto')->store('artikel', 'public');
            $uploadResult = $this->imageService->uploadImage($request->file('foto'));
            $validated['foto'] = $uploadResult['original_path'];
            
        }
        $tanggalWaktu = date('Y-m-d H:i:s', strtotime($request->tanggal . ' ' . $request->waktu));

        $validated['add_by'] = Auth::id();
        $validated['add_date'] = now();
        $validated['active'] = 1;
        $validated['del'] = 0;
        $validated['tanggal'] = $tanggalWaktu;
        //dd($validated);
        Artikel::create($validated);


        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $komisi = KomisiDpr::all();
        //DB::enableQueryLog();
        $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
        $artikel = Artikel::where('id', $id)
                ->where('add_by', Auth::id())
            ->first();
        if($artikel){
         return view('data-artikel.edit', compact('artikel','penugasan','komisi'));
        }
        else{
          return redirect()->route('artikel.index')->with('error', 'Anda tidak memiliki izin untuk mengedit artikel ini.');
        }
        
    }

    public function update(Request $request, $id)
    {
        $artikel = Artikel::findOrFail($id);

        $validated = $request->validate([
          'judul' => 'required|string|max:255',
          'tanggal' => 'required|date',
          'isi' => 'required|string',
          'penulis' => 'required|string',
          'sumber' => 'required|string',
          'keyword' => 'required|string',
          'subyek' => 'required|string',
          'foto' => 'nullable|image|max:2048',
          'event_id' => 'nullable|integer',
          'komisi_dpr_id' => 'nullable|integer',
          'anggota_dpr_id' => 'nullable|integer',
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

        $tanggalWaktu = date('Y-m-d H:i:s', strtotime($request->tanggal . ' ' . $request->waktu));
        $validated['tanggal'] = $tanggalWaktu;
        $validated['edit_by'] = Auth::id();
        $validated['edit_date'] = now();

        $artikel->update($validated);
        // Cek tombol yang ditekan
        if ($request->input('action') === 'kirim_editor') {
          //print_r($validate);exit;

          $validated['artikel_draft_id']=$id;
          $validated['active']=0;
          $validated['edit_by'] = null;
          $validated['add_date'] = now();
          $validated['add_by'] = Auth::id();
          ArtikelPublish::firstOrCreate(
              ['artikel_draft_id' => $id],
              $validated
          );

        }

        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function show($id)
    {
         $artikel = Artikel::where('id', $id)
                ->where('add_by', Auth::id())
            ->first();
        if($artikel){
         return view('data-artikel.show', compact('artikel'));
        }
        else{
          return redirect()->route('artikel.index')->with('error', 'Anda tidak memiliki izin untuk mengedit artikel ini.');
        }
        
    }


    public function destroy($id)
    {
        $artikel = Artikel::findOrFail($id);
        $artikel->delete();
        return back()->with('success', 'Artikel dihapus.');
    }

    //editor artikel
    public function editor(Request $request){
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

}
