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
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    .filter-bar .form-select,
    .filter-bar .form-control {
        border-color: #c9eaf5;
        font-size: .85rem;
    }
    .filter-bar .form-select:focus,
    .filter-bar .form-control:focus {
        border-color: #00a8cc;
        box-shadow: 0 0 0 3px rgba(0,168,204,.1);
    }
    .filter-label {
        font-size: .8rem;
        font-weight: 600;
        color: #555;
        margin-bottom: 4px;
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
    <a href="{{ route('laporan.pdf', array_filter(['dari' => request('dari') ?? $dari ?? '', 'sampai' => request('sampai') ?? $sampai ?? ''])) }}"
       target="_blank"
       class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf me-1"></i> Cetak / Ekspor PDF
    </a>
</div>

{{-- ─── Filter ──────────────────────────────────────────────────── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('laporan.index') }}" id="formFilter">

        <div class="row g-3 align-items-end">

            {{-- Pilih Tahun --}}
            <div class="col-auto">
                <div class="filter-label">
                    <i class="fas fa-calendar-alt me-1 text-info"></i> Tahun
                </div>
                <select id="pilihTahun" class="form-select form-select-sm"
                        style="min-width:115px;"
                        onchange="tampilkanFilter(this.value)">
                    <option value="">-- Tahun --</option>
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}"
                            {{ (isset($dari) && \Carbon\Carbon::parse($dari)->year == $y) ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Dari & Sampai — muncul setelah pilih tahun --}}
            <div class="col" id="wrapperTanggal" style="{{ isset($dari) ? '' : 'display:none;' }}">
                <div class="row g-2 align-items-end">

                    <div class="col-sm-4">
                        <div class="filter-label">Dari Tanggal</div>
                        <input type="date" name="dari" id="input-dari"
                               class="form-control form-control-sm"
                               value="{{ $dari ?? '' }}"
                               onchange="aturSampai(this)">
                    </div>

                    <div class="col-sm-4">
                        <div class="filter-label">
                            Sampai Tanggal
                            <span class="text-muted fw-normal" style="font-size:.72rem;">(opsional)</span>
                        </div>
                        <input type="date" name="sampai" id="input-sampai"
                               class="form-control form-control-sm"
                               value="{{ isset($sampai) && $sampai !== $dari ? $sampai : '' }}"
                               {{ empty($dari) ? 'disabled' : '' }}>
                    </div>

                    <div class="col-sm-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter me-1"></i> Tampilkan
                        </button>
                        <a href="{{ route('laporan.index') }}"
                           class="btn btn-outline-secondary btn-sm"
                           title="Reset filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>

                </div>
            </div>

        </div>

        {{-- Hint sebelum pilih tahun --}}
        <div id="hintTahun" class="mt-2" style="{{ isset($dari) ? 'display:none;' : '' }}">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Pilih tahun terlebih dahulu untuk menampilkan filter tanggal.
            </small>
        </div>

    </form>
</div>

{{-- ─── Report Card ─────────────────────────────────────────────── --}}
<div class="report-card">
    <div class="report-card-header">
        <div>
            <h5 class="fw-bold mb-0" style="font-size:.95rem;">
                <i class="fas fa-chart-bar me-2 text-info"></i>
                Rekap Produksi
                <span class="text-muted fw-normal" style="font-size:.82rem;">
                    @if($dari === $sampai)
                        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }})
                    @else
                        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }})
                    @endif
                </span>
            </h5>
        </div>
        <div class="text-muted" style="font-size:.82rem;">
            @if($dari === $sampai)
                Tanggal : {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
            @else
                Periode :
                {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}
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
                        <th>No. Produksi</th> 
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
                            <td rowspan="{{ $prodCount }}"
                                class="fw-semibold text-center align-middle"
                                style="background:#f7fdff; color:#0277bd;">
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                <div class="text-muted fw-normal" style="font-size:.75rem;">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}
                                </div>
                            </td>
                            @endif

                            <td><span class="code-badge">{{ $prod->production_code }}</span></td>

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

{{-- ─── Script Filter ───────────────────────────────────────────── --}}
<script>
function tampilkanFilter(tahun) {
    const wrapper     = document.getElementById('wrapperTanggal');
    const hint        = document.getElementById('hintTahun');
    const inputDari   = document.getElementById('input-dari');
    const inputSampai = document.getElementById('input-sampai');

    if (!tahun) {
        wrapper.style.display = 'none';
        hint.style.display    = '';
        return;
    }

    // Batasi range sesuai tahun
    inputDari.min   = tahun + '-01-01';
    inputDari.max   = tahun + '-12-31';
    inputSampai.max = tahun + '-12-31';

    // Reset nilai saat ganti tahun
    inputDari.value      = '';
    inputSampai.value    = '';
    inputSampai.disabled = true;

    wrapper.style.display = '';
    hint.style.display    = 'none';
}

function aturSampai(inputDari) {
    const sampai = document.getElementById('input-sampai');
    const tahun  = document.getElementById('pilihTahun').value;

    if (inputDari.value) {
        sampai.disabled = false;
        sampai.min      = inputDari.value;
        sampai.max      = tahun + '-12-31';
        // Reset sampai jika nilainya lebih kecil dari dari
        if (sampai.value && sampai.value < inputDari.value) {
            sampai.value = '';
        }
    } else {
        sampai.disabled = true;
        sampai.value    = '';
    }
}

// Inisialisasi saat load halaman 
document.addEventListener('DOMContentLoaded', () => {
    const dari   = document.getElementById('input-dari');
    const sampai = document.getElementById('input-sampai');
    const tahun  = document.getElementById('pilihTahun');

    if (tahun.value && dari.value) {
        sampai.disabled = false;
        sampai.min      = dari.value;
        sampai.max      = tahun.value + '-12-31';
        dari.min        = tahun.value + '-01-01';
        dari.max        = tahun.value + '-12-31';
    }
});
</script>

@endsection