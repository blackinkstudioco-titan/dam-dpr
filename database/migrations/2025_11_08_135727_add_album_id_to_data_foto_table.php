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
             $table->unsignedBigInteger('album_id')->nullable()->after('id');

             // Jika ingin relasi foreign key:
             // $table->foreign('album_id')->references('id')->on('album_foto')->onDelete('set null');
         });
     }

     public function down(): void
     {
         Schema::table('data_foto', function (Blueprint $table) {
             $table->dropColumn('album_id');
         });
     }
};
