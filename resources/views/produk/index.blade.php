@extends('layouts.app')
@section('title', 'Data Produk')
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
    .search-box {
        position: relative;
        min-width: 220px;
    }
    .search-box i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: .85rem;
    }
    .search-box input {
        padding-left: 30px;
        font-size: .85rem;
        border-radius: 8px;
        border: 1px solid #e0eef3;
    }
    .search-box input:focus {
        border-color: #00a8cc;
        box-shadow: 0 0 0 3px rgba(0,168,204,.1);
    }

    /* Pagination */
    .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        border: 1px solid #e0eef3;
        color: #00a8cc;
        font-size: .82rem;
        padding: 5px 11px;
    }
    .pagination .page-item.active .page-link {
        background-color: #00a8cc;
        border-color: #00a8cc;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link { color: #ccc; }
</style>


{{-- ─── Header ──────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Data Produk</h2>
    <button class="btn btn-info text-white btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus me-2"></i> Tambah Produk
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
            <i class="fas fa-box me-2 text-info"></i>Daftar Produk
        </h5>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control form-control-sm"
                   placeholder="Cari kode, nama, ukuran…">
        </div>
    </div>

    <div class="p-3">
        {{-- Info total data --}}
        <div class="mb-2">
            <small id="tableInfo" class="text-muted"></small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-2">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px;">No</th>
                        <th>Kode</th>
                        <th style="width:70px;">Foto</th>
                        <th>Nama Produk</th>
                        <th>Ukuran</th>
                        <th>Est. Expired</th>
                        <th style="width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">

                    @forelse($products as $i => $p)
                    <tr class="prod-row"
                        data-search="{{ strtolower($p->kode_produk . ' ' . $p->nama_produk . ' ' . $p->ukuran) }}">

                        <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->kode_produk }}</span>
                        </td>
                        <td>
                            <img src="{{ $p->foto_produk ? asset('storage/'.$p->foto_produk) : asset('images/no-image.png') }}"
                                 width="50" height="50"
                                 class="rounded object-fit-cover border"
                                 alt="{{ $p->nama_produk }}">
                        </td>
                        <td class="fw-semibold">{{ $p->nama_produk }}</td>
                        <td>{{ $p->ukuran }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->estimasi_expired }} Hari</span>
                        </td>
                        <td>
                            {{-- Tombol Edit --}}
                            <button class="btn btn-sm btn-warning text-white btn-edit me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama_produk }}"
                                    data-ukuran="{{ $p->ukuran }}"
                                    data-expired="{{ $p->estimasi_expired }}"
                                    data-foto="{{ $p->foto_produk }}"
                                    data-label="{{ $p->foto_label }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Tombol Hapus + Hidden Form --}}
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="confirmDelete({{ $p->id }}, '{{ $p->nama_produk }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="deleteForm{{ $p->id }}"
                                  action="{{ route('produk.destroy', $p->id) }}"
                                  method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-box-open fs-1 d-block mb-3 text-secondary"></i>
                            Belum ada data produk.
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambah"
                               class="text-decoration-none">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse

                </tbody>

                {{-- Empty state saat search tidak ketemu --}}
                <tbody id="emptySearch" style="display:none;">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-search d-block mb-2 fs-3 text-secondary"></i>
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Bootstrap Pagination --}}
        <div class="d-flex justify-content-end mt-2" id="paginationWrapper" style="display:none!important;">
            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
        </div>
    </div>
</div>


