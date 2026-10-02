# Sistem Kontrol Kendaraan Bank Jatim Cabang Tuban

## Isi
- `web` berada di root: PHP Native + MySQL + REST API.
- `flutter_driver`: source aplikasi Android driver.
- Laporan cetak: A4 landscape, tabel mengikuti form pada foto referensi, kolom CK Satpam dan tanda tangan dibiarkan kosong untuk diisi manual.

## Instalasi Web di XAMPP
1. Salin folder project ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin lalu import `database.sql`.
4. Buka `http://localhost/sistem_kontrol_kendaraan_bank_jatim/setup.php`.
5. Buat akun admin dan driver.
6. Hapus `setup.php` setelah akun dibuat.
7. Login di `http://localhost/sistem_kontrol_kendaraan_bank_jatim/`.

## Akun contoh
- Admin: `admin` / `admin123`
- Driver: `89344432` / `driver123`
Sebaiknya segera ganti password dengan password yang kuat.

## Android Flutter
1. Buka folder `flutter_driver` dengan Android Studio/VS Code.
2. Jalankan `flutter pub get`.
3. Emulator Android menggunakan `10.0.2.2` untuk mengakses localhost PC.
4. Jika memakai HP fisik, ubah `apiBase` di `lib/main.dart` menjadi IP LAN komputer, misalnya `http://192.168.1.10/sistem_kontrol_kendaraan_bank_jatim/api`.
5. Pastikan HP dan PC berada di jaringan yang sama serta firewall mengizinkan Apache.
6. Untuk produksi, gunakan HTTPS dan domain/server yang aman.

## Alur
Admin membuat kendaraan + driver -> admin membuat penugasan -> driver login Android -> driver mulai perjalanan (jam & KM awal) -> driver menyelesaikan perjalanan (jam & KM akhir) -> web menerima data -> admin memilih tanggal -> cetak laporan -> tanda tangan Controller Umum dan Controller Satpam dilakukan manual.

## Catatan
Versi ini adalah MVP operasional. Untuk deployment instansi, tambahkan HTTPS, backup database, audit log, pembatasan akses jaringan, dan kebijakan keamanan akun.

## Penting untuk koneksi Android
- Emulator Android Studio: `10.0.2.2` mengarah ke localhost PC.
- HP fisik: ganti `apiBase` di `flutter_driver/lib/main.dart` dengan IP LAN PC, misalnya `http://192.168.1.10/sistem_kontrol_kendaraan_bank_jatim/api`.
- Pastikan Apache aktif, PC dan HP berada pada Wi-Fi/LAN yang sama, dan Windows Firewall mengizinkan Apache.
- Contoh ini mengizinkan HTTP lokal untuk development. Untuk penggunaan nyata di jaringan/hosting instansi, gunakan HTTPS.
