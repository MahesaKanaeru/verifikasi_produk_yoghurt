@extends('layouts.app')
@section('title', 'Data Produksi')
@section('content')

<style>
    .table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef6f9;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
    }
    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f9fb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-box { position: relative; min-width: 220px; }
    .search-box i {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%); color: #aaa; font-size: .85rem;
    }
    .search-box input {
        padding-left: 30px; font-size: .85rem;
        border-radius: 8px; border: 1px solid #e0eef3;
    }
    .search-box input:focus {
        border-color: #00a8cc;
        box-shadow: 0 0 0 3px rgba(0,168,204,.1);
    }
    .product-preview {
        background: #f7fdff; border: 1px solid #e0f7fc;
        border-radius: 12px; padding: 14px; display: none;
    }
    .product-preview img {
        width: 70px; height: 70px; object-fit: cover;
        border-radius: 10px; border: 1px solid #e0f0f5;
    }
    .expiry-preview {
        background: linear-gradient(135deg, #e8f5e9, #f1f8ff);
        border: 1px solid #c8e6c9; border-radius: 10px;
        padding: 10px 14px; display: none;
    }
    .pagination .page-link {
        border-radius: 6px !important; margin: 0 2px;
        border: 1px solid #e0eef3; color: #00a8cc;
        font-size: .82rem; padding: 5px 11px;
    }
    .pagination .page-item.active .page-link {
        background-color: #00a8cc; border-color: #00a8cc; color: #fff;
    }
    .pagination .page-item.disabled .page-link { color: #ccc; }
    .badge-qty {
        background: #e8f5e9; color: #2e7d32;
        font-weight: 600; font-size: .78rem;
        padding: 3px 8px; border-radius: 20px;
    }
</style>


{{-- ─── Header ──────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Produksi & QR Code</h2>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProduksi">
        <i class="fas fa-qrcode me-1"></i> Generate QR Baru
    </button>
</div>

{{-- ─── Flash Messages ──────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- ─── Table Card ──────────────────────────────────────────────── --}}
<div class="table-card">
    <div class="table-card-header">
        <h5 class="fw-bold mb-0" style="font-size:.95rem;">
            <i class="fas fa-industry me-2 text-info"></i>Daftar Produksi
        </h5>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control form-control-sm"
                   placeholder="Cari kode, produk, tanggal…">
        </div>
    </div>

    <div class="p-3">
        <div class="mb-2">
            <small id="tableInfo" class="text-muted"></small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-2">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px;">No</th>
                        <th>Kode Produksi</th>
                        <th>Varian Produk</th>
                        <th>Tgl Produksi</th>
                        <th>Kedaluwarsa</th>
                        <th>Qty</th>
                        <th style="width:60px;">QR</th>
                        <th>Label</th>
                        <th style="width:60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">

                    @forelse($productions as $i => $prod)
                    <tr class="prod-row"
                        data-search="{{ strtolower(
                            $prod->production_number . ' ' .
                            ($prod->product->nama_produk ?? '') . ' ' .
                            \Carbon\Carbon::parse($prod->production_date)->format('d m Y') . ' ' .
                            $prod->plain_expiry
                        ) }}">

                        <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>

                        {{-- Tampilkan production_number (plain: VY00001), bukan production_code (cipher) --}}
                        <td>
                            <code class="fw-bold" style="color:#0d6efd;">{{ $prod->production_number }}</code>
                        </td>

                        <td>{{ $prod->product->nama_produk ?? 'N/A' }}</td>

                        <td>{{ \Carbon\Carbon::parse($prod->production_date)->format('d M Y') }}</td>

                        {{-- Gunakan plain_expiry karena expiration_date di DB sudah cipher --}}
                        <td>{{ $prod->plain_expiry }}</td>

                        {{-- Kolom Qty baru --}}
                        <td>
                            <span class="badge-qty">{{ number_format($prod->qty) }} pcs</span>
                        </td>

                        <td>
                            @if($prod->qr_code_path)
                                <img src="{{ asset('storage/'.$prod->qr_code_path) }}"
                                     width="44" height="44"
                                     class="rounded border shadow-sm"
                                     style="cursor:pointer;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#modalQr{{ $prod->id }}"
                                     alt="QR">
                            @else
                                <span class="badge bg-secondary">Proses</span>
                            @endif
                        </td>

                        <td>
                            @if($prod->final_label_path)
                                <a href="{{ route('production.download-label', $prod->id) }}"
                                   class="btn btn-sm btn-outline-dark">
                                    <i class="fas fa-print me-1"></i>Cetak
                                </a>
                            @else
                                <a href="{{ route('production.download-qr', $prod->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>QR
                                </a>
                            @endif
                        </td>

                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete({{ $prod->id }}, '{{ $prod->production_number }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <form id="deleteForm{{ $prod->id }}"
                                  action="{{ route('production.destroy', $prod->id) }}"
                                  method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-qrcode fs-1 d-block mb-3 text-secondary"></i>
                            Belum ada data produksi.
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahProduksi"
                               class="text-decoration-none">Generate sekarang</a>
                        </td>
                    </tr>
                    @endforelse

                </tbody>

                <tbody id="emptySearch" style="display:none;">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-search d-block mb-2 fs-3 text-secondary"></i>
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-2" id="paginationWrapper" style="display:none!important;">
            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
        </div>
    </div>
</div>


{{-- ─── Modal Preview QR ───────────────────────────────────────── --}}
@foreach($productions as $prod)
    @if($prod->qr_code_path)
    <div class="modal fade" id="modalQr{{ $prod->id }}" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center p-4">
                {{-- Tampilkan production_number (plain) di modal --}}
                <div class="fw-bold fs-5 mb-1">{{ $prod->production_number }}</div>
                <div class="text-muted mb-3" style="font-size:.88rem;">
                    {{ $prod->product->nama_produk ?? 'N/A' }}
                </div>
                <img src="{{ asset('storage/'.$prod->qr_code_path) }}"
                     class="img-fluid rounded mb-3 shadow-sm mx-auto"
                     style="max-width:200px;" alt="QR Code">
                <div class="text-muted mb-1" style="font-size:.85rem;">
                    Exp: <span class="fw-bold text-danger">{{ $prod->plain_expiry }}</span>
                </div>
                <div class="text-muted mb-3" style="font-size:.85rem;">
                    Qty: <span class="fw-bold text-success">{{ number_format($prod->qty) }} pcs</span>
                </div>
                <a href="{{ asset('storage/'.$prod->qr_code_path) }}"
                   download="QR_{{ $prod->production_number }}.png"
                   class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-download me-1"></i> Download QR
                </a>
            </div>
        </div>
    </div>
    @endif
@endforeach


{{-- ─── Modal Generate QR ───────────────────────────────────────── --}}
<div class="modal fade" id="modalTambahProduksi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2 text-primary"></i>Generate QR Code Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('production.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Produk</label>
                        <select name="product_id" id="product_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-nama="{{ $product->nama_produk }}"
                                    data-kode="{{ $product->kode_produk }}"
                                    data-expired="{{ $product->estimasi_expired }}"
                                    data-foto="{{ $product->foto_produk
                                        ? asset('storage/'.$product->foto_produk)
                                        : asset('images/no-image.png') }}">
                                    {{ $product->nama_produk }} ({{ $product->kode_produk }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Preview produk --}}
                    <div class="product-preview mb-3" id="productPreview">
                        <div class="d-flex gap-3 align-items-center">
                            <img id="prev_foto" src="" alt="Foto Produk">
                            <div>
                                <div class="fw-bold mb-1" id="prev_nama"></div>
                                <div class="text-muted small">
                                    Kode: <span class="fw-semibold text-dark" id="prev_kode"></span>
                                </div>
                                <div class="text-muted small">
                                    Masa simpan: <span class="fw-semibold text-dark" id="prev_expired"></span> hari
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Produksi</label>
                        <input type="date" name="production_date" id="production_date"
                               class="form-control" required>
                    </div>

                    {{-- Estimasi kedaluwarsa otomatis --}}
                    <div class="expiry-preview mb-3" id="expiryPreview">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-check text-success fs-5"></i>
                            <div>
                                <div class="text-muted" style="font-size:.76rem; text-transform:uppercase; letter-spacing:.5px;">
                                    Estimasi Kedaluwarsa
                                </div>
                                <div class="fw-bold text-success" id="expiryDateDisplay" style="font-size:.95rem;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Input Qty (field baru) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Jumlah Produksi <span class="text-muted fw-normal">(pcs / botol)</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="qty" id="qty"
                                   class="form-control" min="1" placeholder="Contoh: 500" required>
                            <span class="input-group-text text-muted">pcs</span>
                        </div>
                        <div class="form-text">Jumlah unit yang diproduksi dalam batch ini.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-qrcode me-1"></i> Generate QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ─── Scripts ──────────────────────────────────────────────────── --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const ROWS_PER_PAGE = 10;
let currentPage  = 1;
let filteredRows = [];

document.addEventListener('DOMContentLoaded', () => {
    filteredRows = Array.from(document.querySelectorAll('.prod-row'));
    render();

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        filteredRows = Array.from(document.querySelectorAll('.prod-row'))
            .filter(r => r.dataset.search.includes(q));
        currentPage = 1;
        render();
    });
});

