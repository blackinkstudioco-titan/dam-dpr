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
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'event_id')) {
                // Hapus kolom event_id (tanpa dropForeign karena FK tidak ada)
                $table->dropColumn('event_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->after('pembuat_event');
            // Jika ingin tambahkan FK lagi:
            // $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
        });
    }
};