<?php

namespace App\Http\Controllers\V1\FeatureVdo;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class FeaturedVideos extends Controller
{
    public function getAllFeaturedVideos(Request $request)
    {
        // Fetch featured videos with pagination (2 videos per page)
        $videos = Movie::where('is_featured', 1)->paginate(10);

        return response()->json($videos, 200); // Laravel pagination response
    }
}

