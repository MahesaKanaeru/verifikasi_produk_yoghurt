<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button id="sidebarCollapse" class="btn btn-info text-white me-3">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand fw-bold text-info" href="{{ route('dashboard') }}" style="letter-spacing: 1px;">VTAYA</a>
        </div>

        <div class="ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2 d-none d-md-inline text-muted">{{ Auth::user()->name }}</span>
                    <i class="fas fa-user-circle fs-4 text-info"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                    <li class="px-3 py-2 d-md-none border-bottom">
                        <strong>{{ Auth::user()->name }}</strong>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="#">
                            <i class="fas fa-user-cog me-2 text-muted"></i> Profile Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                        <a class="dropdown-item py-2 text-danger" href="#" id="btn-logout">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>