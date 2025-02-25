<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class AddSpecificTagsToMoviesSeeder extends Seeder
{
    public function run()
    {
        $tagIds = [14,18,20,42,37,65,73,89,119,98,104,124];
        $movieIds = [351,352,353,354,355,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383];

        // Verify tags exist
        $existingTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();
        $missingTags = array_diff($tagIds, $existingTags);
        
        if (!empty($missingTags)) {
            $this->command->error("Missing tags: " . implode(',', $missingTags));
            return;
        }

        // Verify movies exist
        $existingMovies = Movie::whereIn('id', $movieIds)->pluck('id')->toArray();
        $missingMovies = array_diff($movieIds, $existingMovies);
        
        if (!empty($missingMovies)) {
            $this->command->error("Missing movies: " . implode(',', $missingMovies));
            return;
        }

        // Add tags to movies
        $successCount = 0;
        foreach ($existingMovies as $movieId) {
            $movie = Movie::find($movieId);
            $movie->tags()->syncWithoutDetaching($tagIds);
            $successCount++;
            $this->command->info("Added tags to Movie ID: {$movieId}");
        }

        $this->command->info("\nSummary:");
        $this->command->info("Successfully added tags to {$successCount} movies");
    }
}
