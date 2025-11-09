<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('album_foto', function (Blueprint $table) {
            $table->id();
            $table->string('nama_album', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->timestamp('edit_at')->nullable();
            $table->unsignedBigInteger('edit_by')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_foto');
    }
};
