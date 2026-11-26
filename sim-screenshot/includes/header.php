<?php
$pageTitle = $pageTitle ?? 'Dashboard';
$activeMenu = $activeMenu ?? 'dashboard';
$user = current_user();
$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · SIM SCREENSHOT</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>?v=0.4.8-login-flow">
</head>
<body class="app-body">
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <svg class="sidebar-flow-lines" viewBox="0 0 252 900" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <g class="flow-line flow-red">
                <path class="flow-line-base" d="M-35 118 C62 160 220 205 155 292 S22 405 105 495 S250 625 155 735 S30 830 100 930"/>
                <path class="flow-line-run" pathLength="1000" d="M-35 118 C62 160 220 205 155 292 S22 405 105 495 S250 625 155 735 S30 830 100 930"/>
            </g>
            <g class="flow-line flow-white">
                <path class="flow-line-base" d="M285 70 C205 155 45 195 95 285 S245 415 165 510 S12 650 90 745 S235 850 175 930"/>
                <path class="flow-line-run" pathLength="1000" d="M285 70 C205 155 45 195 95 285 S245 415 165 510 S12 650 90 745 S235 850 175 930"/>
            </g>
            <g class="flow-line flow-blue">
                <path class="flow-line-base" d="M-45 320 C45 265 185 310 225 405 S130 545 45 600 S-5 750 100 810 S245 850 280 930"/>
                <path class="flow-line-run" pathLength="1000" d="M-45 320 C45 265 185 310 225 405 S130 545 45 600 S-5 750 100 810 S245 850 280 930"/>
            </g>
            <g class="flow-line flow-yellow">
                <path class="flow-line-base" d="M280 260 C180 300 75 365 105 445 S235 565 180 655 S35 760 65 835 S195 895 240 940"/>
                <path class="flow-line-run" pathLength="1000" d="M280 260 C180 300 75 365 105 445 S235 565 180 655 S35 760 65 835 S195 895 240 940"/>
            </g>
        </svg>
        <a class="brand" href="<?= e(url('dashboard.php')) ?>">
            <img class="brand-logo" src="<?= e(url('assets/images/logo-screenshot-mark-white-transparent.png')) ?>" alt="Logo SCREENSHOT">
            <span><strong>SIM SCREENSHOT</strong><small>Sistem Informasi Manajemen</small></span>
        </a>
        <div class="sidebar-label">RUANG KERJA</div>
        <nav class="nav-menu">
            <?php
            $menus = [
                'dashboard' => ['Dashboard', '▦', 'dashboard.php', 'dashboard'],
                'anggota' => ['Keanggotaan', '♙', 'anggota.php', 'anggota'],
                'kegiatan' => ['Kegiatan', '◷', 'kegiatan.php', 'kegiatan'],
                'keuangan' => ['Keuangan', 'Rp', 'keuangan.php', 'keuangan'],
                'persuratan' => ['Persuratan', '▤', 'persuratan.php', 'persuratan'],
                'laporan' => ['Laporan', '▥', 'laporan.php', 'laporan'],
            ];
            foreach ($menus as $key => [$label, $icon, $href, $permission]):
                if (!can_access($permission)) continue;
            ?>
                <a class="nav-link <?= $activeMenu === $key ? 'active' : '' ?>" href="<?= e(url($href)) ?>">
                    <span class="nav-icon"><?= e($icon) ?></span><span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <div class="avatar"><?= e(strtoupper(substr($user['nama'] ?? 'A', 0, 1))) ?></div>
                <div><strong><?= e($user['nama'] ?? 'Pengguna') ?></strong><small><?= e($user['peran'] ?? '') ?></small></div>
            </div>
            <form class="logout-form" method="post" action="<?= e(url('logout.php')) ?>">
                <?= csrf_field() ?>
                <button class="logout-link" type="submit">↪ Keluar</button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <button class="mobile-menu" id="menuToggle" type="button" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">☰</button>
            <div class="breadcrumb">SCREENSHOT <span>/</span> <?= e($pageTitle) ?></div>
            <div class="topbar-right">
                <span class="date-label"><?= e(date('l, d M Y')) ?></span>
                <span class="top-user"><?= e($user['nama'] ?? 'Admin') ?></span>
            </div>
        </header>
        <main class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status">
                    <?= e($flash['message']) ?>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endif; ?>
