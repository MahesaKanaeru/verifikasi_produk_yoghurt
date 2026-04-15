@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4">Dashboard Overview</h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-info border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-info text-white rounded p-3 me-3">
                        <i class="fas fa-box fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Produk</h6>
                        <h4 class="fw-bold mb-0">12</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success text-white rounded p-3 me-3">
                        <i class="fas fa-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Produksi Aktif</h6>
                        <h4 class="fw-bold mb-0">5 Batch</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning text-white rounded p-3 me-3">
                        <i class="fas fa-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Hampir Kedaluwarsa</h6>
                        <h4 class="fw-bold mb-0">2 Produk</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 card border-0 shadow-sm p-4">
        <h4>Selamat Datang, Admin!</h4>
        <p class="text-muted">Gunakan menu di samping untuk mengelola data produk dan memantau tanggal kedaluwarsa yoghurt VTAYA.</p>
    </div>
</div>
@endsection