<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Produksi - VTAYA Yoghurt Kuningan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 28px 36px;
        }

        /* ── Header ─────────────────────────────────────── */
        .brand {
    font-size: 17px;
    font-weight: 700;
    letter-spacing: 1.2px;
    color: #00789e;
    text-transform: uppercase;
    text-align: center;
}
.address {
    font-size: 9px;
    color: #555;
    margin-top: 3px;
    line-height: 1.5;
    text-align: center;
}
.wa {
    font-size: 9.5px;
    color: #333;
    margin-top: 4px;
    text-align: center;
}
.wa span {
    font-weight: 700;
    color: #1a7a40;
}

        /* ── Divider tipis bawah header ─────────────────── */
        .divider-thin {
            border: none;
            border-top: 1px solid #b0d8e8;
            margin: 4px 0 14px;
        }

        /* ── Meta cetak ──────────────────────────────────── */
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            background: #f4fafd;
            border: 1px solid #cce8f4;
        }
        .meta-table td {
            font-size: 9.5px;
            color: #444;
            padding: 6px 10px;
        }
        .meta-table td.right {
            text-align: right;
        }
        .meta-table strong {
            color: #00789e;
        }

        /* ── Judul Dokumen ───────────────────────────────── */
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #00789e;
            letter-spacing: .8px;
            margin-bottom: 16px;
            text-transform: uppercase;
            border-bottom: 1px dashed #b0d8e8;
            padding-bottom: 10px;
        }

        /* ── Tabel ───────────────────────────────────────── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data-table thead tr {
            background-color: #00789e;
            color: #ffffff;
        }
        table.data-table thead th {
            padding: 8px 7px;
            text-align: left;
            font-weight: 700;
            font-size: 9.5px;
            letter-spacing: .3px;
            border: 1px solid #00658a;
        }
        table.data-table thead th.center { text-align: center; }
        table.data-table thead th.right  { text-align: right;  }

        table.data-table tbody tr:nth-child(odd)  { background: #f4fafd; }
        table.data-table tbody tr:nth-child(even) { background: #ffffff; }

        table.data-table tbody td {
            padding: 6px 7px;
            border: 1px solid #d4eaf3;
            color: #1a1a1a;
            vertical-align: middle;
        }
        table.data-table tbody td.center { text-align: center; }
        table.data-table tbody td.right  { text-align: right;  }
        table.data-table tbody td.mono {
            font-family: 'Courier New', monospace;
            font-size: 9.5px;
            color: #3949ab;
            font-weight: 700;
        }
        table.data-table tbody td.no {
            text-align: center;
            color: #888;
            font-size: 9px;
        }

        /* ── Footer tabel (total) ────────────────────────── */
        table.data-table tfoot tr {
            background-color: #005f7a;
            color: #fff;
        }
        table.data-table tfoot td {
            padding: 8px 7px;
            border: 1px solid #004f68;
            font-weight: 700;
        }
        table.data-table tfoot td.total-label {
            text-align: right;
            font-size: 10px;
            letter-spacing: .3px;
        }
        table.data-table tfoot td.total-value {
            text-align: right;
            font-size: 12px;
        }

        /* ── Footer halaman ──────────────────────────────── */
        .page-footer {
            margin-top: 18px;
            font-size: 8.5px;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ─────────────────────────────────────────────── --}}
        @php
            $logoPath = storage_path('app/public/images/vtaya_logo.png');
            $logoBase64 = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoBase64;
        @endphp

        <div style="text-align: center; padding-bottom: 12px; border-bottom: 3px solid #00789e; margin-bottom: 4px;">
            <img src="{{ $logoSrc }}" alt="Logo VTAYA" height="70" style="object-fit: contain; margin-bottom: 6px;">
            <div class="brand">VTAYA YOGHURT KUNINGAN</div>
            <div class="address">
                Jl. Cigugur Sukamulya ling, RT.35/RW.12, Wage, Kec. Kuningan,<br>
                Kabupaten Kuningan, Jawa Barat 45552
            </div>
            <div class="wa">
                WhatsApp : <span>+62 877-2402-5779</span>
            </div>
        </div>
    {{-- ── META CETAK ──────────────────────────────────────────── --}}
    <table class="meta-table">
        <tr>
            <td>
                <strong>Dicetak oleh</strong> &nbsp;:&nbsp;
                {{ auth()->user()->name ?? 'Sistem' }}
            </td>
            <td class="right">
                <strong>Tanggal Cetak</strong> &nbsp;:&nbsp;
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
            </td>
        </tr>
    </table>

    {{-- ── JUDUL DOKUMEN ───────────────────────────────────────── --}}
    <div class="doc-title">Laporan Produksi</div>

    {{-- ── TABEL DATA ──────────────────────────────────────────── --}}
    @if($productions->isEmpty())
        <p style="text-align:center; color:#999; padding:30px 0;">
            Tidak ada data produksi untuk periode ini.
        </p>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th class="center" style="width:26px;">No</th>
                <th style="width:105px;">Kode Produksi</th>
                <th>Nama Produk</th>
                <th class="center" style="width:70px;">Ukuran</th>
                <th class="center" style="width:85px;">Tgl Produksi</th>
                <th class="center" style="width:85px;">Tgl Kedaluwarsa</th>
                <th class="right"  style="width:65px;">Qty (pcs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productions as $idx => $prod)
            <tr>
                <td class="no">{{ $idx + 1 }}</td>
                <td class="mono">{{ $prod->production_code }}</td>
                <td>{{ $prod->product->nama_produk ?? 'N/A' }}</td>
                <td class="center">{{ $prod->product->ukuran ?? '-' }}</td>
                <td class="center">
                    {{ \Carbon\Carbon::parse($prod->production_date)->format('d M Y') }}
                </td>
                <td class="center">{{ $prod->plain_expiry ?? '-' }}</td>
                <td class="right">{{ number_format($prod->qty) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="total-label">&#9654; Total Keseluruhan Produksi</td>
                <td class="total-value right">{{ number_format($totalQty) }} pcs</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ── FOOTER HALAMAN ──────────────────────────────────────── --}}
    <div class="page-footer">
        Dokumen ini dicetak secara otomatis oleh sistem &mdash;
        VTAYA Yoghurt Kuningan &copy; {{ date('Y') }}
    </div>

</body>
</html>