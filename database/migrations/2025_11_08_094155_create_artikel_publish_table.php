<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('artikel_publish', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artikel_draft_id')->nullable(); // Asumsinya ini foreign key
            $table->date('tanggal')->nullable();
            $table->string('rubrik', 7)->default('UNK');
            $table->text('penulis')->nullable();
            $table->text('sumber')->nullable();
            $table->string('keyword', 250)->nullable();
            $table->text('subyek')->nullable();
            $table->text('judul')->nullable();
            $table->text('foto')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->longText('isi')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('del')->default(0);
            $table->integer('add_by')->default(0);
            $table->dateTime('add_date')->nullable();
            $table->integer('edit_by')->default(0);
            $table->dateTime('edit_date')->nullable();

            // Jika artikel_draft_id adalah foreign key, tambahkan ini:
            // $table->foreign('artikel_draft_id')->references('id')->on('artikel_draft')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel_publish');
    }
};
