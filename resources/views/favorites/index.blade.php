@extends('layouts.admin')
@section('title', 'Favorite Movies')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('messages.my_favorites') }}</h3>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($favorites->count() > 0)
                        <div class="row" id="favoritesContainer">
                            @foreach($favorites as $favorite)
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100">
                                        <img src="{{ $favorite->movie_poster }}"
                                             alt="{{ $favorite->movie_title }}" class="card-img-top" style="height: 330px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $favorite->movie_title }}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>ID:</strong> {{ $favorite->movie_id }}<br>
                                                    <strong>{{ __('messages.added') }}:</strong> {{ $favorite->created_at->format('d M Y') }}
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <button class="btn btn-sm btn-danger btn-remove-favorite" data-id="{{ $favorite->movie_id }}">
                                                <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>{{ __('messages.no_favorites') }} <a href="{{ route('home') }}">{{ __('messages.add_favorites_here') }}</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const removeFavoriteUrl = '{{ route("movies.favorite.remove") }}';
        const getAllFavoritesUrl = '{{ route("movies.favorites") }}';
        const tokenCsrf = '{{ csrf_token() }}';
    </script>
@endsection
