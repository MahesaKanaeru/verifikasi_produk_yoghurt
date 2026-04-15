<nav id="sidebar">
    <div class="sidebar-header text-center">
        <h3 class="fw-bold mb-0" style="letter-spacing: 2px;">VTAYA</h3>
        <small class="opacity-75">Yoghurt Management</small>
    </div>

    <ul class="list-unstyled components mt-4">
        <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>

        <li class="{{ Request::is('produk*') ? 'active' : '' }}">
            <a href="{{ route('produk.index') }}">
                <i class="fas fa-box"></i> Data Produk
            </a>
        </li>

        <li class="{{ Request::is('produksi*') ? 'active' : '' }}">
            <a href="{{ route('produksi.index') }}">
                <i class="fas fa-industry"></i> Data Produksi
            </a>
        </li>
    </ul>
    
    <div class="position-absolute bottom-0 w-100 p-3 text-center opacity-50">
        <small>v1.0.0-Stable</small>
    </div>
</nav>