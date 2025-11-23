<?php

namespace App\Services;

use App\Models\DataFoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BulkUploadService
{
    protected $imageManager;
    
    public function __construct()
    {
        // Initialize ImageManager with GD driver
        $this->imageManager = new ImageManager(new Driver());
    }
    
    /**
     * Upload foto dan buat thumbnail
     */
    public function uploadFoto(UploadedFile $file, int $albumId): array
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //$thumbnailFilename = 'thumb_' . $filename;
        
        // Create year/month/day folder structure
        //$date = now();
        //$basePath = $date->format('Y/m/d');
        // Create year/month/day folder structure
        $date = now();
        $basePath = $date->format('Y/m/d');
        $originalPath = "{$basePath}/photos/{$filename}";
        $thumbnailPath = "/thumbnails/{$filename}";
        
        // Paths
        $originalPath = "{$basePath}/photos/{$filename}";
        $thumbnailPath = "{$basePath}/thumbnails/{$filename}";
        
        try {
            // Store original image
            $file->storeAs('public/' . dirname($originalPath), basename($originalPath));
            
            // Create thumbnail directory if not exists
            $thumbnailDir = storage_path('app/public/' . dirname($thumbnailPath));
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            // Create thumbnail using Intervention Image v3
            $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
            $image = $this->imageManager->read($file->getRealPath());
            $image->cover(300, 300); // Crop and resize
            $image->save($thumbnailFullPath, quality: 85);
            
            Log::info('Photo uploaded successfully', [
                'original' => $originalPath,
                'thumbnail' => $thumbnailPath,
                'album_id' => $albumId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading photo: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
        
        // Extract EXIF data
        $exifData = $this->extractExifData($file->getRealPath());
        
        // Get file size
        $fileSize = $file->getSize();
        
        return [
            'original_foto_url' => $originalPath,
            'thumbnail_foto_url' => $thumbnailPath,
            'f_size' => $fileSize,
            'meta_data' => $exifData,
            'album_id' => $albumId,
            'file_name' => $file->getClientOriginalName(),
        ];
    }
    
    /**
     * Extract EXIF data dari foto
     */
    private function extractExifData(string $filePath): array
    {
        $exifData = [];
        
        try {
            $exif = @exif_read_data($filePath, 'IFD0');
            
            if ($exif !== false) {
                $exifData = [
                    'Make' => $exif['Make'] ?? null,
                    'Model' => $exif['Model'] ?? null,
                    'DateTime' => $exif['DateTime'] ?? null,
                    'DateTimeOriginal' => $exif['DateTimeOriginal'] ?? null,
                    'ExposureTime' => $exif['ExposureTime'] ?? null,
                    'FNumber' => isset($exif['FNumber']) ? $this->convertToDecimal($exif['FNumber']) : null,
                    'ISOSpeedRatings' => $exif['ISOSpeedRatings'] ?? null,
                    'FocalLength' => isset($exif['FocalLength']) ? $this->convertToDecimal($exif['FocalLength']) : null,
                    'Flash' => $exif['Flash'] ?? null,
                    'WhiteBalance' => $exif['WhiteBalance'] ?? null,
                    'Orientation' => $exif['Orientation'] ?? null,
                ];
                
                // Extract GPS if available
                if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
                    $exifData['GPS'] = [
                        'lat' => $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']),
                        'lon' => $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('EXIF extraction error: ' . $e->getMessage());
        }
        
        return array_filter($exifData); // Remove null values
    }
    
    /**
     * Convert EXIF fraction to decimal
     */
    private function convertToDecimal($fraction): ?float
    {
        if (is_string($fraction) && strpos($fraction, '/') !== false) {
            $parts = explode('/', $fraction);
            if (count($parts) === 2 && $parts[1] != 0) {
                return round($parts[0] / $parts[1], 2);
            }
        }
        return is_numeric($fraction) ? (float) $fraction : null;
    }
    
    /**
     * Convert GPS coordinates to decimal
     */
    private function getGps($exifCoord, $hemi): float
    {
        $degrees = count($exifCoord) > 0 ? $this->convertToDecimal($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->convertToDecimal($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->convertToDecimal($exifCoord[2]) : 0;
        
        $flip = ($hemi == 'W' || $hemi == 'S') ? -1 : 1;
        
        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }
    
    /**
     * Save foto data to database
     */
    public function saveFotoData(array $uploadData, array $metaData): DataFoto
    {
        return DataFoto::create([
            'original_foto_url' => $uploadData['original_foto_url'],
            'thumbnail_foto_url' => $uploadData['thumbnail_foto_url'],
            'f_size' => $uploadData['f_size'],
            'meta_data' => $uploadData['meta_data'],
            'album_id' => $uploadData['album_id'],
            'judul' => $metaData['judul'] ?? $uploadData['file_name'],
            'deskrp' => $metaData['deskrp'] ?? null,
            'k_word' => $metaData['k_word'] ?? null,
            'f_lok' => $metaData['f_lok'] ?? null,
            'tgl_masuk' => now(),
            'perekam' => $metaData['perekam'] ?? null,
            'subyek' => $metaData['subyek'] ?? null,
            'k_name' => $metaData['k_name'] ?? null,
            'konseptor' => $metaData['konseptor'] ?? null,
            'depositor' => auth()->user()->name ?? null,
            'l_access' => $metaData['l_access'] ?? 1,
            'kategorisasi_datatempo' => $metaData['kategorisasi_datatempo'] ?? null,
            'komisi_dpr_id' => $metaData['komisi_dpr_id'] ?? null,
            'anggota_dpr_id' => $metaData['anggota_dpr_id'] ?? null,
            'publish' => $metaData['publish'] ?? 0,
            'event_id' => $metaData['event_id'] ?? null,
            'add_by' => auth()->user()->id ?? null,
            
        ]);
    }
    
    /**
     * Delete uploaded files (jika terjadi error)
     */
    public function deleteUploadedFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}