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
            height: 100vh;
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
            padding: 30px;
            text-align: center;
        }
        .btn-login {
            background-color: #00a8cc;
            color: white;
            border-radius: 10px;
            padding: 10px;
            transition: 0.3s;
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
        <h3 class="fw-bold mb-0">VTAYA</h3>
        <small>Admin & Production Login</small>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('login.post') }}" method="POST">
        @csrf @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>F
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login w-100 fw-bold">MASUK</button>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('welcome') }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

</body>
</html>