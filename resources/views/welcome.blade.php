<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VTAYA YOGHURT - Verifikasi Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; color: #444; }

        /* NAVBAR */
        .navbar { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 700; color: #00a8cc !important; }

        /* HERO */
        .hero-section {
            background: linear-gradient(135deg, #ffffff 0%, #e0f7fa 50%, #b2ebf2 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -50px; left: 0;
            width: 100%; height: 150px;
            background: #fff;
            border-radius: 50% 50% 0 0;
        }
        .btn-scan {
            background-color: #00a8cc;
            color: white;
            padding: 12px 32px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }
        .btn-scan:hover { background-color: #007da0; transform: translateY(-3px); color: white; }

        /* STORE BANNER */
        .store-banner { position: relative; height: 320px; overflow: hidden; }
        .store-banner img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .store-banner-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to right, rgba(0,30,40,0.65) 0%, rgba(0,168,204,0.35) 100%);
            display: flex; align-items: center;
        }
        .store-stats span {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            border-radius: 20px;
            padding: 5px 16px;
            font-size: 0.8rem; font-weight: 600; margin: 4px;
            backdrop-filter: blur(4px);
        }

        /* PRODUK */
        .product-card { border: none; border-radius: 12px; background: #f8fdff; transition: 0.3s; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 8px 18px rgba(0,0,0,0.07); }
        .product-card .card-title { font-size: 0.85rem !important; }

        /* FOOTER */
        footer { background-color: #007da0; color: rgba(255,255,255,0.88); }
        footer h6 { color: #fff; font-weight: 700; letter-spacing: 0.5px; }
        footer a { color: rgba(255,255,255,0.82); text-decoration: none; transition: 0.2s; }
        footer a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem; padding: 14px 0;
        }

        /* MODAL */
        .qr-raw {
            font-family: monospace; font-size: 0.72rem;
            background: #f5f5f5; padding: 8px 10px;
            border-radius: 6px; color: #666;
            word-break: break-all; line-height: 1.6;
        }
        .result-row p.label { font-size: 0.75rem; color: #888; margin-bottom: 2px; }
        .result-row p.value { font-weight: 600; color: #333; margin-bottom: 0; font-size: 0.9rem; }

        /* Loading spinner saat auto-verify dari deep link */
        .verifying-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,168,204,0.08);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px;
        }
        .verifying-overlay p { color: #00a8cc; font-weight: 600; font-size: .95rem; }

        /* WATERMARK NIB — tipis + blur */
        .watermark-nib {
            position: absolute; inset: 0;
            pointer-events: none;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            z-index: 999;
            filter: blur(1.2px);
        }
        .watermark-nib-text {
            transform: rotate(-30deg);
            font-size: 2.2rem;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.10);
            white-space: nowrap;
            line-height: 3.5;
            text-align: center;
            width: 250%;
            user-select: none;
            -webkit-user-select: none;
        }

        /* WATERMARK HALAL — tipis + blur */
        .watermark-halal {
            position: absolute; inset: 0;
            pointer-events: none;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            filter: blur(0.8px);
        }
        .watermark-halal-text {
            transform: rotate(-30deg);
            font-size: 1.5rem;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.07);
            white-space: nowrap;
            line-height: 3;
            text-align: center;
            width: 200%;
            user-select: none;
            -webkit-user-select: none;
        }
    </style>
</head>
<body>

{{-- ── Loading overlay (muncul saat auto-verify dari deep link) ───── --}}
<div id="verifyingOverlay" class="verifying-overlay" style="display:none;">
    <div class="spinner-border text-info" style="width:3rem;height:3rem;" role="status"></div>
    <p>Memverifikasi produk…</p>
</div>


{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <img src="{{ asset('storage/images/vtaya_logo_tr.png') }}" alt="VTAYA" height="64" style="object-fit: contain;">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link active" href="#">HOME</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">ABOUT</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">CONTACT</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="nav-link btn btn-outline-info px-4" href="{{ route('login') }}">LOGIN</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


{{-- HERO --}}
<section class="hero-section text-center">
    <div class="container position-relative" style="z-index:1;">
        <p class="text-uppercase text-secondary fw-semibold mb-2"
           style="letter-spacing:3px; font-size:0.78rem;">
            Selamat Datang di VTAYA
        </p>
        <h1 class="display-3 fw-bold mb-3" style="color:#00a8cc;">
            Rasa Asli.<br class="d-sm-none"> Kualitas Nyata.
        </h1>
        <p class="text-muted mb-5 mx-auto" style="max-width:460px;">
            Pastikan yoghurt yang kamu minum benar-benar asli, segar, dan bebas pengawet.
            Scan kode QR pada botol untuk verifikasi produk secara instan.
        </p>
        <button class="btn btn-scan btn-lg" onclick="startScan()">
            <i class="fas fa-barcode me-2"></i> Scan Botol
        </button>
    </div>
</section>


{{-- ABOUT --}}
<section id="about" class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7 text-center">
                <h2 class="fw-bold mb-3">Sejarah Singkat</h2>
                <p class="text-muted">
                    VTAYA Yoghurt bermula dari keinginan menyajikan minuman sehat untuk keluarga — murni,
                    tanpa bahan pengawet. Kami memproses susu segar pilihan dengan teknik fermentasi
                    tradisional untuk menjaga kualitas nutrisi dan cita rasa yang benar-benar otentik.
                </p>
            </div>
        </div>
    </div>

    {{-- STORE BANNER --}}
    <div class="store-banner">
        <img src="{{ asset('storage/images/vtaya.jpeg') }}"
             alt="Toko VTAYA Yoghurt"
             onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
        <div class="store-banner-overlay">
            <div class="container text-white">
                <h4 class="fw-bold mb-1">Toko Kami</h4>
                <p class="mb-3" style="opacity:.85; font-size:0.88rem;">
                    Jl. Cigugur Sukamulya No.82, Kuningan
                </p>
                <div class="store-stats">
                    <span><i class="fas fa-leaf me-1"></i> 100% Alami</span>
                    <span><i class="fas fa-clock me-1"></i> Fermentasi Alami</span>
                    <span><i class="fas fa-ban me-1"></i> Tanpa Pengawet</span>
                </div>
            </div>
        </div>
    </div>

    {{-- VARIAN RASA --}}
    <div class="container mt-5">
        <h3 class="text-center fw-bold mb-4">Varian Rasa</h3>
        <div class="row justify-content-center">
            @forelse($products as $p)
            <div class="col-6 col-md-3 col-lg-2 mb-3">
                <div class="card product-card p-2 text-center h-100 border-0 shadow-sm">
                    <div class="ratio ratio-1x1 mb-2">
                        <img src="{{ $p->foto_produk ? asset('storage/'.$p->foto_produk) : asset('images/no-image.png') }}"
                             class="card-img-top rounded object-fit-cover"
                             alt="{{ $p->nama_produk }}">
                    </div>
                    <div class="card-body p-0 d-flex align-items-center justify-content-center">
                        <h5 class="card-title fw-bold m-0" style="color:#333;">{{ $p->nama_produk }}</h5>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-box-open fs-1 mb-3 text-secondary d-block"></i>
                Belum ada varian rasa yang tersedia.
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- LEGALITAS & SERTIFIKASI --}}
<section id="legalitas" class="py-5" style="background:#f8fdff;">
    <div class="container">
        <h3 class="text-center fw-bold mb-2">Legalitas & Sertifikasi</h3>
        <p class="text-center text-muted mb-4" style="font-size:0.88rem;">
            VTAYA Yoghurt telah terdaftar dan tersertifikasi resmi
        </p>

        <div class="row justify-content-center g-3">

            {{-- CARD HALAL --}}
            <div class="col-10 col-md-5 col-lg-4">
                <div class="card product-card h-100 text-center p-4"
                     style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#halalModal">
                    <div class="mb-3">
                        <img src="{{ asset('storage/images/logo_halal.png') }}" alt="Logo Halal" style="height: 50px; object-fit: contain;">
                    </div>
                    <h6 class="fw-bold mb-1">Sertifikat Halal</h6>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        No. ID32110001040701122
                    </p>
                    <small class="text-secondary mt-2"><i class="fas fa-eye me-1"></i>Lihat Sertifikat</small>
                </div>
            </div>

            {{-- CARD NIB --}}
            <div class="col-10 col-md-5 col-lg-4">
                <div class="card product-card h-100 text-center p-4"
                     style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#nibModal">
                    <div class="mb-3">
                        <i class="fas fa-id-card fs-1" style="color:#00a8cc;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">NIB (Nomor Induk Berusaha)</h6>
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        No. 9120007970624
                    </p>
                    <small class="text-secondary mt-2"><i class="fas fa-eye me-1"></i>Lihat Dokumen</small>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- MODAL HALAL (gambar) --}}
