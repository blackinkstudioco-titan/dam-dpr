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
         Schema::table('data_foto', function (Blueprint $table) {
             if (!Schema::hasColumn('data_foto', 'komisi_dpr_id')) {
                 $table->unsignedBigInteger('komisi_dpr_id')->nullable()->after('anggota_dpr_id');
                 $table->foreign('komisi_dpr_id')
                       ->references('id')
                       ->on('komisi_dpr_ri')
                       ->onDelete('set null');
             }
         });
     }

     public function down(): void
     {
         Schema::table('data_foto', function (Blueprint $table) {
             $table->dropForeign(['komisi_dpr_id']);
             $table->dropColumn('komisi_dpr_id');
         });
     }


};