{{-- ─── Modal Tambah Produk ─────────────────────────────────────── --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('produk.store') }}" method="POST"
              enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2 text-info"></i>Tambah Produk Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Produk</label>
                    <input type="text" class="form-control bg-light" value="{{ $nextKode }}" readonly>
                    <small class="text-muted">Kode di-generate otomatis</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control"
                           placeholder="Contoh: Strawberry Milk" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Ukuran</label>
                        <input type="text" name="ukuran" class="form-control" placeholder="250ml" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Est. Expired (Hari)</label>
                        <input type="number" name="estimasi_expired" class="form-control"
                               placeholder="14" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Produk <span class="text-muted fw-normal">(rasio 1:1)</span></label>
                    <input type="file" name="foto_produk" class="form-control" accept="image/*">
                </div>

                <div class="mb-1">
                    <label class="form-label fw-semibold">Desain Label <span class="text-muted fw-normal">(6×4 inci)</span></label>
                    <input type="file" name="foto_label" class="form-control" accept="image/*">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info text-white">
                    <i class="fas fa-save me-1"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ─── Modal Edit Produk ───────────────────────────────────────── --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2 text-warning"></i>Edit Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Ukuran</label>
                        <input type="text" name="ukuran" id="edit_ukuran" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Est. Expired (Hari)</label>
                        <input type="number" name="estimasi_expired" id="edit_expired"
                               class="form-control" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Update Foto Produk</label>
                    <input type="file" name="foto_produk" class="form-control" accept="image/*">
                    <small id="info_foto_produk" class="d-block mt-1"></small>
                </div>

                <div class="mb-1">
                    <label class="form-label fw-semibold">Update Desain Label</label>
                    <input type="file" name="foto_label" class="form-control" accept="image/*">
                    <small id="info_foto_label" class="d-block mt-1"></small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-white">
                    <i class="fas fa-save me-1"></i> Update Produk
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ─── Scripts ─────────────────────────────────────────────────── --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* ================================================================
   PAGINATION — Bootstrap murni, tanpa DataTables / jQuery
   ================================================================ */
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
        total > 0 ? `Menampilkan ${start}–${end} dari ${total} produk` : '';
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
        const a  = document.createElement('a');
        a.className = 'page-link';
        a.href      = '#';
        a.innerHTML = label;
        if (!disabled) {
            a.addEventListener('click', e => { e.preventDefault(); changePage(page); });
        }
        li.appendChild(a);
        ul.appendChild(li);
    };

    addItem('‹', currentPage - 1, currentPage === 1);

    const pageSet = new Set([1, totalPages]);
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        pageSet.add(i);
    }
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


/* ================================================================
   MODAL EDIT — Isi form otomatis dari data-* tombol edit
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const foto  = this.dataset.foto;
            const label = this.dataset.label;

            document.getElementById('formEdit').action    = `/produk/${this.dataset.id}`;
            document.getElementById('edit_nama').value    = this.dataset.nama;
            document.getElementById('edit_ukuran').value  = this.dataset.ukuran;
            document.getElementById('edit_expired').value = this.dataset.expired;

            const infoFoto  = document.getElementById('info_foto_produk');
            const infoLabel = document.getElementById('info_foto_label');

            infoFoto.className  = foto  ? 'text-success small d-block mt-1' : 'text-danger small d-block mt-1';
            infoFoto.innerHTML  = foto
                ? `<i class="fas fa-check-circle"></i> File saat ini: <strong>${foto}</strong>`
                : `<i class="fas fa-times-circle"></i> Belum ada foto produk`;

            infoLabel.className = label ? 'text-success small d-block mt-1' : 'text-danger small d-block mt-1';
            infoLabel.innerHTML = label
                ? `<i class="fas fa-check-circle"></i> File saat ini: <strong>${label}</strong>`
                : `<i class="fas fa-times-circle"></i> Belum ada desain label`;
        });
    });
});


/* ================================================================
   SWEETALERT — Konfirmasi Hapus
   ================================================================ */
function confirmDelete(id, nama) {
    Swal.fire({
        title: 'Hapus Produk?',
        html:  `Produk <strong>${nama}</strong> akan dihapus permanen.<br>
                <small class="text-muted">Foto & label ikut terhapus dari server.</small>`,
        icon:  'warning',
        showCancelButton:   true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  '<i class="fas fa-trash me-1"></i> Ya, Hapus',
        cancelButtonText:   'Batal',
        reverseButtons:     true,
        focusCancel:        true,
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm' + id).submit();
        }
    });
}
</script>

@endsection