<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViewCountController extends Controller
{
    public function incrementViewCount($videoId): JsonResponse
    {
        // Find the movie by its ID
        $movie = Movie::find($videoId);

        if ($movie) {
            // Increment the view count by 1
            $movie->view_count = $movie->view_count + 1;
            $movie->save();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'View count updated successfully.',
                'view_count' => $movie->view_count
            ]);
        }

        // Return error if movie not found
        return response()->json([
            'success' => false,
            'message' => 'Movie not found.'
        ], 404);
    }
}
