<?php
namespace App\Http\Controllers;

use App\Models\AlbumFoto;
use App\Models\DataFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AddPhotosController extends Controller
{
    protected $imageManager;

    /**
     * How long an upload receipt (see upload()) stays valid before it must
     * be consumed by store(). Generous enough for a slow multi-photo
     * metadata form, short enough that abandoned uploads don't linger.
     */
    protected const RECEIPT_TTL_HOURS = 2;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
        $this->middleware('auth');
    }

    /**
     * SECURITY FIX (AUTHZ-VULN-05 / AUTHZ-VULN-24): only staff (admin/
     * editor) or the album's own creator may add photos to it. Previously
     * any authenticated user could POST to any album id and have their
     * photos accepted into it.
     */
    private function ensureCanManageAlbum(AlbumFoto $album): void
    {
        $user = Auth::user();
        if ($user && $user->hasAnyRole(['admin', 'editor'])) {
            return;
        }
        if (!$user || $album->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }
    }

    private function receiptKey(string $fileId): string
    {
        return 'add_photos_upload:' . $fileId;
    }

    public function index(AlbumFoto $album)
    {
        $this->ensureCanManageAlbum($album);

        return view('add-photos.index', compact('album'));
    }

    public function upload(Request $request, AlbumFoto $album)
    {
        $this->ensureCanManageAlbum($album);

        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:15360'
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Extract EXIF data
            $exifData = $this->extractExifData($file->getRealPath());

            // Set paths
            $date = now();
            $basePath = $date->format('Y/m/d');
            $originalPath = "{$basePath}/photos/{$filename}";
            $thumbnailPath = "/thumbnails/{$filename}";

            // Store original
            $file->storeAs('public/' . dirname($originalPath), basename($originalPath));

            // Create thumbnail directory
            $thumbnailDir = storage_path('app/public' . dirname($thumbnailPath));
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Create thumbnail
            $image = $this->imageManager->read($file->getRealPath());
            $image->cover(300, 300);
            $image->save(storage_path('app/public' . $thumbnailPath), quality: 85);

            $fileSize = $file->getSize();
            $fileName = $file->getClientOriginalName();

            // SECURITY FIX (INJ-VULN-08 / AUTHZ-VULN-05): original_path and
            // thumbnail_path used to be echoed back to the client and then
            // trusted verbatim in store() below, letting a client submit any
            // path it wanted (pointing DataFoto records at arbitrary files
            // on the public disk). The authoritative paths are now kept
            // server-side in a short-lived, user- and album-scoped "receipt"
            // keyed by file_id; store() resolves paths from this receipt
            // instead of trusting the client's copy of them.
            $fileId = (string) Str::uuid();
            Cache::put($this->receiptKey($fileId), [
                'user_id' => Auth::id(),
                'album_id' => $album->id,
                'original_path' => $originalPath,
                'thumbnail_path' => $thumbnailPath,
                'file_size' => $fileSize,
                'file_name' => $fileName,
                'meta_data' => $exifData,
            ], now()->addHours(self::RECEIPT_TTL_HOURS));

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'file_id' => $fileId,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'thumbnail_url' => asset('storage' . $thumbnailPath),
                    'meta_data' => $exifData,
                    'album_id' => $album->id,
                    'event_id' => $album->event_id,
                    'kategorisasi_datatempo' => $album->kategori_foto_id?? null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract EXIF data from image
     */
    private function extractExifData(string $filePath): array
    {
        try {
            $exif = @exif_read_data($filePath);
            if (!$exif) {
                return [];
            }

            // Extract specific EXIF data we want to store
            $metadata = [
                'Make' => $exif['Make'] ?? null,
                'Model' => $exif['Model'] ?? null,
                'DateTime' => $exif['DateTime'] ?? null,
                'DateTimeOriginal' => $exif['DateTimeOriginal'] ?? null,
                'ExposureTime' => $exif['ExposureTime'] ?? null,
                'FNumber' => $exif['FNumber'] ?? null,
                'ISOSpeedRatings' => $exif['ISOSpeedRatings'] ?? null,
                'FocalLength' => $exif['FocalLength'] ?? null,
                'Flash' => $exif['Flash'] ?? null,
                'WhiteBalance' => $exif['WhiteBalance'] ?? null
            ];

            // Convert fraction values to readable format
            if (isset($metadata['ExposureTime']) && strpos($metadata['ExposureTime'], '/') !== false) {
                $metadata['ExposureTime'] = $this->convertToFraction($metadata['ExposureTime']);
            }

            if (isset($metadata['FNumber']) && strpos($metadata['FNumber'], '/') !== false) {
                list($num, $den) = explode('/', $metadata['FNumber']);
                $metadata['FNumber'] = number_format($num / $den, 1);
            }

            if (isset($metadata['FocalLength']) && strpos($metadata['FocalLength'], '/') !== false) {
                list($num, $den) = explode('/', $metadata['FocalLength']);
                $metadata['FocalLength'] = (int)($num / $den);
            }

            // Remove null values
            return array_filter($metadata, function($value) {
                return $value !== null;
            });

        } catch (\Exception $e) {
            Log::warning('Failed to extract EXIF data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Convert fraction string to readable format
     */
    private function convertToFraction(string $fraction): string
    {
        list($num, $den) = explode('/', $fraction);
        if ($den == 1) {
            return $num;
        }
        return "1/{$den}";
    }

    public function store(Request $request, AlbumFoto $album)
    {
        $this->ensureCanManageAlbum($album);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.file_id' => 'required|string',
            'photos.*.judul' => 'required|string|max:255',
        ]);

        // SECURITY FIX (INJ-VULN-08 / AUTHZ-VULN-05 / AUTHZ-VULN-27):
        // resolve every photo's storage path from the server-side upload
        // receipt (see upload() above) instead of trusting the client's
        // original_path/thumbnail_path/file_size/meta_data. A receipt that
        // is missing, expired, or was issued for a different user/album is
        // rejected outright rather than silently accepted.
        $resolved = [];
        foreach ($request->photos as $photo) {
            $receipt = Cache::get($this->receiptKey($photo['file_id']));

            if (
                !$receipt
                || $receipt['user_id'] !== Auth::id()
                || (int) $receipt['album_id'] !== (int) $album->id
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi upload untuk salah satu foto sudah tidak valid. Silakan upload ulang.',
                ], 422);
            }

            $resolved[] = ['input' => $photo, 'receipt' => $receipt];
        }

        DB::beginTransaction();
        try {
            foreach ($resolved as $item) {
                $photo = $item['input'];
                $receipt = $item['receipt'];

                DataFoto::create([
                    'album_id' => $album->id,
                    'event_id' => $album->event_id,
                    'kategorisasi_datatempo' => $album->kategori_foto_id?? null,
                    'original_foto_url' => $receipt['original_path'],
                    'thumbnail_foto_url' => $receipt['thumbnail_path'],
                    'subyek' => null,
                    'judul' => $photo['judul'],
                    'deskrp' => $photo['deskrp'] ?? null,
                    'k_word' => $photo['k_word'] ?? null,
                    'f_lok' => $photo['f_lok'] ?? null,
                    'f_size' => $receipt['file_size'],
                    'l_access' => $photo['l_access'] ?? 1,
                    'publish' => $photo['publish'] ?? 0,
                    'tgl_masuk' => now(),
                    'depositor' => auth()->user()->name,
                    'add_by' => auth()->user()->id,
                    'mm_id' => DataFoto::generateMmId(),
                    'meta_data' => $receipt['meta_data'] ?? null,
                ]);

                Cache::forget($this->receiptKey($photo['file_id']));
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil disimpan',
                'redirect' => route('albums.show', $album)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save photos: ' . $e->getMessage(), [
                'data' => $request->photos
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan foto: ' . $e->getMessage()
            ], 500);
        }
    }
}
