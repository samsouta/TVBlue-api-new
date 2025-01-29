<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewReleaseController extends Controller
{
    public function getLatestVideos(Request $request)
    {
        $query = Movie::query();

        // Filter by sub_genre if provided
        if ($request->has('sub_genre')) {
            $query->whereHas('subGenre', function($q) use ($request) {
                $q->where('name', $request->sub_genre);
            });
        }

        $videos = $query->with(['genre', 'subGenre', 'tags'])
            ->orderBy('released_year', 'desc')
            ->orderBy('posted_date', 'desc')
            ->paginate(20);

        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No new release videos found'], 404);
        }

        return response()->json([
            'data' => $videos->items(),
            'total' => $videos->total(),
            'current_page' => $videos->currentPage(),
            'last_page' => $videos->lastPage(),
            'per_page' => $videos->perPage(),
            'next_page_url' => $videos->nextPageUrl(),
            'prev_page_url' => $videos->previousPageUrl(),
            'first_page_url' => $videos->url(1),
            'last_page_url' => $videos->url($videos->lastPage()),
        ]);
    }
}
