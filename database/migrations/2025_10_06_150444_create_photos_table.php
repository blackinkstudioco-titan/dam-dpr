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
        Schema::create('data_foto', function (Blueprint $table) {
            $table->id();
            $table->string('mm_id', 17)->default('');
            $table->string('judul', 60)->nullable();
            $table->longText('deskrp');
            $table->string('k_word', 250)->nullable();
            $table->string('f_lok', 100)->default('');
            $table->string('thumbnail_foto_url')->nullable();
            $table->string('original_foto_url')->nullable();
            $table->unsignedInteger('f_size')->default(0);
            $table->date('tgl_masuk')->default('1900-01-01');
            $table->string('mm_lok', 60)->default('');
            $table->date('tgl_mm')->default('1900-01-01');
            $table->string('perekam', 60)->default('');
            $table->string('subyek', 20)->default('');
            $table->string('k_name', 50)->default('root');
            $table->bigInteger('l_access')->default(0);
            $table->string('konseptor', 32)->nullable();
            $table->string('depositor', 15)->nullable();
            $table->string('judul_en', 60)->nullable()->comment('Judul dalam bahasa Inggris');
            $table->longText('deskrp_en')->nullable()->comment('Deskripsi dalam bahasa Inggris');
            $table->string('file_release', 100)->nullable();
            $table->integer('download')->default(0);
            $table->integer('view')->default(0);
            $table->unsignedInteger('selection_id')->default(0);
            $table->boolean('publish')->default(true);
            $table->integer('status')->nullable();
            $table->text('meta_data')->nullable();
            $table->string('kategorisasi_datatempo', 100)->nullable();
            $table->string('edit_by')->nullable();
            $table->dateTime('edit_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('mm_id');
            $table->index('tgl_masuk');
            $table->index('publish');
            $table->index('subyek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_foto');
    }
};
