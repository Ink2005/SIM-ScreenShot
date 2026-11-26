<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('keuangan');
$pdo=db();$id=(int)($_GET['id']??0);
$t=['tanggal'=>date('Y-m-d'),'jenis'=>'Pemasukan','kategori'=>'Iuran anggota','keterangan'=>'','nominal'=>'','bukti'=>null];
if($id){$st=$pdo->prepare("SELECT * FROM transaksi_kas WHERE id_transaksi=?");$st->execute([$id]);$t=$st->fetch();if(!$t){set_flash('danger','Transaksi tidak ditemukan.');redirect('keuangan.php');}}
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    foreach(['tanggal','jenis','kategori','keterangan','nominal'] as $k)$t[$k]=trim((string)($_POST[$k]??''));
    if(!$t['tanggal']||!$t['jenis']||!$t['kategori']||!$t['keterangan'])$errors[]='Semua kolom wajib diisi.';
    if(!is_numeric($t['nominal'])||(float)$t['nominal']<=0)$errors[]='Nominal harus berupa angka lebih besar dari nol.';
    $newFile=null;
    if(!$errors){try{$newFile=upload_file('bukti','bukti',['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png']);}catch(Throwable $ex){$errors[]=$ex->getMessage();}}
    if(!$errors){
        $file=$newFile?:($t['bukti']??null);
        if($id){$pdo->prepare("UPDATE transaksi_kas SET tanggal=?,jenis=?,kategori=?,keterangan=?,nominal=?,bukti=? WHERE id_transaksi=?")->execute([$t['tanggal'],$t['jenis'],$t['kategori'],$t['keterangan'],(float)$t['nominal'],$file,$id]);if($newFile)remove_upload($t['bukti']);}
        else{$pdo->prepare("INSERT INTO transaksi_kas(tanggal,jenis,kategori,keterangan,nominal,bukti,id_pengguna) VALUES(?,?,?,?,?,?,?)")->execute([$t['tanggal'],$t['jenis'],$t['kategori'],$t['keterangan'],(float)$t['nominal'],$file,current_user()['id_pengguna']]);}
        set_flash('success',$id?'Transaksi berhasil diperbarui.':'Transaksi berhasil dicatat.');redirect('keuangan.php');
    }
}
$balance=(float)$pdo->query("SELECT COALESCE(SUM(CASE WHEN LOWER(jenis)='pemasukan' THEN nominal ELSE -nominal END),0) FROM transaksi_kas")->fetchColumn();
$pageTitle=$id?'Edit Transaksi':'Tambah Transaksi';$activeMenu='keuangan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><a class="back-link" href="<?= e(url('keuangan.php')) ?>">← Kembali ke Keuangan</a><h1><?= e($pageTitle) ?></h1><p>Lengkapi informasi transaksi kas organisasi.</p></div></div>
<?php if($errors): ?><div class="alert alert-danger"><ul><?php foreach($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel form-panel"><?= csrf_field() ?>
<div class="panel-heading"><div><h2>Informasi Transaksi</h2><p>Pastikan nominal dan bukti sesuai dengan transaksi yang dicatat.</p></div><div class="balance-chip">Saldo kas saat ini <strong><?= e(rupiah($balance)) ?></strong></div></div>
<div class="form-grid">
<label>Tanggal transaksi *<input type="date" name="tanggal" value="<?= e($t['tanggal']) ?>" required></label>
<label>Jenis transaksi *<select name="jenis"><option <?= $t['jenis']==='Pemasukan'?'selected':'' ?>>Pemasukan</option><option <?= $t['jenis']==='Pengeluaran'?'selected':'' ?>>Pengeluaran</option></select></label>
<label>Kategori *<select name="kategori"><?php foreach(['Iuran anggota','Donasi','Dana kegiatan','Perlengkapan','Administrasi','Transportasi','Konsumsi','Lainnya'] as $c): ?><option value="<?= e($c) ?>" <?= $t['kategori']===$c?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?></select></label>
<label>Nominal (Rp) *<input type="number" name="nominal" min="1" step="1" value="<?= e($t['nominal']) ?>" placeholder="Contoh: 1200000" required></label>
<label class="span-2">Keterangan *<input name="keterangan" value="<?= e($t['keterangan']) ?>" required placeholder="Contoh: Iuran anggota November"></label>
<label class="span-2">Bukti transaksi (PDF/JPG/PNG, maks. 10 MB)<input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png"><?php if(!empty($t['bukti'])): ?><small class="muted">File saat ini: <?= e(basename($t['bukti'])) ?></small><?php endif; ?></label>
</div><div class="form-actions"><a class="btn btn-ghost" href="<?= e(url('keuangan.php')) ?>">Batal</a><button class="btn btn-primary">Simpan Transaksi</button></div>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
