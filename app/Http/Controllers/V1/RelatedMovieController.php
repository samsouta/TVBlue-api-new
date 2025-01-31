<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatedMovieController extends Controller
{
    public function getRelatedVideos($videoId): JsonResponse
    {
        $movie = Movie::with('tags')->find($videoId);

        if ($movie) {
            // Get tag IDs of current movie
            $movieTagIds = $movie->tags->pluck('id');

            // Get related videos that share tags
            $relatedVideos = Movie::with(['genre', 'subGenre', 'tags', 'actresses'])
                ->whereHas('tags', function($query) use ($movieTagIds) {
                    $query->whereIn('tags.id', $movieTagIds);
                })
                ->where('id', '!=', $movie->id)
                ->orderBy('posted_date', 'desc')
                ->limit(30)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Related videos fetched successfully.',
                'related_videos' => $relatedVideos
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Movie not found.'
        ], 404);
    }
}
