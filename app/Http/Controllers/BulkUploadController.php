<?php

namespace App\Http\Controllers;

use App\Models\AlbumFoto;
use App\Models\DataFoto;
use App\Models\KategoriFoto;
use App\Models\KomisiDpr;
use App\Models\AnggotaDpr;
use App\Models\Event;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\BulkUploadFotoRequest;
use App\Services\BulkUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BulkUploadController extends Controller
{
    protected $uploadService;

    /**
     * How long an upload receipt (see uploadFiles()) stays valid before it
     * must be consumed by saveMetadata()/deleteFile().
     */
    protected const RECEIPT_TTL_HOURS = 2;

    public function __construct(BulkUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        $this->middleware('auth');
    }

    /**
     * SECURITY FIX (AUTHZ-VULN-07): only staff (admin/editor) or the
     * album's own creator may upload/attach photos to it. Previously any
     * authenticated user could pass any album_id and have it accepted.
     */
    private function ensureCanManageAlbum(int $albumId): AlbumFoto
    {
        $album = AlbumFoto::findOrFail($albumId);
        $user = Auth::user();

        if ($user && $user->hasAnyRole(['admin', 'editor'])) {
            return $album;
        }
        if (!$user || $album->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }

        return $album;
    }

    private function receiptKey(string $fileId): string
    {
        return 'bulk_upload:' . $fileId;
    }

    /**
     * Tampilkan halaman bulk upload
     */
    public function index()
    {
        $albums = AlbumFoto::withCount('fotos')
            ->latest()
            ->get();
        $penugasan = Event::whereMonth('tanggal', now()->month) ->whereYear('tanggal', now()->year)->get();
        $komisi = KomisiDpr::all();
        $kategoriFoto = KategoriFoto::orderBy('k_name', 'asc')->get();
        return view('bulk-upload.index', compact('albums','penugasan','komisi','kategoriFoto'));
    }

    /**
     * Step 1: Buat album baru
     */
    public function storeAlbum(StoreAlbumRequest $request)
    {
        try {
            $album = AlbumFoto::create([
                'nama_album' => $request->nama_album,
                'deskripsi' => $request->deskripsi,
                'event_id' => $request->event_id,
                'komisi_dpr_id' => $request->komisi_dpr_id,
                'kategori_foto_id' => $request->kategori_foto_id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Album berhasil dibuat',
                'data' => [
                    'id' => $album->id,
                    'nama_album' => $album->nama_album,
                    'deskripsi' => $album->deskripsi,
                    'event_id' => $request->event_id,
                    'komisi_dpr_id' => $request->komisi_dpr_id,
                    'kategori_foto_id' => $request->kategori_foto_id,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating album: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat album: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 2: Upload foto ke temporary storage
     */
    public function uploadFiles(Request $request)
    {
        $request->validate([
            'album_id' => 'required|exists:album_foto,id',
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:15360',
        ]);

        $album = $this->ensureCanManageAlbum((int) $request->album_id);

        try {
            $albumId = $album->id;
            $file = $request->file('file');

            // Upload dan extract data
            $uploadData = $this->uploadService->uploadFoto($file, $albumId);

            // SECURITY FIX (INJ-VULN-07 / AUTHZ-VULN-07): original_path and
            // thumbnail_path used to be echoed back to the client and
            // trusted verbatim in saveMetadata()/deleteFile() below. They're
            // now kept server-side in a short-lived, user- and
            // album-scoped receipt keyed by file_id.
            $fileId = (string) Str::uuid();
            Cache::put($this->receiptKey($fileId), [
                'user_id' => Auth::id(),
                'album_id' => $albumId,
                'original_path' => $uploadData['original_foto_url'],
                'thumbnail_path' => $uploadData['thumbnail_foto_url'],
                'file_size' => $uploadData['f_size'],
                'file_name' => $uploadData['file_name'],
                'meta_data' => $uploadData['meta_data'],
            ], now()->addHours(self::RECEIPT_TTL_HOURS));

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'file_id' => $fileId,
                    'file_name' => $uploadData['file_name'],
                    'file_size' => $uploadData['f_size'],
                    'thumbnail_url' => asset('storage/' . $uploadData['thumbnail_foto_url']),
                    'meta_data' => $uploadData['meta_data'],
                    'album_id' => $albumId,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 3: Simpan metadata foto
     */
    public function saveMetadata(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.file_id' => 'required|string',
            'photos.*.judul' => 'required|string|max:255',
            'photos.*.deskrp' => 'nullable|string',
            'photos.*.k_word' => 'nullable|string',
            'photos.*.f_lok' => 'nullable|string',
            'photos.*.perekam' => 'nullable|string',
            'photos.*.subyek' => 'nullable|string',
            'photos.*.k_name' => 'nullable|string',
            'photos.*.konseptor' => 'nullable|string',
            'photos.*.l_access' => 'nullable|integer|in:1,2,3',
            'photos.*.kategorisasi_datatempo' => 'nullable|integer',
            'photos.*.publish' => 'nullable|in:0,1',
        ]);

        // SECURITY FIX (INJ-VULN-07 / AUTHZ-VULN-07 / AUTHZ-VULN-27):
        // resolve every photo's storage path + album from the server-side
        // upload receipt instead of the client-supplied original_path/
        // thumbnail_path/album_id. A receipt that's missing, expired, or
        // belongs to a different user is rejected outright.
        $resolved = [];
        foreach ($request->photos as $photoData) {
            $receipt = Cache::get($this->receiptKey($photoData['file_id']));

            if (!$receipt || $receipt['user_id'] !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi upload untuk salah satu foto sudah tidak valid. Silakan upload ulang.',
                ], 422);
            }

            // Re-check album ownership at save time too (album_id is only
            // ever taken from the receipt, never from the client).
            $this->ensureCanManageAlbum((int) $receipt['album_id']);

            $resolved[] = ['input' => $photoData, 'receipt' => $receipt];
        }

        DB::beginTransaction();

        try {
            $lastMetaData = null;
            $firstAlbumId = null;

            foreach ($resolved as $item) {
                $photoData = $item['input'];
                $receipt = $item['receipt'];
                $firstAlbumId ??= $receipt['album_id'];

                $uploadData = [
                    'original_foto_url' => $receipt['original_path'],
                    'thumbnail_foto_url' => $receipt['thumbnail_path'],
                    'f_size' => $receipt['file_size'],
                    'meta_data' => $receipt['meta_data'] ?? [],
                    'album_id' => $receipt['album_id'],
                    'file_name' => $receipt['file_name'] ?? 'untitled.jpg',
                ];

                $metaData = [
                    'judul' => $photoData['judul'],
                    'deskrp' => $photoData['deskrp'] ?? null,
                    'k_word' => $photoData['k_word'] ?? null,
                    'f_lok' => $photoData['f_lok'] ?? null,
                    'perekam' => $photoData['perekam'] ?? null,
                    'subyek' => $photoData['subyek'] ?? null,
                    'k_name' => $photoData['k_name'] ?? null,
                    'konseptor' => $photoData['konseptor'] ?? null,
                    'l_access' => $photoData['l_access'] ?? 1,
                    'kategorisasi_datatempo' => $photoData['kategorisasi_datatempo'] ?? null,
                    'komisi_dpr_id' => $photoData['komisi_dpr_id'] ?? null,
                    'event_id' => $photoData['event_id'] ?? null,
                    'publish' => $photoData['publish'] ?? 0,
                    'status' => 0, // 0 untuk draft
                ];

                $this->uploadService->saveFotoData($uploadData, $metaData);
                Cache::forget($this->receiptKey($photoData['file_id']));
                $lastMetaData = $metaData;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($resolved) . ' foto berhasil disimpan',
                'data' => [
                    'total' => count($resolved),
                    'album_id' => $firstAlbumId,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save metadata', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data untuk dropdown/select
     */
    public function getFormData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'kategori' => KategoriFoto::select('id', 'k_name')->get(),
                'komisi' => KomisiDpr::select('id', 'nama_komisi')->get(),
                'anggota' => AnggotaDpr::select('id', 'nama')->get(),
            ]
        ]);
    }

    /**
     * Delete temporary uploaded file
     *
     * SECURITY FIX (INJ-VULN-06 / AUTHZ-VULN-06 / AUTHZ-VULN-27): this used
     * to accept an arbitrary `paths[]` array from the client and delete
     * whatever was in it — any authenticated user could delete any file on
     * the public disk this way. It now only deletes files belonging to a
     * receipt (see uploadFiles()) issued to the current user, i.e. only
     * files that user just uploaded and hasn't saved yet.
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'file_id' => 'required|string',
        ]);

        $receipt = Cache::get($this->receiptKey($request->file_id));

        if (!$receipt || $receipt['user_id'] !== Auth::id()) {
            // Nothing to do — either already deleted/consumed, or not this
            // user's upload. Report success either way so the UI can drop
            // it from the preview list without leaking which case it was.
            return response()->json(['success' => true]);
        }

        try {
            $this->uploadService->deleteUploadedFiles([
                $receipt['original_path'],
                $receipt['thumbnail_path'],
            ]);

            Cache::forget($this->receiptKey($request->file_id));

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file'
            ], 500);
        }
    }
}
