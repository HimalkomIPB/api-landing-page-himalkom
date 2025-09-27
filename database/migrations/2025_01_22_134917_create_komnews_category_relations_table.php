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
        Schema::create('komnews_category_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('komnews_id');
            $table->unsignedBigInteger('komnews_category_id');
            $table->foreign('komnews_id')->references('id')->on('komnews')->onDelete('cascade');
            $table->foreign('komnews_category_id')->references('id')->on('komnews_categories')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['komnews_id', 'komnews_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komnews_category_relations');
    }
};
