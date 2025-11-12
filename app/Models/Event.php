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
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    /**
     * Relasi ke model User sebagai pembuat event.
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_event');
    }
}