<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom baru (nullable) ke tabel anggota_dpr.
     */
    public function up(): void
    {
        Schema::table('anggota_dpr', function (Blueprint $table) {
            // Kolom string default panjang 255, dibuat nullable
            $table->string('fraksi')->nullable()->after('fraksi_id');
            $table->string('dapil')->nullable()->after('fraksi');
            $table->string('jenis_kelamin')->nullable()->after('dapil');
        });
    }

    /**
     * Rollback perubahan (hapus kolom yang ditambahkan).
     */
    public function down(): void
    {
        Schema::table('anggota_dpr', function (Blueprint $table) {
            // Perlu cek urutan drop agar aman pada sebagian DB driver
            $table->dropColumn(['jenis_kelamin', 'dapil', 'fraksi']);
        });
    }
};
