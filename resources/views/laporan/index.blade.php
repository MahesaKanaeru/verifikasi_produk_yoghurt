@extends('layouts.app')
@section('title', 'Laporan Produksi')
@section('content')

<style>
    .report-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef6f9;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .report-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f9fb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .filter-bar {
        background: #f7fdff;
        border: 1px solid #e0f7fc;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .code-badge {
        background: #e8eaf6;
        color: #3949ab;
        font-family: monospace;
        font-size: .8rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }
</style>

{{-- ─── Header ──────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Laporan Produksi</h2>

    {{-- Tombol PDF — kirim filter aktif ke route pdf --}}
    <a href="{{ route('laporan.pdf', array_filter(['dari' => request('dari'), 'sampai' => request('sampai')])) }}"
       target="_blank"
       class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf me-1"></i> Cetak / Ekspor PDF
    </a>
</div>

{{-- ─── Filter Tanggal ─────────────────────────────────────────── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('laporan.index') }}" class="row g-3 align-items-end">
        <div class="col-sm-4">
            <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">Dari Tanggal</label>
            <input type="date" name="dari" id="input-dari"
                   class="form-control form-control-sm"
                   value="{{ $dari ?? '' }}"
                   onchange="toggleSampai(this)">
        </div>
        <div class="col-sm-4">
            <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">Sampai Tanggal</label>
            <input type="date" name="sampai" id="input-sampai"
                   class="form-control form-control-sm"
                   value="{{ isset($sampai) && $sampai !== $dari ? $sampai : '' }}"
                   {{ empty($dari) ? 'disabled' : '' }}>
        </div>
        <div class="col-sm-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fas fa-filter me-1"></i> Tampilkan
            </button>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<script>
function toggleSampai(inputDari) {
    const sampai = document.getElementById('input-sampai');
    if (inputDari.value) {
        sampai.disabled = false;
        sampai.min = inputDari.value; // sampai tidak boleh sebelum dari
    } else {
        sampai.disabled = true;
        sampai.value = '';
    }
}
</script>

{{-- ─── Report Card ─────────────────────────────────────────────── --}}
<div class="report-card">
    <div class="report-card-header">
        <div>
            <h5 class="fw-bold mb-0" style="font-size:.95rem;">
                <i class="fas fa-chart-bar me-2 text-info"></i>
                Rekap Produksi
                @if(request('dari') || request('sampai'))
                    <span class="text-muted fw-normal" style="font-size:.82rem;">
                    @if($dari === $sampai)
                        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }})
                    @else
                        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }})
                    @endif
                </span>
                @else
                    <span class="text-muted fw-normal" style="font-size:.82rem;">(Semua Data)</span>
                @endif
            </h5>
        </div>
        <div class="text-muted" style="font-size:.82rem;">
    @if($dari === $sampai)
        Tanggal : {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
    @else
        Periode : {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
        &mdash;
        {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
    @endif
</div>
    </div>

    <div class="p-4">

        @if($grouped->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-box-open fs-1 d-block mb-3 text-secondary"></i>
                Tidak ada data produksi untuk periode ini.
            </div>
        @else

        <div class="table-responsive">
            <table class="table table-bordered align-middle" style="font-size:.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px;">No</th>
                        <th>Tanggal Produksi</th>
                        <th>Kode Produksi</th>
                        <th>Nama Produk</th>
                        <th class="text-end">Qty (pcs)</th>
                        <th class="text-end">Subtotal Hari (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNo = 1; @endphp

                    @foreach($grouped as $date => $prods)
                        @php
                            $subtotal  = $prods->sum('qty');
                            $prodCount = $prods->count();
                        @endphp

                        @foreach($prods as $loopIdx => $prod)
                        <tr>
                            <td class="text-muted text-center">{{ $rowNo++ }}</td>

                            @if($loopIdx === 0)
                            <td rowspan="{{ $prodCount }}" class="fw-semibold text-center align-middle"
                                style="background:#f7fdff; color:#0277bd;">
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                <div class="text-muted fw-normal" style="font-size:.75rem;">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
                                </div>
                            </td>
                            @endif

                            <td><span class="code-badge">{{ $prod->production_number }}</span></td>

                            <td>{{ $prod->product->nama_produk ?? 'N/A' }}</td>

                            <td class="text-end fw-semibold">{{ number_format($prod->qty) }}</td>

                            @if($loopIdx === 0)
                            <td rowspan="{{ $prodCount }}"
                                class="text-end fw-bold align-middle"
                                style="background:#e8f5e9; color:#2e7d32;">
                                {{ number_format($subtotal) }}
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>

                <tfoot>
                    <tr style="background:#00a8cc; color:#fff;">
                        <td colspan="4" class="fw-bold text-end fs-6">
                            <i class="fas fa-boxes me-1"></i> Total Seluruh Produksi
                        </td>
                        <td colspan="2" class="fw-bold text-end fs-5">
                            {{ number_format($totalQty) }} pcs
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @endif
    </div>
</div>

@endsection