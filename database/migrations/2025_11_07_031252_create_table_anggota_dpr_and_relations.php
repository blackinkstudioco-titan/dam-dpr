<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Tabel Fraksi ---
        Schema::create('fraksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fraksi', 100);
            $table->timestamps();
        });

        // --- Tabel Komisi DPR RI ---
        Schema::create('komisi_dpr_ri', function (Blueprint $table) {
            $table->id();
            $table->string('nama_komisi', 100);
            $table->timestamps();
        });

        // --- Tabel Anggota DPR ---
        Schema::create('anggota_dpr', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('periode_terpilih', 20)->nullable();
            $table->string('partai', 100)->nullable();
            $table->unsignedBigInteger('fraksi_id')->nullable();
            $table->unsignedBigInteger('komisi_dpr_id')->nullable();
            $table->timestamps();

            // Relasi
            $table->foreign('fraksi_id')->references('id')->on('fraksi')->onDelete('set null');
            $table->foreign('komisi_dpr_id')->references('id')->on('komisi_dpr_ri')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_dpr');
        Schema::dropIfExists('fraksi');
        Schema::dropIfExists('komisi_dpr_ri');
    }
};
