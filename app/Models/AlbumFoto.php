<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlbumFoto extends Model
{
    protected $table = 'album_foto';

    protected $fillable = [
        'nama_album',
        'deskripsi',
        'created_by',
        'event_id',
        'komisi_dpr_id',
        'edit_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'edit_at' => 'datetime',
        'event_id' => 'integer',
        'event_id' => 'integer',
    ];

    /** 🔗 Relasi ke User */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_by');
    }

    /** 🖼️ Relasi ke DataFoto */
    public function fotos(): HasMany
    {
        return $this->hasMany(DataFoto::class, 'album_id');
    }

    /** 📊 Helper Methods */
    public function fotosCount(): int
    {
        return $this->fotos()->count();
    }

    public function publishedFotos(): HasMany
    {
        return $this->fotos()->where('publish', true);
    }

    /** 🔍 Scopes */
    public function scopeWithFotoCount($query)
    {
        return $query->withCount('fotos');
    }

    public function latestPhoto()
    {
        return $this->hasOne(DataFoto::class, 'album_id')->latest();
    }

    public function getThumbnailAttribute()
    {
        return $this->latestPhoto?->thumbnail_foto_url ?? 'images/no-image.png';
    }

    public function getPhotoCountAttribute()
    {
        return $this->fotos()->count();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function komisiDPR(): BelongsTo
    {
        return $this->belongsTo(komisiDpr::class, 'komisi_dpr_id');
    }

}
