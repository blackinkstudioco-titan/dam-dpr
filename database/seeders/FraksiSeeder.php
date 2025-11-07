<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FraksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fraksiList = [
            'Fraksi PDI Perjuangan',
            'Fraksi Partai Golkar',
            'Fraksi Partai Gerindra',
            'Fraksi Partai NasDem',
            'Fraksi Partai Kebangkitan Bangsa',
            'Fraksi Partai Keadilan Sejahtera',
            'Fraksi Partai Amanat Nasional',
            'Fraksi Partai Demokrat',
        ];

        foreach ($fraksiList as $namaFraksi) {
            DB::table('fraksi')->updateOrInsert(
                ['nama_fraksi' => $namaFraksi],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
