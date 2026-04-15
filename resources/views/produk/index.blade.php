@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Data Produk</h2>
    <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus me-2"></i> Tambah Produk
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Ukuran</th>
                        <th>Est. Expired</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    <tr>
                        <td><span class="badge bg-light text-dark border">{{ $p->kode_produk }}</span></td>
                        <td>
                            <img src="{{ $p->foto_produk ? asset('storage/'.$p->foto_produk) : asset('images/no-image.png') }}" 
                                 width="50" height="50" class="rounded object-fit-cover" alt="Foto Produk">
                        </td>
                        <td>{{ $p->nama_produk }}</td>
                        <td>{{ $p->ukuran }}</td>
                        <td>{{ $p->estimasi_expired }} Hari</td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white btn-edit" 
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

                            <form action="{{ route('produk.destroy', $p->id) }}" method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Produk (Otomatis)</label>
                    <input type="text" class="form-control bg-light" value="{{ $nextKode }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Strawberry Milk" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ukuran</label>
                        <input type="text" name="ukuran" class="form-control" placeholder="250ml" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Est. Expired (Hari)</label>
                        <input type="number" name="estimasi_expired" class="form-control" placeholder="14" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Foto Produk (1:1)</label>
                    <input type="file" name="foto_produk" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Desain Label (6x4 Inci)</label>
                    <input type="file" name="foto_label" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info text-white">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"> 
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ukuran</label>
                        <input type="text" name="ukuran" id="edit_ukuran" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Est. Expired (Hari)</label>
                        <input type="number" name="estimasi_expired" id="edit_expired" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Update Foto Produk </label>
                    <input type="file" name="foto_produk" class="form-control" accept="image/*">
                    <small id="info_foto_produk" class="d-block mt-1"></small> 
                </div>

                <div class="mb-3">
                    <label class="form-label">Update Desain Label </label>
                    <input type="file" name="foto_label" class="form-control" accept="image/*">
                    <small id="info_foto_label" class="d-block mt-1"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning text-white">Update Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Edit
        const btnEdits = document.querySelectorAll('.btn-edit');
        const formEdit = document.getElementById('formEdit');
        const editNama = document.getElementById('edit_nama');
        const editUkuran = document.getElementById('edit_ukuran');
        const editExpired = document.getElementById('edit_expired');
        const infoFoto = document.getElementById('info_foto_produk');
        const infoLabel = document.getElementById('info_foto_label');

        btnEdits.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const ukuran = this.getAttribute('data-ukuran');
                const expired = this.getAttribute('data-expired');
                const foto = this.getAttribute('data-foto');
                const label = this.getAttribute('data-label');

                formEdit.action = `/produk/${id}`;
                editNama.value = nama;
                editUkuran.value = ukuran;
                editExpired.value = expired;

                if (foto) {
                    infoFoto.innerHTML = `<i class="fas fa-check-circle"></i> File saat ini: <strong>${foto}</strong>`;
                    infoFoto.className = "text-success d-block mt-1 small";
                } else {
                    infoFoto.innerHTML = `<i class="fas fa-times-circle"></i> Belum ada foto produk yang diupload`;
                    infoFoto.className = "text-danger d-block mt-1 small";
                }

                if (label) {
                    infoLabel.innerHTML = `<i class="fas fa-check-circle"></i> File saat ini: <strong>${label}</strong>`;
                    infoLabel.className = "text-success d-block mt-1 small";
                } else {
                    infoLabel.innerHTML = `<i class="fas fa-times-circle"></i> Belum ada desain label yang diupload`;
                    infoLabel.className = "text-danger d-block mt-1 small";
                }
            });
        });

        // Alert
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Mencegah form langsung submit
                
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data produk ini tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); 
                    }
                });
            });
        });
    });
</script>
@endsection