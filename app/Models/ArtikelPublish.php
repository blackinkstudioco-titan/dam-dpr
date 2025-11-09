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
    ];

    // Jika ada relasi ke model ArtikelDraft
    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'artikel_draft_id');
    }

    // Tambahkan relasi lainnya jika diperlukan
}
