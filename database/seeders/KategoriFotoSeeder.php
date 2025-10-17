<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KategoriFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            ['id' => 1, 'k_name' => 'Pemandangan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'k_name' => 'Potret', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'k_name' => 'Dokumentasi Acara', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'k_name' => 'Produk', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'k_name' => 'Arsitektur', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'k_name' => 'Budaya', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'k_name' => 'Seni Pertunjukan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'k_name' => 'Human Interest', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'k_name' => 'Olahraga', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'k_name' => 'Kuliner', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('kategori_foto')->insert($categories);

        $this->command->info('Kategori foto seeded successfully!');
    }
}
