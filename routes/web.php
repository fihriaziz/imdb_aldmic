<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Movie routes
Route::middleware('auth')->group(function () {
    Route::get('/', [MovieController::class, 'home'])->name('home');
    Route::get('/movies', [MovieController::class, 'getMovies'])->name('movies');
    Route::get('/movies/{id}', [MovieController::class, 'detail_movie'])->name('movies.detail');

    // Favorite routes
    Route::get('/favorites', [MovieController::class, 'getFavorites'])->name('favorites.index');
    Route::get('/movies/favorites', [MovieController::class, 'getFavoritesAPI'])->name('movies.favorites');
    Route::post('/movies/favorite/add', [MovieController::class, 'addFavorite'])->name('movies.favorite.add');
    Route::post('/movies/favorite/remove', [MovieController::class, 'removeFavorite'])->name('movies.favorite.remove');
    Route::post('/movies/favorite/check', [MovieController::class, 'isFavorite'])->name('movies.favorite.check');

    // Language routes
    Route::post('/language/change', [LanguageController::class, 'change'])->name('language.change');
});

// Language API route (accessible without auth for AJAX)
Route::get('/api/languages', [LanguageController::class, 'getAvailable'])->name('languages.available');
