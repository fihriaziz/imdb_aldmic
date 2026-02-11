@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4">Movie List</h1>

            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-lg"
                            placeholder="Search movies..."
                            value="{{ request('search', 'movie') }}"
                        >
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Movies Grid -->
    @if(isset($movies) && count($movies) > 0)
        <div class="row g-4">
            @foreach($movies as $movie)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card" style="cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        @if($movie['Poster'] && $movie['Poster'] != 'N/A')
                            <img
                                src="{{ $movie['Poster'] }}"
                                class="card-img-top"
                                alt="{{ $movie['Title'] }}"
                                style="height: 300px; object-fit: cover;"
                            >
                        @else
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                                <span class="text-white text-center">No Image Available</span>
                            </div>
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $movie['Title'] }}</h5>
                            <p class="card-text text-muted">
                                <small>{{ $movie['Year'] ?? 'N/A' }}</small>
                            </p>

                            <a
                                href="{{ route('movies.detail', $movie['imdbID']) }}"
                                class="btn btn-sm btn-primary w-100 mt-3"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Results Info -->
        <div class="row mt-4">
            <div class="col-md-12">
                <p class="text-muted">
                    Showing {{ count($movies) }} results
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            No movies found. Try searching for a different title.
        </div>
    @endif
</div>

<style>
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
