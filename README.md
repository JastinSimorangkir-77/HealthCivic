# RS Sehat Sentosa — Portal Pasien

Portal pasien Laravel dengan halaman terpisah untuk ringkasan, janji temu, antrean, konsultasi, BPJS/asuransi, katalog farmasi, profil, masuk, dan pendaftaran akun.

## Menjalankan secara lokal

Persyaratan: PHP 8.3 atau lebih baru, Composer, Node.js 22 atau lebih baru, dan npm.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

Jika `.env` sudah tersedia, jangan menimpanya dengan `.env.example`. Atur database dan rahasia `APP_KEY` yang kuat sebelum digunakan. Enkripsi sesi dan enkripsi atribut sensitif menggunakan kunci aplikasi; jangan menonaktifkannya atau mengganti `APP_KEY` setelah data pasien tersimpan. Uji dengan `php artisan test`; audit dependensi dengan `composer audit`.

## Akun dan perlindungan data

- Pasien membuat akun dengan nama, email, nomor telepon, kata sandi minimal 12 karakter berisi huruf dan angka, serta persetujuan penggunaan data.
- Login, pendaftaran, permohonan kunjungan, dan pesanan diberi rate limit.
- Sesi diregenerasi saat login/registrasi dan dibatalkan saat logout.
- Password di-hash oleh cast Laravel. Telepon, data kunjungan, nomor BPJS, catatan konsultasi, item pesanan, dan alamat pengantaran dienkripsi di database.
- Setiap kunjungan/pesanan terikat pada akun pasien; ringkasan hanya memuat data akun tersebut.
- Data identitas dan catatan klinis tidak disimpan sebagai input lama pada sesi validasi.
- Role petugas tidak dapat dipilih saat pendaftaran. Administrator sistem perlu menetapkan role pada akun yang sudah dibuat melalui basis data: `doctor` dapat mengonfirmasi kunjungan, `pharmacy_admin` dapat mengonfirmasi pesanan farmasi, dan `admin` dapat mengakses keduanya. Contoh: `UPDATE users SET role = 'doctor' WHERE email = 'dokter@example.com';`
- Konfirmasi menyimpan status, waktu, dan akun petugas yang memprosesnya. Perubahan status hanya dapat dilakukan satu kali dari status menunggu.

## Batasan sebelum produksi

Ini adalah fondasi produk, **belum siap menerima atau memproses pasien sungguhan**. Nama, jam, alamat, poli, jadwal, serta katalog produk dan harga bersifat contoh; produk bukan rekomendasi medis. Kode kunjungan dan antrean hanyalah referensi permohonan—bukan slot dokter atau nomor antrean operasional. Portal menyediakan antrean konfirmasi dasar bagi petugas dengan role, tetapi belum memiliki pengelolaan akun/role petugas dari UI, verifikasi email, pemulihan kata sandi, pengubahan profil, koneksi BPJS/rujukan, rekam medis, konsultasi klinis/telemedisin, stok/resep, pembayaran, notifikasi, maupun layanan pengantaran.

Sebelum peluncuran, rumah sakit perlu menambahkan dan menguji integrasi jadwal/antrean, verifikasi penjamin, sistem farmasi dan resep, pembayaran, notifikasi, email verifikasi dan pemulihan akun, serta kanal dukungan resmi. Perkuat penetapan role dan autentikasi staf, audit trail lengkap, retensi/penghapusan data, backup terenkripsi, monitoring, dan prosedur respons insiden. Lakukan tinjauan keamanan, privasi, klinis, serta kepatuhan rekam medis oleh pihak rumah sakit. Produksi wajib menggunakan HTTPS, `APP_DEBUG=false`, secret terkelola aman, dan database/session/cache/queue/mail yang siap produksi.
