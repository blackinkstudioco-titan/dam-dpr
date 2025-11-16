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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkUploadController extends Controller
{
    protected $uploadService;

    public function __construct(BulkUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
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
        return view('bulk-upload.index', compact('albums','penugasan','komisi'));
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

        try {
            $albumId = $request->album_id;
            $file = $request->file('file');

            // Upload dan extract data
            $uploadData = $this->uploadService->uploadFoto($file, $albumId);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'file_id' => uniqid('temp_'),
                    'file_name' => $uploadData['file_name'],
                    'file_size' => $uploadData['f_size'],
                    'original_path' => $uploadData['original_foto_url'],
                    'thumbnail_path' => $uploadData['thumbnail_foto_url'],
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
        // Add logging
        Log::info('Received metadata request', [
            'photos' => $request->photos
        ]);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.file_id' => 'required',
            'photos.*.judul' => 'required|string|max:255',
            'photos.*.deskrp' => 'nullable|string',
            'photos.*.k_word' => 'nullable|string',
            'photos.*.f_lok' => 'nullable|string',
            'photos.*.perekam' => 'nullable|string',
            'photos.*.subyek' => 'nullable|string',
            'photos.*.k_name' => 'nullable|string',
            'photos.*.konseptor' => 'nullable|string',
            'photos.*.l_access' => 'nullable|integer|in:1,2,3',
            'photos.*.kategorisasi_datatempo' => 'nullable|exists:kategori_foto,id',
            //'photos.*.komisi_dpr_id' => 'nullable|exists:komisi_dpr,id',
            //'photos.*.anggota_dpr_id' => 'nullable|exists:anggota_dpr,id',
            'photos.*.publish' => 'nullable|in:0,1', // Ubah dari boolean ke in:0,1
            'photos.*.original_path' => 'required',
            'photos.*.thumbnail_path' => 'required',
            'photos.*.album_id' => 'required|exists:album_foto,id',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->photos as $photoData) {
                // Add logging for each photo
                Log::info('Processing photo metadata', [
                    'photo_data' => $photoData
                ]);
                
                $uploadData = [
                    'original_foto_url' => $photoData['original_path'],
                    'thumbnail_foto_url' => $photoData['thumbnail_path'],
                    'f_size' => $photoData['file_size'] ?? 0,
                    'meta_data' => $photoData['meta_data'] ?? [],
                    'album_id' => $photoData['album_id'],
                    'file_name' => $photoData['file_name'] ?? 'untitled.jpg',
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
                ];

                // Cast status ke integer
                $metaData['status'] = 0; // 0 untuk draft

                $foto = $this->uploadService->saveFotoData($uploadData, $metaData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->photos) . ' foto berhasil disimpan',
                'data' => [
                    'all data' => $metaData,
                    'total' => count($request->photos),
                    'album_id' => $request->photos[0]['album_id'] ?? null,
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
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
        ]);

        try {
            $this->uploadService->deleteUploadedFiles($request->paths);

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
