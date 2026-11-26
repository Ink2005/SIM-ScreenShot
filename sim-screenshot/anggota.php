<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('anggota');

$pdo = db();
$q = trim((string)($_GET['q'] ?? ''));
$divisi = trim((string)($_GET['divisi'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$angkatan = trim((string)($_GET['angkatan'] ?? ''));
$where = []; $params = [];
if ($q !== '') { $where[] = "(nama LIKE ? OR nim LIKE ? OR email LIKE ?)"; array_push($params, "%$q%", "%$q%", "%$q%"); }
if ($divisi !== '') { $where[] = "divisi = ?"; $params[] = $divisi; }
if ($status !== '') { $where[] = "status = ?"; $params[] = $status; }
if ($angkatan !== '') { $where[] = "angkatan = ?"; $params[] = $angkatan; }
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$total = (int)$pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM anggota WHERE LOWER(status)='aktif'")->fetchColumn();
$inactive = $total - $active;
$divisionCounts = $pdo->query("SELECT divisi, COUNT(*) total FROM anggota GROUP BY divisi ORDER BY divisi")->fetchAll();
$divisions = ['Fotosin','Jurnalistik','DKV','Pengurus Harian'];
$years = $pdo->query("SELECT DISTINCT angkatan FROM anggota WHERE angkatan IS NOT NULL AND angkatan<>'' ORDER BY angkatan DESC")->fetchAll(PDO::FETCH_COLUMN);

$page = max(1, (int)($_GET['page'] ?? 1)); $perPage = 10;
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM anggota" . $whereSql); $countStmt->execute($params);
$filteredTotal = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($filteredTotal / $perPage)); $page = min($page, $pages); $offset = ($page-1)*$perPage;
$stmt = $pdo->prepare("SELECT * FROM anggota" . $whereSql . " ORDER BY nama ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params); $members = $stmt->fetchAll();

$pageTitle='Keanggotaan'; $activeMenu='anggota';
include __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><span class="eyebrow">DATA ORGANISASI</span><h1>Manajemen Keanggotaan</h1><p>Kelola data dan informasi seluruh anggota SCREENSHOT.</p></div><a class="btn btn-primary" href="<?= e(url('anggota_form.php')) ?>">＋ Tambah Anggota</a></div>
<div class="stats-grid stats-four compact-stats">
    <article class="stat-card"><span>Total Anggota</span><strong><?= $total ?></strong><small>Seluruh anggota terdaftar</small></article>
    <article class="stat-card"><span>Anggota Aktif</span><strong><?= $active ?></strong><small><?= $total?number_format($active/$total*100,1,',','.'):0 ?>% dari total anggota</small></article>
    <article class="stat-card"><span>Anggota Tidak Aktif</span><strong><?= $inactive ?></strong><small><?= $total?number_format($inactive/$total*100,1,',','.'):0 ?>% dari total anggota</small></article>
    <article class="stat-card"><span>Total Divisi</span><strong>4</strong><small>Divisi resmi SCREENSHOT</small></article>
</div>
<div class="division-cards">
<?php foreach ($divisions as $name): $count=0; foreach($divisionCounts as $d) if($d['divisi']===$name) $count=(int)$d['total']; ?>
    <div class="division-card"><span><?= e($name) ?></span><strong><?= $count ?></strong><small><?= $total?number_format($count/$total*100,1,',','.'):0 ?>% dari total</small></div>
<?php endforeach; ?>
</div>
<section class="panel">
    <form class="filter-bar" method="get">
        <input class="search-input" type="search" name="q" value="<?= e($q) ?>" placeholder="⌕ Cari nama, NIM, atau email...">
        <select name="divisi"><option value="">Semua Divisi</option><?php foreach($divisions as $d): ?><option <?= $divisi===$d?'selected':'' ?> value="<?= e($d) ?>"><?= e($d) ?></option><?php endforeach; ?></select>
        <select name="status"><option value="">Semua Status</option><option value="aktif" <?= $status==='aktif'?'selected':'' ?>>Aktif</option><option value="tidak aktif" <?= $status==='tidak aktif'?'selected':'' ?>>Tidak Aktif</option></select>
        <select name="angkatan"><option value="">Semua Angkatan</option><?php foreach($years as $y): ?><option value="<?= e($y) ?>" <?= $angkatan===$y?'selected':'' ?>><?= e($y) ?></option><?php endforeach; ?></select>
        <button class="btn btn-outline" type="submit">Filter</button>
        <a class="btn btn-ghost" href="<?= e(url('anggota.php')) ?>">Reset</a>
    </form>
    <div class="table-wrap">
        <table><thead><tr><th>No.</th><th>NIM</th><th>Nama Anggota</th><th>Program Studi</th><th>Angkatan</th><th>Divisi</th><th>Bergabung</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (!$members): ?><tr><td colspan="9"><div class="empty-state"><strong>Tidak ada anggota yang cocok</strong><span>Coba kata kunci lain atau atur ulang filter pencarian.</span></div></td></tr>
        <?php else: foreach($members as $i=>$m): ?>
        <tr><td><?= $offset+$i+1 ?></td><td class="mono"><?= e($m['nim']) ?></td><td><strong><?= e($m['nama']) ?></strong><small class="table-sub"><?= e($m['email'] ?? '') ?></small></td><td><?= e($m['program_studi'] ?: '-') ?></td><td><?= e($m['angkatan'] ?: '-') ?></td><td><?= e($m['divisi'] ?: '-') ?></td><td><?= e(tanggal_id($m['tanggal_bergabung'])) ?></td><td><?= status_badge($m['status']) ?></td><td><div class="action-links"><a href="<?= e(url('anggota_detail.php?id='.$m['id_anggota'])) ?>">Lihat</a><a href="<?= e(url('anggota_form.php?id='.$m['id_anggota'])) ?>">Edit</a><form method="post" action="<?= e(url('anggota_delete.php')) ?>" onsubmit="return confirm('Hapus data anggota ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id_anggota'] ?>"><button class="link-danger" type="submit">Hapus</button></form></div></td></tr>
        <?php endforeach; endif; ?>
        </tbody></table>
    </div>
    <div class="table-footer"><span>Menampilkan <?= $filteredTotal ? $offset+1 : 0 ?>–<?= min($offset+$perPage,$filteredTotal) ?> dari <?= $filteredTotal ?> anggota</span>
        <div class="pagination">
        <?php if($page>1): ?><a href="?<?= e(page_query(['page'=>$page-1])) ?>">‹</a><?php endif; ?>
        <span>Halaman <?= $page ?> / <?= $pages ?></span>
        <?php if($page<$pages): ?><a href="?<?= e(page_query(['page'=>$page+1])) ?>">›</a><?php endif; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
