<?php
namespace App\Http\Controllers;

use App\Models\AlbumFoto;
use App\Models\DataFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AddPhotosController extends Controller
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function index(AlbumFoto $album)
    {
        return view('add-photos.index', compact('album'));
    }

    public function upload(Request $request, AlbumFoto $album)
    {
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

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'file_id' => uniqid('temp_'),
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'original_path' => $originalPath,
                    'thumbnail_path' => $thumbnailPath,
                    'thumbnail_url' => asset('storage' . $thumbnailPath),
                    'meta_data' => $exifData,
                    'album_id' => $album->id,
                    'event_id' => $album->event_id
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
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*.file_id' => 'required',
            'photos.*.original_path' => 'required',
            'photos.*.thumbnail_path' => 'required'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->photos as $photo) {
                // Log the incoming photo data for debugging
                Log::info('Processing photo:', $photo);

                DataFoto::create([
                    'album_id' => $album->id,
                    'event_id' => $album->event_id,
                    'original_foto_url' => $photo['original_path'],
                    'thumbnail_foto_url' => $photo['thumbnail_path'],
                    'judul' => $photo['judul'],
                    'deskrp' => $photo['deskrp'] ?? null,
                    'k_word' => $photo['k_word'] ?? null,
                    'f_lok' => $photo['f_lok'] ?? null,
                    'f_size' => $photo['file_size'],
                    'l_access' => $photo['l_access'] ?? 1,
                    'publish' => $photo['publish'] ?? 0,
                    'tgl_masuk' => now(),
                    'depositor' => auth()->user()->name,
                    'mm_id' => DataFoto::generateMmId(),
                    'meta_data' => $photo['meta_data'] ?? null  // Add this line to save EXIF data
                ]);
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