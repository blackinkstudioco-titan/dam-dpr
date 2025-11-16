<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class DataFoto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'data_foto';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'mm_id',
        'judul',
        'deskrp',
        'k_word',
        'f_lok',
        'thumbnail_foto_url',
        'original_foto_url',
        'f_size',
        'tgl_masuk',
        'mm_lok',
        'tgl_mm',
        'perekam',
        'subyek',
        'k_name',
        'l_access',
        'konseptor',
        'depositor',
        'judul_en',
        'deskrp_en',
        'file_release',
        'download',
        'view',
        'selection_id',
        'publish',
        'status',
        'meta_data',
        'kategorisasi_datatempo',
        'edit_by',
        'edit_date',
        'anggota_dpr_id', // ✅ field baru untuk relasi ke anggota DPR
        'komisi_dpr_id',
        'album_id',
        'event_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_masuk' => 'date',
        'tgl_mm' => 'date',
        'edit_date' => 'datetime',
        'publish' => 'integer', // Ubah dari boolean ke integer
        'meta_data' => 'array',
        'f_size' => 'integer',
        'download' => 'integer',
        'view' => 'integer',
        'selection_id' => 'integer',
        'l_access' => 'integer',
        'anggota_dpr_id' => 'integer', // ✅ tambahkan cast integer
        'komisi_dpr_id' => 'integer',
        'event_id' => 'integer',
    ];
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function komisiDpr()
    {
          return $this->belongsTo(KomisiDpr::class, 'komisi_dpr_id');
    }
    
    public function album()
    {
        return $this->belongsTo(AlbumFoto::class, 'album_id');
    }



    /**
     * Get the full URL of the original photo.
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                ($this->original_foto_url && Storage::disk('public')->exists($this->original_foto_url))
                    ? Storage::url($this->original_foto_url)
                    : asset('images/no-image.png')
        );
    }

    /**
     * Get the full URL of the thumbnail photo.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                ($this->thumbnail_foto_url && Storage::disk('public')->exists($this->thumbnail_foto_url))
                    ? Storage::url($this->thumbnail_foto_url)
                    : asset('images/no-image-thumb.png')
        );
    }

    /**
     * Get formatted file size.
     */
    protected function formattedFileSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = $this->f_size;
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];

                if ($bytes == 0) {
                    return '0 B';
                }

                for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                    $bytes /= 1024;
                }

                return round($bytes, 2) . ' ' . $units[$i];
            }
        );
    }

    /**
     * Get formatted metadata (EXIF data).
     */
    protected function formattedMetaData(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->meta_data) {
                    return [];
                }

                $meta = is_array($this->meta_data) ? $this->meta_data : json_decode($this->meta_data, true);
                $formatted = [];

                $exifMap = [
                    'Make' => 'Kamera',
                    'Model' => 'Model',
                    'DateTime' => 'Tanggal Foto',
                    'DateTimeOriginal' => 'Tanggal Asli',
                    'ExposureTime' => 'Exposure Time',
                    'FNumber' => 'F-Stop',
                    'ISOSpeedRatings' => 'ISO',
                    'FocalLength' => 'Focal Length',
                    'Flash' => 'Flash',
                    'WhiteBalance' => 'White Balance',
                    'Orientation' => 'Orientasi',
                    'GPS' => 'Lokasi GPS',
                ];

                foreach ($exifMap as $key => $label) {
                    if (isset($meta[$key])) {
                        $formatted[$label] = $this->formatExifValue($key, $meta[$key]);
                    }
                }

                return $formatted;
            }
        );
    }

    /**
     * Format EXIF values for better readability.
     */
    private function formatExifValue(string $key, mixed $value): string
    {
        return match ($key) {
            'FNumber' => 'f/' . $value,
            'FocalLength' => $value . 'mm',
            'Flash' => $value == 16 ? 'Tidak Menyala' : 'Menyala',
            'WhiteBalance' => $value == 0 ? 'Auto' : 'Manual',
            'GPS' => is_array($value)
                ? sprintf('Lat: %.6f, Lon: %.6f', $value['lat'] ?? 0, $value['lon'] ?? 0)
                : $value,
            default => is_array($value) ? json_encode($value) : (string) $value,
        };
    }

    /** 🔢 Increment counters */
    public function incrementView(): bool
    {
        return $this->increment('view');
    }

    public function incrementDownload(): bool
    {
        return $this->increment('download');
    }

    /** 🔍 Scopes */
    public function scopePublished($query)
    {
        return $query->where('publish', true);
    }

    public function scopeBySubject($query, string $subject)
    {
        return $query->where('subyek', $subject);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('deskrp', 'like', "%{$search}%")
              ->orWhere('k_word', 'like', "%{$search}%")
              ->orWhere('mm_id', 'like', "%{$search}%");
        });
    }

    public function scopeDateRange($query, ?string $startDate, ?string $endDate)
    {
        if ($startDate) $query->where('tgl_masuk', '>=', $startDate);
        if ($endDate) $query->where('tgl_masuk', '<=', $endDate);
        return $query;
    }

    /** 🧭 Relationships */
    public function kategori()
    {
        return $this->belongsTo(KategoriFoto::class, 'kategorisasi_datatempo', 'id');
    }

    public function anggotaDpr()
    {
        return $this->belongsTo(AnggotaDpr::class, 'anggota_dpr_id');
    }

    /** 🧠 Boot method to auto-generate mm_id. */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->mm_id) {
                $model->mm_id = self::generateMmId();
            }
        });
    }

    /** Generate unique MM ID. */
    public static function generateMmId(): string
    {
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/MM(\d{4})(\d{4})/', $lastRecord->mm_id, $matches)) {
            $number = (int) $matches[2] + 1;
        } else {
            $number = 1;
        }

        return sprintf('MM%s%04d', $year, $number);
    }
}
