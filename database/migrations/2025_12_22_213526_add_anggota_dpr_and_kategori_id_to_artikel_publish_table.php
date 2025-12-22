<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            // ✅ string (varchar 255), nullable
            $table->string('anggota_dpr', 255)->nullable()->after('anggota_dpr_id');

            // ✅ integer, nullable
            $table->unsignedBigInteger('kategori_id')->nullable()->after('event_id');

            // (opsional) jika ingin foreign key ke tabel kategori:
            // $table->foreign('kategori_id')->references('id')->on('kategori')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            // Hapus FK dulu kalau kamu menambahkan FK
            // $table->dropForeign(['kategori_id']);

            $table->dropColumn(['anggota_dpr', 'kategori_id']);
        });
    }
};
