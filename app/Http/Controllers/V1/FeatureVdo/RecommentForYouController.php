<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class RecommentForYouController extends Controller
{
    public function getRecommendations(Request $request)
    {
        $page = $request->get('page', 1);

        $popularMovies = Movie::with(['genre', 'subGenre', 'tags', 'actresses'])  // Added relationships
            ->orderBy('view_count', 'desc')
            ->paginate(4, ['*'], 'page', $page);

        $popularMovies->through(function ($movie) {
            $movie->is_new = $movie->getIsNewAttribute();
            return $movie;
        });

        return response()->json($popularMovies, 200);
    }
}
