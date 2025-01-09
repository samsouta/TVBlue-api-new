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
        $perPage = $request->query('per_page', 10);

        $movies = Movie::with('genre', 'subGenre')
            ->orderBy('posted_date', 'desc')
            ->paginate($perPage);

        $movies->getCollection()->transform(function ($movie) {
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur') // Convert to Malaysia timezone
                ->format('Y-m-d H:i:s');
            return $movie;
        });

        return response()->json($movies);
    }

    public function show($id)
    {
        try {
            $movie = Movie::with('genre', 'subGenre')->findOrFail($id);

            $likeCount = $movie->likes()->count();

            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur')  // Convert to Malaysia timezone
                ->format('Y-m-d H:i:s');

            return response()->json([
                'status' => 'success',
                'message' => 'Movie details',
                'movie' => $movie,
                'like_count' => $likeCount,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Movie not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch movie details', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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
            $thumbnailUrl = $validated['thumbnail_url'] . "?t=" . $timestamp;

            $postedDate = Carbon::now('Asia/Kuala_Lumpur')->setTimezone('UTC')->toDateTimeString();

            $movie = Movie::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'posted_date' => $postedDate,
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

            return response()->json($movie, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Related model not found', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create movie', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $movie = Movie::findOrFail($id);

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
            $thumbnailUrl = $validated['thumbnail_url'] . "?t=" . $timestamp;

            $movie->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'posted_date' => Carbon::now('Asia/Kuala_Lumpur')->setTimezone('UTC')->toDateTimeString(),
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

            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur')
                ->format('Y-m-d H:i:s');

            return response()->json($movie, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Movie not found', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update movie', 'details' => $e->getMessage()], 500);
        }
    }

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
