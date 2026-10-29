# GJP Absensi

Sistem absensi terdiri dari Kotlin (Jetpack Compose), Laravel, dan layanan verifikasi wajah Python.

## Persiapan

- PHP 8.1, Composer, dan database MySQL atau SQLite
- Flutter 3.38+
- Python 3.11+
- `cloudflared` hanya jika aplikasi diakses dari luar komputer server

## Backend Laravel

```powershell
cd backend
Copy-Item .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

Jika memakai SQLite, aktifkan ekstensi `pdo_sqlite` di `php.ini`.

Isi koneksi database, `HR_DASHBOARD_USERNAME`, dan `HR_DASHBOARD_PASSWORD` di
`backend/.env`. Jangan commit file `.env`.

## Verifikasi wajah Python

```powershell
cd ml
python -m venv .venv
.venv\Scripts\python -m pip install -r requirements.txt
.venv\Scripts\python face_service.py
```

Laravel mengakses layanan ini melalui `FACE_SERVICE_URL`, dengan nilai lokal
standar `http://127.0.0.1:8765`.

## Frontend Android (Kotlin + Jetpack Compose)

Buka folder `frontend-kotlin` di Android Studio:

1. Buka Android Studio -> **File > Open**.
2. Pilih folder `H:\absen-face\frontend-kotlin`.
3. Tunggu Gradle sync selesai.
4. Desain UI dapat langsung dilihat dan diatur secara visual melalui tab **Split/Design** (`@Preview`) pada setiap screen di `app/src/main/kotlin/com/example/frontend/ui/screens/`.
5. Jalankan aplikasi langsung ke device atau emulator dari Android Studio.

## Sebelum dipasang di client

- Gunakan password HR yang unik dan kuat.
- Gunakan named Cloudflare Tunnel atau domain HTTPS tetap, bukan quick tunnel.
- Simpan foto acuan wajah dan `.env` hanya di server client.
- Kalibrasi `FACE_MATCH_THRESHOLD` menggunakan dataset milik client.
