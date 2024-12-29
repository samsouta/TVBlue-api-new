<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        // Default to 10 items per page if not provided in the query string
        $perPage = $request->query('per_page', 10);

        // Fetch movies with pagination, sorted by posted_date (new to old)
        $movies = Movie::with('genre', 'subGenre')
            ->orderBy('posted_date', 'desc')  // Sort by posted_date descending (new to old)
            ->paginate($perPage);

        // Format the posted_date for each movie
        $movies->getCollection()->transform(function ($movie) {
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur') // Ensure Malaysia timezone
                ->format('Y-m-d H:i:s');
            return $movie;
        });

        return response()->json($movies);
    }

    /** 
     * 
     * 
     * 
     * with id
     */
    public function show($id)
    {
        try {
            // Find the movie by its ID with related genre and subGenre
            $movie = Movie::with('genre', 'subGenre', 'likes')->findOrFail($id);

            // Get the like count
            $likeCount = $movie->likes()->count();

            // Format the posted_date with Malaysia time zone
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur')  // Ensure Malaysia timezone
                ->format('Y-m-d H:i:s');

            // Return the response with the like count
            return response()->json([
                'status' => 'success',
                'message' => 'Movie details',
                'movie' => $movie,
                'like_count' => $likeCount, // Add the like count to the response
            ]);
        } catch (ModelNotFoundException $e) {
            // If no movie is found with the given ID, return a 404 error
            return response()->json([
                'status' => 'error',
                'message' => 'Movie not found',
            ], 404);
        } catch (Exception $e) {
            // Generic exception handling for any other errors
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch movie details',
                'details' => $e->getMessage(),
            ], 500);
        }
    }





    // Create a new movie
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'required|integer',
                'view_count' => 'required|integer',
                'rating_total' => 'required|integer',
                'rating_count' => 'required|integer',
                'language' => 'required|string',
                'released_year' => 'required|string',
                'thumbnail_url' => 'required|url|unique:movies,thumbnail_url',
                'video_url' => 'required|url|unique:movies,video_url',
                'is_featured' => 'required|boolean',
                'genre_id' => 'required|exists:genres,id',  // Ensure the genre exists in the genres table
                'sub_genre_id' => 'required|exists:sub_genres,id',  // Ensure the sub-genre exists
            ]);

            $timestamp = time();
            // Append the timestamp to the thumbnail_url
            $thumbnailUrl = $validated['thumbnail_url'] . "?t=" . $timestamp;

            $movie = Movie::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'posted_date' => Carbon::now('Asia/Kuala_Lumpur')->toIso8601String(),
                'duration' => $validated['duration'],
                'view_count' => $validated['view_count'],
                'rating_total' => $validated['rating_total'],
                'rating_count' => $validated['rating_count'],
                'language' => $validated['language'],
                'released_year' => Carbon::parse($validated['released_year'])->format('Y-m-d'),
                'thumbnail_url' => $thumbnailUrl,
                'video_url' => $validated['video_url'],
                'is_featured' => $validated['is_featured'],
                'genre_id' => $validated['genre_id'],
                'sub_genre_id' => $validated['sub_genre_id'],
            ]);

            // Format the posted_date before returning
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur') // Ensure Malaysia timezone
                ->format('Y-m-d H:i:s');

            // Return a success response with the created movie data
            return response()->json($movie, 201);
        } catch (ValidationException $e) {
            // Validation failed, return error message
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            // If any of the related models (genre or sub-genre) are not found
            return response()->json(['error' => 'Related model not found', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            // Generic exception handling for any other errors
            return response()->json(['error' => 'Failed to create movie', 'details' => $e->getMessage()], 500);
        }
    }

    // Update a movie
    public function update(Request $request, $id)
    {
        try {
            // Find the movie by its ID
            $movie = Movie::findOrFail($id);

            // Validate the incoming request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'duration' => 'required|integer',
                'view_count' => 'required|integer',
                'rating_total' => 'required|integer',
                'rating_count' => 'required|integer',
                'language' => 'required|string',
                'released_year' => 'required|string',
                'thumbnail_url' => 'required|url|unique:movies,thumbnail_url',
                'video_url' => 'required|url|unique:movies,video_url',
                'is_featured' => 'required|boolean',
                'genre_id' => 'required|exists:genres,id',
                'sub_genre_id' => 'required|exists:sub_genres,id',
            ]);

            $timestamp = time();
            // Append the timestamp to the thumbnail_url
            $thumbnailUrl = $validated['thumbnail_url'] . "?t=" . $timestamp;

            // Update the movie with the validated data
            $movie->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'posted_date' => Carbon::now('Asia/Kuala_Lumpur')->toIso8601String(),
                'duration' => $validated['duration'],
                'view_count' => $validated['view_count'],
                'rating_total' => $validated['rating_total'],
                'rating_count' => $validated['rating_count'],
                'language' => $validated['language'],
                'released_year' => $validated['released_year'],
                'thumbnail_url' => $thumbnailUrl,
                'video_url' => $validated['video_url'],
                'is_featured' => $validated['is_featured'],
                'genre_id' => $validated['genre_id'],
                'sub_genre_id' => $validated['sub_genre_id'],
            ]);

            // Format the posted_date before returning
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur') // Ensure Malaysia timezone
                ->format('Y-m-d H:i:s');

            // Return a success response with the updated movie data
            return response()->json($movie, 200);
        } catch (ValidationException $e) {
            // Validation failed, return error message
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            // If the movie with the given ID is not found
            return response()->json(['error' => 'Movie not found', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            // Generic exception handling for any other errors
            return response()->json(['error' => 'Failed to update movie', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // Delete a movie
    public function destroy($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }
}
