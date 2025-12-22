<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtikelPublish extends Model
{
    protected $table = 'artikel_publish';

    protected $fillable = [
        'artikel_draft_id',
        'tanggal',
        'rubrik',
        'penulis',
        'sumber',
        'keyword',
        'subyek',
        'judul',
        'foto',
        'deskripsi',
        'isi',
        'active',
        'del',
        'add_by',
        'add_date',
        'edit_by',
        'edit_date',
        'event_id',
        'komisi_dpr_id',
        'anggota_dpr_id',
        'scheduled_publish_at',      // ← Tambahkan
        'scheduled_unpublish_at',    // ← Tambahkan
        'schedule_status',           // ← Tambahkan
        'anggota_dpr',
        'kategori_id',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'add_date' => 'datetime',
        'edit_date' => 'datetime',
        'active' => 'boolean',
        'del' => 'boolean',
        'add_by' => 'integer',
        'edit_by' => 'integer',
        'event_id' => 'integer',
        'komisi_dpr_id' => 'integer',
        'anggota_dpr_id' => 'integer',
        'scheduled_publish_at' => 'datetime',    // ← Tambahkan
        'scheduled_unpublish_at' => 'datetime',  // ← Tambahkan
        'kategori_id'  => 'integer',
    ];

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */
    public function anggotaDpr()
    {
        return $this->belongsTo(AnggotaDpr::class, 'anggota_dpr_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function komisiDpr()
    {
        return $this->belongsTo(KomisiDpr::class, 'komisi_dpr_id', 'id');
    }

    public function artikelDraft()
    {
        return $this->belongsTo(Artikel::class, 'artikel_draft_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'add_by', 'id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edit_by', 'id');
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('del', 0);
    }

    public function scopeByRubrik($query, $rubrik)
    {
        return $query->where('rubrik', $rubrik);
    }

    /** 🔍 Scopes untuk Schedule */
    public function scopeScheduledToPublish($query)
    {
        return $query->where('scheduled_publish_at', '<=', now())
                     ->where('schedule_status', 'pending')
                     ->where(function($q) {
                         $q->where('active', 0)
                           ->orWhere('active', '0')
                           ->orWhereNull('active');
                     });
    }

    public function scopeScheduledToUnpublish($query)
    {
        return $query->where('scheduled_unpublish_at', '<=', now())
                     ->where('schedule_status', 'published')
                     ->where(function($q) {
                         $q->where('active', 1)
                           ->orWhere('active', '1');
                     });
    }

    public function scopePendingSchedule($query)
    {
        return $query->where('schedule_status', 'pending')
                     ->whereNotNull('scheduled_publish_at');
    }

    /**
     * ========================================
     * ACCESSORS
     * ========================================
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : asset('images/no-image.png');
    }

    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Unknown User';
    }

    public function getEditorNameAttribute()
    {
        return $this->editor ? $this->editor->name : 'Unknown User';
    }

    public function getScheduleStatusLabelAttribute(): string
    {
        return match($this->schedule_status) {
            'pending' => 'Menunggu',
            'published' => 'Sudah Dipublish',
            'unpublished' => 'Sudah Di-unpublish',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Terjadwal',
        };
    }

    /** Helper Methods untuk Schedule */
    public function isScheduled(): bool
    {
        return $this->scheduled_publish_at !== null || $this->scheduled_unpublish_at !== null;
    }

    public function canBeScheduled(): bool
    {
        return $this->schedule_status === 'pending' || $this->schedule_status === 'cancelled';
    }
}