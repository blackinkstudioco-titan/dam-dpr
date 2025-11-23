<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('album_foto', function (Blueprint $table) {
            // Tambah kolom kategori_foto_id setelah kolom komisi_dpr_id
            $table->unsignedBigInteger('kategori_foto_id')
                  ->nullable()
                  ->after('komisi_dpr_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('album_foto', function (Blueprint $table) {
            // Drop kolom kategori_foto_id
            $table->dropColumn('kategori_foto_id');
        });
    }
};