<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatedMovieController extends Controller
{
    public function getRelatedVideos($videoId): JsonResponse
    {
        // Find the movie by its ID
        $movie = Movie::find($videoId);

        if ($movie) {
            // Get movies with the same genre, excluding the current movie
            $relatedVideos = Movie::where('genre_id', $movie->genre_id)
                ->where('id', '!=', $movie->id)  // Exclude the current movie
                ->limit(10)  
                ->get();

            // Return the related videos
            return response()->json([
                'success' => true,
                'message' => 'Related videos fetched successfully.',
                'related_videos' => $relatedVideos
            ]);
        }

        // Return error if movie not found
        return response()->json([
            'success' => false,
            'message' => 'Movie not found.'
        ], 404);
    }
}
