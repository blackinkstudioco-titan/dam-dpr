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
        Schema::create('kategori_foto', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('k_name', 100)->default('');
            $table->timestamps();

            // Index
            $table->index('k_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_foto');
    }
};
