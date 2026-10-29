# Frontend Kotlin (Jetpack Compose) - GJP Absensi

Project ini adalah versi **Pure Native Android (Kotlin + Jetpack Compose)** dari aplikasi GJP Absensi. Project ini dibuat agar dapat dibuka dan dikontrol secara visual maupun kode langsung melalui **Android Studio**.

## Fitur Utama
1. **100% Kotlin + Jetpack Compose**: Tampilan modern, reaktif, dan deklaratif.
2. **Android Studio Interactive Preview (`@Preview`)**:
   - Setiap screen memiliki fungsi `@Preview` yang bisa langsung dilihat desain dan layout-nya di tab **Split / Design** Android Studio tanpa perlu menjalankan emulator.
3. **Integrasi Penuh Backend & Face Verification**:
   - `POST /mobile/login` (Otentikasi & Device ID binding)
   - `POST /attendances/verify` (Capture foto selfie via CameraX / Galeri & Verifikasi wajah dengan Python ML)
4. **Semua Layar Tersedia Persis Seperti Desain Flutter**:
   - **LoginScreen**: Input email/username, password visibility toggle, checkbox ingat saya, error handling.
   - **HomeScreen**: Header karyawan, foto profil, kartu ringkasan hari ini, tombol aksi Absen Masuk & Pulang, kartu peta lokasi kantor, jadwal kerja.
   - **ScanScreen**: Preview kamera depan (CameraX), framing guide wajah, badge kamera aktif, tombol galeri/test photo, ambil foto selfie & verifikasi.
   - **SuccessScreen**: Background hijau/biru, ikon centang, detail status (Tepat Waktu/Terlambat), selisih waktu, skor kemiripan wajah.
   - **HistoryScreen**: Filter periode & status, kartu statistik total kehadiran, daftar riwayat absensi.
   - **IzinScreen**: Informasi pengajuan izin/cuti.
   - **AccountScreen**: Ganti foto profil, info ID perangkat, privasi, tombol keluar.
   - **AppBottomBar**: Navigasi bawah (Beranda, Riwayat, Izin, Akun).

## Cara Membuka di Android Studio
1. Buka **Android Studio**.
2. Pilih **Open** (atau **File > Open**).
3. Pilih folder: `H:\absen-face\frontend-kotlin` (buka folder ini sebagai root project).
4. Tunggu Gradle sync selesai.
5. Untuk melihat desain UI secara visual:
   - Buka file di `app/src/main/kotlin/com/example/frontend/ui/screens/` (misal `HomeScreen.kt` atau `LoginScreen.kt`).
   - Di pojok kanan atas editor, klik tombol **Split** atau **Design** untuk melihat Compose Preview interaktif!
