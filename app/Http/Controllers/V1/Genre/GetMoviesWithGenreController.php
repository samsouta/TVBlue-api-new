<?php

namespace App\Http\Controllers\V1\Genre;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class GetMoviesWithGenreController extends Controller
{
    public function getMoviesBySubGenre(Request $request)
    {
        // Get the sub_genre query parameter
        $subGenreParam = $request->query('sub_genre');

        // Ensure the sub_genre parameter is provided
        if (!$subGenreParam) {
            return response()->json(['error' => 'Please provide a sub_genre'], 400);
        }

        // Normalize the sub-genre parameter (lowercase and remove spaces)
        $normalizedSubGenre = strtolower(str_replace(' ', '', $subGenreParam));

        // Set default pagination per page value (can also be set dynamically via the query)
        $perPage = 10; // Default per page value

        // Query the movies by the sub_genre
        $moviesQuery = Movie::query();
        $moviesQuery->with(['genre', 'subGenre', 'tags', 'actresses']) // Added eager loading relationships
            ->whereHas('subGenre', function ($query) use ($normalizedSubGenre) {
                // Match the sub-genre after normalizing
                $query->whereRaw('LOWER(REPLACE(name, " ", "")) = ?', [$normalizedSubGenre]);
            });

        // Add pagination, the page parameter is automatically handled by Laravel
        $movies = $moviesQuery
            ->orderBy('posted_date', 'desc') // Changed to posted_date to match other controllers
            ->paginate($perPage);

        // If no movies found, return an empty response with a message
        if ($movies->isEmpty()) {
            return response()->json(['message' => 'No movies found for the given sub_genre'], 404);
        }
        
        $movies->through(function ($movie) {
            $movie->is_new = $movie->is_new;
            return $movie;
        });

        // Return the paginated movies
        return response()->json($movies);
    }
}
