<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    // Fetch all genres with their subgenres
    public function index()
    {
        $genres = Genre::with('subGenres')->get();

        return response()->json(['status' => 'success', 'data' => $genres]);
    }

    // Display the specified genre by ID
    public function show($id)
    {
        $genre = Genre::with('subGenres')->find($id);

        if (!$genre) {
            return response()->json(['status' => 'error', 'message' => 'Genre not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $genre]);
    }

    // Create a new genre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $genre = Genre::create($validated);

        return response()->json(['status' => 'success', 'message' => 'Genre created successfully', 'data' => $genre], 201);
    }

    // Update an existing genre
    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json(['status' => 'error', 'message' => 'Genre not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $genre->update($validated);

        return response()->json(['status' => 'success', 'message' => 'Genre updated successfully', 'data' => $genre]);
    }

    // Delete an existing genre
    public function destroy($id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json(['status' => 'error', 'message' => 'Genre not found'], 404);
        }

        $genre->delete();

        return response()->json(['status' => 'success', 'message' => 'Genre deleted successfully']);
    }
}
