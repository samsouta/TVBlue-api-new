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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->dateTime('posted_date');
            $table->integer('duration');
            $table->bigInteger('view_count');
            $table->integer('rating_total');
            $table->integer('rating_count');
            $table->timestamps();
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_genre_id')->constrained()->onDelete('cascade');
            $table->string('language');
            $table->integer('released_year');
            $table->string('thumbnail_url')->unique();
            $table->string('video_url')->unique();
            $table->boolean('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
