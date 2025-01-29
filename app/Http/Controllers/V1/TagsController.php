<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags'
        ]);

        $tag = Tag::create([
            'name' => $request->name
        ]);

        return response()->json($tag, 201);
    }

    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json($tag);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id
        ]);

        $tag = Tag::findOrFail($id);
        $tag->update([
            'name' => $request->name
        ]);

        return response()->json($tag);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    public function addTagsToMovie(Request $request, $movieId)
    {
        try {
            $validated = $request->validate([
                'tag_ids' => 'required|array',
                'tag_ids.*' => 'exists:tags,id'
            ]);

            $movie = Movie::findOrFail($movieId);
            $tags = Tag::whereIn('id', $validated['tag_ids'])->get();
            $movie->tags()->sync($tags);

            return response()->json([
                'message' => 'Tags added successfully!',
                'movie' => $movie->load('tags')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add tags',
                'message' => $e->getMessage()
            ], 422);
        }
    }

        public function getMovieTags($movieId)
    {
        $movie = Movie::with(['tags', 'actresses'])->findOrFail($movieId);
        
        return response()->json([
            'movie' => $movie,
        ]);
    }
}
