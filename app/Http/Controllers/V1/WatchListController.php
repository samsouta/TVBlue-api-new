<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\WatchList;
use Illuminate\Http\Request;

class WatchListController extends Controller
{
    // Add movie to watchlist
    public function addToWatchlist(Request $request, $movieId)
    {
        $userId = auth()->id();

        // Check if movie is already in watchlist
        $exists = WatchList::where('user_id', $userId)->where('movie_id', $movieId)->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Movie is already in your watchlist'
            ], 400);
        }

        // Add to watchlist
        Watchlist::create([
            'user_id' => $userId,
            'movie_id' => $movieId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Movie added to watchlist'
        ], 201);
    }

    // Remove movie from watchlist
    public function removeFromWatchlist($movieId)
    {
        $userId = auth()->id();

        $watchlist = Watchlist::where('user_id', $userId)->where('movie_id', $movieId)->first();

        if (!$watchlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Movie not found in your watchlist'
            ], 404);
        }

        $watchlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Movie removed from watchlist'
        ], 200);
    }

    // Get all movies in user's watchlist
    public function getUserWatchlist()
    {
        $userId = auth()->id();

        $watchlist = Watchlist::with('movie')->where('user_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $watchlist
        ]);
    }
}
