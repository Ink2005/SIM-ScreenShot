<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) redirect('dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $identifier = trim((string)($_POST['identifier'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        $error = 'Username/email dan password wajib diisi.';
    } else {
        $stmt = db()->prepare("SELECT id_pengguna, nama, username, email, kata_sandi, peran, status_aktif FROM pengguna WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute(['username' => $identifier, 'email' => $identifier]);
        $user = $stmt->fetch();

        $valid = false;
        if ($user && (int)($user['status_aktif'] ?? 1) === 1) {
            $stored = (string)$user['kata_sandi'];
            if (password_verify($password, $stored)) {
                $valid = true;
            } elseif (preg_match('/^[a-f0-9]{64}$/i', $stored) && hash_equals(strtolower($stored), hash('sha256', $password))) {
                // Kompatibilitas sementara untuk hash SHA-256 dari dump SQL lama.
                $valid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                db()->prepare("UPDATE pengguna SET kata_sandi = ? WHERE id_pengguna = ?")->execute([$newHash, $user['id_pengguna']]);
            }
        }

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id_pengguna' => (int)$user['id_pengguna'],
                'nama' => $user['nama'],
                'username' => $user['username'],
                'email' => $user['email'] ?? '',
                'peran' => $user['peran'],
            ];

            // One-time marker: the welcome animation is shown only after a successful login.
            $_SESSION['login_transition_pending'] = time();
            redirect('welcome.php');
        }
        $error = 'Akun tidak ditemukan atau password tidak sesuai.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · SIM SCREENSHOT</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>?v=0.4.8-login-flow">
</head>
<body class="login-body">
<div class="login-wrap">
    <section class="login-intro">
        <svg class="login-flow-lines" viewBox="0 0 252 900" preserveAspectRatio="none" aria-hidden="true" focusable="false">
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
        <div class="brand brand-light"><img class="brand-logo" src="<?= e(url('assets/images/logo-screenshot-mark-white-transparent.png')) ?>" alt="Logo SCREENSHOT"><span><strong>SIM SCREENSHOT</strong><small>Sistem Informasi Manajemen</small></span></div>
        <div class="intro-copy">
            <span class="eyebrow">SATU SISTEM. LEBIH TERORGANISIR.</span>
            <h1>Sistem Informasi<br>Manajemen</h1>
            <p>Kelola keanggotaan, kegiatan, keuangan, dan persuratan dalam satu ruang kerja yang terpusat.</p>
        </div>
        <div class="intro-foot">SCREENSHOT<br><span>Ruang kolaborasi, informasi terintegrasi.</span></div>
    </section>
    <section class="login-panel">
        <div class="login-card">
            <span class="eyebrow red">AKSES PENGURUS</span>
            <h2>Selamat Datang</h2>
            <p class="muted">Silakan masuk untuk mengakses sistem.</p>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="form-stack">
                <?= csrf_field() ?>
                <label>Username atau email
                    <input type="text" name="identifier" placeholder="Masukkan username atau email" autocomplete="username" required>
                </label>
                <label>Password
                    <input type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                </label>
                <div class="login-options"><label class="check-label"><input type="checkbox" name="remember"> Ingat saya</label><span class="muted small">Hubungi administrator jika lupa password.</span></div>
                <button class="btn btn-primary btn-block" type="submit">Masuk <span>→</span></button>
            </form>
            <div class="login-note">Khusus pengurus organisasi SCREENSHOT</div>
        </div>
        <div class="login-copyright">© <?= date('Y') ?> SIM SCREENSHOT. Seluruh hak dilindungi.</div>
    </section>
</div>
<script src="<?= e(url('assets/js/app.js')) ?>?v=0.4.8-login-flow"></script>
</body>
</html>
