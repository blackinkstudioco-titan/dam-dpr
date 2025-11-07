<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomisiDprSeeder extends Seeder
{
    public function run(): void
    {
        $komisiList = [
            [
                'nama_komisi' => 'Komisi I DPR RI',
                'bidang' => 'Pertahanan, Luar Negeri, Komunikasi dan Informatika'
            ],
            [
                'nama_komisi' => 'Komisi II DPR RI',
                'bidang' => 'Pemerintahan Dalam Negeri, Otonomi Daerah, Aparatur Negara dan Reformasi Birokrasi'
            ],
            [
                'nama_komisi' => 'Komisi III DPR RI',
                'bidang' => 'Hukum, HAM, dan Keamanan'
            ],
            [
                'nama_komisi' => 'Komisi IV DPR RI',
                'bidang' => 'Pertanian, Kehutanan, Kelautan dan Perikanan'
            ],
            [
                'nama_komisi' => 'Komisi V DPR RI',
                'bidang' => 'Perhubungan, Pekerjaan Umum, Perumahan Rakyat, dan Pembangunan Desa'
            ],
            [
                'nama_komisi' => 'Komisi VI DPR RI',
                'bidang' => 'Perdagangan, Perindustrian, Investasi, Koperasi, UKM dan BUMN'
            ],
            [
                'nama_komisi' => 'Komisi VII DPR RI',
                'bidang' => 'Energi, Riset dan Teknologi, serta Lingkungan Hidup'
            ],
            [
                'nama_komisi' => 'Komisi VIII DPR RI',
                'bidang' => 'Agama, Sosial, Pemberdayaan Perempuan dan Perlindungan Anak'
            ],
            [
                'nama_komisi' => 'Komisi IX DPR RI',
                'bidang' => 'Ketenagakerjaan, Kesehatan, dan Kependudukan'
            ],
            [
                'nama_komisi' => 'Komisi X DPR RI',
                'bidang' => 'Pendidikan, Kebudayaan, Riset, Pariwisata, dan Pemuda Olahraga'
            ],
            [
                'nama_komisi' => 'Komisi XI DPR RI',
                'bidang' => 'Keuangan, Perencanaan Pembangunan, dan Perbankan'
            ],
            [
                'nama_komisi' => 'Komisi XII DPR RI',
                'bidang' => 'Pertambangan, Energi Baru Terbarukan, dan Industri Strategis'
            ],
            [
                'nama_komisi' => 'Komisi XIII DPR RI',
                'bidang' => 'Reformasi Regulasi, Inovasi, dan Peningkatan Daya Saing Nasional'
            ],
        ];

        foreach ($komisiList as $komisi) {
            DB::table('komisi_dpr_ri')->updateOrInsert(
                ['nama_komisi' => $komisi['nama_komisi']],
                [
                    'bidang' => $komisi['bidang'],
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }
    }
}
