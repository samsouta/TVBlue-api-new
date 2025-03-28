<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movies', function (Blueprint $table) {
            // First, modify the column to have a default value
            $table->string('video_type')->default('free')->change();
        });

        // Then update all existing records that have null video_type
        DB::table('movies')->whereNull('video_type')->update(['video_type' => 'free']);
    }

    public function down()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('video_type')->default(null)->change();
        });
    }
};