<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class FeaturedVideos extends Controller
{
    public function getAllFeaturedVideos(Request $request)
    {
        $movies = Movie::where('is_featured', 1)
            ->with(['genre', 'subGenre', 'tags', 'actresses'])
            ->orderBy('posted_date', 'desc') // Sort by posted_date in descending order
            ->paginate(20);

        $movies->through(function ($movie) {
            $movie->is_new = $movie->is_new;
            return $movie;
        });

        return response()->json([
            'data' => $movies->items(),  // The video data
            'total' => $movies->total(),
            'current_page' => $movies->currentPage(),
            'last_page' => $movies->lastPage(),
            'per_page' => $movies->perPage(),
            'next_page_url' => $movies->nextPageUrl(),
            'prev_page_url' => $movies->previousPageUrl(),
            'first_page_url' => $movies->url(1),  // URL for the first page
            'last_page_url' => $movies->url($movies->lastPage()),  // URL for the last page
        ]);
    }
}
