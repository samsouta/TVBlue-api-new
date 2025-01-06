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

        // Search logic with grouped conditions
        $videos = Movie::with(['genre', 'subGenre']) // Eager load genre and subGenre
            ->where(function ($q) use ($query) {
                // Searching in Movie title and description (case-insensitive)
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%'])
                    // Searching in Genre description (case-insensitive)
                    ->orWhereHas('genre', function ($q1) use ($query) {
                        $q1->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($query) . '%']);
                    })
                    // Searching in SubGenre name through Genre (case-insensitive)
                    ->orWhereHas('subGenre', function ($q2) use ($query) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
                    });
            })
            ->paginate($perPage, ['*'], 'page', $page); // Accepts page number from query params

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
