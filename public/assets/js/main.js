$(document).ready(function() {
    let allMovies = [];
    let favoriteMovies = {};

    function renderMovies(movies) {
        const container = $('#moviesContainer');
        container.empty();

        if (movies.length === 0) {
            container.html('<div class="alert alert-info col-md-12">Tidak ada film yang ditemukan</div>');
            return;
        }

        movies.forEach(function(movie) {
            const isFav = favoriteMovies[movie.imdbID] || false;
            const btnClass = isFav ? 'btn-danger' : 'btn-warning';
            const btnTitle = isFav ? 'Hapus dari favorit' : 'Tambah ke favorit';

            const movieCard = `
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="${movie.Poster}"
                             alt="${movie.Title}" class="card-img-top" style="height: 330px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">${movie.Title}</h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Tahun:</strong> ${movie.Year}<br>
                                    <strong class="mb-3">Tipe:</strong> ${movie.Type}<br>
                                    <strong class="mb-3">ID:</strong> ${movie.imdbID}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <button class="btn btn-sm btn-primary btn-detail" data-id="${movie.imdbID}">Lihat Detail</button>
                            <button class="btn btn-sm ${btnClass} btn-favorite ml-2" data-id="${movie.imdbID}" data-title="${movie.Title}" data-poster="${movie.Poster}" title="${btnTitle}">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(movieCard);
        });
    }

    function fetchMovies(searchQuery = 'movie') {
        $.ajax({
            url: apiUrl,
            type: 'GET',
            data: { search: searchQuery },
            success: function(response) {
                if (response.success) {
                    allMovies = response.data;
                    loadFavoriteStatus();
                    renderMovies(allMovies);
                } else {
                    allMovies = [];
                    renderMovies([]);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching movies:', error);
                renderMovies([]);
            }
        });
    }

    // Load all favorite status sekali saja
    function loadFavoriteStatus() {
        $.ajax({
            url: getAllFavoritesUrl,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    favoriteMovies = {};
                    response.data.forEach(function(fav) {
                        favoriteMovies[fav.movie_id] = true;
                    });
                }
            }
        });
    }

    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val().trim();

        clearTimeout(searchTimeout);

        if (searchTerm.length === 0) {
            fetchMovies('movie');
        } else if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(function() {
                fetchMovies(searchTerm);
            }, 500);
        }
    });

    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        const movieId = $(this).data('id');
        showMovieModal(movieId);
    });

    function showMovieModal(movieId) {
        const movieDetail = allMovies.find(m => m.imdbID === movieId);

        if (movieDetail) {
            $('#modalMovieTitle').text(movieDetail.Title);
            $('#modalMoviePoster').attr('src', movieDetail.Poster);
            $('#modalMovieYear').text(movieDetail.Year);
            $('#modalMovieType').text(movieDetail.Type);
            $('#modalMovieId').text(movieDetail.imdbID);

            // Set button favorite di modal
            const modalFavoriteBtn = $('#modalMovieModal .btn-favorite-detail');
            modalFavoriteBtn.data('id', movieDetail.imdbID);
            modalFavoriteBtn.data('title', movieDetail.Title);
            modalFavoriteBtn.data('poster', movieDetail.Poster);

            updateButtonFavoriteStatus(modalFavoriteBtn);

            $('#movieModal').modal('show');
        }
    }

    // Handle favorite button click - toggle
    $(document).on('click', '.btn-favorite, .btn-favorite-detail', function(e) {
        e.preventDefault();
        const btn = $(this);
        const movieId = btn.data('id');
        const movieTitle = btn.data('title');
        const moviePoster = btn.data('poster');
        const isFav = favoriteMovies[movieId] || false;

        if (isFav) {
            // Remove from favorite
            toggleFavorite(movieId, false, btn);
        } else {
            // Add to favorite
            toggleFavorite(movieId, true, btn, movieTitle, moviePoster);
        }
    });

    function toggleFavorite(movieId, isAdd, btn, movieTitle, moviePoster) {
        const url = isAdd ? addFavoriteUrl : removeFavoriteUrl;
        const data = {
            movie_id: movieId,
            _token: tokenCsrf
        };

        if (isAdd) {
            data.movie_title = movieTitle;
            data.movie_poster = moviePoster;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    // Update local state
                    favoriteMovies[movieId] = isAdd;

                    // Update button style
                    if (isAdd) {
                        btn.addClass('btn-danger').removeClass('btn-warning');
                        btn.attr('title', 'Hapus dari favorit');
                        showNotification('Film berhasil ditambahkan ke favorit');
                    } else {
                        btn.addClass('btn-warning').removeClass('btn-danger');
                        btn.attr('title', 'Tambah ke favorit');
                        showNotification('Film berhasil dihapus dari favorit');
                    }
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan');
            }
        });
    }

    function updateButtonFavoriteStatus(btn) {
        const movieId = btn.data('id');
        const isFav = favoriteMovies[movieId] || false;

        if (isFav) {
            btn.addClass('btn-danger').removeClass('btn-warning');
            btn.attr('title', 'Hapus dari favorit');
        } else {
            btn.removeClass('btn-danger').addClass('btn-warning');
            btn.attr('title', 'Tambah ke favorit');
        }
    }

    function showNotification(message) {
        // Simple notification (bisa diganti dengan toast library)
        const alert = $(`<div class="alert alert-success alert-notification">${message}</div>`);
        $('body').prepend(alert);
        setTimeout(() => alert.fadeOut(300, function() { $(this).remove(); }), 3000);
    }

    $(document).on('click', '.btn-remove-favorite', function(e) {
        e.preventDefault();
        const movieId = $(this).data('id');
        const button = $(this);

        if(confirm('Apakah Anda yakin ingin menghapus film ini dari favorit?')) {
            toggleFavorite(movieId, false, button);
            setTimeout(() => {
                button.closest('.col-md-3').fadeOut(300, function() {
                    $(this).remove();
                    if($('#favoritesContainer').children().length === 0) {
                        location.reload();
                    }
                });
            }, 500);
        }
    });

    loadFavoriteStatus();
    fetchMovies('movie');
});

