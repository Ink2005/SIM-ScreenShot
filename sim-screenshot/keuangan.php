<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('keuangan');
$pdo=db();
$q=trim((string)($_GET['q']??''));$jenis=trim((string)($_GET['jenis']??''));$from=trim((string)($_GET['from']??''));$to=trim((string)($_GET['to']??''));
$summary=$pdo->query("SELECT COALESCE(SUM(CASE WHEN LOWER(jenis)='pemasukan' THEN nominal ELSE 0 END),0) pemasukan, COALESCE(SUM(CASE WHEN LOWER(jenis)='pengeluaran' THEN nominal ELSE 0 END),0) pengeluaran FROM transaksi_kas")->fetch();
$where=[];$params=[];
if($q!==''){$where[]="(keterangan LIKE ? OR kategori LIKE ?)";$params[]="%$q%";$params[]="%$q%";}
if($jenis!==''){$where[]="jenis=?";$params[]=$jenis;}
if($from!==''){$where[]="tanggal>=?";$params[]=$from;}
if($to!==''){$where[]="tanggal<=?";$params[]=$to;}
$sql="SELECT t.*,p.nama pencatat FROM transaksi_kas t LEFT JOIN pengguna p ON p.id_pengguna=t.id_pengguna".($where?' WHERE '.implode(' AND ',$where):'')." ORDER BY t.tanggal DESC,t.id_transaksi DESC";
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$pageTitle='Keuangan';$activeMenu='keuangan';include __DIR__.'/includes/header.php';
?>
<div class="page-heading"><div><span class="eyebrow">PENGELOLAAN KAS</span><h1>Manajemen Keuangan</h1><p>Catat dan pantau seluruh pemasukan serta pengeluaran organisasi.</p></div><a class="btn btn-primary" href="<?= e(url('transaksi_form.php')) ?>">＋ Tambah Transaksi</a></div>
<div class="stats-grid stats-three">
    <article class="stat-card"><span>Total Pemasukan</span><strong class="text-green"><?= e(rupiah($summary['pemasukan'])) ?></strong><small>Akumulasi pemasukan</small></article>
    <article class="stat-card"><span>Total Pengeluaran</span><strong class="text-red"><?= e(rupiah($summary['pengeluaran'])) ?></strong><small>Akumulasi pengeluaran</small></article>
    <article class="stat-card"><span>Saldo Kas Saat Ini</span><strong><?= e(rupiah((float)$summary['pemasukan']-(float)$summary['pengeluaran'])) ?></strong><small>Pemasukan dikurangi pengeluaran</small></article>
</div>
<section class="panel">
<form class="filter-bar" method="get"><input class="search-input" type="search" name="q" value="<?= e($q) ?>" placeholder="⌕ Cari keterangan atau kategori..."><select name="jenis"><option value="">Semua Jenis</option><option value="Pemasukan" <?= $jenis==='Pemasukan'?'selected':'' ?>>Pemasukan</option><option value="Pengeluaran" <?= $jenis==='Pengeluaran'?'selected':'' ?>>Pengeluaran</option></select><input type="date" name="from" value="<?= e($from) ?>" aria-label="Dari tanggal"><input type="date" name="to" value="<?= e($to) ?>" aria-label="Sampai tanggal"><button class="btn btn-outline">Terapkan</button><a class="btn btn-ghost" href="<?= e(url('keuangan.php')) ?>">Reset</a></form>
<div class="table-wrap"><table><thead><tr><th>No.</th><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Jenis Transaksi</th><th>Nominal</th><th>Pencatat</th><th>Aksi</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="8"><div class="empty-state"><strong>Belum ada transaksi</strong><span>Tambahkan pemasukan atau pengeluaran untuk mulai mencatat kas organisasi.</span></div></td></tr>
<?php else: foreach($rows as $i=>$t): ?><tr><td><?= $i+1 ?></td><td><?= e(tanggal_id($t['tanggal'])) ?></td><td><strong><?= e($t['keterangan']) ?></strong><?php if($t['bukti']): ?><small class="table-sub">Ada bukti transaksi</small><?php endif; ?></td><td><?= e($t['kategori']??'-') ?></td><td><?= status_badge($t['jenis']) ?></td><td class="money <?= strtolower($t['jenis'])==='pemasukan'?'text-green':'text-red' ?>"><?= strtolower($t['jenis'])==='pemasukan'?'+':'−' ?><?= e(rupiah($t['nominal'])) ?></td><td><?= e($t['pencatat']??'-') ?></td><td><div class="action-links"><a href="<?= e(url('transaksi_form.php?id='.$t['id_transaksi'])) ?>">Edit</a><form method="post" action="<?= e(url('transaksi_delete.php')) ?>" onsubmit="return confirm('Hapus transaksi ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$t['id_transaksi'] ?>"><button class="link-danger">Hapus</button></form></div></td></tr><?php endforeach; endif; ?>
</tbody></table></div><div class="table-footer"><span><?= count($rows) ?> transaksi ditemukan</span><span class="muted small">Saldo dihitung otomatis dari seluruh transaksi.</span></div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
