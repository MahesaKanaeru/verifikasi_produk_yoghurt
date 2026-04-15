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
        body {
            font-family: 'Poppins', sans-serif;
            color: #444;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-weight: 700;
            color: #00a8cc !important;
        }
        
        /* Hero Section dengan gradasi warna susu */
        .hero-section {
            background: linear-gradient(135deg, #ffffff 0%, #e0f7fa 50%, #b2ebf2 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Ilustrasi sederhana awan/susu (Vector vibe) */
        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            height: 150px;
            background: #fff;
            border-radius: 50% 50% 0 0;
        }

        .btn-scan {
            background-color: #00a8cc;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }
        .btn-scan:hover {
            background-color: #007da0;
            transform: translateY(-3px);
            color: white;
        }

        .about-img {
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
            background: #f8fdff;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        footer {
            background-color: #f0faff;
            padding-top: 50px;
        }
        .footer-bottom {
            background-color: #00a8cc;
            color: white;
            padding: 15px 0;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">VTAYA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">ABOUT ME</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">CONTACT US</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-info ms-lg-3 px-4" href="{{ route('login') }}">LOGIN</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">VTAYA YOGHURT</h1>
            <p class="lead mb-5 text-uppercase tracking-widest">Yoghurt Segar Tanpa Pengawet</p>
            <button class="btn btn-scan btn-lg" onclick="startScan()">
                <i class="fas fa-qrcode me-2"></i> SCAN QR HERE
            </button>
        </div>
    </section>

    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6 mb-4 mb-md-0 text-center">
                    <img src="https://via.placeholder.com/400x400/b2ebf2/00a8cc?text=Vtaya+Yoghurt" alt="Ftaya Yoghurt" class="img-fluid about-img">
                </div>
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">Sejarah Singkat</h2>
                    <p class="text-muted">VTAYA Yoghurt bermula dari keinginan untuk menyajikan minuman sehat keluarga yang murni tanpa bahan pengawet. Kami memproses susu segar pilihan dengan teknik fermentasi tradisional untuk menjaga kualitas nutrisi dan rasa yang otentik dan tanpa pengawet.</p>
                </div>
            </div>

            <h3 class="text-center fw-bold mb-4">Varian Rasa</h3>
            <div class="row justify-content-center">

                @forelse($products as $p)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="card product-card p-3 text-center h-100 border-0 shadow-sm">
                        
                        <div class="ratio ratio-1x1 mb-3">
                            <img src="{{ $p->foto_produk ? asset('storage/'.$p->foto_produk) : asset('images/no-image.png') }}" 
                                class="card-img-top rounded object-fit-cover" 
                                alt="{{ $p->nama_produk }}">
                        </div>
                        
                        <div class="card-body p-0 d-flex align-items-center justify-content-center">
                            <h5 class="card-title fw-bold m-0" style="font-size: 1.1rem; color: #333;">
                                {{ $p->nama_produk }}
                            </h5>
                        </div>

                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted py-4">
                    <i class="fas fa-box-open fs-1 mb-3 text-secondary"></i>
                    <p>Belum ada varian rasa yang tersedia saat ini.</p>
                </div>
                @endforelse

            </div>
        </div>
    </section>

    <footer id="contact" style="background: #f0faff; border-top: 1px solid #e0f0f5;">
        <div class="container pt-5 pb-4">
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-4">
                    <h5 class="fw-bold mb-4 text-info"><i class="fas fa-info-circle me-2"></i>INFO KONTAK</h5>
                    <div class="d-flex mb-3">
                        <i class="fab fa-instagram me-3 fs-4 text-danger"></i>
                        <span>@vtaya_yoghurt</span>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="fab fa-whatsapp me-3 fs-4 text-success"></i>
                        <span>+62 812-3456-7890</span>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="fas fa-map-marker-alt me-3 fs-4 text-primary"></i>
                        <span>Jl. Cigugur Sukamulya No.82, Kuningan</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <h5 class="fw-bold mb-4 text-info"><i class="fas fa-clock me-2"></i>JADWAL BUKA</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2 d-flex justify-content-between border-bottom pb-1">
                            <span>Senin - Minggu</span>
                            <span class="fw-semibold">08:00 - 19:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="opacity-10">

            <div class="text-center pt-3">
                <p class="mb-0 text-muted small">&copy; 2026 <span class="fw-bold text-info">VTAYA YOGHURT</span>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Simulasi Fungsi Scan
        function startScan() {
            alert("Membuka Kamera... \n(Integrasikan library seperti html5-qrcode di sini nanti)");
            // Nantinya di sini kamu panggil library QR Scanner
        }
    </script>
</body>
</html>