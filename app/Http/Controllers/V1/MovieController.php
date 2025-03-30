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


        $movies = Movie::with(['genre', 'subGenre', 'tags', 'actresses'])
            ->orderBy('posted_date', 'desc')
            ->paginate($perPage);

        $movies->through(function ($movie) {
            $movie->is_new = $movie->is_new;
            return $movie;
        });

        return response()->json($movies);
    }

    public function show($id)
    {
        try {
            $movie = Movie::with(['genre', 'subGenre', 'tags', 'actresses'])->findOrFail($id);

            $movie->like_count = $movie->getLikeCount();

            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur')  // Convert to Malaysia timezone
                ->format('Y-m-d H:i:s');

            return response()->json([
                'status' => 'success',
                'movie' => $movie,
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
                'tag_id' => 'array|exists:tags,id',
                'actress_id' => 'array|exists:actresses,id',
                'video_type' => 'required|in:free,premium',
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
                'video_type' => $validated['video_type'],
            ]);
            // Attach tags and actresses
            $movie->tags()->sync($validated['tag_id']); // Add tags
            $movie->actresses()->sync($validated['actress_id']);

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
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'nullable|integer',
                'view_count' => 'nullable|integer',
                'rating_total' => 'nullable|integer',
                'rating_count' => 'nullable|integer',
                'language' => 'nullable|string',
                'released_year' => 'nullable|string',
                'thumbnail_url' => 'nullable|url|unique:movies,thumbnail_url,' . $id,
                'video_url' => 'nullable|url',
                'is_featured' => 'nullable|boolean',
                'genre_id' => 'nullable|exists:genres,id',
                'sub_genre_id' => 'nullable|exists:sub_genres,id',
                'tag_id' => 'nullable|array|exists:tags,id',
                'actress_id' => 'nullable|array|exists:actresses,id',
                'video_type' => 'nullable|in:free,premium',
            ]);

            $updateData = [];

            // Only include fields that were actually provided in the request
            foreach ($validated as $field => $value) {
                if ($request->has($field)) {
                    if ($field === 'thumbnail_url' && $value) {
                        $timestamp = time();
                        $updateData[$field] = $value . "?t=" . $timestamp;
                    } else if ($field === 'released_year' && $value) {
                        $updateData[$field] = Carbon::parse($value)->format('Y-m-d');
                    } else {
                        $updateData[$field] = $value;
                    }
                }
            }

            // Only update posted_date if other fields are being updated
            if (!empty($updateData)) {
                $updateData['posted_date'] = Carbon::now('Asia/Kuala_Lumpur')
                    ->setTimezone('UTC')
                    ->toDateTimeString();
            }

            $movie->update($updateData);

            // Update relationships only if they were provided
            if (isset($validated['tag_id'])) {
                $movie->tags()->sync($validated['tag_id']);
            }

            if (isset($validated['actress_id'])) {
                $movie->actresses()->sync($validated['actress_id']);
            }

            // Format the response date
            $movie->posted_date = Carbon::parse($movie->posted_date)
                ->timezone('Asia/Kuala_Lumpur')
                ->format('Y-m-d H:i:s');

            return response()->json([
                'status' => 'success',
                'message' => 'Movie updated successfully',
                'movie' => $movie
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Movie not found', 'details' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update movie', 'details' => $e->getMessage()], 500);
        }
    }


    // destroy

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
