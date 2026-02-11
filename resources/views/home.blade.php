@extends('layouts.admin')
@section('title', 'Home Pages')
@push('css')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .lazy-image {
            opacity: 0;
        }

        .lazy-image.loaded {
            opacity: 1;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('messages.movie_list') }}</h3>
                    <a href="{{ route('favorites.index') }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-heart"></i> {{ __('messages.my_favorites') }}
                    </a>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="ml-3">
                        <input type="text" class="form-control" placeholder="{{ __('messages.search_movies') }}" id="searchInput">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="moviesContainer">
                        <p class="text-center col-md-12">Loading...</p>
                    </div>
                    <div class="text-center" id="loadingIndicator" style="display: none; padding: 20px;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading more movies...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Movie Detail -->
    <div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-labelledby="movieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" id="modalMovieModal">
                <div class="modal-header">
                    <h5 class="modal-title" id="movieModalLabel">Detail Film</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="modalMoviePoster" src="" alt="Poster" class="img-fluid mb-3">
                        </div>
                        <div class="col-md-8">
                            <h4 id="modalMovieTitle"></h4>
                            <hr>
                            <p>
                                <strong>Tahun:</strong> <span id="modalMovieYear"></span><br>
                                <strong>Tipe:</strong> <span id="modalMovieType"></span><br>
                                <strong>ID Film:</strong> <span id="modalMovieId"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-warning btn-favorite-detail" title="Tambah ke favorit">
                        <i class="fas fa-star"></i> Favorit
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
<script>
    const apiUrl = '{{ route("movies") }}';
    const addFavoriteUrl = '{{ route("movies.favorite.add") }}';
    const removeFavoriteUrl = '{{ route("movies.favorite.remove") }}';
    const getAllFavoritesUrl = '{{ route("movies.favorites") }}';
</script>
@endpush
