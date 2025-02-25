<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class CheckMoviesWithoutTagsSeeder extends Seeder
{
    public function run()
    {
        $moviesWithoutTags = Movie::doesntHave('tags')
            ->select('id', 'title', 'language')
            ->get()
            ->groupBy('language');
        
        if ($moviesWithoutTags->isEmpty()) {
            $this->command->info("All movies have tags!");
            return;
        }

        $this->command->error("\nMovies without tags by language:");
        
        foreach ($moviesWithoutTags as $language => $movies) {
            $movieIds = $movies->pluck('id')->toArray();
            
            $this->command->warn("\nLanguage: " . strtoupper($language));
            $this->command->warn("Count: " . count($movieIds));
            $this->command->info('"movie_ids": [' . implode(',', $movieIds) . ']');
            
            foreach ($movies as $movie) {
                $this->command->line("- Movie ID: {$movie->id} - Title: {$movie->title}");
            }
        }

        $this->command->error("\nTotal movies without tags: " . $moviesWithoutTags->flatten()->count());
    }
}
