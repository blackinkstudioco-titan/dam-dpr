<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomisiDpr extends Model
{
    use HasFactory;

    protected $table = 'komisi_dpr';

    protected $fillable = [
        'nama_komisi',
        'bidang',
    ];
}
