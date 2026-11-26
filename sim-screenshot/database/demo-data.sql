-- OPSIONAL: data contoh dari layar demonstrasi SIM.pdf.
-- Jalankan hanya jika Anda ingin melihat contoh transaksi dan arsip surat.
-- Data ini bukan data aktual organisasi dan dapat dihapus kapan saja.
USE db_organisasi;

INSERT INTO transaksi_kas (tanggal,jenis,kategori,keterangan,nominal,bukti,id_pengguna) VALUES
('2026-11-17','Pemasukan','Iuran anggota','Iuran anggota November',1200000,NULL,1),
('2026-11-16','Pengeluaran','Perlengkapan','Pembelian tinta printer',450000,NULL,1),
('2026-11-14','Pemasukan','Donasi','Donasi alumni',750000,NULL,1),
('2026-11-12','Pengeluaran','Administrasi','Cetak kop dan amplop',350000,NULL,1);

INSERT INTO surat (nomor_surat,jenis,tanggal,tanggal_diterima,pengirim_tujuan,jenis_surat,perihal,keterangan,status,berkas,id_pengguna) VALUES
('048/SM/XI/2026','masuk','2026-11-18','2026-11-18','Bagian Kemahasiswaan','Pemberitahuan','Pemberitahuan pembaruan data','Pembaruan data administrasi organisasi.','Diarsipkan',NULL,2),
('047/SM/XI/2026','masuk','2026-11-15','2026-11-15','Dekan Fakultas','Keputusan','Pengesahan susunan pengurus','Dokumen contoh arsip surat masuk.','Diarsipkan',NULL,2),
('046/SM/XI/2026','masuk','2026-11-11','2026-11-11','Biro Akademik','Pemberitahuan','Verifikasi data keanggotaan','Dokumen contoh arsip surat masuk.','Diarsipkan',NULL,2),
('045/SM/X/2026','masuk','2026-10-29','2026-10-29','Wakil Dekan III','Persetujuan','Persetujuan penggunaan ruang','Dokumen contoh arsip surat masuk.','Diarsipkan',NULL,2),
('044/SM/X/2026','masuk','2026-10-24','2026-10-24','Bagian Kemahasiswaan','Permintaan','Permintaan rekap anggota','Dokumen contoh arsip surat masuk.','Diarsipkan',NULL,2),
('036/SK/XI/2026','keluar','2026-11-17',NULL,'Wakil Dekan III','Permohonan','Permohonan peminjaman ruang','Dokumen contoh arsip surat keluar.','Terkirim',NULL,2),
('035/SK/XI/2026','keluar','2026-11-13',NULL,'Biro Akademik','Permohonan','Permohonan legalisasi dokumen','Dokumen contoh arsip surat keluar.','Terkirim',NULL,2),
('034/SK/XI/2026','keluar','2026-11-10',NULL,'Bagian Kemahasiswaan','Pemberitahuan','Pembaruan data pengurus','Dokumen contoh arsip surat keluar.','Terkirim',NULL,2),
('033/SK/X/2026','keluar','2026-10-28',NULL,'Ketua Program Studi','Laporan','Penyerahan laporan organisasi','Dokumen contoh arsip surat keluar.','Terkirim',NULL,2),
('032/SK/X/2026','keluar','2026-10-22',NULL,'Wakil Dekan III','Permohonan','Permohonan surat keterangan','Dokumen contoh arsip surat keluar.','Terkirim',NULL,2);
