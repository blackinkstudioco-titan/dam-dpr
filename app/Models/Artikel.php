<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    protected $table = 'artikel';

    public $timestamps = false;

    protected $fillable = [
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
        'komisi_dpr_id',
        'anggota_dpr_id',
        'event_id',
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
        'komisi_dpr_id' => 'integer',
        'anggota_dpr_id' => 'integer',
        'event_id' => 'integer',
        'kategori_id'  => 'integer',

    ];

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }
    public function komisiDpr()
    {
        return $this->belongsTo(KomisiDpr::class, 'komisi_dpr_id', 'id');
    }
    public function anggotaDpr()
    {
        return $this->belongsTo(KomisiDpr::class, 'anggota_dpr_id', 'id');
    }

    /**
     * User yang membuat artikel ini
     * Relasi berdasarkan field 'add_by' = user.id
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'add_by', 'id');
    }

    /**
     * User yang mengedit artikel ini
     * Relasi berdasarkan field 'edit_by' = user.id
     */
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

    public function scopeByPenulis($query, $penulis)
    {
        return $query->where('penulis', $penulis);
    }

    /**
     * ========================================
     * ACCESSORS
     * ========================================
     */

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    /**
     * Get creator name with fallback
     */
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Unknown User';
    }

    /**
     * Get editor name with fallback
     */
    public function getEditorNameAttribute()
    {
        return $this->editor ? $this->editor->name : 'Unknown User';
    }
}