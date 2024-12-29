<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Add a comment to a movie
    public function store(Request $request, $movieId)
    {
        // Validate the request data
        $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        // Find the movie by ID
        $movie = Movie::findOrFail($movieId);

        // Create the comment
        $comment = new Comment();
        $comment->user_id = Auth::id(); // Get the authenticated user's ID
        $comment->movie_id = $movie->id;
        $comment->comment_text = $request->comment_text;
        $comment->save();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }


    // Get all comments for a movie
    public function index($movieId)
    {
        // Find the movie by ID
        $movie = Movie::findOrFail($movieId);

        // Get all comments for the movie
        $comments = $movie->comments()->with('user')->get();

        // Return the comments
        return response()->json([
            'status' => 'success',
            'comments' => $comments,
        ]);
    }
}
