<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AbsenKu HR — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --green:   #075A3E;
    --green2:  #0b7452;
    --green3:  #0e8f66;
    --soft:    #eaf4ef;
    --bg:      #f4f7f5;
    --ink:     #17201c;
    --muted:   #6f7a74;
    --line:    #e0e7e3;
    --red:     #b63f45;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    background: var(--bg);
    color: var(--ink);
    font-family: 'Inter', Arial, sans-serif;
    min-height: 100vh;
    display: grid;
    place-items: center;
}

/* ── Split layout ─────────────────── */
.wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 100vh;
    width: 100%;
}

/* ── Left — brand panel ───────────── */
.brand-panel {
    background: linear-gradient(155deg, #063724 0%, #075A3E 50%, #0b7452 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 60px 48px;
    position: relative;
    overflow: hidden;
}

.brand-panel::before {
    content: '';
    position: absolute;
    top: -120px;
    right: -120px;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.brand-panel::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 280px;
    height: 280px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}

.logo-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 48px;
}

/* ── Logo SVG badge ───────────────── */
.logo-mark {
    width: 60px;
    height: 66px;
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.5);
    border-radius: 14px 14px 28px 28px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.logo-mark svg {
    width: 32px;
    height: 32px;
    fill: none;
    stroke: #ffffff;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.logo-text h1 {
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -1px;
    line-height: 1;
}

.logo-text span {
    font-size: 11px;
    font-weight: 500;
    color: #aad1bf;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.brand-tagline {
    text-align: center;
    color: rgba(255,255,255,.8);
    font-size: 18px;
    font-weight: 600;
    line-height: 1.5;
    max-width: 320px;
    margin-bottom: 40px;
}

.brand-tagline em {
    color: #ffffff;
    font-style: normal;
}

.feature-list {
    list-style: none;
    width: 100%;
    max-width: 320px;
    display: grid;
    gap: 14px;
}

.feature-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,.8);
    font-size: 14px;
}

.feat-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,.12);
    border-radius: 8px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    font-size: 15px;
}

/* ── Right — form panel ──────────── */
.form-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 60px 48px;
    background: #fff;
}

.form-inner {
    width: 100%;
    max-width: 380px;
}

.form-header {
    margin-bottom: 36px;
}

.form-header h2 {
    font-size: 26px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
}

.form-header p {
    margin-top: 6px;
    color: var(--muted);
    font-size: 14px;
}

.field {
    margin-bottom: 18px;
}

.field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 7px;
}

.field input {
    width: 100%;
    height: 46px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    padding: 0 14px;
    font-size: 15px;
    font-family: inherit;
    color: var(--ink);
    background: var(--bg);
    transition: border-color .15s, box-shadow .15s;
    outline: none;
}

.field input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(7,90,62,.12);
    background: #fff;
}

.field input.is-invalid {
    border-color: var(--red);
}

.field-error {
    margin-top: 6px;
    font-size: 12px;
    color: var(--red);
    font-weight: 500;
}

.btn-login {
    width: 100%;
    height: 48px;
    background: linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    margin-top: 8px;
    transition: opacity .15s, transform .1s;
    letter-spacing: .2px;
}

.btn-login:hover  { opacity: .92; }
.btn-login:active { transform: scale(.98); }

.divider {
    margin: 28px 0;
    border: none;
    border-top: 1px solid var(--line);
}

.login-footer {
    text-align: center;
    font-size: 12px;
    color: var(--muted);
    line-height: 1.7;
}

.login-footer strong {
    color: var(--ink);
}

/* ── Alert error ─────────────────── */
.alert-error {
    background: #fdebed;
    border: 1px solid #f0c2c5;
    border-radius: 9px;
    padding: 11px 14px;
    font-size: 13px;
    color: var(--red);
    font-weight: 500;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .wrapper { grid-template-columns: 1fr; }
    .brand-panel { display: none; }
    .form-panel { padding: 40px 24px; min-height: 100vh; }
}
</style>
</head>
<body>
<div class="wrapper">

    {{-- ── Kiri: Brand Panel ── --}}
    <div class="brand-panel">
        <div class="logo-wrap">
            <div class="logo-mark">
                <svg viewBox="0 0 24 24">
                    {{-- Face ID / scan icon --}}
                    <path d="M7 3H5a2 2 0 0 0-2 2v2"/>
                    <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                    <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                    <path d="M17 21h2a2 2 0 0 0 2-2v-2"/>
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M8 17c0-2.21 1.79-4 4-4s4 1.79 4 4"/>
                </svg>
            </div>
            <div class="logo-text">
                <h1>AbsenKu</h1>
                <span>HR Control Center</span>
            </div>
        </div>

        <p class="brand-tagline">
            Pantau & kendalikan<br>
            <em>perangkat kantor</em> karyawan<br>
            secara real-time.
        </p>

        <ul class="feature-list">
            <li>
                <span class="feat-icon">📱</span>
                Monitoring galeri HP kantor
            </li>
            <li>
                <span class="feat-icon">🕐</span>
                Riwayat absensi real-time
            </li>
            <li>
                <span class="feat-icon">🚩</span>
                Teguran & flagging konten SOP
            </li>
            <li>
                <span class="feat-icon">⬇️</span>
                Download seluruh galeri (ZIP)
            </li>
        </ul>
    </div>

    {{-- ── Kanan: Form Login ── --}}
    <div class="form-panel">
        <div class="form-inner">
            <div class="form-header">
                <h2>Selamat datang 👋</h2>
                <p>Masuk sebagai Administrator HR untuk melanjutkan.</p>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        autofocus
                    >
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    >
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    Masuk ke Dashboard HR
                </button>
            </form>

            <hr class="divider">

            <div class="login-footer">
                <strong>AbsenKu HR Control Center</strong><br>
                Akses terbatas untuk Administrator HR.<br>
                Hubungi IT support jika lupa kredensial.
            </div>
        </div>
    </div>

</div>
</body>
</html>
