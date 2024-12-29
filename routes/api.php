<?php

use App\Http\Controllers\V1\GenreController;
use App\Http\Controllers\V1\LikeController;
use App\Http\Controllers\V1\MovieController;
use App\Http\Controllers\Auth\AuthController; // Import AuthController
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\FeatureVdo\FeaturedVideos;
use App\Http\Controllers\V1\FeatureVdo\RecommentForYouController;
use App\Http\Controllers\V1\Genre\GetMoviesWithGenreController;
use App\Http\Controllers\V1\RelatedMovieController;
use App\Http\Controllers\V1\ViewCountController;
use App\Http\Controllers\V1\WatchListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    Route::get('/movie/{movieId}/like-count', [LikeController::class, 'getLikeCount']);
    Route::get('/movie/{movieId}/comments', [CommentController::class, 'index']);
    Route::get('/movie', [GetMoviesWithGenreController::class, 'getMoviesByGenre']);
    Route::post('/movie/{id}/view', [ViewCountController::class, 'incrementViewCount']);
    Route::get('/featured-videos', [FeaturedVideos::class, 'getAllFeaturedVideos']);
    Route::get('/recommendations', [RecommentForYouController::class, 'getRecommendations']);
    Route::get('/movie/{videoId}/related', [RelatedMovieController::class, 'getRelatedVideos']);
    // Login route for authentication
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Authenticated routes (Sanctum middleware)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/movie/{movieId}/like', [LikeController::class, 'likeMovie']);
    Route::post('/movie/{movieId}/unlike', [LikeController::class, 'unlikeMovie']);

    Route::post('/movie/{movieId}/comment', [CommentController::class, 'store']);

    Route::post('/watchlist/{movieId}', [WatchListController::class, 'addToWatchlist']);
    Route::delete('/watchlist/{movieId}', [WatchlistController::class, 'removeFromWatchlist']);
    Route::get('/watchlist', [WatchlistController::class, 'getUserWatchlist']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