<div class="modal fade" id="halalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-certificate me-2 text-info"></i>Sertifikat Halal</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0"
                 style="position:relative; user-select:none; -webkit-user-select:none;"
                 oncontextmenu="return false;"
                 ondragstart="return false;">

                {{-- Canvas render (blokir visual search & klik kanan) --}}
                <canvas id="halalCanvas"
                        style="max-width:100%; display:block; margin:0 auto; pointer-events:none; -webkit-user-drag:none;">
                </canvas>

                {{-- Overlay transparan blokir klik kanan di atas canvas --}}
                <div style="position:absolute; inset:0; z-index:10;"
                     oncontextmenu="return false;"
                     ondragstart="return false;"
                     onselectstart="return false;">
                </div>

                <p class="mt-2 mb-2 text-muted" style="font-size:0.85rem; position:relative; z-index:11;">
                    No. Sertifikat: ID32110001040701122
                </p>
            </div>
        </div>
    </div>
</div>


{{-- MODAL NIB (PDF via PDF.js canvas) --}}
<div class="modal fade" id="nibModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-id-card me-2 text-info"></i>NIB - Nomor Induk Berusaha</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetNibViewer()"></button>
            </div>
            <div class="modal-body" style="height:75vh; position:relative; padding:0; background:#525659;">

                {{-- Loading indicator --}}
                <div id="nibLoading" class="d-flex align-items-center justify-content-center"
                    style="position:absolute; inset:0; color:#fff; flex-direction:column; gap:10px; pointer-events:none;">
                    <div class="spinner-border text-light" role="status"></div>
                    <span style="font-size:.85rem;">Memuat dokumen…</span>
                </div>

                {{-- Container scroll untuk semua halaman canvas --}}
                <div id="nibPagesContainer" oncontextmenu="return false;"
                    style="height:100%; overflow-y:auto; overflow-x:hidden; padding:12px; display:none;
                            -webkit-user-select:none; user-select:none; position:relative; z-index:1;">
                </div>

                {{-- Watermark NIB — tipis + blur --}}
                <div class="watermark-nib">
                    <div class="watermark-nib-text">
                        VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT<br>
                        VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT<br>
                        VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT<br>
                        VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT<br>
                        VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT &nbsp;&nbsp;&nbsp; VTAYA YOGHURT
                    </div>
                </div>

            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="resetNibViewer()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer id="contact" class="pt-5">
    <div class="container pb-4">
        <div class="row g-4">
            <div class="col-md-5">
                <h6 class="mb-3">VTAYA YOGHURT</h6>
                <p style="font-size:0.85rem; opacity:0.8; line-height:1.7;">
                    Yoghurt segar tanpa pengawet yang diproduksi dengan teknik fermentasi alami.
                    Kami berkomitmen menghadirkan produk berkualitas untuk kesehatan keluarga Indonesia.
                </p>
            </div>
            <div class="col-md-4 offset-md-3">
                <h6 class="mb-3">HUBUNGI KAMI</h6>
                <a href="https://www.instagram.com/vtaya_yoghurt/" target="_blank"
                   class="d-flex align-items-center mb-3 gap-3">
                    <i class="fab fa-instagram fs-5"></i>
                    <span style="font-size:0.88rem;">@vtaya_yoghurt</span>
                </a>
                <a href="https://wa.me/6287724025779" target="_blank" class="d-flex align-items-center mb-3 gap-3">
                    <i class="fab fa-whatsapp fs-5"></i>
                    <span style="font-size:0.88rem;">+62 877-2402-5779</span>
                </a>
                <a href="https://maps.app.goo.gl/VT3PLbrsw2keGZSr6" target="_blank"
                   class="d-flex align-items-start gap-3">
                    <i class="fas fa-map-marker-alt fs-5 mt-1"></i>
                    <span style="font-size:0.88rem; line-height:1.5;">Jl. Cigugur Sukamulya No.82, Kuningan</span>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom text-center">
        &copy; {{ date('Y') }} VTAYA YOGHURT &mdash; All rights reserved.
    </div>
