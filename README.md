# GJP Absensi

Sistem absensi terdiri dari Flutter, Laravel, dan layanan verifikasi wajah Python.

## Persiapan

- PHP 8.2+, Composer, dan database MySQL atau SQLite
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

## Flutter

```powershell
cd frontend
flutter pub get
flutter run -d <device-id> --dart-define=API_URL=https://<host-backend>/api
```

Buka folder `frontend`, bukan hanya `frontend/android`, jika menggunakan Android
Studio. Konfigurasi Android sudah memakai Flutter embedding v2 dan menjalankan
`lib/main.dart`.

## Sebelum dipasang di client

- Gunakan password HR yang unik dan kuat.
- Gunakan named Cloudflare Tunnel atau domain HTTPS tetap, bukan quick tunnel.
- Simpan foto acuan wajah dan `.env` hanya di server client.
- Kalibrasi `FACE_MATCH_THRESHOLD` menggunakan dataset milik client.
