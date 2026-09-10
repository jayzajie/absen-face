<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | GJP Sistem Kehadiran</title>
    <link rel="stylesheet" href="{{ asset('css/hr.css') }}">
</head>
<body class="auth-page">
<div class="auth-shell">
    <section class="auth-brand" aria-labelledby="brand-heading">
        <div class="auth-logo-panel">
            <img class="brand-logo" src="{{ route('brand.logo') }}" alt="GJP">
            <div class="brand-copy">
                <strong>Sistem Kehadiran</strong>
                <span>Portal administrator HR</span>
            </div>
        </div>

        <div class="auth-brand-message">
            <h1 id="brand-heading">Kehadiran yang mudah diperiksa.</h1>
            <p>Kelola catatan masuk, pulang, karyawan, dan perangkat kantor dalam satu ruang kerja.</p>
        </div>

        <small>Akses internal GJP</small>
    </section>

    <main class="auth-form-side">
        <div class="auth-form">
            <h2>Masuk ke portal HR</h2>
            <p>Gunakan akun administrator yang diberikan oleh tim IT.</p>

            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" data-loading-form>
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username" required autofocus aria-invalid="{{ $errors->any() ? 'true' : 'false' }}">
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required aria-describedby="login-help" aria-invalid="{{ $errors->any() ? 'true' : 'false' }}">
                </div>

                <button type="submit" class="button">Masuk ke dashboard</button>
            </form>

            <p class="auth-help" id="login-help">Lupa kredensial? Hubungi tim IT GJP. Portal ini hanya tersedia untuk administrator HR.</p>
        </div>
    </main>
</div>

<div class="loading-status" role="status" aria-live="polite">Memeriksa akun...</div>
<script>
document.querySelector('[data-loading-form]').addEventListener('submit', () => document.body.classList.add('busy'));
</script>
</body>
</html>
