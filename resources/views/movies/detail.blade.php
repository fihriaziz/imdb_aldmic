@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('movies.list') }}" class="btn btn-secondary mb-3">
                ← Back to Movies
            </a>
        </div>
    </div>

    @if(isset($movie))
        <div class="row">
            <!-- Movie Poster -->
            <div class="col-md-4 mb-4">
                @if($movie['Poster'] && $movie['Poster'] != 'N/A')
                    <img
                        src="{{ $movie['Poster'] }}"
                        class="img-fluid rounded shadow"
                        alt="{{ $movie['Title'] }}"
                    >
                @else
                    <div class="bg-secondary rounded shadow d-flex align-items-center justify-content-center" style="height: 400px;">
                        <span class="text-white text-center">No Poster Available</span>
                    </div>
                @endif
            </div>

            <!-- Movie Details -->
            <div class="col-md-8">
                <h1 class="mb-2">{{ $movie['Title'] ?? 'N/A' }}</h1>

                <div class="mb-4">
                    <span class="badge bg-primary me-2">{{ $movie['Year'] ?? 'N/A' }}</span>
                    <span class="badge bg-info me-2">{{ $movie['Type'] ?? 'N/A' }}</span>
                    <span class="badge bg-warning text-dark">{{ $movie['Rated'] ?? 'N/A' }}</span>
                </div>

                <!-- IMDb Rating -->
                @if(isset($movie['imdbRating']) && $movie['imdbRating'] != 'N/A')
                    <div class="alert alert-success mb-4">
                        <strong>IMDb Rating:</strong> {{ $movie['imdbRating'] }}/10
                    </div>
                @endif

                <!-- Plot -->
                @if(isset($movie['Plot']) && $movie['Plot'] != 'N/A')
                    <div class="mb-4">
                        <h5>Plot</h5>
                        <p class="lead">{{ $movie['Plot'] }}</p>
                    </div>
                @endif

                <!-- Details Grid -->
                <div class="row mb-4">
                    @if(isset($movie['Director']) && $movie['Director'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Director</h6>
                            <p class="mb-0">{{ $movie['Director'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Writer']) && $movie['Writer'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Writer</h6>
                            <p class="mb-0">{{ $movie['Writer'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Actors']) && $movie['Actors'] != 'N/A')
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">Cast</h6>
                            <p class="mb-0">{{ $movie['Actors'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Genre']) && $movie['Genre'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Genre</h6>
                            <p class="mb-0">{{ $movie['Genre'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Runtime']) && $movie['Runtime'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Runtime</h6>
                            <p class="mb-0">{{ $movie['Runtime'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Country']) && $movie['Country'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Country</h6>
                            <p class="mb-0">{{ $movie['Country'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['Language']) && $movie['Language'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Language</h6>
                            <p class="mb-0">{{ $movie['Language'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['BoxOffice']) && $movie['BoxOffice'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Box Office</h6>
                            <p class="mb-0">{{ $movie['BoxOffice'] }}</p>
                        </div>
                    @endif

                    @if(isset($movie['imdbVotes']) && $movie['imdbVotes'] != 'N/A')
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">IMDb Votes</h6>
                            <p class="mb-0">{{ $movie['imdbVotes'] }}</p>
                        </div>
                    @endif
                </div>

                <!-- IMDb Link -->
                @if(isset($movie['imdbID']))
                    <a
                        href="https://www.imdb.com/title/{{ $movie['imdbID'] }}"
                        target="_blank"
                        class="btn btn-outline-primary"
                    >
                        View on IMDb
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Movie Not Found</h4>
            <p>The movie you're looking for could not be found. Please try searching again.</p>
            <a href="{{ route('movies.list') }}" class="btn btn-primary">Back to Movies</a>
        </div>
    @endif
</div>
@endsection