</footer>


{{-- ─── MODAL: SCANNER ──────────────────────────────────────────── --}}
<div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-barcode me-2 text-info"></i>Scan QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        onclick="stopScanner()"></button>
            </div>
            <div class="modal-body pb-1">
                <div id="qr-reader" style="width:100%;"></div>
                <p class="text-center text-muted small mt-2">
                    Arahkan kamera ke QR Code pada botol
                </p>
            </div>
        </div>
    </div>
</div>


{{-- ─── MODAL: HASIL VERIFIKASI ────────────────────────────────── --}}
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <h6 class="modal-title mb-0">Produk Terverifikasi</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center mb-3">
                    <img id="resultImage" src="" alt="Produk"
                         class="rounded shadow-sm" style="height:140px; object-fit:cover;">
                </div>
                <div class="row g-2 result-row">
                    <div class="col-12">
                        <p class="label">Kode Produksi</p>
                        <p class="value" id="resultCode"></p>
                    </div>
                    <div class="col-12">
                        <p class="label">Nama Produk</p>
                        <p class="value" id="resultProduct"></p>
                    </div>
                    <div class="col-6">
                        <p class="label">Ukuran</p>
                        <p class="value" id="resultSize"></p>
                    </div>
                    <div class="col-6">
                        <p class="label">Tgl Produksi</p>
                        <p class="value" id="resultProdDate"></p>
                    </div>
                    <div class="col-6">
                        <p class="label">Tgl Kedaluwarsa</p>
                        <p class="value" id="resultExpDate"></p>
                    </div>
                    <div class="col-6">
                        <p class="label">Status Produk</p>
                        <div id="resultStatus"></div>
                    </div>
                    <div class="col-12 mt-2">
                        <a href="javascript:void(0)" id="btnToggleEnkripsi"
                        class="text-secondary text-decoration-none small"
                        onclick="toggleEnkripsi()">
                            <i class="fas fa-eye me-1" id="iconToggle"></i>
                            <span id="labelToggle">Show result enkripsi</span>
                        </a>
                    </div>
                    <div id="enkripsiBlock" style="display:none;">
                        <hr class="my-2">
                        <p class="text-muted text-center mb-2" style="font-size:0.72rem; letter-spacing:.5px;">
                            <i class="fas fa-lock me-1"></i> Informasi Kriptografi AES
                        </p>

                        <div class="col-12 mb-2">
                            <p class="label">Enkripsi Kode Produksi</p>
                            <p class="qr-raw" id="resultCodeEncrypted"></p>
                        </div>

                        <div class="col-12">
                            <p class="label">Enkripsi Tanggal Kedaluwarsa</p>
                            <p class="qr-raw" id="resultExpiryEncrypted"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


