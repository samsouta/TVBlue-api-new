<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewReleaseController extends Controller
{
    // Method to fetch "newly released" videos with pagination
    public function getLatestVideos(Request $request)
    {
        // Fetch paginated videos ordered by 'released_year' in descending order
        $videos = Movie::orderBy('released_year', 'desc')  // Order by released_year from new to old
            ->orderBy('posted_date', 'desc')  // Order by posted_date from new to old
            ->paginate(20);  // Pagination with 20 items per page

        // If no videos found, return a message
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No new release videos found'], 404);
        }

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
