<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infolombas', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->foreignId('infolomba_category_id')->constrained()->restrictOnDelete();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('link', 2048);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infolombas');
    }
};
