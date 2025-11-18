<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriFotoSeeder_2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $names = [
            'rapat paripurna',
            'rapat paripurna luar biasa',
            'rapat kerja',
            'rapat dengar pendapat',
            'rapat dengar pendapat umum',
            'rapat fraksi',
            'rapat pimpinan DPR',
            'rapat konsultasi',
            'rapat Badan Musyawarah',
            'rapat komisi',
            'rapat gabungan komisi',
            'rapat Badan Legislasi',
            'rapat Badan Anggaran',
            'rapat BURT',
            'rapat BKSAP',
            'rapat BAKN',
            'rapat Badan Kehormatan',
            'rapat panitia khusus',
            'rapat panitia kerja atau tim',
            'audiensi',
            'kunjungan kerja',
            'kunjungan kerja spesifik',
            'kunjungan dapil',
            'konferensi internasional',
            'konferensi pers',
            'wawancara',
            'siaran pers',
            'lain-lain',
        ];

        // Siapkan payload untuk upsert (bulk)
        $data = array_map(fn ($name) => [
            'k_name'     => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $names);

        // Upsert berdasarkan k_name agar tidak duplikat
        DB::table('kategori_foto')->upsert(
            $data,
            ['k_name'],   // unique-by
            ['updated_at'] // kolom yang diupdate jika sudah ada
        );
    }
}