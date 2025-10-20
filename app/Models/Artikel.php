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
        'edit_date'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'add_date' => 'datetime',
        'edit_date' => 'datetime',
        'active' => 'boolean',
        'del' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('del', 0);
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
