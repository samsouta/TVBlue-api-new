<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actress_movie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actress_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actress_movie');
    }
};