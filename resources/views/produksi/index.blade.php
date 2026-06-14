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
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBulkProduksi">
        <i class="fas fa-qrcode me-1"></i> Generate QR Baru
    </button>
</div>

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
                            $prod->production_code . ' ' .
                            ($prod->product->nama_produk ?? '') . ' ' .
                            \Carbon\Carbon::parse($prod->production_date)->format('d m Y') . ' ' .
                            $prod->plain_expiry
                        ) }}">

                        <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>

                        <td>
                            <code class="fw-bold" style="color:#0d6efd;">{{ $prod->production_code }}</code>
                        </td>

                        <td>{{ $prod->product->nama_produk ?? 'N/A' }}</td>

                        <td>{{ \Carbon\Carbon::parse($prod->production_date)->format('d M Y') }}</td>

                        <td>{{ $prod->plain_expiry }}</td>

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
                                <a href="{{ route('production.print-batch', $prod->id) }}"
                                    class="btn btn-sm btn-outline-success btn-cetak-massal"
                                    data-qty="{{ $prod->qty }}"
                                    data-nama="{{ $prod->product->nama_produk ?? 'Produk' }}"
                                    onclick="cetakMassal(event, this)">
                                        <i class="fas fa-copy me-1"></i>Cetak Massal ({{ $prod->qty }})
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
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalBulkProduksi"
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


{{-- ─── Modal Bulk Generate ───────────────────────────────────────── --}}
<div class="modal fade" id="modalBulkProduksi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2 text-primary"></i>Bulk Generate QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('production.bulk-store') }}" method="POST" id="bulkForm">
                @csrf
                <div class="modal-body" style="max-height:65vh; overflow-y:auto;">

                    {{-- Tanggal produksi (shared) --}}
                    <div class="mb-4 p-3 rounded-3" style="background:#f7fdff; border:1px solid #e0f7fc;">
                        <label class="form-label fw-semibold mb-1">
                            <i class="fas fa-calendar-alt me-1 text-info"></i>Tanggal Produksi
                            <span class="text-muted fw-normal">(berlaku untuk semua item)</span>
                        </label>
                        <input type="date" name="production_date" id="bulk_production_date"
                               class="form-control" required>
                    </div>

                    {{-- Daftar item produksi --}}
                    <div id="bulkItems">
                        {{-- Row pertama (tidak bisa dihapus) --}}
                        <div class="bulk-item-row mb-3 p-3 rounded-3 border position-relative" data-index="0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-primary item-label" style="font-size:.88rem;">
                                    <i class="fas fa-box me-1"></i>Produksi #<span class="item-number">1</span>
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold">Produk</label>
                                    <select name="items[0][product_id]" class="form-select form-select-sm bulk-product-select" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-expired="{{ $product->estimasi_expired }}"
                                                data-nama="{{ $product->nama_produk }}">
                                                {{ $product->nama_produk }} ({{ $product->kode_produk }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Qty (pcs)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="items[0][qty]"
                                               class="form-control" min="1" placeholder="Qty" required>
                                        <span class="input-group-text text-muted">pcs</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Preview kedaluwarsa per baris --}}
                            <div class="bulk-expiry-preview mt-2" style="display:none; font-size:.82rem;"
                                 data-for="0">
                                <i class="fas fa-calendar-check text-success me-1"></i>
                                Estimasi kedaluwarsa:
                                <strong class="text-success bulk-expiry-date"></strong>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol tambah baris --}}
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" id="addBulkRow">
                        <i class="fas fa-plus me-1"></i> Tambah Produk Lain
                    </button>

                    {{-- Ringkasan --}}
                    <div class="mt-3 p-2 rounded-3 text-muted" id="bulkSummary"
                         style="background:#f8f9fa; font-size:.82rem; display:none;">
                        <i class="fas fa-info-circle me-1"></i>
                        <span id="bulkSummaryText"></span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="bulkSubmitBtn">
                        <i class="fas fa-layer-group me-1"></i>
                        Generate <span id="bulkCountLabel">1</span> QR Code
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
/* ─── Pagination & Search ───────────────────────────────────────── */
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

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        html: '{!! session('success') !!}',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        html: '{!! session('error') !!}', 
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Tutup',
    });
    @endif
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
    Array.from(pageSet).sort((a, b) => a - b).forEach(p => {
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

/* ─── Konfirmasi Hapus ──────────────────────────────────────────── */
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
        if (result.isConfirmed) {
            // Loading saat proses hapus
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
            document.getElementById('deleteForm' + id).submit();
        }
    });
}

