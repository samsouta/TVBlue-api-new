<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class SubGenreController extends Controller
{
    public function store(Request $request, $genreId)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Find the genre
        $genre = Genre::find($genreId);

        if (!$genre) {
            return response()->json([
                'status' => 'error',
                'message' => 'Genre not found'
            ], 404);
        }

        // Create the subgenre
        $subGenre = $genre->subGenres()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'SubGenre added successfully',
            'data' => $subGenre
        ], 201);
    }


    // Remove a sub-genre from a genre
    public function destroy($genreId, $subGenreId)
    {
        // Find the genre by its ID
        $genre = Genre::find($genreId);

        if (!$genre) {
            return response()->json(['status' => 'error', 'message' => 'Genre not found'], 404);
        }

        // Find the sub-genre by its ID
        $subGenre = $genre->subGenres()->find($subGenreId);

        if (!$subGenre) {
            return response()->json(['status' => 'error', 'message' => 'SubGenre not found'], 404);
        }

        // Delete the sub-genre
        $subGenre->delete();

        return response()->json(['status' => 'success', 'message' => 'SubGenre deleted successfully']);
    }
}
