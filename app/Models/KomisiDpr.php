<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomisiDpr extends Model
{
    use HasFactory;

    protected $table = 'komisi_dpr_ri';

    protected $fillable = [
        'nama_komisi',
        'bidang',
    ];
}