function render() {
    document.querySelectorAll('.prod-row').forEach(r => r.style.display = 'none');
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    filteredRows.slice(start, start + ROWS_PER_PAGE).forEach(r => r.style.display = '');

    const emptySearch = document.getElementById('emptySearch');
    if (emptySearch) {
        const hasQuery = document.getElementById('searchInput').value.trim() !== '';
        emptySearch.style.display = (filteredRows.length === 0 && hasQuery) ? '' : 'none';
    }

    renderInfo();
    renderPagination();
}

function renderInfo() {
    const total = filteredRows.length;
    const start = total === 0 ? 0 : (currentPage - 1) * ROWS_PER_PAGE + 1;
    const end   = Math.min(currentPage * ROWS_PER_PAGE, total);
    document.getElementById('tableInfo').textContent =
        total > 0 ? `Menampilkan ${start}–${end} dari ${total} data` : '';
}

function renderPagination() {
    const totalPages = Math.ceil(filteredRows.length / ROWS_PER_PAGE);
    const wrapper    = document.getElementById('paginationWrapper');
    const ul         = document.getElementById('pagination');
    if (totalPages <= 1) { wrapper.style.display = 'none'; return; }
    wrapper.style.display = 'flex';
    ul.innerHTML = '';

    const addItem = (label, page, disabled = false, active = false) => {
        const li = document.createElement('li');
        li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
        const a = document.createElement('a');
        a.className = 'page-link'; a.href = '#'; a.innerHTML = label;
        if (!disabled) a.addEventListener('click', e => { e.preventDefault(); changePage(page); });
        li.appendChild(a); ul.appendChild(li);
    };

    addItem('‹', currentPage - 1, currentPage === 1);
    const pageSet = new Set([1, totalPages]);
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) pageSet.add(i);
    let prev = 0;
    Array.from(pageSet).sort((a,b)=>a-b).forEach(p => {
        if (prev && p - prev > 1) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link">…</span>';
            ul.appendChild(li);
        }
        addItem(p, p, false, p === currentPage);
        prev = p;
    });
    addItem('›', currentPage + 1, currentPage === totalPages);
}

