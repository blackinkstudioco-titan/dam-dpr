<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            // Jadwal publish otomatis
            $table->timestamp('scheduled_publish_at')->nullable()->after('active');
            // Jadwal unpublish otomatis
            $table->timestamp('scheduled_unpublish_at')->nullable()->after('scheduled_publish_at');
            // Status penjadwalan
            $table->enum('schedule_status', ['pending', 'published', 'unpublished', 'cancelled'])
                  ->default('pending')
                  ->after('scheduled_unpublish_at');
        });
    }

    public function down(): void
    {
        Schema::table('artikel_publish', function (Blueprint $table) {
            $table->dropColumn(['scheduled_publish_at', 'scheduled_unpublish_at', 'schedule_status']);
        });
    }
};