<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3a8ef6, #6fb1fc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: transparent;
            border-bottom: none;
        }
        .avatar-wrapper {
            width: 90px;
            height: 90px;
            margin: 0 auto;
            margin-top: -65px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar {
            width: 60px;
            height: 60px;
        }
        .form-control {
            border-radius: 0.75rem;
        }
        .btn-login {
            border-radius: 0.75rem;
            background-color: #3a8ef6;
            border: none;
        }
        .btn-login:hover {
            background-color: #2f79d9;
        }
    </style>
</head>
<body>

<div class="container px-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <div class="card-header text-center">
                    <div class="avatar-wrapper">
                        <img src="https://cdn-icons-png.flaticon.com/512/747/747376.png" alt="avatar" class="img-fluid avatar">
                    </div>
                    <h4 class="fw-bold text-primary">Welcome</h4>
                    <p class="text-muted small">Please log in to continue</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            @error('email')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required>
                            </div>
                            @error('password')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-login text-white">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                        </div>

                        {{-- @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-decoration-none text-primary" href="{{ route('password.request') }}">
                                    <i class="bi bi-question-circle"></i> Lupa kata sandi?
                                </a>
                            </div>
                        @endif --}}
                    </form>
                </div>

                <div class="text-center text-muted mt-2 small">
                    &copy; {{ date('Y') }} RRCONS Admin
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
