<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artikel', function (Blueprint $table) {
            // Ubah field tanggal menjadi date_time
            $table->dateTime('tanggal')->change();

            // Tambah field baru
            $table->unsignedBigInteger('komisi_dpr_id')->nullable()->after('tanggal');
            $table->unsignedBigInteger('anggota_dpr_id')->nullable()->after('komisi_dpr_id');
            $table->unsignedBigInteger('event_id')->nullable()->after('anggota_dpr_id');

            // Tambahkan foreign key jika tabel terkait ada
            $table->foreign('komisi_dpr_id')->references('id')->on('komisi_dpr')->onDelete('set null');
            $table->foreign('anggota_dpr_id')->references('id')->on('anggota_dpr')->onDelete('set null');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('artikel', function (Blueprint $table) {
            $table->date('tanggal')->change();

            $table->dropForeign(['komisi_dpr_id']);
            $table->dropForeign(['anggota_dpr_id']);
            $table->dropForeign(['event_id']);

            $table->dropColumn(['komisi_dpr_id', 'anggota_dpr_id', 'event_id']);
        });
    }
};