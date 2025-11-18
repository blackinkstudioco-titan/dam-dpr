<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AnggotaDpr;

class AnggotaDprSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan path file CSV sudah benar
        $csvFile = database_path('seeders/data/anggota_dpr.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("File CSV tidak ditemukan di: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // ==================================================================
        // ⚠️ PENTING: Lewati 3 Baris Header
        // File CSV Anda memiliki 3 baris judul sebelum data dimulai.
        // Kita baca 3 kali untuk memindahkan pointer ke baris ke-4.
        // Tambahkan delimiter ';' agar pembacaan baris akurat.
        // ==================================================================
        fgetcsv($file, 0, ';'); // Baris 1 (Judul Laporan)
        fgetcsv($file, 0, ';'); // Baris 2 (Kosong/Info)
        fgetcsv($file, 0, ';'); // Baris 3 (Header Kolom: No, Nama, dst)

        $this->command->info('Mulai import data Anggota DPR (Delimiter titik koma)...');
        
        DB::beginTransaction();

        try {
            // Baca baris per baris dengan pemisah titik koma (;)
            while (($row = fgetcsv($file, 0, ';')) !== false) {
                
                // Cek validitas baris: Minimal harus ada data sampai index 1 (Nama)
                if (count($row) < 2 || empty(trim($row[1]))) {
                    continue;
                }

                // ==========================================
                // MAPPING KOLOM (Sesuai Fisik File CSV Anda)
                // Delimiter: Titik Koma (;)
                // [0] No
                // [1] Nama Anggota
                // [2] Nama Fraksi
                // [3] Dapil
                // [4] Jenis Kelamin
                // ==========================================

                $namaLengkap = trim($row[1]);
                $namaFraksi  = isset($row[2]) ? trim($row[2]) : '';
                $namaDapil   = isset($row[3]) ? trim($row[3]) : '';
                
                // Ambil Jenis Kelamin dari Index 4
                $rawJK       = isset($row[4]) ? trim($row[4]) : 'L';
                // Ambil huruf depan & kapital (misal "Laki-laki" -> "L")
                $jenisKelamin = strtoupper(substr($rawJK, 0, 1)); 
                // Validasi fallback jika kosong/salah
                if (!in_array($jenisKelamin, ['L', 'P'])) {
                    $jenisKelamin = 'L'; 
                }

                AnggotaDpr::updateOrCreate(
                    ['nama' => $namaLengkap], // Kunci unik (Nama)
                    [
                        'fraksi_id'        => null,
                        'komisi_dpr_id'    => null,
                        'partai'           => $namaFraksi, // Simpan string Nama Fraksi
                        'fraksi'           => $namaFraksi, // Simpan string Nama Fraksi
                        'dapil'            => $namaDapil,
                        'jenis_kelamin'    => $jenisKelamin,
                        'periode_terpilih' => '2024-2029',
                    ]
                );
            }

            DB::commit();
            fclose($file);
            $this->command->info('✅ Sukses! Data Anggota DPR berhasil diimport.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Gagal import: ' . $e->getMessage());
            // Tampilkan baris yang error untuk debugging
            if (isset($row)) {
                $this->command->warn('Baris terakhir dibaca: ' . json_encode($row));
            }
        }
    }
}