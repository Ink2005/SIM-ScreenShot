<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('laporan');
$pdo=db();$tab=$_GET['tab']??'anggota';if(!in_array($tab,['anggota','kegiatan','keuangan','persuratan'],true))$tab='anggota';
$from=$_GET['from']??date('Y-01-01');$to=$_GET['to']??date('Y-m-d');
$rows=[];$headers=[];$summary=[];
if($tab==='anggota'){
 $headers=['NIM','Nama Anggota','Program Studi','Angkatan','Divisi','Status'];
 $st=$pdo->prepare("SELECT nim,nama,program_studi,angkatan,divisi,status FROM anggota WHERE (tanggal_bergabung IS NULL OR tanggal_bergabung BETWEEN ? AND ?) ORDER BY nama");$st->execute([$from,$to]);$rows=$st->fetchAll();
 $summary=['Total Anggota'=>count($rows),'Aktif'=>count(array_filter($rows,fn($r)=>strtolower($r['status'])==='aktif')),'Divisi'=>count(array_unique(array_column($rows,'divisi')))];
}elseif($tab==='kegiatan'){
 $headers=['Nama Kegiatan','Jenis','Tanggal Mulai','Tanggal Selesai','Lokasi','Peserta','Status'];
 $st=$pdo->prepare("SELECT k.nama_kegiatan,k.jenis_kegiatan,k.tanggal_mulai,k.tanggal_selesai,k.lokasi,k.status,COUNT(kp.id_anggota) peserta FROM kegiatan k LEFT JOIN kegiatan_peserta kp ON kp.id_kegiatan=k.id_kegiatan WHERE k.tanggal_mulai BETWEEN ? AND ? GROUP BY k.id_kegiatan ORDER BY k.tanggal_mulai");$st->execute([$from,$to]);$rows=$st->fetchAll();
 $summary=['Total Kegiatan'=>count($rows),'Selesai'=>count(array_filter($rows,fn($r)=>$r['status']==='Selesai')),'Total Peserta'=>array_sum(array_column($rows,'peserta'))];
}elseif($tab==='keuangan'){
 $headers=['Tanggal','Jenis','Kategori','Keterangan','Nominal'];
 $st=$pdo->prepare("SELECT tanggal,jenis,kategori,keterangan,nominal FROM transaksi_kas WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal");$st->execute([$from,$to]);$rows=$st->fetchAll();
 $summary=$pdo->prepare("SELECT COALESCE(SUM(CASE WHEN LOWER(jenis)='pemasukan' THEN nominal ELSE 0 END),0) pemasukan,COALESCE(SUM(CASE WHEN LOWER(jenis)='pengeluaran' THEN nominal ELSE 0 END),0) pengeluaran FROM transaksi_kas WHERE tanggal BETWEEN ? AND ?");$summary->execute([$from,$to]);$x=$summary->fetch();$summary=['Pemasukan'=>rupiah($x['pemasukan']),'Pengeluaran'=>rupiah($x['pengeluaran']),'Saldo'=>rupiah($x['pemasukan']-$x['pengeluaran'])];
}else{
 $headers=['Nomor Surat','Jenis','Tanggal','Pengirim / Tujuan','Perihal','Kategori','Status'];
 $st=$pdo->prepare("SELECT nomor_surat,jenis,tanggal,pengirim_tujuan,perihal,jenis_surat,status FROM surat WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal");$st->execute([$from,$to]);$rows=$st->fetchAll();
 $summary=['Total Surat'=>count($rows),'Surat Masuk'=>count(array_filter($rows,fn($r)=>$r['jenis']==='masuk')),'Surat Keluar'=>count(array_filter($rows,fn($r)=>$r['jenis']==='keluar'))];
}
if(($_GET['export']??'')==='csv'){
 header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="laporan-'.$tab.'-'.$from.'-'.$to.'.csv"');
 $out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,$headers,';');
 foreach($rows as $row){$line=[];foreach($headers as $idx=>$h){$keys=array_keys($row);$line[]=$row[$keys[$idx]]??'';}fputcsv($out,$line,';');}fclose($out);exit;
}
$labels=['anggota'=>'Keanggotaan','kegiatan'=>'Kegiatan','keuangan'=>'Keuangan','persuratan'=>'Persuratan'];
$pageTitle='Pusat Laporan';$activeMenu='laporan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><span class="eyebrow">INFORMASI MANAJEMEN</span><h1>Pusat Laporan</h1><p>Ubah data organisasi menjadi informasi yang siap dibagikan.</p></div><div class="heading-actions"><button class="btn btn-outline" onclick="window.print()">Cetak Laporan</button><a class="btn btn-primary" href="<?= e(url('laporan.php?'.http_build_query(['tab'=>$tab,'from'=>$from,'to'=>$to,'export'=>'csv']))) ?>">Export CSV / Excel</a></div></div>
<section class="panel report-panel">
<div class="tabs report-tabs"><?php foreach($labels as $key=>$label): ?><a class="<?= $tab===$key?'active':'' ?>" href="<?= e(url('laporan.php?tab='.$key.'&from='.$from.'&to='.$to)) ?>"><?= e($label) ?></a><?php endforeach; ?></div>
<form class="filter-bar report-filter" method="get"><input type="hidden" name="tab" value="<?= e($tab) ?>"><label>Tanggal mulai<input type="date" name="from" value="<?= e($from) ?>"></label><label>Tanggal akhir<input type="date" name="to" value="<?= e($to) ?>"></label><button class="btn btn-primary">Tampilkan Laporan</button></form>
<div class="report-sheet"><div class="report-brand"><img class="brand-logo" src="<?= e(url('assets/images/logo-screenshot-mark-black-transparent.png')) ?>" alt="Logo SCREENSHOT"><div><strong>SIM SCREENSHOT</strong><small>Sistem Informasi Manajemen</small></div></div><div class="report-title"><h2>LAPORAN <?= strtoupper(e($labels[$tab])) ?></h2><p>Periode <?= e(tanggal_id($from)) ?> – <?= e(tanggal_id($to)) ?></p></div>
<div class="report-summary"><?php foreach($summary as $label=>$value): ?><div><span><?= e($label) ?></span><strong><?= is_numeric($value)?e((string)$value):e($value) ?></strong></div><?php endforeach; ?></div>
<div class="table-wrap"><table><thead><tr><?php foreach($headers as $h): ?><th><?= e($h) ?></th><?php endforeach; ?></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="<?= count($headers) ?>"><div class="empty-state">Tidak ada data pada periode yang dipilih.</div></td></tr><?php else: foreach($rows as $row): ?><tr><?php foreach(array_values($row) as $value): ?><td><?= e((string)$value) ?></td><?php endforeach; ?></tr><?php endforeach; endif; ?>
</tbody></table></div><div class="report-foot">Diterbitkan <?= e(tanggal_id(date('Y-m-d'))) ?> · SIM SCREENSHOT</div></div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
