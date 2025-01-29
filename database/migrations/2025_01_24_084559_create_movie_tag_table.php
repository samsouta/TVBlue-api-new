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
        Schema::create('movie_tag', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('movie_id')->constrained()->onDelete('cascade'); // Foreign Key for Movies
            $table->foreignId('tag_id')->constrained()->onDelete('cascade'); // Foreign Key for Tags
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_tag');
    }
};
