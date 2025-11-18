<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaDpr extends Model
{
    use HasFactory;

    protected $table = 'anggota_dpr';

    protected $fillable = [
        'nama',
        'fraksi_id',
        'komisi_dpr_id',
        'periode_terpilih',
        'partai',
        'fraksi',         // baru
        'dapil',          // baru
        'jenis_kelamin',  // baru
    ];

    public function fraksi()
    {
        return $this->belongsTo(Fraksi::class, 'fraksi_id');
    }

    public function komisi()
    {
        return $this->belongsTo(KomisiDpr::class, 'komisi_dpr_id');
    }
}
