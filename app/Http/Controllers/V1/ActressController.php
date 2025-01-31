<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Actress;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ActressController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $actresses = Actress::withCount('movies')
            ->with(['movies' => function($query) {
                $query->orderBy('posted_date', 'desc');  // Order from newest to oldest
            }])
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($actresses);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image_url' => 'nullable|url',
                'age' => 'nullable|integer|min:18',
                'nationality' => 'nullable|string|max:100',
                'birth_date' => 'nullable|date',
                'movie_ids' => 'nullable|array',
                'movie_ids.*' => 'exists:movies,id',
                'is_popular' => 'boolean'
            ]);

            $actress = Actress::create($validated);

            if (isset($validated['movie_ids'])) {
                $actress->movies()->attach($validated['movie_ids']);
            }

            return response()->json($actress->load('movies'), 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function show($id)
    {
        $actress = Actress::findOrFail($id);
        $movies = $actress->movies()
            ->with(['genre', 'subGenre', 'tags'])
            ->orderBy('posted_date', 'desc')
            ->paginate(10);

        return response()->json([
            'actress' => $actress,
            'movies' => $movies
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $actress = Actress::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image_url' => 'nullable|url',
                'age' => 'nullable|integer|min:18',
                'nationality' => 'nullable|string|max:100',
                'birth_date' => 'nullable|date',
                'movie_ids' => 'nullable|array',
                'movie_ids.*' => 'exists:movies,id',
                'is_popular' => 'boolean'
            ]);

            $actress->update($validated);

            if (isset($validated['movie_ids'])) {
                $actress->movies()->sync($validated['movie_ids']);
            }

            return response()->json($actress->load('movies'));
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        $actress = Actress::findOrFail($id);
        $actress->movies()->detach();
        $actress->delete();
        return response()->json(['message' => 'Actress deleted successfully']);
    }

    public function getMovies($id)
    {
        $actress = Actress::findOrFail($id);
        $movies = $actress->movies()
            ->with(['genre', 'subGenre', 'tags'])
            ->orderBy('posted_date', 'desc')
            ->paginate(10);

        return response()->json([
            'actress' => $actress,
            'movies' => $movies
        ]);
    }

    public function attachMovie(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'movie_ids' => 'required|array',
                'movie_ids.*' => 'exists:movies,id'
            ]);

            $actress = Actress::findOrFail($id);
            $actress->movies()->attach($validated['movie_ids']);

            return response()->json([
                'message' => 'Movies attached successfully',
                'actress' => $actress->load('movies')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function detachMovie(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'movie_ids' => 'required|array',
                'movie_ids.*' => 'exists:movies,id'
            ]);

            $actress = Actress::findOrFail($id);
            $actress->movies()->detach($validated['movie_ids']);

            return response()->json([
                'message' => 'Movies detached successfully',
                'actress' => $actress->load('movies')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function getAllNames()
    {
        $actresses = Actress::select('id', 'name','image_url','is_popular')->get();
        return response()->json([
            'status' => 'success',
            'data' => $actresses
        ]);
    }
}
