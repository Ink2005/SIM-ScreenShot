<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('kegiatan');
$pdo=db();$id=(int)($_GET['id']??0);
$ev=['nama_kegiatan'=>'','jenis_kegiatan'=>'Program kerja','tanggal_mulai'=>date('Y-m-d'),'tanggal_selesai'=>date('Y-m-d'),'lokasi'=>'','status'=>'Direncanakan','deskripsi'=>'','evaluasi'=>''];
$selected=[];
if($id){$st=$pdo->prepare("SELECT * FROM kegiatan WHERE id_kegiatan=?");$st->execute([$id]);$ev=$st->fetch();if(!$ev){set_flash('danger','Kegiatan tidak ditemukan.');redirect('kegiatan.php');}$ps=$pdo->prepare("SELECT id_anggota FROM kegiatan_peserta WHERE id_kegiatan=?");$ps->execute([$id]);$selected=array_map('intval',$ps->fetchAll(PDO::FETCH_COLUMN));}
$members=$pdo->query("SELECT id_anggota,nim,nama,divisi FROM anggota WHERE LOWER(status)='aktif' ORDER BY nama")->fetchAll();$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();foreach(array_keys($ev) as $k)if(isset($_POST[$k]))$ev[$k]=trim((string)$_POST[$k]);
 $selected=array_map('intval',$_POST['peserta']??[]);
 if($ev['nama_kegiatan']==='')$errors[]='Nama kegiatan wajib diisi.';
 if($ev['tanggal_mulai']==='')$errors[]='Tanggal mulai wajib diisi.';
 if($ev['tanggal_selesai']!==''&&$ev['tanggal_selesai']<$ev['tanggal_mulai'])$errors[]='Tanggal selesai tidak boleh sebelum tanggal mulai.';
 if(!$errors){
  if($id){$pdo->prepare("UPDATE kegiatan SET nama_kegiatan=?,jenis_kegiatan=?,tanggal_mulai=?,tanggal_selesai=?,lokasi=?,status=?,deskripsi=?,evaluasi=? WHERE id_kegiatan=?")->execute([$ev['nama_kegiatan'],$ev['jenis_kegiatan'],$ev['tanggal_mulai'],$ev['tanggal_selesai']?:null,$ev['lokasi'],$ev['status'],$ev['deskripsi'],$ev['evaluasi'],$id]);}
  else{$pdo->prepare("INSERT INTO kegiatan(nama_kegiatan,jenis_kegiatan,tanggal_mulai,tanggal_selesai,lokasi,status,deskripsi,evaluasi,id_pengguna) VALUES(?,?,?,?,?,?,?,?,?)")->execute([$ev['nama_kegiatan'],$ev['jenis_kegiatan'],$ev['tanggal_mulai'],$ev['tanggal_selesai']?:null,$ev['lokasi'],$ev['status'],$ev['deskripsi'],$ev['evaluasi'],current_user()['id_pengguna']]);$id=(int)$pdo->lastInsertId();}
  $pdo->prepare("DELETE FROM kegiatan_peserta WHERE id_kegiatan=?")->execute([$id]);
  $ins=$pdo->prepare("INSERT IGNORE INTO kegiatan_peserta(id_kegiatan,id_anggota) VALUES(?,?)");foreach($selected as $memberId)$ins->execute([$id,$memberId]);
  set_flash('success','Data kegiatan dan peserta berhasil disimpan.');redirect('kegiatan.php');
 }
}
$pageTitle=$id?'Detail / Edit Kegiatan':'Tambah Kegiatan';$activeMenu='kegiatan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('kegiatan.php')) ?>">← Kembali ke Kegiatan</a><h1><?= e($pageTitle) ?></h1><p>Catat rencana, pelaksanaan, peserta, dan evaluasi kegiatan.</p></div></div>
<?php if($errors): ?><div class="alert alert-danger"><ul><?php foreach($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="panel form-panel"><?= csrf_field() ?><div class="panel-heading"><div><h2>Informasi Kegiatan</h2><p>Modul kegiatan disusun berdasarkan ruang lingkup proposal.</p></div></div>
<div class="form-grid"><label class="span-2">Nama kegiatan *<input name="nama_kegiatan" value="<?= e($ev['nama_kegiatan']) ?>" required placeholder="Contoh: Rapat Kerja Organisasi"></label>
<label>Jenis kegiatan<select name="jenis_kegiatan"><?php foreach(['Program kerja','Rapat','Pelatihan','Seminar','Sosial','Lainnya'] as $v): ?><option <?= $ev['jenis_kegiatan']===$v?'selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select></label>
<label>Status<select name="status"><?php foreach(['Direncanakan','Berlangsung','Selesai','Dibatalkan'] as $v): ?><option <?= $ev['status']===$v?'selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select></label>
<label>Tanggal mulai *<input type="date" name="tanggal_mulai" value="<?= e($ev['tanggal_mulai']) ?>" required></label>
<label>Tanggal selesai<input type="date" name="tanggal_selesai" value="<?= e($ev['tanggal_selesai']) ?>"></label>
<label class="span-2">Lokasi<input name="lokasi" value="<?= e($ev['lokasi']) ?>" placeholder="Lokasi kegiatan"></label>
<label class="span-2">Deskripsi / rencana kegiatan<textarea name="deskripsi" rows="3"><?= e($ev['deskripsi']) ?></textarea></label>
<label class="span-2">Evaluasi kegiatan<textarea name="evaluasi" rows="3" placeholder="Isi setelah kegiatan dilaksanakan"><?= e($ev['evaluasi']) ?></textarea></label></div>
<div class="subsection-heading"><div><h3>Peserta Kegiatan</h3><p>Pilih anggota yang mengikuti kegiatan.</p></div><span class="muted small"><?= count($members) ?> anggota aktif</span></div>
<div class="participant-grid"><?php foreach($members as $m): ?><label class="participant-option"><input type="checkbox" name="peserta[]" value="<?= (int)$m['id_anggota'] ?>" <?= in_array((int)$m['id_anggota'],$selected,true)?'checked':'' ?>><span><strong><?= e($m['nama']) ?></strong><small><?= e($m['nim']) ?> · <?= e($m['divisi']) ?></small></span></label><?php endforeach; ?></div>
<div class="form-actions"><a class="btn btn-ghost" href="<?= e(url('kegiatan.php')) ?>">Batal</a><button class="btn btn-primary">Simpan Kegiatan</button></div></form>
<?php include __DIR__.'/includes/footer.php'; ?>
