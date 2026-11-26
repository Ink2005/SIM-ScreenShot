<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('anggota');
$pdo = db();
$id = (int)($_GET['id'] ?? 0);
$member = ['nama'=>'','nim'=>'','program_studi'=>'','angkatan'=>'','divisi'=>'','email'=>'','nomor_telepon'=>'','alamat'=>'','tanggal_bergabung'=>date('Y-m-d'),'status'=>'aktif'];
if ($id) {
    $stmt=$pdo->prepare("SELECT * FROM anggota WHERE id_anggota=?"); $stmt->execute([$id]); $member=$stmt->fetch();
    if (!$member) { set_flash('danger','Data anggota tidak ditemukan.'); redirect('anggota.php'); }
}
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    foreach (array_keys($member) as $key) if (isset($_POST[$key])) $member[$key]=trim((string)$_POST[$key]);
    if ($member['nama']==='') $errors[]='Nama lengkap wajib diisi.';
    if ($member['nim']==='') $errors[]='NIM wajib diisi.';
    if ($member['divisi']==='') $errors[]='Divisi wajib dipilih.';
    if ($member['tanggal_bergabung']==='') $errors[]='Tanggal bergabung wajib diisi.';
    if ($member['email']!=='' && !filter_var($member['email'], FILTER_VALIDATE_EMAIL)) $errors[]='Format email tidak valid.';
    $dup=$pdo->prepare("SELECT COUNT(*) FROM anggota WHERE nim=? AND id_anggota<>?"); $dup->execute([$member['nim'],$id]);
    if ((int)$dup->fetchColumn()>0) $errors[]='NIM sudah digunakan oleh anggota lain.';
    if (!$errors) {
        $data=[$member['nama'],$member['nim'],$member['program_studi'],$member['angkatan'],$member['divisi'],$member['status'],$member['tanggal_bergabung'],$member['email'] ?: null,$member['nomor_telepon'] ?: null,$member['alamat'] ?: null];
        if ($id) {
            $data[]=$id;
            $pdo->prepare("UPDATE anggota SET nama=?,nim=?,program_studi=?,angkatan=?,divisi=?,status=?,tanggal_bergabung=?,email=?,nomor_telepon=?,alamat=? WHERE id_anggota=?")->execute($data);
            set_flash('success','Perubahan data anggota berhasil disimpan.');
        } else {
            $pdo->prepare("INSERT INTO anggota (nama,nim,program_studi,angkatan,divisi,status,tanggal_bergabung,email,nomor_telepon,alamat) VALUES (?,?,?,?,?,?,?,?,?,?)")->execute($data);
            set_flash('success','Anggota baru berhasil ditambahkan.');
        }
        redirect('anggota.php');
    }
}
$pageTitle=$id?'Edit Anggota':'Tambah Anggota'; $activeMenu='anggota';
include __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('anggota.php')) ?>">← Kembali ke Keanggotaan</a><h1><?= e($pageTitle) ?></h1><p>Lengkapi informasi di bawah. Kolom bertanda * wajib diisi.</p></div></div>
<?php if($errors): ?><div class="alert alert-danger"><strong>Periksa kembali formulir:</strong><ul><?php foreach($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="panel form-panel">
    <?= csrf_field() ?>
    <div class="panel-heading"><div><h2>Informasi Anggota</h2><p>Data identitas dan keanggotaan organisasi.</p></div><span class="muted small">* Wajib diisi</span></div>
    <div class="form-grid">
        <label>NIM *<input name="nim" value="<?= e($member['nim']) ?>" required maxlength="20" placeholder="Masukkan NIM"></label>
        <label>Nama lengkap *<input name="nama" value="<?= e($member['nama']) ?>" required maxlength="100" placeholder="Masukkan nama lengkap"></label>
        <label>Program studi<input name="program_studi" value="<?= e($member['program_studi']) ?>" placeholder="Masukkan program studi"></label>
        <label>Angkatan<input name="angkatan" value="<?= e($member['angkatan']) ?>" maxlength="4" placeholder="Contoh: 2024"></label>
        <label>Divisi *<select name="divisi" required><option value="">Pilih divisi</option><?php foreach(['Fotosin','Jurnalistik','DKV','Pengurus Harian'] as $d): ?><option value="<?= e($d) ?>" <?= $member['divisi']===$d?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?></select></label>
        <label>Email<input type="email" name="email" value="<?= e($member['email']) ?>" placeholder="nama@student.ac.id"></label>
        <label>Nomor telepon<input name="nomor_telepon" value="<?= e($member['nomor_telepon']) ?>" placeholder="08xxxxxxxxxx"></label>
        <label>Tanggal bergabung *<input type="date" name="tanggal_bergabung" value="<?= e($member['tanggal_bergabung']) ?>" required></label>
        <label>Status keanggotaan *<select name="status"><option value="aktif" <?= strtolower($member['status'])==='aktif'?'selected':'' ?>>Aktif</option><option value="tidak aktif" <?= strtolower($member['status'])==='tidak aktif'?'selected':'' ?>>Tidak Aktif</option></select></label>
        <label class="span-2">Alamat<textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap"><?= e($member['alamat']) ?></textarea></label>
    </div>
    <div class="form-actions"><a class="btn btn-ghost" href="<?= e(url('anggota.php')) ?>">Batal</a><button class="btn btn-primary" type="submit"><?= $id?'Simpan Perubahan':'Simpan Anggota' ?></button></div>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
