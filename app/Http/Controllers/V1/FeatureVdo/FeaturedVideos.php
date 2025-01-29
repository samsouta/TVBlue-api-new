<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class FeaturedVideos extends Controller
{
    public function getAllFeaturedVideos(Request $request)
    {
        $videos = Movie::where('is_featured', 1)
            ->with(['genre', 'subGenre', 'tags', 'actresses'])
            ->orderBy('posted_date', 'desc') // Sort by posted_date in descending order
            ->paginate(20);

        return response()->json([
            'data' => $videos->items(),  // The video data
            'total' => $videos->total(),
            'current_page' => $videos->currentPage(),
            'last_page' => $videos->lastPage(),
            'per_page' => $videos->perPage(),
            'next_page_url' => $videos->nextPageUrl(),
            'prev_page_url' => $videos->previousPageUrl(),
            'first_page_url' => $videos->url(1),  // URL for the first page
            'last_page_url' => $videos->url($videos->lastPage()),  // URL for the last page
        ]);
    }
}
