<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload image and create thumbnail.
     */
    public function uploadImage(UploadedFile $file): array
    {
        try {
            // Generate unique filename
            $timestamp = time();
            $randomString = Str::random(13);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$timestamp}_{$randomString}.{$extension}";

            // Create year/month/day folder structure
            $date = now();
            $basePath = $date->format('Y/m/d');
            $originalPath = "{$basePath}/photos/{$filename}";
            $thumbnailPath = "/thumbnails/{$filename}";

            // Store original image
            $file->storeAs('public/' . dirname($originalPath), basename($originalPath));

            // Create thumbnail
            $this->createThumbnail($file, $filename);

            // Extract EXIF data
            $exifData = $this->extractExifData($file);

            // Get file size
            $fileSize = $file->getSize();

            return [
                'filename' => $filename,
                'original_path' => $originalPath,
                'thumbnail_path' => $thumbnailPath,
                'file_size' => $fileSize,
                'exif_data' => $exifData,
            ];

        } catch (\Exception $e) {
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Create thumbnail from uploaded file using GD.
     */
    protected function createThumbnail(UploadedFile $file, string $filename): void
    {
        try {
            // Create thumbnail directory if not exists
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = $thumbnailDir . '/' . $filename;
            $sourcePath = $file->getRealPath();

            // Get image info
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                // If can't create thumbnail, copy original
                copy($sourcePath, $thumbnailPath);
                return;
            }

            list($originalWidth, $originalHeight, $imageType) = $imageInfo;

            // Calculate thumbnail dimensions (max 300x300, maintain aspect ratio)
            $maxSize = 300;
            $ratio = min($maxSize / $originalWidth, $maxSize / $originalHeight);
            $thumbWidth = (int)($originalWidth * $ratio);
            $thumbHeight = (int)($originalHeight * $ratio);

            // Create source image based on type
            $sourceImage = match($imageType) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
                IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
                default => null,
            };

            if (!$sourceImage) {
                copy($sourcePath, $thumbnailPath);
                return;
            }

            // Create thumbnail
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

            // Preserve transparency for PNG and GIF
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
            }

            // Resize
            imagecopyresampled(
                $thumbnail, $sourceImage,
                0, 0, 0, 0,
                $thumbWidth, $thumbHeight,
                $originalWidth, $originalHeight
            );

            // Save thumbnail based on type
            match($imageType) {
                IMAGETYPE_JPEG => imagejpeg($thumbnail, $thumbnailPath, 80),
                IMAGETYPE_PNG => imagepng($thumbnail, $thumbnailPath, 8),
                IMAGETYPE_GIF => imagegif($thumbnail, $thumbnailPath),
                default => null,
            };

            // Free memory
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

        } catch (\Exception $e) {
            // If thumbnail creation fails, copy original as thumbnail
            $thumbnailPath = storage_path('app/public/thumbnails/' . $filename);
            copy($file->getRealPath(), $thumbnailPath);
        }
    }

    /**
     * Extract EXIF data from image.
     */
    protected function extractExifData(UploadedFile $file): array
    {
        try {
            $exifData = [];

            // Check if file is an image that can have EXIF
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/tiff'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return $exifData;
            }

            // Try to read EXIF data
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($file->getRealPath());

                if ($exif) {
                    // Extract common EXIF fields
                    $exifData = [
                        'Make' => $exif['Make'] ?? null,
                        'Model' => $exif['Model'] ?? null,
                        'DateTime' => $exif['DateTime'] ?? null,
                        'DateTimeOriginal' => $exif['DateTimeOriginal'] ?? null,
                        'ExposureTime' => $exif['ExposureTime'] ?? null,
                        'FNumber' => $exif['FNumber'] ?? null,
                        'ISOSpeedRatings' => $exif['ISOSpeedRatings'] ?? null,
                        'FocalLength' => $exif['FocalLength'] ?? null,
                        'Flash' => $exif['Flash'] ?? null,
                        'WhiteBalance' => $exif['WhiteBalance'] ?? null,
                        'Orientation' => $exif['Orientation'] ?? null,
                    ];

                    // Extract GPS data if available
                    if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
                        $exifData['GPS'] = [
                            'lat' => $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef'] ?? 'N'),
                            'lon' => $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef'] ?? 'E'),
                        ];
                    }

                    // Remove null values
                    $exifData = array_filter($exifData, function($value) {
                        return $value !== null;
                    });
                }
            }

            return $exifData;

        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Convert GPS coordinates to decimal format.
     */
    protected function getGps(array $coordinate, string $hemisphere): float
    {
        if (!is_array($coordinate) || count($coordinate) < 3) {
            return 0;
        }

        $degrees = $this->gpsToDecimal($coordinate[0]);
        $minutes = $this->gpsToDecimal($coordinate[1]);
        $seconds = $this->gpsToDecimal($coordinate[2]);

        $flip = ($hemisphere === 'W' || $hemisphere === 'S') ? -1 : 1;

        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    /**
     * Convert GPS fraction to decimal.
     */
    protected function gpsToDecimal($coordinate): float
    {
        if (is_string($coordinate) && strpos($coordinate, '/') !== false) {
            $parts = explode('/', $coordinate);
            if (count($parts) == 2 && $parts[1] != 0) {
                return (float) $parts[0] / (float) $parts[1];
            }
        }
        return (float) $coordinate;
    }

    /**
     * Delete image files (original and thumbnail).
     */
    public function deleteImage(string $path, string $type = 'original'): bool
    {
        try {
            if ($type === 'thumbnail') {
                // Delete from public/thumbnails
                $fullPath = 'public' . $path;
            } else {
                // Delete from public/year/month/day/photos
                $fullPath = 'public/' . $path;
            }

            if (Storage::exists($fullPath)) {
                return Storage::delete($fullPath);
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get image dimensions.
     */
    public function getImageDimensions(string $path): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);

            if (file_exists($fullPath)) {
                list($width, $height) = getimagesize($fullPath);
                return [
                    'width' => $width,
                    'height' => $height,
                ];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
