<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('anggota');
$id=(int)($_GET['id']??0);
$stmt=db()->prepare("SELECT * FROM anggota WHERE id_anggota=?"); $stmt->execute([$id]); $m=$stmt->fetch();
if(!$m){set_flash('danger','Data anggota tidak ditemukan.');redirect('anggota.php');}
$pageTitle='Detail Anggota';$activeMenu='anggota';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('anggota.php')) ?>">← Kembali ke Keanggotaan</a><h1>Detail Anggota</h1><p>Informasi lengkap anggota yang tersimpan dalam sistem.</p></div><a class="btn btn-primary" href="<?= e(url('anggota_form.php?id='.$id)) ?>">Edit Anggota</a></div>
<section class="panel detail-panel"><div class="profile-head"><div class="avatar avatar-large"><?= e(strtoupper(substr($m['nama'],0,1))) ?></div><div><h2><?= e($m['nama']) ?></h2><p class="muted mono"><?= e($m['nim']) ?></p><?= status_badge($m['status']) ?></div></div>
<div class="detail-grid">
<?php foreach(['Program Studi'=>'program_studi','Angkatan'=>'angkatan','Divisi'=>'divisi','Email'=>'email','Nomor Telepon'=>'nomor_telepon','Alamat'=>'alamat','Tanggal Bergabung'=>'tanggal_bergabung'] as $label=>$key): ?>
<div class="detail-item"><span><?= e($label) ?></span><strong><?= e($key==='tanggal_bergabung'?tanggal_id($m[$key]):($m[$key]?:'-')) ?></strong></div>
<?php endforeach; ?>
</div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
