<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        // Validate input
        if (!$query) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search query is required.',
                'data' => [],
            ], 400);
        }

        // Convert query to lowercase for case-insensitive matching
        $query = strtolower($query);

        // Split the query into individual keywords
        $keywords = explode(' ', $query);

        // Search logic with grouped conditions
        $videos = Movie::with(['genre', 'subGenre' ,'tags', 'actresses']) // Eager load genre and subGenre
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhereRaw('LOWER(title) LIKE ?', ['%' . $keyword . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%'])
                        // Searching in Genre description
                        ->orWhereHas('genre', function ($q1) use ($keyword) {
                            $q1->whereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
                        })
                        // Searching in SubGenre name through Genre
                        ->orWhereHas('subGenre', function ($q2) use ($keyword) {
                            $q2->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%']);
                        })
                        // Added search in Tags
                        ->orWhereHas('tags', function ($q3) use ($keyword) {
                            $q3->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%'])
                                ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
                        })
                        // Added search in Actresses name
                        ->orWhereHas('actresses', function ($q4) use ($keyword) {
                            $q4->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%']);
                        });
                }
            })
            ->orderBy('posted_date', 'desc') // Order by newest to oldest
            ->paginate($perPage, ['*'], 'page', $page);

        // Return response based on the result
        if ($videos->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No videos found matching your search.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Videos retrieved successfully.',
            'data' => $videos->items(), // Only returning the items of the current page
            'total' => $videos->total(),
            'current_page' => $videos->currentPage(),
            'last_page' => $videos->lastPage(),
            'per_page' => $videos->perPage(),
            'next_page_url' => $videos->nextPageUrl(),
            'prev_page_url' => $videos->previousPageUrl(),
            'first_page_url' => $videos->url(1),  // URL for the first page
            'last_page_url' => $videos->url($videos->lastPage()),  // URL for the last page
        ], 200);
    }
}