function changePage(page) {
    const total = Math.ceil(filteredRows.length / ROWS_PER_PAGE);
    if (page < 1 || page > total) return;
    currentPage = page;
    render();
    document.querySelector('.table-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* Modal preview produk + estimasi kedaluwarsa */
document.addEventListener('DOMContentLoaded', () => {
    const selectProduk = document.getElementById('product_id');
    const inputTanggal = document.getElementById('production_date');
    const elPreview    = document.getElementById('productPreview');
    const elExpiry     = document.getElementById('expiryPreview');

    function hitungKedaluwarsa() {
        const opt  = selectProduk.options[selectProduk.selectedIndex];
        const days = parseInt(opt?.dataset?.expired ?? 0);
        const tgl  = inputTanggal.value;
        if (!selectProduk.value || !tgl || isNaN(days)) { elExpiry.style.display = 'none'; return; }
        const d = new Date(tgl);
        d.setDate(d.getDate() + days);
        document.getElementById('expiryDateDisplay').textContent =
            d.toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
        elExpiry.style.display = 'block';
    }

    selectProduk.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!this.value) { elPreview.style.display = 'none'; elExpiry.style.display = 'none'; return; }
        document.getElementById('prev_foto').src            = opt.dataset.foto;
        document.getElementById('prev_nama').textContent    = opt.dataset.nama;
        document.getElementById('prev_kode').textContent    = opt.dataset.kode;
        document.getElementById('prev_expired').textContent = opt.dataset.expired;
        elPreview.style.display = 'block';
        hitungKedaluwarsa();
    });

    inputTanggal.addEventListener('change', hitungKedaluwarsa);
});

/* Konfirmasi hapus */
function confirmDelete(id, kode) {
    Swal.fire({
        title: 'Hapus Data Produksi?',
        html:  `Kode <strong>${kode}</strong> akan dihapus permanen.<br>
                <small class="text-muted">File QR & label ikut terhapus dari server.</small>`,
        icon:  'warning',
        showCancelButton:   true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  '<i class="fas fa-trash-alt me-1"></i> Ya, Hapus',
        cancelButtonText:   'Batal',
        reverseButtons:     true,
        focusCancel:        true,
    }).then(result => {
        if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
    });
}
</script>

@endsection