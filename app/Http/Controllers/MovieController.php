<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FavoriteMovie;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function getMovies(Request $request)
    {
        $apiKey = env('OMDB_MOVIE_API_KEY');
        $searchQuery = $request->get('search', 'movie');
        $page = $request->get('page', 1);

        try {
            $response = Http::get('https://www.omdbapi.com/', [
                'apikey' => $apiKey,
                's' => $searchQuery,
                'type' => 'movie',
                'page' => $page
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['Search'])) {
                $movies = $data['Search'];
                $totalResults = isset($data['totalResults']) ? (int)$data['totalResults'] : 0;
            } else {
                $movies = [];
                $totalResults = 0;
            }

            return response()->json([
                'success' => true,
                'data' => $movies,
                'totalResults' => $totalResults,
                'currentPage' => (int)$page,
                'totalPages' => ceil($totalResults / 8)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => []], 500);
        }
    }

    public function addFavorite(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|string',
            'movie_title' => 'required|string',
            'movie_poster' => 'required|string'
        ]);

        $userId = Auth::id();

        try {
            $favorite = FavoriteMovie::create([
                'user_id' => $userId,
                'movie_id' => $request->movie_id,
                'movie_title' => $request->movie_title,
                'movie_poster' => $request->movie_poster
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Film ditambahkan ke favorit',
                'data' => $favorite
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Film sudah ada di favorit atau terjadi error'
            ], 400);
        }
    }

    public function removeFavorite(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|string'
        ]);

        $userId = Auth::id();

        try {
            FavoriteMovie::where('user_id', $userId)
                ->where('movie_id', $request->movie_id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Film dihapus dari favorit'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus favorit'
            ], 400);
        }
    }

    public function getFavorites()
    {
        $userId = Auth::id();
        $favorites = FavoriteMovie::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('favorites.index', ['favorites' => $favorites]);
    }

    public function getFavoritesAPI(Request $request)
    {
        $userId = Auth::id();
        $favorites = FavoriteMovie::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $favorites
        ]);
    }

    public function isFavorite(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|string'
        ]);

        $userId = Auth::id();
        $isFavorite = FavoriteMovie::where('user_id', $userId)
            ->where('movie_id', $request->movie_id)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }

    public function detail_movie($id)
    {
        $apiKey = env('OMDB_MOVIE_API_KEY');

        try {
            $response = Http::get('https://www.omdbapi.com/', [
                'apikey' => $apiKey,
                'i' => $id,
                'type' => 'movie',
                'plot' => 'full'
            ]);

            $data = $response->json();

            if ($response->successful() && $data['Response'] === 'True') {
                $movie = $data;
            } else {
                $movie = null;
            }

            return view('movies.detail', ['movie' => $movie]);
        } catch (\Exception $e) {
            return view('movies.detail', ['movie' => null]);
        }
    }
}

