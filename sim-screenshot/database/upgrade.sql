-- Jalankan SETELAH database/db_organisasi.sql diimpor.
USE db_organisasi;

ALTER TABLE anggota
  ADD COLUMN email VARCHAR(150) NULL AFTER divisi,
  ADD COLUMN nomor_telepon VARCHAR(30) NULL AFTER email,
  ADD COLUMN alamat TEXT NULL AFTER nomor_telepon;

ALTER TABLE pengguna
  ADD COLUMN email VARCHAR(150) NULL AFTER username,
  ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1 AFTER peran,
  ADD UNIQUE KEY uq_pengguna_email (email);

ALTER TABLE surat
  ADD COLUMN tanggal_diterima DATE NULL AFTER tanggal,
  ADD COLUMN jenis_surat VARCHAR(50) NULL AFTER pengirim_tujuan,
  ADD COLUMN keterangan TEXT NULL AFTER perihal,
  ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'Diarsipkan' AFTER berkas;

ALTER TABLE transaksi_kas
  ADD COLUMN kategori VARCHAR(80) NULL AFTER jenis;

CREATE TABLE kegiatan (
  id_kegiatan INT AUTO_INCREMENT PRIMARY KEY,
  nama_kegiatan VARCHAR(180) NOT NULL,
  jenis_kegiatan VARCHAR(60) NOT NULL DEFAULT 'Program kerja',
  tanggal_mulai DATE NOT NULL,
  tanggal_selesai DATE NULL,
  lokasi VARCHAR(180) NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Direncanakan',
  deskripsi TEXT NULL,
  evaluasi TEXT NULL,
  id_pengguna INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_kegiatan_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE kegiatan_peserta (
  id_kegiatan INT NOT NULL,
  id_anggota INT NOT NULL,
  PRIMARY KEY (id_kegiatan,id_anggota),
  CONSTRAINT fk_kp_kegiatan FOREIGN KEY (id_kegiatan) REFERENCES kegiatan(id_kegiatan) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_kp_anggota FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun demo untuk mulai mengakses prototipe.
-- Username: admin | Email: admin@screenshot.or.id | Password: Screenshot26
INSERT INTO pengguna (nama, username, email, kata_sandi, peran, status_aktif, id_anggota)
SELECT 'Administrator', 'admin', 'admin@screenshot.or.id',
       '$2y$12$/s.2EeF3izpiw74nEKxv3OaYw2JBjWuiOKsFFOvrLqUSRp/q30I2G',
       'Admin', 1, NULL
WHERE NOT EXISTS (SELECT 1 FROM pengguna WHERE username='admin');

-- Mengisi email akun pengurus lama jika belum ada. Email bersifat opsional.
UPDATE pengguna SET email='ketua@screenshot.or.id' WHERE username='ketua' AND (email IS NULL OR email='');
UPDATE pengguna SET email='sekretaris@screenshot.or.id' WHERE username='sekretaris' AND (email IS NULL OR email='');
UPDATE pengguna SET email='bendahara@screenshot.or.id' WHERE username='bendahara' AND (email IS NULL OR email='');
