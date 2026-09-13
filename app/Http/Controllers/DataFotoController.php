<?php

namespace App\Http\Controllers;

use App\Models\DataFoto;
use App\Models\User;
use App\Models\Event; //penugasan
use App\Models\KomisiDpr;
use App\Models\AnggotaDpr;
use App\Models\KategoriFoto;
use App\Services\ImageService;
use App\Http\Requests\StoreDataFotoRequest;
use App\Http\Requests\UpdateDataFotoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DataFotoController extends Controller
{
    /**
     * Image service instance.
     */
    public function __construct(
        protected ImageService $imageService
    ) {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

    if($request->cat=='artikel'):
      echo 'artikel';
    else:

        $query = DataFoto::query()->with('kategori:id,k_name');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by publish status
        if ($request->filled('status')) {
            match($request->status) {
                'published' => $query->where('publish', true),
                'unpublished' => $query->where('publish', false),
                default => null
            };
        }

        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategorisasi_datatempo', $request->kategori);
        }

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by subject
        if ($request->filled('subyek')) {
            $query->bySubject($request->subyek);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        if (auth()->user()?->hasAnyRole(['uploader'])){
            $query->where('add_by', Auth::id());
        }


        $dataFoto = $query->paginate($request->get('per_page', 12))
                         ->withQueryString();

        return view('data-foto.index', [
            'dataFoto' => $dataFoto,
            'kategoriFoto' => KategoriFoto::all(),
        ]);
      endif;


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $komisi = KomisiDpr::all();
        DB::enableQueryLog();
        $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
        //$sql = vsprintf(str_replace('?', '%s', $penugasan->toSql()), $penugasan->getBindings());
        //dd($sql);
        //dd($penugasan);
        $kategoriFoto = KategoriFoto::pluck('k_name', 'id');
        return view('data-foto.create', compact('komisi', 'kategoriFoto','penugasan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDataFotoRequest $request)
    {
        DB::beginTransaction();

        try {
          //dd(Auth::user()->id);
          // Validasi ukuran file
           $request->validate([
               'foto' => 'required|image|mimes:jpeg,jpg,png,gif|max:15360', // 15 MB
           ]);
            // Upload image and extract EXIF
            $uploadResult = $this->imageService->uploadImage($request->file('foto'));
            
            // Create new DataFoto with auto-generated MM ID
            $dataFoto = DataFoto::create([
                'judul' => $request->judul,
                'deskrp' => $request->deskrp,
                'k_word' => $request->k_word,
                'f_lok' => $uploadResult['filename'],
                'f_size' => $uploadResult['file_size'],
                'tgl_masuk' => now()->toDateString(),
                'mm_lok' => $request->mm_lok,
                'tgl_mm' => $request->tgl_mm,
                'perekam' => $request->perekam,
                'subyek' => $request->subyek,
                'k_name' => Auth::user()->name,
                'konseptor' => $request->konseptor,
                'depositor' => $request->depositor,
                'judul_en' => $request->judul_en,
                'deskrp_en' => $request->deskrp_en,
                'kategorisasi_datatempo' => $request->kategorisasi_datatempo,
                'publish' => $request->boolean('publish'),
                'meta_data' => $uploadResult['exif_data'],
                'thumbnail_foto_url' => $uploadResult['thumbnail_path'],
                'original_foto_url' => $uploadResult['original_path'],
                'anggota_dpr_id' => $request->anggota_dpr_id,
                'komisi_dpr_id' => $request->komisi_dpr_id,
                'event_id' => $request->event_id, //penugasan
                'add_by' => Auth::user()->id,
                'add_date' => now(),
                'anggota_dpr' => $request->anggota_dpr,
            ]);

            // Update data
            //$dataFoto->update($updateData);

            // Save keywords to keyword table
            \App\Models\Keyword::createFromString($request->k_word);

            DB::commit();

            return redirect()
                ->route('data-foto.index')
                ->with('success', 'Data foto berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error storing foto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat upload: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataFoto $dataFoto)
    {
        // Load relationship
        $dataFoto->load(
            'kategori:id,k_name',
            'anggotaDpr:id,nama,fraksi_id,komisi_dpr_id',
            'anggotaDpr.fraksi:id,nama_fraksi',
            'KomisiDpr:id,nama_komisi',
        );

        // Increment view counter
        $dataFoto->incrementView();

        return view('data-foto.show', [
            'dataFoto' => $dataFoto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataFoto $dataFoto)
    {
        
        // Pastikan hanya pemilik data yang bisa akses
        if (auth()->user()?->hasAnyRole(['uploader'])){
            if ($dataFoto->add_by !== Auth::id()) {
                    abort(403, 'Anda tidak memiliki akses untuk mengedit foto ini.');
            }
        }

        // Load relationship
        $dataFoto->load('kategori:id,k_name');
        $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
        $komisi = KomisiDpr::all();
        return view('data-foto.edit', [
            'dataFoto' => $dataFoto,
            'penugasan'=>$penugasan,
            'komisi'=>$komisi,
            'kategoriFoto' => KategoriFoto::getDropdownOptions(),
        ]);
    }

    /**
     * SECURITY FIX (AUTHZ-VULN-09): mirrors the ownership check already in
     * edit() — an uploader could not open the edit FORM for someone else's
     * photo, but could still POST straight to update()/destroy()/
     * bulkDelete()/togglePublish() for that same photo, since none of them
     * repeated the check. admin/editor are unrestricted by design.
     */
    private function ensureCanModify(DataFoto $dataFoto): void
    {
        if (auth()->user()?->hasAnyRole(['uploader']) && $dataFoto->add_by !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah foto ini.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataFotoRequest $request, DataFoto $dataFoto)
    {
        $this->ensureCanModify($dataFoto);

        DB::beginTransaction();

        try {


            $updateData = [
                'judul' => $request->judul,
                'deskrp' => $request->deskrp,
                'k_word' => $request->k_word,
                'mm_lok' => $request->mm_lok,
                'tgl_mm' => $request->tgl_mm,
                'perekam' => $request->perekam,
                'subyek' => $request->subyek,
                'konseptor' => $request->konseptor,
                'depositor' => $request->depositor,
                'judul_en' => $request->judul_en,
                'deskrp_en' => $request->deskrp_en,
                'kategorisasi_datatempo' => $request->kategorisasi_datatempo,
                'publish' => $request->boolean('publish'),
                'anggota_dpr_id' => $request->anggota_dpr_id,
                'komisi_dpr_id' => $request->komisi_dpr_id,
                'edit_by' => Auth::user()->id,
                'edit_date' => now(),
                'event_id' => $request->event_id, //penugasan 
                'anggota_dpr' => $request->anggota_dpr,
            ];

            // Check if new photo is uploaded
            if ($request->hasFile('foto')) {
                $request->validate([
                    'foto' => 'required|image|mimes:jpeg,jpg,png,gif|max:15360', // 15 MB
                ]);
                // Delete old photo files
                if ($dataFoto->thumbnail_foto_url) {
                    $this->imageService->deleteImage($dataFoto->thumbnail_foto_url, 'thumbnail');
                }
                if ($dataFoto->original_foto_url) {
                    $this->imageService->deleteImage($dataFoto->original_foto_url, 'original');
                }

                // Upload new photo
                $uploadResult = $this->imageService->uploadImage($request->file('foto'));

                $updateData = array_merge($updateData, [
                    'f_lok' => $uploadResult['filename'],
                    'f_size' => $uploadResult['file_size'],
                    'meta_data' => $uploadResult['exif_data'],
                    'thumbnail_foto_url' => $uploadResult['thumbnail_path'],
                    'original_foto_url' => $uploadResult['original_path'],
                ]);
            }
            $dataFoto->update($updateData);
            // Save keywords to keyword table
            \App\Models\Keyword::createFromString($request->k_word);

            DB::commit();

            return redirect()
                ->route('data-foto.index')
                ->with('success', 'Data foto berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating foto: ' . $e->getMessage(), [
                'foto_id' => $dataFoto->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat update: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataFoto $dataFoto)
    {
        $this->ensureCanModify($dataFoto);

        DB::beginTransaction();

        try {
            // Delete photo files
            if ($dataFoto->thumbnail_foto_url) {
                $this->imageService->deleteImage($dataFoto->thumbnail_foto_url, 'thumbnail');
            }
            if ($dataFoto->original_foto_url) {
                $this->imageService->deleteImage($dataFoto->original_foto_url, 'original');
            }

            // Delete record
            $dataFoto->delete();

            DB::commit();

            return redirect()
                ->route('data-foto.index')
                ->with('success', 'Data foto berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting foto: ' . $e->getMessage(), [
                'foto_id' => $dataFoto->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('data-foto.index')
                ->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Download photo file.
     *
     * SECURITY/BUSINESS-LOGIC FIX: this method used to hand out the raw,
     * un-watermarked original to ANY authenticated user (any role,
     * including the low-privilege "guest" role), which defeated the whole
     * point of the watermarking system described for this app — only
     * admin/editor/uploader should get originals, everyone else should get
     * a watermarked copy. The watermarking logic already existed in a
     * download_wm() method, but no route ever pointed to it, so it was
     * dead code and never actually protected anything. That logic is now
     * merged into this method (which is the one every view actually links
     * to), so the intended access control is finally enforced.
     */
    public function download(DataFoto $dataFoto)
    {
        if (!$dataFoto->original_foto_url || !Storage::disk('public')->exists($dataFoto->original_foto_url)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($dataFoto->original_foto_url);
        $fileName = $dataFoto->judul . '_' . $dataFoto->f_lok;

        $user = Auth::user();
        $allowedRoles = ['admin', 'editor', 'uploader'];

        // Increment download counter
        $dataFoto->incrementDownload();

        // Admin/editor/uploader get the original file, unmodified.
        if ($user && in_array($user->role, $allowedRoles)) {
            return response()->download($filePath, $fileName);
        }

        // Everyone else (e.g. self-registered "guest" role accounts) gets
        // a watermarked copy instead of the raw original.
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $manager = new ImageManager(new Driver());
        $image = $manager->read($filePath);

        $watermarkPath = public_path('images/wm3_dpr_ri_logo.png');
        if (!file_exists($watermarkPath)) {
            abort(500, 'Watermark tidak ditemukan.');
        }

        $watermark = $manager->read($watermarkPath);
        $watermark->resize(512, null);

        $image->place(
            $watermark,        // element
            'bottom-right',    // position
            20,                // offset X
            40,                // offset Y
            80                 // opacity
        );

        $tempPath = storage_path('app/public/temp_' . uniqid() . '.jpg');
        $tempDir = dirname($tempPath);

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $image->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Increment view counter via AJAX.
     */
    public function incrementView(DataFoto $dataFoto)
    {
        $dataFoto->incrementView();

        return response()->json([
            'success' => true,
            'views' => $dataFoto->fresh()->view,
        ]);
    }

    /**
     * Bulk delete multiple photos.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:data_foto,id',
        ]);

        DB::beginTransaction();

        try {
            $photos = DataFoto::whereIn('id', $request->ids)->get();

            // SECURITY FIX (AUTHZ-VULN-04/16): this route only required
            // 'auth' (no role check at all), and never checked ownership,
            // so any authenticated user could mass-delete any photos by id.
            // The route now also requires role:admin,editor,uploader (see
            // routes/web.php); this closes the remaining gap for uploaders.
            foreach ($photos as $photo) {
                $this->ensureCanModify($photo);
            }

            foreach ($photos as $photo) {
                // Delete files
                if ($photo->thumbnail_foto_url) {
                    $this->imageService->deleteImage($photo->thumbnail_foto_url, 'thumbnail');
                }
                if ($photo->original_foto_url) {
                    $this->imageService->deleteImage($photo->original_foto_url, 'original');
                }

                $photo->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' foto berhasil dihapus!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error bulk deleting fotos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto.',
            ], 500);
        }
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(DataFoto $dataFoto)
    {
        // SECURITY FIX (AUTHZ-VULN-16): same missing role/ownership check
        // as bulkDelete() above — the route now also requires
        // role:admin,editor,uploader (see routes/web.php).
        $this->ensureCanModify($dataFoto);

        try {
            $dataFoto->update([
                'publish' => !$dataFoto->publish,
                'edit_by' => Auth::user()->name,
                'edit_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'publish' => $dataFoto->publish,
                'message' => $dataFoto->publish ? 'Foto berhasil dipublish!' : 'Foto berhasil di-unpublish!',
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling publish status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }
}
