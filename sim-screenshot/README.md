# SIM SCREENSHOT — Prototype PHP Native

Prototype lokal sistem informasi organisasi SCREENSHOT menggunakan PHP Native, MariaDB/MySQL, PDO, HTML, CSS, dan JavaScript.

## Modul

- Login dan logout
- Dashboard ringkasan
- Keanggotaan (CRUD, pencarian, filter, pagination)
- Kegiatan (CRUD, peserta, evaluasi) — rancangan awal berdasarkan proposal karena layar modul ini belum tersedia di SIM.pdf
- Keuangan (CRUD, kategori, saldo otomatis, unggah bukti)
- Persuratan (surat masuk/keluar, CRUD, arsip PDF)
- Laporan keanggotaan, kegiatan, keuangan, persuratan; cetak dan ekspor CSV yang dapat dibuka di Excel

## Persyaratan

- XAMPP dengan Apache dan MySQL/MariaDB aktif
- PHP 8.2 atau lebih baru direkomendasikan
- Ekstensi PHP `pdo_mysql`, `fileinfo`, dan `mbstring` aktif

## Instalasi di Windows / XAMPP

1. Ekstrak folder `sim-screenshot` ke:
   `C:\xampp\htdocs\sim-screenshot`
2. Buka XAMPP Control Panel, lalu jalankan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, lalu impor file:
   `C:\xampp\htdocs\sim-screenshot\database\db_organisasi.sql`
5. Setelah selesai, impor:
   `C:\xampp\htdocs\sim-screenshot\database\upgrade.sql`
6. Opsional untuk melihat data contoh transaksi dan surat dari layar demonstrasi:
   impor `database\demo-data.sql`.
   Data pada file ini adalah data contoh presentasi, bukan data aktual organisasi.
7. Buka `config\database.php`. Konfigurasi default XAMPP adalah user `root` dan password kosong. Sesuaikan jika konfigurasi XAMPP Anda berbeda.
8. Buka:
   `http://localhost/sim-screenshot/`

## Akun demo

- Username: `admin`
- Email: `admin@screenshot.or.id`
- Password: `Screenshot26`

Segera ubah password demo sebelum aplikasi digunakan dengan data organisasi yang sesungguhnya. Akun pengurus lama dari dump SQL tetap dipertahankan; hash SHA-256 lama akan ditingkatkan ke `password_hash()` saat berhasil login.

## Catatan database

File `db_organisasi.sql` adalah dump awal yang diberikan untuk proyek. `upgrade.sql` menambahkan kolom kontak anggota, email dan status akun, metadata surat, kategori transaksi, serta tabel kegiatan dan peserta.

Jalankan dump awal satu kali, kemudian `upgrade.sql` satu kali. Jangan menjalankan `upgrade.sql` berulang kali karena perintah `ALTER TABLE` akan menambahkan kolom yang sudah ada.

## Catatan keamanan dan batas prototipe

- Formulir POST menggunakan token CSRF dan query database menggunakan prepared statements.
- Password akun demo menggunakan `password_hash()`.
- Upload surat dibatasi PDF maksimal 10 MB. Bukti transaksi menerima PDF/JPG/PNG maksimal 10 MB.
- Untuk penggunaan produksi, tambahkan HTTPS, backup terjadwal, audit log, pembatasan percobaan login, kebijakan retensi dokumen, dan pengujian keamanan.
- Modul Kegiatan adalah rancangan awal dari kebutuhan proposal, bukan desain layar yang sudah ada pada SIM.pdf.
- Laporan dapat dicetak melalui browser dan diekspor sebagai CSV (bisa dibuka di Excel). Ekspor PDF khusus belum dibuat; gunakan Print → Save as PDF untuk prototipe.
- Dashboard menampilkan agregat dari data yang benar-benar ada di database. Jumlah 128 anggota, 84 surat, dan nilai ringkasan pada screenshot adalah data demonstrasi, bukan hasil dump SQL.

## Troubleshooting singkat

**Database connection error**
- Pastikan MySQL XAMPP aktif.
- Pastikan nama database `db_organisasi`.
- Periksa username/password di `config/database.php`.

**Unknown column**
- Pastikan `upgrade.sql` sudah diimpor setelah dump awal.

**Gagal upload file**
- Periksa `upload_max_filesize` dan `post_max_size` pada `php.ini` (minimal 10M; disarankan 16M).
- Pastikan folder `uploads` dapat ditulisi oleh Apache.

**Halaman 404**
- Pastikan nama folder persis `sim-screenshot` dan lokasinya berada di `C:\xampp\htdocs`.

