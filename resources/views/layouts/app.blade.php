<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VTAYA Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            align-items: stretch;
            width: 100%;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #00a8cc;
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
            z-index: 999;
        }

        #sidebar.active {
            margin-left: -260px;
        }

        /* Content Area */
        #content {
            width: 100%; /* Ini kunci supaya content tidak hilang */
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* CSS khusus Sidebar lainnya tetap sama seperti sebelumnya */
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar ul li a {
            padding: 12px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            margin: 8px 15px;
            border-radius: 12px;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        #sidebar ul li.active > a {
            color: #00a8cc !important;
            background: #fff !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
        #content {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container-fluid.p-4 {
    flex: 1; /* Ini akan mendorong footer ke bawah jika konten sedikit */
}

footer {
    background: #fff;
    border-top: 1px solid #dee2e6;
}
    </style>
</head>
<body>

    <div class="wrapper">
        @include('layouts.sidebar')

        <div id="content">
            @include('layouts.navbar')

            <div class="container-fluid p-4">
                @yield('content')
            </div>

            @include('layouts.footer')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
        
        // 1. SweetAlert untuk Hapus Produk
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Cegah form langsung submit
                
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data produk ini tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Warna merah bootstrap
                    cancelButtonColor: '#6c757d', // Warna abu-abu bootstrap
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Lanjutkan submit jika user pilih Ya
                    }
                });
            });
        });

        // 2. SweetAlert untuk Logout
        const btnLogout = document.getElementById('btn-logout');
        if(btnLogout) {
            btnLogout.addEventListener('click', function (e) {
                e.preventDefault(); // Cegah link langsung pindah
                
                Swal.fire({
                    title: 'Yakin ingin keluar?',
                    text: "Anda harus login kembali untuk mengakses sistem.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0dcaf0', // Warna info/cyan VTAYA
                    cancelButtonColor: '#dc3545', // Warna merah batal
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            });
        }

    });
    </script>
</body>
</html>