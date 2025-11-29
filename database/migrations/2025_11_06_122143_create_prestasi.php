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
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->year('tahun')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi')->nullable();
            $table->unsignedBigInteger('prestasi_kategori_id')->nullable();
            $table->string('bukti_path');
            $table->timestamps();
            $table->foreign('prestasi_kategori_id')
                ->references('id')->on('prestasi_category')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
