<nav id="sidebar">
    <div class="sidebar-header text-center">
        <img src="{{ asset('storage/images/vtaya_logo_tr.png') }}" alt="VTAYA" height="64" style="object-fit: contain;">
        <small class="d-block opacity-75 mt-1">Vtaya Yoghurt</small>
        <small class="d-block opacity-75 mt-1">Kelola Produk dan produksi</small>
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

        <li class="{{ Request::is('production*') ? 'active' : '' }}">
            <a href="{{ route('production.index') }}">
                <i class="fas fa-industry"></i> Data Produksi
            </a>
        </li>

        <li class="{{ Request::is('laporan*') ? 'active' : '' }}">
            <a href="{{ route('laporan.index') }}">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </li>
    </ul>

    <div class="position-absolute bottom-0 w-100 p-3 text-center opacity-50">
        <small>v1.0.0-Stable</small>
    </div>
</nav>