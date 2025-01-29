<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class ChangeSubGenreMovieSeeder extends Seeder
{
    public function run()
    {
        $oldSubGenreId = 25;
        $newSubGenreId = 24;

        $movies = Movie::where('sub_genre_id', $oldSubGenreId)->get();

        $count = $movies->count();
        $this->command->info("Found {$count} movies with sub-genre ID {$oldSubGenreId}");

        foreach ($movies as $movie) {
            $movie->update(['sub_genre_id' => $newSubGenreId]);
            $this->command->info("Updated movie: {$movie->title}");
        }

        $this->command->info("Successfully updated {$count} movies from sub-genre ID {$oldSubGenreId} to {$newSubGenreId}");
    }
}
