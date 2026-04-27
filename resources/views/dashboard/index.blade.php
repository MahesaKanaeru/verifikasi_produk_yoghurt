@extends('layouts.app')

@section('content')

<style>
/* ── Base ─────────────────────────────────── */
.db-section { animation: fadeUp .45s ease both; }
.db-section:nth-child(2) { animation-delay: .08s; }
.db-section:nth-child(3) { animation-delay: .16s; }
.db-section:nth-child(4) { animation-delay: .24s; }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Welcome card ─────────────────────────── */
.welcome-card {
    background: linear-gradient(135deg, #00a8cc 0%, #0077a8 55%, #005f87 100%);
    border-radius: 18px;
    color: #fff;
    padding: 28px 28px 24px;
    position: relative;
    overflow: hidden;
    height: 100%;
}
.welcome-card::before {
    content: "";
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.welcome-card::after {
    content: "";
    position: absolute;
    bottom: -60px; right: 40px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.welcome-card .day-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px);
    border-radius: 30px;
    padding: 4px 14px;
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: 14px;
}
.welcome-card h2 {
    font-size: 1.55rem;
    font-weight: 700;
    margin-bottom: 6px;
    line-height: 1.25;
}
.welcome-card p {
    font-size: .85rem;
    opacity: .82;
    margin: 0;
    line-height: 1.6;
}

/* ── Quick access card ────────────────────── */
.quick-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e6f7fb;
    padding: 24px 20px;
    height: 100%;
    box-shadow: 0 2px 12px rgba(0,168,204,.07);
}
.quick-card h6 {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 16px;
}
.quick-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 16px;
    border-radius: 12px;
    border: 1.5px solid #e0f7fc;
    background: #f7fdff;
    color: #00a8cc;
    font-weight: 600;
    font-size: .88rem;
    text-decoration: none;
    transition: all .22s ease;
    width: 100%;
    margin-bottom: 10px;
}
.quick-btn:last-child { margin-bottom: 0; }
.quick-btn:hover {
    background: #00a8cc;
    border-color: #00a8cc;
    color: #fff;
    transform: translateX(3px);
    box-shadow: 0 4px 14px rgba(0,168,204,.25);
}
.quick-btn .qb-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    background: rgba(0,168,204,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
    transition: background .22s;
}
.quick-btn:hover .qb-icon {
    background: rgba(255,255,255,.22);
    color: #fff;
}

/* ── Stat cards ───────────────────────────── */
.stat-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef6f9;
    padding: 20px 20px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.08);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.teal   { background: #e0f7fa; color: #00a8cc; }
.stat-icon.green  { background: #e8f5e9; color: #2e7d32; }
.stat-icon.orange { background: #fff3e0; color: #e65100; }
.stat-label {
    font-size: .72rem;
    color: #9e9e9e;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 2px;
}
.stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #222;
    line-height: 1;
}
.stat-value small {
    font-size: .75rem;
    font-weight: 500;
    color: #aaa;
    margin-left: 3px;
}

/* ── Recent QR table ──────────────────────── */
.table-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eef6f9;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
}
.table-card-header {
    padding: 18px 20px 14px;
    border-bottom: 1px solid #f0f9fb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.table-card-header h5 {
    font-size: .95rem;
    font-weight: 700;
    color: #222;
    margin: 0;
}
.table-card-header a {
    font-size: .78rem;
    color: #00a8cc;
    text-decoration: none;
    font-weight: 600;
}
.table-card-header a:hover { text-decoration: underline; }

/* Scrollable table on mobile */
.table-responsive-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.db-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .82rem;
    min-width: 480px;
}
.db-table thead th {
    background: #f7fdff;
    color: #9e9e9e;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-size: .68rem;
    padding: 10px 16px;
    border-bottom: 1px solid #eef6f9;
    white-space: nowrap;
}
.db-table tbody tr {
    border-bottom: 1px solid #f4f9fb;
    transition: background .15s;
}
.db-table tbody tr:last-child { border-bottom: none; }
.db-table tbody tr:hover { background: #f7fdff; }
.db-table tbody td {
    padding: 11px 16px;
    color: #333;
    vertical-align: middle;
}
.badge-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .7rem;
    font-weight: 700;
}
.badge-fresh  { background: #e8f5e9; color: #2e7d32; }
.badge-warn   { background: #fff3e0; color: #e65100; }
.badge-expired{ background: #fce4ec; color: #b71c1c; }

/* ── Mobile tweaks ────────────────────────── */
@media (max-width: 767.98px) {
    .welcome-card { padding: 22px 20px 20px; border-radius: 15px; }
    .welcome-card h2 { font-size: 1.2rem; }
    .quick-card { padding: 18px 16px; border-radius: 15px; }
    .stat-card { padding: 16px 15px; border-radius: 13px; }
    .stat-value { font-size: 1.35rem; }
    .stat-icon { width: 42px; height: 42px; font-size: 1rem; }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3">

    {{-- ① Welcome  ─────────────────────────────── --}}
    <div class="row g-3 mb-4 db-section">
        <div class="col-12">
            <div class="welcome-card">
                <div class="day-pill">
                    <i class="fas fa-calendar-day" style="font-size:.7rem;"></i>
                    <span id="hari-ini">Memuat...</span>
                </div>
                <h2>Halo, {{ $adminName ?? 'Admin' }} 👋</h2>
                <p>
                    Pantau produksi yoghurt VTAYA, kelola data produk,<br class="d-none d-md-inline">
                    dan pastikan semua batch dalam kondisi prima hari ini.
                </p>
            </div>
        </div>
    </div>

    {{-- ② Stat Cards ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4 db-section">

        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-value">
                        {{ $totalProduk ?? 0 }}
                        <small>varian</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stat-label">Total Produksi</div>
                    <div class="stat-value">
                        {{ $totalProduksi ?? 0 }}
                        <small>batch</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-label">Hampir Kedaluwarsa</div>
                    <div class="stat-value">
                        {{ $hampirKedaluwarsa ?? 0 }}
                        <small>batch</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ③ Recent QR Table ─────────────────────────────────────── --}}
    <div class="db-section">
        <div class="table-card">
            <div class="table-card-header">
                <h5><i class="fas fa-qrcode me-2 text-info"></i>QR Produksi Terbaru</h5>
                <a href="{{ route('production.index') }}">Lihat semua →</a>
            </div>
            <div class="table-responsive-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Produksi</th>
                            <th>Produk</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Exp</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentQr ?? [] as $i => $item)
                        <tr>
                            <td style="color:#bbb; font-size:.7rem;">{{ $i + 1 }}</td>
                            <td style="font-family: monospace; font-weight:600; color:#00a8cc;">
                                {{ $item->production_code }}
                            </td>
                            <td>{{ $item->product->nama_produk ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->production_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expiration_date)->format('d M Y') }}</td>
                            <td>
                                @php
                                    $daysLeft = now()->diffInDays($item->expiration_date, false);
                                @endphp
                                @if($daysLeft < 0)
                                    <span class="badge-status badge-expired">Kedaluwarsa</span>
                                @elseif($daysLeft <= 7)
                                    <span class="badge-status badge-warn">{{ $daysLeft }}h lagi</span>
                                @else
                                    <span class="badge-status badge-fresh">Segar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#bbb; font-size:.85rem;">
                                <i class="fas fa-inbox me-2"></i>Belum ada data produksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
// Tampilkan hari + tanggal Indonesia real-time
const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const now = new Date();
const label = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
document.getElementById('hari-ini').textContent = label;
</script>

@endsection