## Glassmorphism Rich UI — v0.4

Pembaruan visual menyeluruh dengan gaya glassmorphism:
- Sidebar dark glass dengan blur, transparansi, dan aksen merah SCREENSHOT.
- Topbar frosted glass yang berubah transparansinya saat halaman digulir.
- Kartu statistik, panel, panel laporan, dan kartu divisi memakai permukaan kaca berlapis.
- Formulir, filter, tombol, tabel, badge, notifikasi, dan ringkasan laporan mengikuti gaya glass.
- Login memakai latar berlapis, panel transparan, blur, dan animasi dekoratif.
- Animasi rich yang sudah ada tetap dipertahankan: reveal, count-up, 3D tilt, spotlight, ripple, chart, dan progress.
- Tidak ada overlay hitam/gelap untuk perpindahan halaman; navigasi tetap normal dengan efek masuk konten yang ringan.
- Fallback permukaan solid untuk browser tanpa dukungan backdrop-filter.
- Mendukung prefers-reduced-motion.

Perubahan ini hanya menyentuh lapisan presentasi CSS dan versi cache asset. PHP authentication, session, role permissions, CSRF, query, CRUD, upload, serta skema MariaDB tidak diubah.


## Logo sesuai warna latar — v0.4.2

Penyesuaian logo:
- Sidebar dan area gelap memakai logo putih.
- Halaman login bagian intro (latar gelap) memakai logo putih.
- Sheet laporan / area putih memakai logo hitam.
- Tujuannya agar elemen logo tetap terbaca dan warna tidak terlihat mati di permukaan terang.

Perubahan ini tidak menyentuh database, autentikasi, session, role permission, CSRF, query, maupun CRUD.


## Logo transparan — v0.4.3

Latar putih/hitam bawaan pada kedua aset logo telah dihapus menggunakan sumber resolusi tinggi:
- `logo-screenshot-mark-white-transparent.png`: logo putih transparan untuk permukaan gelap.
- `logo-screenshot-mark-black-transparent.png`: logo hitam transparan untuk permukaan terang.

Pemakaian:
- Sidebar dan panel intro login menggunakan logo putih transparan.
- Laporan/cetakan pada latar putih menggunakan logo hitam transparan.

PNG mempertahankan alpha transparency dan detail tepi antialias. Tidak ada perubahan pada database atau proses aplikasi.


## Page-entry animation enhancement — v0.4.4

Animasi masuk halaman mengikuti pendekatan pada paket referensi Medium:
- Komponen halaman masuk dengan fade-in bertahap.
- Ditambahkan soft-focus blur yang berkurang dari 5px menjadi 0px.
- Gerakan naik dan stagger delay dari sistem reveal Rich yang sudah ada tetap berjalan.
- Efek diterapkan saat komponen pertama kali terlihat dan saat komponen lain masuk viewport.
- Animasi login, topbar, grafik, tombol, hover, spotlight kursor, dan seluruh animasi Rich sebelumnya tetap dipertahankan.
- Tidak menambahkan overlay gelap atau transisi layar penuh.

Implementasi bersifat aditif pada CSS; JavaScript interaksi tidak diganti. PHP, autentikasi, session, hak akses, CSRF, CRUD, upload, dan database tidak diubah.


## Page-entry animation refinement — v0.4.5

Penyempurnaan animasi masuk halaman:
- Menghapus efek blur pada kemunculan elemen.
- Mempertahankan fade-in, gerakan naik, scale ringan, dan stagger dari sistem reveal Rich yang sudah ada.
- Tidak menambahkan animasi layar penuh atau overlay gelap.
- Seluruh animasi Glassmorphism, hover, pointer spotlight, 3D tilt, ripple, grafik, dan login tetap dipertahankan.

Perubahan hanya pada CSS dan cache-busting stylesheet. Tidak ada perubahan pada PHP, autentikasi, session, hak akses, CSRF, CRUD, upload, maupun skema database.


## Glass panel entrance animation — v0.4.6

Penyempurnaan animasi masuk panel:
- Panel glass, kartu statistik, kartu divisi, sheet laporan, dan ringkasan laporan memiliki animasi masuk khusus.
- Elemen bergerak naik sekitar 34px, membesar halus dari 96.5%, lalu settle dengan overshoot ringan.
- Fade-in dan stagger dipertahankan.
- Efek blur tidak digunakan.
- Wrapper grid tidak dianimasikan bersamaan dengan panel anak agar gerakan panel lebih terlihat dan tidak saling menutupi.
- Animasi hover, spotlight kursor, 3D tilt, ripple, login, topbar, tabel, grafik, dan progress tetap dipertahankan.
- `prefers-reduced-motion` tetap dihormati.

