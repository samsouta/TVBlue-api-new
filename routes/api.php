<?php

use App\Http\Controllers\V1\GenreController;
use App\Http\Controllers\V1\LikeController;
use App\Http\Controllers\V1\MovieController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Payment\PaymentController;
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
use Illuminate\Support\Facades\Artisan;
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

/**
 * Public routes
 */
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
    Route::get('act/names/all', [ActressController::class, 'getAllNames']);

    // show home page video type 
    Route::get('/featured-videos', [FeaturedVideos::class, 'getAllFeaturedVideos']);
    Route::get('/new-releases', [NewReleaseController::class, 'getLatestVideos']);
    Route::get('/recommendations', [RecommentForYouController::class, 'getRecommendations']);
    // show home page video type end


    //code for payment
    Route::post('/generate-premium-code-chaw', [PaymentController::class, 'generatePremiumCode']);

    // Login route for authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/google-login', [GoogleController::class, 'login']);
});

/**
 * Protected routes
 */
Route::middleware('auth:sanctum', 'verify.session')->prefix('v1')->group(function () {
    Route::post('/movie/{movieId}/like', [LikeController::class, 'likeMovie']);
    Route::post('/movie/{movieId}/unlike', [LikeController::class, 'unlikeMovie']);
    Route::post('/movie/{movieId}/comment', [CommentController::class, 'store']);
    Route::post('/watchlist/{movieId}', [WatchListController::class, 'addToWatchlist']);
    Route::delete('/watchlist/{movieId}', [WatchlistController::class, 'removeFromWatchlist']);
    Route::get('/watchlist', [WatchlistController::class, 'getUserWatchlist']);

    //user profile
    Route::get('/user-profile/{id}', [UserController::class, 'getUserById']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // payment gateway
    Route::post('/payment/create-order', [PaymentController::class, 'PayPalCreateOrder']);
    Route::post('/payment/capture-payment', [PaymentController::class, 'PayPalCapturePayment']);
    Route::post('/payment/card', [PaymentController::class, 'payWithCard']);
    Route::post('/payment/redeem-premium-code', [PaymentController::class, 'redeemPremiumCode']);
    
});


/**
 * Clear Cache
 */
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return response()->json(['message' => 'Cache cleared!']);
});

/**
 * my world (onlyOne)
 */
Route::get('/users-chaw', [UserController::class, 'UserProfileAll']);
