<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('persuratan');
$pdo=db();$id=(int)($_GET['id']??0);$kind=($_GET['jenis']??'masuk')==='keluar'?'keluar':'masuk';
$s=['nomor_surat'=>'','jenis'=>$kind,'tanggal'=>date('Y-m-d'),'tanggal_diterima'=>date('Y-m-d'),'pengirim_tujuan'=>'','jenis_surat'=>'Pemberitahuan','perihal'=>'','keterangan'=>'','status'=>$kind==='masuk'?'Diarsipkan':'Terkirim','berkas'=>null];
if($id){$st=$pdo->prepare("SELECT * FROM surat WHERE id_surat=?");$st->execute([$id]);$s=$st->fetch();if(!$s){set_flash('danger','Surat tidak ditemukan.');redirect('persuratan.php');}$kind=$s['jenis'];}
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();foreach(['nomor_surat','tanggal','tanggal_diterima','pengirim_tujuan','jenis_surat','perihal','keterangan','status'] as $k)$s[$k]=trim((string)($_POST[$k]??''));
 $s['jenis']=$kind;
 foreach(['nomor_surat','tanggal','pengirim_tujuan','jenis_surat','perihal','keterangan'] as $k)if($s[$k]==='')$errors[]='Kolom '.str_replace('_',' ',$k).' wajib diisi.';
 $dup=$pdo->prepare("SELECT COUNT(*) FROM surat WHERE nomor_surat=? AND id_surat<>?");$dup->execute([$s['nomor_surat'],$id]);if((int)$dup->fetchColumn())$errors[]='Nomor surat sudah digunakan.';
 $newFile=null;if(!$errors){try{$newFile=upload_file('berkas','surat',['application/pdf'=>'pdf']);}catch(Throwable $ex){$errors[]=$ex->getMessage();}}
 if(!$id&&!$newFile&&!$s['berkas'])$errors[]='File surat PDF wajib diunggah.';
 if(!$errors){$file=$newFile?:($s['berkas']??null);
  if($id){$pdo->prepare("UPDATE surat SET nomor_surat=?,jenis=?,tanggal=?,tanggal_diterima=?,pengirim_tujuan=?,jenis_surat=?,perihal=?,keterangan=?,status=?,berkas=? WHERE id_surat=?")->execute([$s['nomor_surat'],$kind,$s['tanggal'],$kind==='masuk'?($s['tanggal_diterima']?:null):null,$s['pengirim_tujuan'],$s['jenis_surat'],$s['perihal'],$s['keterangan'],$s['status'],$file,$id]);if($newFile)remove_upload($s['berkas']);}
  else{$pdo->prepare("INSERT INTO surat(nomor_surat,jenis,tanggal,tanggal_diterima,pengirim_tujuan,jenis_surat,perihal,keterangan,status,berkas,id_pengguna) VALUES(?,?,?,?,?,?,?,?,?,?,?)")->execute([$s['nomor_surat'],$kind,$s['tanggal'],$kind==='masuk'?($s['tanggal_diterima']?:null):null,$s['pengirim_tujuan'],$s['jenis_surat'],$s['perihal'],$s['keterangan'],$s['status'],$file,current_user()['id_pengguna']]);}
  set_flash('success',$id?'Surat berhasil diperbarui.':'Surat berhasil diarsipkan.');redirect('persuratan.php?jenis='.$kind);
 }
}
$pageTitle=$id?'Edit Surat':($kind==='masuk'?'Tambah Surat Masuk':'Tambah Surat Keluar');$activeMenu='persuratan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('persuratan.php?jenis='.$kind)) ?>">← Kembali ke Persuratan</a><h1><?= e($pageTitle) ?></h1><p>Lengkapi informasi di bawah. Kolom bertanda * wajib diisi.</p></div></div>
<?php if($errors): ?><div class="alert alert-danger"><ul><?php foreach($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel form-panel"><?= csrf_field() ?><div class="panel-heading"><div><h2>Informasi Surat <?= $kind==='masuk'?'Masuk':'Keluar' ?></h2><p>Dokumen disimpan sebagai arsip digital organisasi.</p></div><span class="badge badge-info">PDF · Maks. 10 MB</span></div>
<div class="form-grid">
<label>Nomor surat *<input name="nomor_surat" value="<?= e($s['nomor_surat']) ?>" required placeholder="Contoh: 048/SM/XI/2026"></label>
<label>Tanggal surat *<input type="date" name="tanggal" value="<?= e($s['tanggal']) ?>" required></label>
<label><?= $kind==='masuk'?'Pengirim':'Tujuan surat' ?> *<input name="pengirim_tujuan" value="<?= e($s['pengirim_tujuan']) ?>" required placeholder="<?= $kind==='masuk'?'Nama instansi/pengirim':'Nama penerima/instansi' ?>"></label>
<label>Jenis surat *<select name="jenis_surat"><?php foreach(['Pemberitahuan','Permohonan','Keputusan','Persetujuan','Laporan','Permintaan','Lainnya'] as $c): ?><option <?= $s['jenis_surat']===$c?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?></select></label>
<?php if($kind==='masuk'): ?><label>Tanggal diterima *<input type="date" name="tanggal_diterima" value="<?= e($s['tanggal_diterima']) ?>"></label><?php endif; ?>
<label>Status<select name="status"><?php foreach(($kind==='masuk'?['Diarsipkan','Diproses']:['Terkirim','Diarsipkan']) as $st): ?><option <?= $s['status']===$st?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select></label>
<label class="span-2">Perihal *<input name="perihal" value="<?= e($s['perihal']) ?>" required placeholder="Masukkan perihal surat"></label>
<label class="span-2">Keterangan *<textarea name="keterangan" rows="3" required placeholder="Keterangan tambahan"><?= e($s['keterangan']) ?></textarea></label>
<label class="span-2">File surat <?= $id?'(unggah PDF baru jika ingin mengganti)':'*' ?><input type="file" name="berkas" accept=".pdf,application/pdf" <?= !$id?'required':'' ?>><?php if($s['berkas']): ?><small class="muted">File saat ini: <?= e(basename($s['berkas'])) ?></small><?php endif; ?></label>
</div><div class="form-actions"><a class="btn btn-ghost" href="<?= e(url('persuratan.php?jenis='.$kind)) ?>">Batal</a><button class="btn btn-primary">Simpan Surat</button></div></form>
<?php include __DIR__.'/includes/footer.php'; ?>
