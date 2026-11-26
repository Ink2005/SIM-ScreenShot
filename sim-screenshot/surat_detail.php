<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('persuratan');
$id=(int)($_GET['id']??0);$st=db()->prepare("SELECT s.*,p.nama pencatat FROM surat s LEFT JOIN pengguna p ON p.id_pengguna=s.id_pengguna WHERE s.id_surat=?");$st->execute([$id]);$s=$st->fetch();
if(!$s){set_flash('danger','Surat tidak ditemukan.');redirect('persuratan.php');}
$pageTitle='Detail Surat';$activeMenu='persuratan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('persuratan.php?jenis='.$s['jenis'])) ?>">← Kembali ke Persuratan</a><h1>Detail Surat</h1><p>Informasi lengkap yang tersimpan dalam sistem.</p></div><a class="btn btn-primary" href="<?= e(url('surat_form.php?id='.$id)) ?>">Edit Surat</a></div>
<section class="panel detail-panel"><div class="detail-title"><div><span class="eyebrow"><?= strtoupper(e($s['jenis']))==='MASUK'?'SURAT MASUK':'SURAT KELUAR' ?></span><h2><?= e($s['perihal']) ?></h2><p class="mono muted"><?= e($s['nomor_surat']) ?></p></div><?= status_badge($s['status']) ?></div>
<div class="detail-grid"><div class="detail-item"><span>Tanggal surat</span><strong><?= e(tanggal_id($s['tanggal'])) ?></strong></div><?php if($s['jenis']==='masuk'): ?><div class="detail-item"><span>Tanggal diterima</span><strong><?= e(tanggal_id($s['tanggal_diterima'])) ?></strong></div><?php endif; ?><div class="detail-item"><span><?= $s['jenis']==='masuk'?'Pengirim':'Tujuan' ?></span><strong><?= e($s['pengirim_tujuan']) ?></strong></div><div class="detail-item"><span>Jenis surat</span><strong><?= e($s['jenis_surat']) ?></strong></div><div class="detail-item"><span>Keterangan</span><strong><?= e($s['keterangan']) ?></strong></div><div class="detail-item"><span>Dicatat oleh</span><strong><?= e($s['pencatat']??'-') ?></strong></div></div>
<?php if($s['berkas']): ?><div class="file-card"><span class="file-icon">PDF</span><div><strong><?= e(basename($s['berkas'])) ?></strong><small>Dokumen arsip surat</small></div><a class="btn btn-outline btn-sm" target="_blank" href="<?= e(url('uploads/'.$s['berkas'])) ?>">Buka Dokumen ↗</a></div><?php endif; ?>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
