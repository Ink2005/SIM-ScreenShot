<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('dashboard');

$pdo = db();
$totalMembers = (int)$pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$activeMembers = (int)$pdo->query("SELECT COUNT(*) FROM anggota WHERE LOWER(status) = 'aktif'")->fetchColumn();
$inactiveMembers = $totalMembers - $activeMembers;
$income = (float)$pdo->query("SELECT COALESCE(SUM(nominal),0) FROM transaksi_kas WHERE LOWER(jenis)='pemasukan'")->fetchColumn();
$expense = (float)$pdo->query("SELECT COALESCE(SUM(nominal),0) FROM transaksi_kas WHERE LOWER(jenis)='pengeluaran'")->fetchColumn();
$balance = $income - $expense;
$totalLetters = (int)$pdo->query("SELECT COUNT(*) FROM surat")->fetchColumn();
$incoming = (int)$pdo->query("SELECT COUNT(*) FROM surat WHERE LOWER(jenis)='masuk'")->fetchColumn();
$outgoing = (int)$pdo->query("SELECT COUNT(*) FROM surat WHERE LOWER(jenis)='keluar'")->fetchColumn();
$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();

$divisions = $pdo->query("SELECT divisi, COUNT(*) AS total FROM anggota GROUP BY divisi ORDER BY total DESC")->fetchAll();
$recentTransactions = $pdo->query("SELECT t.*, p.nama AS pencatat FROM transaksi_kas t LEFT JOIN pengguna p ON p.id_pengguna=t.id_pengguna ORDER BY t.tanggal DESC, t.id_transaksi DESC LIMIT 5")->fetchAll();
$recentLetters = $pdo->query("SELECT s.* FROM surat s ORDER BY s.tanggal DESC, s.id_surat DESC LIMIT 5")->fetchAll();

$monthly = $pdo->query("SELECT DATE_FORMAT(tanggal,'%Y-%m') AS periode, SUM(CASE WHEN LOWER(jenis)='pemasukan' THEN nominal ELSE 0 END) AS pemasukan, SUM(CASE WHEN LOWER(jenis)='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran FROM transaksi_kas WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(tanggal,'%Y-%m') ORDER BY periode")->fetchAll();
$maxBar = 1;
foreach ($monthly as $m) $maxBar = max($maxBar, (float)$m['pemasukan'], (float)$m['pengeluaran']);

$pageTitle = 'Dashboard'; $activeMenu = 'dashboard';
include __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><span class="eyebrow">RUANG KERJA</span><h1>Selamat Datang, <?= e(current_user()['nama']) ?></h1><p>Berikut ringkasan informasi organisasi SCREENSHOT.</p></div>
    <a class="btn btn-outline" href="<?= e(url('laporan.php')) ?>">Lihat Laporan <span>→</span></a>
</div>

<div class="stats-grid stats-four">
    <article class="stat-card"><div class="stat-top"><span>Total Anggota</span><span class="stat-icon red-soft">♙</span></div><strong><?= number_format($totalMembers) ?></strong><small>Seluruh anggota terdaftar</small></article>
    <article class="stat-card"><div class="stat-top"><span>Anggota Aktif</span><span class="stat-icon green-soft">✓</span></div><strong><?= number_format($activeMembers) ?></strong><small><?= $totalMembers ? number_format($activeMembers/$totalMembers*100,1,',','.') : '0,0' ?>% dari total anggota</small></article>
    <article class="stat-card"><div class="stat-top"><span>Saldo Kas</span><span class="stat-icon blue-soft">Rp</span></div><strong class="stat-money"><?= e(rupiah($balance)) ?></strong><small>Pemasukan dikurangi pengeluaran</small></article>
    <article class="stat-card"><div class="stat-top"><span>Total Surat</span><span class="stat-icon amber-soft">▤</span></div><strong><?= number_format($totalLetters) ?></strong><small><?= $incoming ?> masuk · <?= $outgoing ?> keluar</small></article>
</div>

