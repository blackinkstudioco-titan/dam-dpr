<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_foto', function (Blueprint $table) {
            // tambahkan kolom baru dengan relasi opsional (nullable)
            $table->unsignedBigInteger('anggota_dpr_id')->nullable()->default(0)->after('id');

            // index opsional untuk optimasi query
            $table->index('anggota_dpr_id');
        });
    }

    public function down(): void
    {
        Schema::table('data_foto', function (Blueprint $table) {
            $table->dropColumn('anggota_dpr_id');
        });
    }
};
