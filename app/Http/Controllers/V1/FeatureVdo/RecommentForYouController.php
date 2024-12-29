<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class RecommentForYouController extends Controller
{
    public function getRecommendations(Request $request)
    {
        // Get the page number from the request, defaulting to 1 if not provided
        $page = $request->get('page', 1);

        // Option 1: Popular Movies based on view_count or rating_total
        $popularMovies = Movie::orderBy('view_count', 'desc') // or order by rating_total
            ->paginate(4, ['*'], 'page', $page); // Paginate with 5 movies per page

            return response()->json($popularMovies, 200);
    }
}