{{-- ─── MODAL: ERROR ───────────────────────────────────────────── --}}
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-3" id="errorHeader">
                <div class="d-flex align-items-center gap-2">
                    <i id="errorIcon" class="fas"></i>
                    <h6 class="modal-title mb-0" id="errorTitle"></h6>
                </div>
                <button type="button" class="btn-close" id="errorBtnClose" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
    let scanner = null;

    /* AUTO-VERIFY — dari deep link ?scan= */
    document.addEventListener('DOMContentLoaded', () => {
        const scanParam = new URLSearchParams(window.location.search).get('scan');
        if (scanParam) {
            document.getElementById('verifyingOverlay').style.display = 'flex';
            history.replaceState({}, '', window.location.pathname);
            setTimeout(() => verifyQRCode(scanParam), 600);
        }
    });

    /* SCANNER KAMERA */
    function startScan() {
        new bootstrap.Modal(document.getElementById('scannerModal')).show();
        scanner = new Html5QrcodeScanner(
            'qr-reader',
            { fps: 10, qrbox: { width: 240, height: 240 } },
            false
        );
        scanner.render(onScanSuccess, () => {});
    }

    function stopScanner() {
        if (scanner) { scanner.clear().catch(() => {}); scanner = null; }
    }

    function onScanSuccess(decodedText) {
        stopScanner();
        bootstrap.Modal.getInstance(document.getElementById('scannerModal')).hide();
        verifyQRCode(decodedText);
    }

    /* VERIFY — kirim ke API */
    function verifyQRCode(qrData) {
        fetch('/api/verify-qr', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ qr_data: qrData }),
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('verifyingOverlay').style.display = 'none';
            data.success ? showResult(data.data) : showError(data.message, data.integrity);
        })
        .catch(() => {
            document.getElementById('verifyingOverlay').style.display = 'none';
            showError('Gagal memproses QR Code. Periksa koneksi internet.', 'ERROR');
        });
    }

    /* BADGE status kedaluwarsa */
    function statusBadge(expStr) {
        const monthMap = {
            'Jan':0,'Feb':1,'Mar':2,'Apr':3,
            'Mei':4,'May':4,
            'Jun':5,'Jul':6,
            'Agu':7,'Aug':7,
            'Sep':8,'Okt':9,'Oct':9,
            'Nov':10,'Des':11,'Dec':11
        };

        const parts = expStr.trim().split(' ');
        const day   = parseInt(parts[0]);
        const month = monthMap[parts[1]];
        const year  = parseInt(parts[2]);

        if (isNaN(day) || month === undefined || isNaN(year)) {
            return `<span class="badge bg-secondary">Format tanggal tidak valid</span>`;
        }

        const exp   = new Date(year, month, day);
        const today = new Date();
        today.setHours(0,0,0,0);
        exp.setHours(0,0,0,0);

        const diff = Math.ceil((exp - today) / 86400000);

        if (diff < 0)  return `<span class="badge bg-danger">Kedaluwarsa ${Math.abs(diff)} hari lalu</span>`;
        if (diff <= 7) return `<span class="badge bg-warning text-dark">Sisa ${diff} hari lagi</span>`;
        return `<span class="badge bg-success">Baik &bull; sisa ${diff} hari</span>`;
    }

    /* MODAL — hasil verifikasi */
    function showResult(d) {
        document.getElementById('resultImage').src            = d.product_image;
        document.getElementById('resultCode').textContent     = d.production_code;
        document.getElementById('resultProduct').textContent  = d.product_name;
        document.getElementById('resultSize').textContent     = d.product_size;
        document.getElementById('resultProdDate').textContent = d.production_date;
        document.getElementById('resultExpDate').textContent  = d.expiration_date;
        document.getElementById('resultStatus').innerHTML     = statusBadge(d.expiration_date);
        document.getElementById('resultCodeEncrypted').textContent   = d.production_code_encrypted;
        document.getElementById('resultExpiryEncrypted').textContent = d.expiry_encrypted;

        document.getElementById('enkripsiBlock').style.display = 'none';
        document.getElementById('iconToggle').className        = 'fas fa-eye me-1';
        document.getElementById('labelToggle').textContent     = 'Tampilkan Data Enkripsi';

        new bootstrap.Modal(document.getElementById('resultModal')).show();
    }

    /* TOGGLE enkripsi */
    function toggleEnkripsi() {
        const block = document.getElementById('enkripsiBlock');
        const shown = block.style.display !== 'none';

        block.style.display = shown ? 'none' : 'block';
        document.getElementById('iconToggle').className = `fas ${shown ? 'fa-eye' : 'fa-eye-slash'} me-1`;
        document.getElementById('labelToggle').textContent = shown
            ? 'Show result enkripsi'
            : 'Hide result enkripsi';
    }

    /* MODAL — error */
    function showError(message, integrity) {
        const cfg = {
            NOT_FOUND: {
                cls:   'bg-danger text-white',
                icon:  'fa-times-circle',
                title: 'Produk Tidak Ditemukan',
                closeWhite: true,
            },
            MANIPULATED: {
                cls:   'bg-warning',
                icon:  'fa-exclamation-triangle',
                title: 'Indikasi Manipulasi',
                closeWhite: false,
            },
            DECRYPT_ERROR: {
                cls:   'bg-warning',
                icon:  'fa-exclamation-circle',
                title: 'Data Tidak Dapat Dibaca',
                closeWhite: false,
            },
        };

        const c = cfg[integrity] ?? {
            cls:   'bg-secondary text-white',
            icon:  'fa-times-circle',
            title: 'Verifikasi Gagal',
            closeWhite: true,
        };

        document.getElementById('errorHeader').className    = `modal-header py-3 ${c.cls}`;
        document.getElementById('errorIcon').className      = `fas ${c.icon} me-2`;
        document.getElementById('errorTitle').textContent   = c.title;
        document.getElementById('errorBtnClose').className  = `btn-close${c.closeWhite ? ' btn-close-white' : ''}`;
        document.getElementById('errorMessage').textContent = message;

        new bootstrap.Modal(document.getElementById('errorModal')).show();
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

    let nibLoaded = false;

    document.getElementById('nibModal').addEventListener('shown.bs.modal', function () {
        if (nibLoaded) return;
        nibLoaded = true;

        const url       = "{{ asset('storage/images/nib_sri.pdf') }}";
        const container = document.getElementById('nibPagesContainer');
        const loading   = document.getElementById('nibLoading');

        pdfjsLib.getDocument(url).promise.then(function (pdf) {
            const renderPage = (pageNum) => {
                pdf.getPage(pageNum).then(function (page) {
                    const scale    = 1.5;
                    const viewport = page.getViewport({ scale });

                    const canvas = document.createElement('canvas');
                    canvas.width  = viewport.width;
                    canvas.height = viewport.height;
                    canvas.style.display   = 'block';
                    canvas.style.margin    = '0 auto 12px auto';
                    canvas.style.maxWidth  = '100%';
                    canvas.style.boxShadow = '0 2px 8px rgba(0,0,0,.3)';
                    canvas.oncontextmenu   = () => false;

                    const ctx = canvas.getContext('2d');
                    page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                        container.appendChild(canvas);

                        if (pageNum === 1) {
                            loading.style.display   = 'none';
                            container.style.display = 'block';
                        }

                        if (pageNum < pdf.numPages) {
                            renderPage(pageNum + 1);
                        }
                    });
                });
            };
            renderPage(1);
        }).catch(function (err) {
            loading.innerHTML = '<span style="font-size:.85rem;">Gagal memuat dokumen.</span>';
            console.error(err);
        });
    });

    function resetNibViewer() {
        // tidak perlu reset, biar canvas tetap ter-cache
    }
    let halalCanvasRendered = false;

    document.getElementById('halalModal').addEventListener('shown.bs.modal', function () {
        if (halalCanvasRendered) return;
        halalCanvasRendered = true;

        const canvas = document.getElementById('halalCanvas');
        const ctx    = canvas.getContext('2d');

        const img = new Image();
        // Crossorigin penting jika storage di domain berbeda
        img.crossOrigin = 'anonymous';
        img.src = "{{ asset('storage/images/halal_certificate.png') }}";

        img.onload = function () {
            // Sesuaikan ukuran canvas dengan gambar asli
            canvas.width  = img.naturalWidth;
            canvas.height = img.naturalHeight;
            ctx.drawImage(img, 0, 0);

            // Tambahkan watermark teks di atas canvas (lapisan kedua perlindungan)
            ctx.save();
            ctx.globalAlpha = 0.08;
            ctx.font = 'bold 60px Poppins, sans-serif';
            ctx.fillStyle = '#000000';
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(-Math.PI / 6); // -30 derajat
            for (let y = -canvas.height; y < canvas.height; y += 180) {
                for (let x = -canvas.width; x < canvas.width; x += 400) {
                    ctx.fillText('VTAYA YOGHURT', x, y);
                }
            }
            ctx.restore();
        };

        img.onerror = function () {
            ctx.fillStyle = '#eee';
            ctx.fillRect(0, 0, 400, 200);
            ctx.fillStyle = '#999';
            ctx.font = '16px sans-serif';
            ctx.fillText('Gagal memuat sertifikat.', 20, 100);
        };
    });
</script>
</body>
</html>