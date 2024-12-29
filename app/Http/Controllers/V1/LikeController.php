<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Add a Like to a Movie
    public function likeMovie($movieId)
    {
        // Check if the user is logged in
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'You need to be logged in to like a movie.'], 401);
        }

        // Check if the movie exists
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found.'], 404);
        }

        // Check if the user has already liked the movie
        $existingLike = Like::where('user_id', $user->id)->where('movie_id', $movieId)->first();
        if ($existingLike) {
            return response()->json(['message' => 'You have already liked this movie.'], 400);
        }

        // Add the like
        $like = new Like();
        $like->user_id = $user->id;
        $like->movie_id = $movieId;
        $like->created_at = now();
        $like->save();

        return response()->json(['message' => 'Movie liked successfully.'], 200);
    }

    // Remove a Like from a Movie
    public function unlikeMovie($movieId)
    {
        // Check if the user is logged in
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'You need to be logged in to unlike a movie.'], 401);
        }

        // Check if the movie exists
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found.'], 404);
        }

        // Check if the user has already liked the movie
        $existingLike = Like::where('user_id', $user->id)->where('movie_id', $movieId)->first();
        if (!$existingLike) {
            return response()->json(['message' => 'You have not liked this movie yet.'], 400);
        }

        // Remove the like
        $existingLike->delete();

        return response()->json(['message' => 'Movie unliked successfully.'], 200);
    }

    // Get the count of likes for a Movie
    public function getLikeCount($movieId)
    {
        // Check if the movie exists
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found.'], 404);
        }

        // Get the like count
        $likeCount = Like::where('movie_id', $movieId)->count();

        return response()->json(['like_count' => $likeCount], 200);
    }
}
