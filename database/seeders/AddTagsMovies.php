<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class AddTagsMovies extends Seeder
{
    public function run()
    {
        $targetTags = [11,10,5,61,62,63,64];

        $movies = Movie::where('genre_id', 13)
            ->where('sub_genre_id', 29)
            // ->where(function ($query) {
            //     $query->where('title', 'like', '%creampie%')
            //         ->orWhere('description', 'like', '%creampie%');
            // })
            ->get();

        $this->command->info('Found ' . $movies->count() . ' matching movies');

        foreach ($movies as $movie) {
            // Check if movie already has all these tags
            $existingTags = $movie->tags()->pluck('tags.id')->toArray();  // Specify the table name
            $missingTags = array_diff($targetTags, $existingTags);

            if (empty($missingTags)) {
                $this->command->info('Skipping movie (already has tags): ' . $movie->title);
                continue;
            }

            $movie->tags()->sync($targetTags);
            $this->command->info('Added tags to movie: ' . $movie->title);
        }

        $this->command->info('Tags added successfully to filtered movies!');
    }
}