Perubahan hanya pada CSS, JavaScript pemilihan target animasi, dan cache-busting asset. PHP autentikasi, session, hak akses, CSRF, CRUD, upload, serta database tidak diubah.


## Animated sidebar flow lines — v0.4.7

Dekorasi SVG animatif pada sidebar:
- Empat garis meliuk dengan warna merah, putih, biru, dan kuning.
- Highlight bergerak sepanjang kurva dengan kecepatan dan fase berbeda agar tidak serempak.
- Garis dasar ber-opacity rendah menjaga kurva tetap terbaca tanpa terlalu mencolok.
- Lapisan SVG berada di belakang logo, menu, dan kontrol sidebar serta `pointer-events: none`, sehingga tidak menghalangi klik.
- SVG menyesuaikan lebar sidebar; intensitas diturunkan pada layar kecil dan kelas sidebar compact/collapsed yang umum.
- Menghormati `prefers-reduced-motion`.

Perubahan hanya pada markup SVG dekoratif, CSS, dan cache-busting. Tidak ada perubahan pada JavaScript interaksi, PHP authentication, session, hak akses, CSRF, CRUD, upload, atau database.


## Login animated flow lines — v0.4.8

Animasi garis SVG yang sama dengan sidebar juga diterapkan pada panel gelap halaman login:
- Empat garis merah, putih, biru, dan kuning bergerak pada kurva meliuk.
- Kecepatan dan fase tiap warna berbeda.
- Opacity login sedikit lebih rendah dibanding sidebar agar logo dan teks tetap terbaca.
- Lapisan SVG berada di belakang seluruh konten intro dan tidak menangkap interaksi.
- Animasi responsif dan menghormati `prefers-reduced-motion`.

Autentikasi, CSRF, session, proses verifikasi password, redirect, dan database tidak diubah.


## Login success brand reveal — v0.4.9

Setelah autentikasi berhasil, pengguna diarahkan ke `welcome.php` sebelum dashboard:
- Logo SCREENSHOT tampil dengan efek 3D pop-up/emboss, shadow, dan aura lembut.
- Garis energi SVG merah, biru, kuning, dan putih bergerak mengitari logo.
- Wordmark dan sapaan pengguna muncul bertahap.
- Progress indicator berjalan selama transisi.
- Durasi normal sekitar 2.35 detik; pengguna dapat melewati animasi.
- Menghormati `prefers-reduced-motion`.
- Transisi hanya muncul setelah login berhasil. Marker session satu kali dikonsumsi pada `welcome.php`, sehingga refresh tidak mengulang animasi.

Perubahan autentikasi dibatasi pada tujuan redirect setelah kredensial tervalidasi. Password verification, kompatibilitas hash SHA-256 lama, session regeneration, CSRF, dan struktur session user tetap dipertahankan. Tidak ada perubahan database atau CRUD.


## Brand reveal duration refinement — v0.5.0

Durasi animasi sambutan setelah login berhasil diperpanjang:
- Durasi transisi normal menjadi sekitar 5 detik.
- Logo 3D, garis energi, aura, wordmark, dan sapaan tetap menggunakan desain sebelumnya.
- Progress bar diperpanjang agar sinkron dengan durasi transisi.
- Tautan "Lewati animasi" tetap tersedia.
- Pengguna dengan `prefers-reduced-motion` tetap diarahkan lebih cepat.
- Fallback meta refresh diperpanjang menjadi 6 detik jika JavaScript tidak berjalan.

Tidak ada perubahan pada autentikasi, verifikasi password, session, CSRF, hak akses, CRUD, maupun database.


## Logout brand reveal — v0.5.1

Alur logout diperbarui:
- Tombol Keluar di sidebar menggunakan POST dengan token CSRF.
- `logout.php` menolak GET dan memvalidasi CSRF sebelum mengakhiri sesi.
- Session data, cookie autentikasi, dan server-side session dihancurkan sebelum HTML animasi dikirim.
- Setelah logout, halaman menampilkan brand reveal sekitar 5 detik dengan logo 3D, aura, garis energi merah/biru/kuning/putih, ucapan terima kasih, dan progress bar.
- Pengguna dapat memilih "Kembali ke login sekarang".
- Setelah animasi selesai, pengguna diarahkan ke halaman login.
- Header respons cache mencegah halaman logout tersimpan sebagai halaman terautentikasi.
- Menghormati `prefers-reduced-motion`.

Login success transition, password verification, session regeneration, CSRF login, hak akses, CRUD, dan database tetap dipertahankan.