function cetakMassal(e, el) {
    e.preventDefault();

    const qty      = el.dataset.qty;
    const nama     = el.dataset.nama;
    const url      = el.href;
    const prodId   = url.split('/').filter(Boolean).pop(); // ambil ID dari URL
    const cookieKey = 'pdf_ready';

    // Hapus cookie lama kalau ada (biar polling tidak langsung selesai)
    document.cookie = `${cookieKey}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;

    Swal.fire({
        title: 'Membuat PDF...',
        html: `<b>${nama}</b> &bull; ${qty} pcs<br>
               <small class="text-muted">Mohon tunggu, file PDF sedang disiapkan...</small>`,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();

            // Trigger download
            setTimeout(() => {
                window.location.href = url;
            }, 300);

            // Polling cookie tiap 500ms
            const interval = setInterval(() => {
                const cookies = document.cookie.split(';').map(c => c.trim());
                const found   = cookies.find(c => c.startsWith(cookieKey + '='));

                if (found) {
                    clearInterval(interval);

                    // Hapus cookie
                    document.cookie = `${cookieKey}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;

                    Swal.fire({
                        icon: 'success',
                        title: 'PDF Siap!',
                        html: `<b>${qty} label</b> berhasil disusun.<br>
                               <small class="text-muted">File sedang diunduh...</small>`,
                        timer: 2500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                }
            }, 500);

            // Safety timeout 3 menit — kalau cookie tidak muncul, tetap tutup loading
            setTimeout(() => {
                clearInterval(interval);
                if (Swal.isVisible()) {
                    Swal.fire({
                        icon: 'info',
                        title: 'File Sedang Diunduh',
                        html: `Proses selesai. Cek folder unduhan kamu.<br>
                               <small class="text-muted">Jika file belum muncul, coba klik tombol Cetak Massal lagi.</small>`,
                        confirmButtonColor: '#00a8cc',
                        confirmButtonText: 'OK',
                    });
                }
            }, 180000);
        }
    });
}

