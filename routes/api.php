<?php

use App\Http\Controllers\V1\GenreController;
use App\Http\Controllers\V1\LikeController;
use App\Http\Controllers\V1\MovieController;
use App\Http\Controllers\Auth\AuthController; // Import AuthController
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\V1\ActressController;
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\FeatureVdo\FeaturedVideos;
use App\Http\Controllers\V1\FeatureVdo\NewReleaseController;
use App\Http\Controllers\V1\FeatureVdo\RecommentForYouController;
use App\Http\Controllers\V1\Genre\GetMoviesWithGenreController;
use App\Http\Controllers\V1\RelatedMovieController;
use App\Http\Controllers\V1\SearchController;
use App\Http\Controllers\V1\SubGenreController;
use App\Http\Controllers\V1\tags\GetMoviesWithTags;
use App\Http\Controllers\V1\TagsController;
use App\Http\Controllers\V1\ViewCountController;
use App\Http\Controllers\V1\WatchListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will be
| assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Movie and genre routes
    Route::apiResource('movies', MovieController::class);
    Route::apiResource('genres', GenreController::class);
    Route::apiResource('tags', TagsController::class);
    Route::apiResource('actresses', ActressController::class);

    

    Route::get('/mov/by-subgenre', [GetMoviesWithGenreController::class, 'getMoviesBySubGenre']);
    Route::get('/mov/by-tag', [GetMoviesWithTags::class, 'searchByTag']);
    Route::get('/search', [SearchController::class, 'search']);

    Route::get('/movie/{movieId}/like-count', [LikeController::class, 'getLikeCount']);
    Route::get('/movie/{movieId}/comments', [CommentController::class, 'index']);
    Route::post('/movie/{id}/view', [ViewCountController::class, 'incrementViewCount']);
    Route::get('/movie/{videoId}/related', [RelatedMovieController::class, 'getRelatedVideos']);
    Route::post('/movie/{id}/tags', [TagsController::class, 'addTagsToMovie']);
    Route::get('/movie/{id}/tags', [TagsController::class, 'getMovieTags']);
    Route::post('genres/{genreId}/subgenres', [SubGenreController::class, 'store']);
    Route::delete('genres/{genreId}/subgenres/{subGenreId}', [SubGenreController::class, 'destroy']);
    Route::get('act/{id}/movie', [ActressController::class, 'getMovies']);
    Route::post('act/{id}/movie', [ActressController::class, 'attachMovie']);
    Route::delete('act/{id}/movie', [ActressController::class, 'detachMovie']);

    // show home page video type 
    Route::get('/featured-videos', [FeaturedVideos::class, 'getAllFeaturedVideos']);
    Route::get('/new-releases', [NewReleaseController::class, 'getLatestVideos']);
    Route::get('/recommendations', [RecommentForYouController::class, 'getRecommendations']);
    // show home page video type end

    

});

// Authenticated routes (Sanctum middleware)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/movie/{movieId}/like', [LikeController::class, 'likeMovie']);
    Route::post('/movie/{movieId}/unlike', [LikeController::class, 'unlikeMovie']);

    Route::post('/movie/{movieId}/comment', [CommentController::class, 'store']);

    Route::post('/watchlist/{movieId}', [WatchListController::class, 'addToWatchlist']);
    Route::delete('/watchlist/{movieId}', [WatchlistController::class, 'removeFromWatchlist']);
    Route::get('/watchlist', [WatchlistController::class, 'getUserWatchlist']);

    Route::get('/user-profile', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/user-profile', [UserController::class, 'getUserProfile']);
});


// Login route for authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/google-login', [GoogleController::class, 'login']);
