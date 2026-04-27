<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VTAYA YOGHURT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background-color: #00a8cc;
            color: white;
            padding: 24px 28px;
            text-align: center;
        }
        .btn-login {
            background-color: #00a8cc;
            color: white;
            border-radius: 10px;
            padding: 10px;
            transition: 0.3s;
            border: none;
        }
        .btn-login:hover {
            background-color: #007da0;
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card bg-white">

    <div class="login-header">
        {{-- Logo row --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                {{-- Ganti dengan <img src="..." height="30"> kalau sudah ada logo asli --}}
                <div class="bg-white bg-opacity-25 rounded px-2 py-1 small fw-bold">FKOM</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white bg-opacity-25 rounded px-2 py-1 small fw-bold">VTAYA</div>
            </div>
        </div>

        <h3 class="fw-bold mb-0">VTAYA</h3>
        <small>Portal Login Admin & Produksi</small>
    </div>

    <div class="p-4">
        @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label small fw-semibold">Password</label>
                    <a href="#" class="text-muted small" onclick="resetForm(); return false;">Reset field</a>
                </div>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label small text-muted" for="remember">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-login w-100 fw-bold">MASUK</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('welcome') }}" class="text-decoration-none text-muted small">← Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.querySelector('[name=email]').value = '';
        document.querySelector('[name=password]').value = '';
        document.querySelector('[name=remember]').checked = false;
        document.querySelector('[name=email]').focus();
    }
</script>

</body>
</html>