/* ─── Bulk Generate ─────────────────────────────────────────────── */
(function () {
    let bulkIndex = 1;
    const products  = @json($productsJson);
    const bulkDate  = document.getElementById('bulk_production_date');
    const bulkItems = document.getElementById('bulkItems');

    function getExpiryText(days, dateVal) {
        if (!dateVal || isNaN(parseInt(days))) return null;
        const d = new Date(dateVal);
        d.setDate(d.getDate() + parseInt(days));
        return d.toLocaleDateString('id-ID', {
            weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
        });
    }

    function updateRowExpiry(row) {
        const sel     = row.querySelector('.bulk-product-select');
        const preview = row.querySelector('.bulk-expiry-preview');
        const opt     = sel.options[sel.selectedIndex];
        const days    = opt?.dataset?.expired;
        const dateVal = bulkDate.value;
        if (sel.value && dateVal && days) {
            const txt = getExpiryText(days, dateVal);
            if (txt) {
                preview.querySelector('.bulk-expiry-date').textContent = txt;
                preview.style.display = 'block';
                return;
            }
        }
        preview.style.display = 'none';
    }

    function updateSummary() {
        const count    = bulkItems.querySelectorAll('.bulk-item-row').length;
        const summary  = document.getElementById('bulkSummary');
        const sumText  = document.getElementById('bulkSummaryText');
        const btnLabel = document.getElementById('bulkCountLabel');
        btnLabel.textContent = count;
        if (count > 1) {
            sumText.textContent = `${count} batch produksi akan di-generate sekaligus dengan nomor berurutan.`;
            summary.style.display = 'block';
        } else {
            summary.style.display = 'none';
        }
    }

    function renumberRows() {
        bulkItems.querySelectorAll('.bulk-item-row').forEach((row, i) => {
            row.querySelector('.item-number').textContent  = i + 1;
            row.querySelector('.bulk-product-select').name = `items[${i}][product_id]`;
            row.querySelector('input[type="number"]').name = `items[${i}][qty]`;
        });
    }

    function addRow() {
        const idx = bulkIndex++;
        const div = document.createElement('div');
        div.className     = 'bulk-item-row mb-3 p-3 rounded-3 border position-relative';
        div.dataset.index = idx;
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-primary item-label" style="font-size:.88rem;">
                    <i class="fas fa-box me-1"></i>Produksi #<span class="item-number">${bulkItems.querySelectorAll('.bulk-item-row').length + 1}</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="padding:2px 8px;font-size:.75rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Produk</label>
                    <select name="items[${idx}][product_id]" class="form-select form-select-sm bulk-product-select" required>
                        <option value="">-- Pilih Produk --</option>
                        ${products.map(p =>
                            `<option value="${p.id}" data-expired="${p.expired}" data-nama="${p.nama}">${p.nama} (${p.kode})</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Qty (pcs)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" name="items[${idx}][qty]"
                               class="form-control" min="1" placeholder="Qty" required>
                        <span class="input-group-text text-muted">pcs</span>
                    </div>
                </div>
            </div>
            <div class="bulk-expiry-preview mt-2" style="display:none; font-size:.82rem;">
                <i class="fas fa-calendar-check text-success me-1"></i>
                Estimasi kedaluwarsa: <strong class="text-success bulk-expiry-date"></strong>
            </div>`;

        bulkItems.appendChild(div);
        div.querySelector('.bulk-product-select').addEventListener('change', () => updateRowExpiry(div));
        div.querySelector('.btn-remove-row').addEventListener('click', () => {
            div.remove();
            renumberRows();
            updateSummary();
        });
        updateSummary();
    }

    // Submit bulk form → loading SweetAlert
    document.getElementById('bulkForm').addEventListener('submit', function (e) {
        // Validasi manual sebelum submit
        const tanggal = bulkDate.value;
        if (!tanggal) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal belum diisi!',
                text: 'Pilih tanggal produksi terlebih dahulu.',
                confirmButtonColor: '#00a8cc',
            });
            return;
        }

        const selects = bulkItems.querySelectorAll('.bulk-product-select');
        let adaKosong = false;
        selects.forEach(s => { if (!s.value) adaKosong = true; });
        if (adaKosong) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Produk belum dipilih!',
                text: 'Pastikan semua baris sudah memilih produk.',
                confirmButtonColor: '#00a8cc',
            });
            return;
        }

        // Kalau valid, tampilkan loading
        const jumlah = bulkItems.querySelectorAll('.bulk-item-row').length;
        Swal.fire({
            title: 'Generating QR Code...',
            html: `Sedang membuat <b>${jumlah} batch</b> produksi.<br>
                   <small class="text-muted">Mohon jangan tutup halaman ini.</small>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
        });
    });

    document.getElementById('addBulkRow').addEventListener('click', () => addRow());

    document.querySelector('#bulkItems .bulk-product-select')
        .addEventListener('change', function () {
            updateRowExpiry(this.closest('.bulk-item-row'));
        });

    bulkDate.addEventListener('change', function () {
        bulkItems.querySelectorAll('.bulk-item-row').forEach(row => updateRowExpiry(row));
    });

    document.getElementById('modalBulkProduksi').addEventListener('hidden.bs.modal', function () {
        const rows = bulkItems.querySelectorAll('.bulk-item-row');
        rows.forEach((r, i) => { if (i > 0) r.remove(); });
        bulkItems.querySelector('.bulk-product-select').value = '';
        bulkItems.querySelector('input[type="number"]').value = '';
        bulkItems.querySelector('.bulk-expiry-preview').style.display = 'none';
        document.getElementById('bulk_production_date').value = '';
        updateSummary();
    });
})();
</script>

@endsection