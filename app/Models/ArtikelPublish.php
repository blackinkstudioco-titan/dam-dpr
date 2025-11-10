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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'add_date' => 'datetime',
        'edit_date' => 'datetime',
        'active' => 'boolean',
        'del' => 'boolean',
        'add_by' => 'integer',
        'edit_by' => 'integer',
    ];

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */

    /**
     * Relasi ke artikel draft
     */
    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikel_draft_id');
    }

    /**
     * User yang membuat artikel publish ini
     * Relasi berdasarkan field 'add_by' = user.id
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'add_by', 'id');
    }

    /**
     * User yang mengedit artikel publish ini
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