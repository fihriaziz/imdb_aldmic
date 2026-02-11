$(document).ready(function() {
    let allMovies = [];
    let favoriteMovies = {};
    let currentPage = 1;
    let currentSearch = 'movie';
    let totalPages = 0;
    let isLoading = false;

    // Setup Intersection Observer untuk Lazy Loading Images
    const lazyImageObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');

                if (src && !img.classList.contains('loaded')) {
                    img.src = src;
                    img.classList.add('loaded');
                    img.addEventListener('load', function() {
                        // Fade in effect saat gambar loaded
                        img.style.animation = 'fadeIn 0.5s ease-in';
                    });
                    lazyImageObserver.unobserve(img);
                }
            }
        });
    }, {
        rootMargin: '50px' // Load 50px sebelum reach viewport
    });

    window.lazyImageObserver = lazyImageObserver;

    function renderMovies(movies, append = false) {
        const container = $('#moviesContainer');

        if (!append) {
            container.empty();
        }

        if (movies.length === 0 && !append) {
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
                        <div class="position-relative" style="background: #f0f0f0; height: 330px; display: flex; align-items: center; justify-content: center;">
                            <img class="lazy-image card-img-top"
                                 data-src="${movie.Poster}"
                                 alt="${movie.Title}"
                                 style="height: 330px; object-fit: cover; width: 100%;">
                            <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
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

        // Setup lazy loading untuk gambar yang baru di-render
        setupLazyLoading();

        // Tambahkan sentinel untuk infinite scroll
        if (append && currentPage < totalPages) {
            container.append('<div id="scrollSentinel" class="col-md-12"></div>');
            setupInfiniteScroll();
        } else if (!append && currentPage < totalPages) {
            container.append('<div id="scrollSentinel" class="col-md-12"></div>');
            setupInfiniteScroll();
        }
    }

    // Setup lazy loading untuk images
    function setupLazyLoading() {
        const lazyImages = document.querySelectorAll('.lazy-image:not(.loaded)');

        if (window.lazyImageObserver) {
            lazyImages.forEach(img => window.lazyImageObserver.observe(img));
        }
    }

    function setupInfiniteScroll() {
        const sentinel = document.getElementById('scrollSentinel');

        if (!sentinel) return;

        // Hapus observer lama jika ada
        if (window.sentinelObserver) {
            window.sentinelObserver.disconnect();
        }

        // Setup Intersection Observer
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !isLoading && currentPage < totalPages) {
                    isLoading = true;
                    fetchMovies(currentSearch, currentPage + 1);
                }
            });
        }, {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        });

        observer.observe(sentinel);
        window.sentinelObserver = observer;
    }

    function fetchMovies(searchQuery = 'movie', page = 1) {
        currentSearch = searchQuery;
        currentPage = page;

        // Show loading indicator untuk page > 1
        if (page > 1) {
            $('#loadingIndicator').show();
        }

        $.ajax({
            url: apiUrl,
            type: 'GET',
            data: { search: searchQuery, page: page },
            success: function(response) {
                if (response.success) {
                    totalPages = response.totalPages;

                    if (page === 1) {
                        allMovies = response.data;
                        renderMovies(allMovies, false);
                    } else {
                        allMovies = allMovies.concat(response.data);
                        renderMovies(response.data, true);
                    }

                    loadFavoriteStatus();
                    isLoading = false;
                } else {
                    allMovies = [];
                    renderMovies([]);
                    isLoading = false;
                }

                // Hide loading indicator
                $('#loadingIndicator').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching movies:', error);
                isLoading = false;
                $('#loadingIndicator').hide();
            }
        });
    }

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
                    // Re-render to update button colors
                    const container = $('#moviesContainer');
                    container.find('.btn-favorite').each(function() {
                        const btn = $(this);
                        const movieId = btn.data('id');
                        const isFav = favoriteMovies[movieId] || false;

                        if (isFav) {
                            btn.addClass('btn-danger').removeClass('btn-warning');
                            btn.attr('title', 'Hapus dari favorit');
                        } else {
                            btn.removeClass('btn-danger').addClass('btn-warning');
                            btn.attr('title', 'Tambah ke favorit');
                        }
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
            fetchMovies('movie', 1);
        } else if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(function() {
                fetchMovies(searchTerm, 1);
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

            const modalFavoriteBtn = $('#modalMovieModal .btn-favorite-detail');
            modalFavoriteBtn.data('id', movieDetail.imdbID);
            modalFavoriteBtn.data('title', movieDetail.Title);
            modalFavoriteBtn.data('poster', movieDetail.Poster);

            updateButtonFavoriteStatus(modalFavoriteBtn);

            $('#movieModal').modal('show');
        }
    }

    $(document).on('click', '.btn-favorite, .btn-favorite-detail', function(e) {
        e.preventDefault();
        const btn = $(this);
        const movieId = btn.data('id');
        const movieTitle = btn.data('title');
        const moviePoster = btn.data('poster');
        const isFav = favoriteMovies[movieId] || false;

        if (isFav) {
            toggleFavorite(movieId, false, btn);
        } else {
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
                    favoriteMovies[movieId] = isAdd;

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

