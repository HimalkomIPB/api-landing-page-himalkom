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
        Schema::create('community_portofolios', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('name');
            $table->string('author');
            $table->string('description');
            $table->string('link');
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_portofolios');
    }
};
