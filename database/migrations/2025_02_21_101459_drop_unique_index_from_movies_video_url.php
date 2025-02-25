<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop the unique index on the video_url column.
        // Make sure the index name matches what exists in your database.
        DB::statement('ALTER TABLE movies DROP INDEX movies_video_url_unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 2: Re-add the unique index.
        // When indexing a TEXT column, we must specify a key length.
        DB::statement('ALTER TABLE movies ADD UNIQUE `movies_video_url_unique` (`video_url`(191))');
    }
};