<div class="dashboard-grid">
    <section class="panel panel-large">
        <div class="panel-heading"><div><h2>Ringkasan Keuangan</h2><p>Pemasukan dan pengeluaran yang tercatat di sistem</p></div><a href="<?= e(url('keuangan.php')) ?>" class="text-link">Lihat transaksi →</a></div>
        <div class="finance-summary">
            <div><span>Total Pemasukan</span><strong class="text-green"><?= e(rupiah($income)) ?></strong></div>
            <div><span>Total Pengeluaran</span><strong class="text-red"><?= e(rupiah($expense)) ?></strong></div>
        </div>
        <div class="bar-chart">
            <?php if (!$monthly): ?><div class="empty-state compact">Belum ada transaksi untuk ditampilkan.</div><?php else: foreach ($monthly as $m): ?>
                <div class="bar-group">
                    <div class="bar-pair">
                        <span class="bar income-bar" style="height:<?= max(3, (float)$m['pemasukan']/$maxBar*100) ?>%" title="<?= e(rupiah($m['pemasukan'])) ?>"></span>
                        <span class="bar expense-bar" style="height:<?= max(3, (float)$m['pengeluaran']/$maxBar*100) ?>%" title="<?= e(rupiah($m['pengeluaran'])) ?>"></span>
                    </div>
                    <small><?= e(date('M y', strtotime($m['periode'].'-01'))) ?></small>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="chart-legend"><span><i class="legend-dot income-dot"></i>Pemasukan</span><span><i class="legend-dot expense-dot"></i>Pengeluaran</span></div>
    </section>

    <section class="panel">
        <div class="panel-heading"><div><h2>Keanggotaan</h2><p>Distribusi anggota per divisi</p></div><a href="<?= e(url('anggota.php')) ?>" class="text-link">Detail →</a></div>
        <?php if (!$divisions): ?><div class="empty-state compact">Belum ada data anggota.</div><?php else: foreach ($divisions as $d): $pct = $totalMembers ? $d['total']/$totalMembers*100 : 0; ?>
            <div class="division-row"><div class="division-meta"><strong><?= e($d['divisi'] ?: 'Belum ditentukan') ?></strong><span><?= (int)$d['total'] ?> <small><?= number_format($pct,1,',','.') ?>%</small></span></div><div class="progress"><span style="width:<?= $pct ?>%"></span></div></div>
        <?php endforeach; endif; ?>
        <div class="mini-stats"><div><strong><?= $activeMembers ?></strong><small>Aktif</small></div><div><strong><?= $inactiveMembers ?></strong><small>Tidak aktif</small></div><div><strong><?= $totalEvents ?></strong><small>Kegiatan</small></div></div>
    </section>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-heading"><div><h2>Transaksi Terbaru</h2><p>Aktivitas keuangan organisasi</p></div><a href="<?= e(url('keuangan.php')) ?>" class="text-link">Semua →</a></div>
        <?php if (!$recentTransactions): ?><div class="empty-state compact">Belum ada transaksi.</div><?php else: ?>
        <div class="simple-list">
            <?php foreach ($recentTransactions as $t): ?>
            <div class="simple-row"><span class="list-symbol <?= strtolower($t['jenis'])==='pemasukan'?'symbol-green':'symbol-red' ?>"><?= strtolower($t['jenis'])==='pemasukan'?'↙':'↗' ?></span><div class="simple-main"><strong><?= e($t['keterangan']) ?></strong><small><?= e(tanggal_id($t['tanggal'])) ?> · <?= e($t['kategori'] ?? '-') ?></small></div><strong class="<?= strtolower($t['jenis'])==='pemasukan'?'text-green':'text-red' ?>"><?= strtolower($t['jenis'])==='pemasukan'?'+':'−' ?><?= e(rupiah($t['nominal'])) ?></strong></div>
            <?php endforeach; ?>
        </div><?php endif; ?>
    </section>
    <section class="panel">
        <div class="panel-heading"><div><h2>Persuratan Terbaru</h2><p>Arsip surat organisasi</p></div><a href="<?= e(url('persuratan.php')) ?>" class="text-link">Semua →</a></div>
        <?php if (!$recentLetters): ?><div class="empty-state compact">Belum ada surat yang diarsipkan.</div><?php else: ?>
        <div class="simple-list">
            <?php foreach ($recentLetters as $s): ?>
            <div class="simple-row"><span class="list-symbol symbol-blue">▤</span><div class="simple-main"><strong><?= e($s['perihal']) ?></strong><small><?= e($s['nomor_surat']) ?> · <?= e(tanggal_id($s['tanggal'])) ?></small></div><?= status_badge($s['status'] ?? 'Diarsipkan') ?></div>
            <?php endforeach; ?>
        </div><?php endif; ?>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
