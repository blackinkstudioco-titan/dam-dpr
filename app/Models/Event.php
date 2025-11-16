<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'nama_event',
        'deskripsi',
        'tanggal',
        'pembuat_event',
        'event_id', // ✅ field baru
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'event_id' => 'integer',
    ];

    /**
     * Relasi ke model User sebagai pembuat event.
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_event');
    }
    public function dataFoto()
    {
        return $this->hasMany(DataFoto::class, 'event_id');
    }
    public function AlbumFoto()
    {
        return $this->hasMany(AlbumFoto::class, 'event_id');
    }

}