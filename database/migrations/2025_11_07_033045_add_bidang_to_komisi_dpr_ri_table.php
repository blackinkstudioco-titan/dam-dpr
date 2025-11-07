<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('komisi_dpr_ri', function (Blueprint $table) {
            if (!Schema::hasColumn('komisi_dpr_ri', 'bidang')) {
                $table->string('bidang', 255)->nullable()->after('nama_komisi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('komisi_dpr_ri', function (Blueprint $table) {
            $table->dropColumn('bidang');
        });
    }
};
