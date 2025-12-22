<?php


// database/migrations/2025_12_17_000002_update_fk_on_artikel.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artikel', function (Blueprint $table) {
            // 1) Drop foreign keys by **name**
            // Jika nama tepat seperti yang kamu sebutkan:
            $table->dropForeign('artikel_anggota_dpr_id_foreign');
            $table->dropForeign('artikel_event_id_foreign');
            $table->dropForeign('artikel_komisi_dpr_id_foreign');

            // (Opsional) Drop index yang tersisa jika kamu memang ingin hapus index juga:
            // $table->dropIndex(['komisi_dpr_id']);
            // $table->dropIndex(['anggota_dpr_id']);
            // $table->dropIndex(['event_id']);

            // 2) Ubah kolom jadi nullable (sesuaikan tipe aktual di DB)
            $table->unsignedBigInteger('komisi_dpr_id')->nullable()->change();
            // (Opsional) kamu juga bisa buat nullable untuk dua kolom lain jika perlu
            // $table->unsignedBigInteger('anggota_dpr_id')->nullable()->change();
            // $table->unsignedBigInteger('event_id')->nullable()->change();

            // 3) Re-add FK dengan kebijakan yang aman (opsional)
            // - Jika kamu hanya ingin drop FK sepenuhnya, lewati bagian ini.
            // - Jika kamu ingin tetap ada FK namun kolom boleh NULL, tambahkan lagi:
            $table->foreign('komisi_dpr_id')
                  ->references('id')->on('komisi_dpr')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // penting agar child diset NULL jika parent dihapus

            // Contoh re-add untuk dua kolom lain (opsional):
            // $table->foreign('anggota_dpr_id')
            //       ->references('id')->on('anggota_dpr')
            //       ->onUpdate('cascade')
            //       ->onDelete('set null');
            // $table->foreign('event_id')
            //       ->references('id')->on('event')
            //       ->onUpdate('cascade')
            //       ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('artikel', function (Blueprint $table) {
            // Balikkan perubahan
            $table->dropForeign(['komisi_dpr_id']);
            // $table->dropForeign(['anggota_dpr_id']);
            // $table->dropForeign(['event_id']);

            $table->unsignedBigInteger('komisi_dpr_id')->nullable(false)->change();
            // $table->unsignedBigInteger('anggota_dpr_id')->nullable(false)->change();
            // $table->unsignedBigInteger('event_id')->nullable(false)->change();

            // Re-add FK default restrictive (ubah sesuai kebutuhan)
            $table->foreign('komisi_dpr_id')
                  ->references('id')->on('komisi_dpr')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // $table->foreign('anggota_dpr_id')
            //       ->references('id')->on('anggota_dpr')
            //       ->onUpdate('cascade')
            //       ->onDelete('restrict');
            // $table->foreign('event_id')
            //       ->references('id')->on('event')
            //       ->onUpdate('cascade')
            //       ->onDelete('restrict');
        });
    }
};
