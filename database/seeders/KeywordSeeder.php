<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keywords = [
            // Fotografi
            'fotografi',
            'fotografi landscape',
            'fotografi portrait',
            'fotografi wedding',
            'fotografi street',
            'fotografi macro',
            'fotografi wildlife',
            'fotografi food',
            'fotografi fashion',
            'fotografi architecture',

            // Teknologi
            'teknologi',
            'teknologi informasi',
            'teknologi digital',
            'teknologi terbaru',
            'teknologi canggih',

            // Alam
            'alam',
            'alam indonesia',
            'alam liar',
            'alam semesta',
            'alamat',

            // Seni
            'seni',
            'seni budaya',
            'seni rupa',
            'seni tradisional',
            'seni modern',

            // Travel
            'travel',
            'traveling',
            'travel indonesia',
            'travel guide',
            'travel photography',

            // Bisnis
            'bisnis',
            'bisnis online',
            'bisnis digital',
            'bisnis plan',
            'bisnis startup',

            // Pendidikan
            'pendidikan',
            'pendidikan indonesia',
            'pendidikan karakter',
            'pendidikan online',
            'pendidikan tinggi',

            // Kesehatan
            'kesehatan',
            'kesehatan mental',
            'kesehatan masyarakat',
            'kesehatan lingkungan',
            'kesehatan reproduksi',

            // Adjektif
            'beautiful',
            'amazing',
            'awesome',
            'creative',
            'innovative',
            'professional',
            'quality',
            'premium',
            'exclusive',
            'limited',
            'kacapi',
        ];

        $data = [];
        foreach ($keywords as $keyword) {
            $data[] = [
                'k_word' => $keyword,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('keyword')->insert($data);

        $this->command->info('Keywords seeded successfully!');
    }
}
