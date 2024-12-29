<?php

namespace App\Http\Controllers\V1\Genre;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class GetMoviesWithGenreController extends Controller
{
    public function getMoviesByGenre(Request $request)
    {
        $genreParam = $request->query('genre');
        $subGenreParam = $request->query('sub_genre');
        $perPage = $request->query('per_page', 10); // Default 10 items per page

        if (!$genreParam && !$subGenreParam) {
            return response()->json(['error' => 'Please provide genre or sub_genre'], 400);
        }

        $moviesQuery = Movie::query();

        if ($genreParam) {
            $normalizedGenre = strtolower(str_replace(' ', '', $genreParam));
            $moviesQuery->whereHas('genre', function ($query) use ($normalizedGenre) {
                $query->whereRaw('LOWER(REPLACE(name, " ", "")) = ?', [$normalizedGenre]);
            });
        }

        if ($subGenreParam) {
            $normalizedSubGenre = strtolower(str_replace(' ', '', $subGenreParam));
            $moviesQuery->whereHas('subGenre', function ($query) use ($normalizedSubGenre) {
                $query->whereRaw('LOWER(REPLACE(name, " ", "")) = ?', [$normalizedSubGenre]);
            });
        }

        $movies = $moviesQuery
            ->orderBy('created_at', 'desc') // Sort by newest to oldest
            ->paginate($perPage);

        if ($movies->isEmpty()) {
            return response()->json(['message' => 'No movies found'], 404);
        }

        return response()->json($movies);
    }

    
}
