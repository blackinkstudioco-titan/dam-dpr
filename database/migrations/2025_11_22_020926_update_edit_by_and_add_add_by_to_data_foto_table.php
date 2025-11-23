<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah semua nilai edit_by menjadi '1' agar bisa dikonversi ke integer
        DB::table('data_foto')->update(['edit_by' => 1]);

        Schema::table('data_foto', function (Blueprint $table) {
            // Ubah kolom edit_by dari VARCHAR ke INTEGER
            $table->unsignedBigInteger('edit_by')->default(1)->change();

            // Tambahkan kolom add_by dengan tipe integer
            $table->unsignedBigInteger('add_by')->default(1)->after('edit_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_foto', function (Blueprint $table) {
            // Kembalikan edit_by ke VARCHAR(255)
            $table->string('edit_by', 255)->nullable()->change();

            // Hapus kolom add_by
            $table->dropColumn('add_by');
        });
    }
};