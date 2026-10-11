<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('auth.seo.title') }}</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- Icon -->
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('front/image/logo.png') }}"
    >

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
            margin: -65px auto 15px;
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

        .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-text {
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
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
    @include('partials.page-loader')

    <main class="container px-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4">

                    <!-- Header -->
                    <div class="card-header text-center">
                        <div class="avatar-wrapper">
                            <img
                                src="https://cdn-icons-png.flaticon.com/512/747/747376.png"
                                alt="{{ __('auth.sections.login.avatar_alt') }}"
                                class="img-fluid avatar"
                            >
                        </div>

                        <h1 class="h4 fw-bold text-primary">
                            {{ __('auth.sections.login.heading') }}
                        </h1>

                        <p class="text-muted small">
                            {{ __('auth.sections.login.subtitle') }}
                        </p>
                    </div>

                    <!-- Form -->
                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            novalidate
                        >
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label
                                    for="email"
                                    class="form-label"
                                >
                                    {{ __('auth.sections.login.email.label') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i
                                            class="bi bi-envelope"
                                            aria-hidden="true"
                                        ></i>
                                    </span>

                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="{{ __('auth.sections.login.email.placeholder') }}"
                                        autocomplete="email"
                                        required
                                        autofocus
                                        class="form-control @error('email') is-invalid @enderror"
                                        @error('email')
                                            aria-invalid="true"
                                            aria-describedby="email-error"
                                        @enderror
                                    >
                                </div>

                                @error('email')
                                    <small
                                        id="email-error"
                                        class="text-danger d-block mt-1"
                                        role="alert"
                                    >
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label
                                    for="password"
                                    class="form-label"
                                >
                                    {{ __('auth.sections.login.password.label') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i
                                            class="bi bi-lock"
                                            aria-hidden="true"
                                        ></i>
                                    </span>

                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        placeholder="{{ __('auth.sections.login.password.placeholder') }}"
                                        autocomplete="current-password"
                                        required
                                        class="form-control @error('password') is-invalid @enderror"
                                        @error('password')
                                            aria-invalid="true"
                                            aria-describedby="password-error"
                                        @enderror
                                    >
                                </div>

                                @error('password')
                                    <small
                                        id="password-error"
                                        class="text-danger d-block mt-1"
                                        role="alert"
                                    >
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-2">
                                <button
                                    type="submit"
                                    class="btn btn-login text-white"
                                >
                                    <i
                                        class="bi bi-box-arrow-in-right me-1"
                                        aria-hidden="true"
                                    ></i>

                                    {{ __('auth.sections.login.submit') }}
                                </button>
                            </div>

                            {{--
                            @if (Route::has('password.request'))
                                <div class="text-center">
                                    <a
                                        class="text-decoration-none text-primary"
                                        href="{{ route('password.request') }}"
                                    >
                                        <i
                                            class="bi bi-question-circle"
                                            aria-hidden="true"
                                        ></i>

                                        {{ __('auth.sections.login.forgot_password') }}
                                    </a>
                                </div>
                            @endif
                            --}}
                        </form>
                    </div>

                    <!-- Footer -->
                    <footer class="text-center text-muted mt-2 small">
                        &copy;
                        {{ now()->year }}
                        {{ __('auth.sections.login.footer') }}
                    </footer>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>