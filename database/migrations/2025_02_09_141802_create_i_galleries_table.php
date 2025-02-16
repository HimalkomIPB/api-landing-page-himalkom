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
        Schema::create('i_galleries', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description");
            $table->string("contributor");
            $table->unsignedBigInteger("angkatan");
            $table->string("image");
            $table->string("link");
            $table->unsignedBigInteger("subject_id");
            $table->foreign("subject_id")->references("id")->on("i_gallery_subjects")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i_galleries');
    }
};
