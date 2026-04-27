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
        <a class="navbar-brand" href="#">VTAYA</a>
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
                <a href="#" class="d-flex align-items-center mb-3 gap-3">
                    <i class="fab fa-whatsapp fs-5"></i>
                    <span style="font-size:0.88rem;">+62 812-xxxx-xxxx</span>
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
                    <div class="col-12">
                        <p class="label">Isi QR (Enkripsi)</p>
                        <p class="qr-raw" id="resultRawQR"></p>
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

<script>
    let scanner    = null;
    let rawQRData  = '';

    /* ================================================================
       AUTO-VERIFY — Cek ?scan= saat halaman dimuat
       Dipanggil saat konsumen klik link dari QR (deep link flow):
         /v/ENCRYPTED → redirect → /?scan=ENCRYPTED → halaman ini
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        const params    = new URLSearchParams(window.location.search);
        const scanParam = params.get('scan');

        if (scanParam) {
            // Tampilkan spinner sebentar agar terasa responsif
            document.getElementById('verifyingOverlay').style.display = 'flex';

            // Bersihkan URL dari query string (biar URL bar rapi)
            history.replaceState({}, '', window.location.pathname);

            // Tunggu Bootstrap siap, lalu verifikasi
            setTimeout(() => {
                rawQRData = scanParam;
                verifyQRCode(scanParam);
            }, 600);
        }
    });

    /* ================================================================
       SCANNER KAMERA (scan dari halaman web)
    ================================================================ */
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
        rawQRData = decodedText;
        stopScanner();
        bootstrap.Modal.getInstance(document.getElementById('scannerModal')).hide();
        verifyQRCode(decodedText);
    }

    /* ================================================================
       VERIFY — Kirim ke API
       Menerima:
         - enkripsi mentah (QR lama)
         - deep link penuh (QR baru discan dari kamera web)
         - query param ?scan= (deep link flow)
       Semua ditangani di ScanController::verifyQr()
    ================================================================ */
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

    /* ================================================================
       HELPER — Badge status kedaluwarsa
    ================================================================ */
    function statusBadge(expStr) {
    // Support format: "25 Apr 2025" atau "25 Mei 2025" (Indonesia)
    const monthMap = {
        'Jan':0,'Feb':1,'Mar':2,'Apr':3,
        'Mei':4,'May':4,           // ← handle dua-duanya
        'Jun':5,'Jul':6,
        'Agu':7,'Aug':7,           // ← handle dua-duanya
        'Sep':8,'Okt':9,'Oct':9,   // ← handle dua-duanya
        'Nov':10,'Des':11,'Dec':11 // ← handle dua-duanya
    };

    const parts = expStr.trim().split(' ');
    const day   = parseInt(parts[0]);
    const month = monthMap[parts[1]];
    const year  = parseInt(parts[2]);

    // Jika parse gagal, jangan tampilkan badge salah
    if (isNaN(day) || month === undefined || isNaN(year)) {
        return `<span class="badge bg-secondary">Format tanggal tidak valid</span>`;
    }

    const exp   = new Date(year, month, day);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    exp.setHours(0, 0, 0, 0);

    const diff = Math.ceil((exp - today) / 86400000);

    if (diff < 0)  return `<span class="badge bg-danger">Kedaluwarsa ${Math.abs(diff)} hari lalu</span>`;
    if (diff <= 7) return `<span class="badge bg-warning text-dark">Sisa ${diff} hari lagi</span>`;
    return `<span class="badge bg-success">Baik &bull; sisa ${diff} hari</span>`;
}

    /* ================================================================
       MODAL — Tampilkan hasil
    ================================================================ */
    function showResult(d) {
        document.getElementById('resultImage').src            = d.product_image;
        document.getElementById('resultCode').textContent     = d.production_code;
        document.getElementById('resultProduct').textContent  = d.product_name;
        document.getElementById('resultSize').textContent     = d.product_size;
        document.getElementById('resultProdDate').textContent = d.production_date;
        document.getElementById('resultExpDate').textContent  = d.expiration_date;
        document.getElementById('resultStatus').innerHTML     = statusBadge(d.expiration_date);
        document.getElementById('resultRawQR').textContent    = rawQRData;
        new bootstrap.Modal(document.getElementById('resultModal')).show();
    }

    function showError(message, integrity) {
        const cfg = {
            MANIPULATED: { cls: 'bg-warning',          icon: 'fa-exclamation-triangle', title: 'Indikasi Manipulasi',    close: 'btn-close' },
            NOT_FOUND:   { cls: 'bg-danger text-white', icon: 'fa-times-circle',         title: 'Produk Tidak Ditemukan', close: 'btn-close btn-close-white' },
        };
        const c = cfg[integrity] ?? {
            cls: 'bg-danger text-white', icon: 'fa-times-circle',
            title: 'Verifikasi Gagal',   close: 'btn-close btn-close-white',
        };

        document.getElementById('errorHeader').className    = `modal-header py-3 ${c.cls}`;
        document.getElementById('errorIcon').className      = `fas ${c.icon}`;
        document.getElementById('errorTitle').textContent   = c.title;
        document.getElementById('errorBtnClose').className  = c.close;
        document.getElementById('errorMessage').textContent = message;
        new bootstrap.Modal(document.getElementById('errorModal')).show();
    }
</script>
</body>
</html>