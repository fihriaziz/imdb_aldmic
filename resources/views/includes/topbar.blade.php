<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Language Switcher -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-globe"></i>
                <span class="ml-2 d-none d-lg-inline text-gray-600 small">
                    @if (app()->getLocale() === 'id')
                        Bahasa Indonesia
                    @else
                        English
                    @endif
                </span>
            </a>
            <!-- Dropdown - Language Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="languageDropdown">
                <form action="{{ route('language.change') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="language" value="en">
                    <button type="submit" class="dropdown-item @if(app()->getLocale() === 'en') active @endif">
                        <i class="fas fa-check fa-sm fa-fw mr-2 @if(app()->getLocale() !== 'en') invisible @endif"></i>
                        English
                    </button>
                </form>
                <form action="{{ route('language.change') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="language" value="id">
                    <button type="submit" class="dropdown-item @if(app()->getLocale() === 'id') active @endif">
                        <i class="fas fa-check fa-sm fa-fw mr-2 @if(app()->getLocale() !== 'id') invisible @endif"></i>
                        Bahasa Indonesia
                    </button>
                </form>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    @if (Auth::check())
                        {{ Auth::user()->name }}
                    @endif
                </span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </button>
                </form>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->
