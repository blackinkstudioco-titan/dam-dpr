<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            // 1. Ubah field tanggal menjadi datetime
            $table->dateTime('tanggal')->change();

            // 2. Tambah field event_id (nullable, int)
            $table->unsignedBigInteger('event_id')->nullable()->after('tanggal');

            // 3. Tambah field komisi_dpr_id (nullable, int)
            $table->unsignedBigInteger('komisi_dpr_id')->nullable()->after('event_id');

            // 4. Tambah field anggota_dpr_id (nullable, int)
            $table->unsignedBigInteger('anggota_dpr_id')->nullable()->after('komisi_dpr_id');

            // Tambahkan foreign key jika tabel terkait ada
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
            $table->foreign('komisi_dpr_id')->references('id')->on('komisi_dpr')->onDelete('set null');
            $table->foreign('anggota_dpr_id')->references('id')->on('anggota_dpr')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            // Rollback perubahan
            $table->date('tanggal')->change();

            $table->dropForeign(['event_id']);
            $table->dropForeign(['komisi_dpr_id']);
            $table->dropForeign(['anggota_dpr_id']);

            $table->dropColumn(['event_id', 'komisi_dpr_id', 'anggota_dpr_id']);
        });
    }
};