<?php

namespace App\Http\Controllers\V1\tags;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Http\Request;

class GetMoviesWithTags extends Controller
{
    public function searchByTag(Request $request)
    {
        $tagName = $request->query('tag');
        $perPage = $request->query('per_page', 10); // Default 10 items per page
        
        if (!$tagName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tag name is required'
            ], 400);
        }

        $movies = Movie::whereHas('tags', function($query) use ($tagName) {
            $query->where('name', 'like', "%{$tagName}%");
        })
        ->with(['tags', 'genre', 'subGenre', 'actresses'])
        ->orderBy('posted_date', 'desc')
        ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $movies
        ]);
    }
}
