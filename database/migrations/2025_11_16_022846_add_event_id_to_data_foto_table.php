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
            $table->unsignedBigInteger('event_id')->nullable()->after('album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_foto', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
};