<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/database.php';

const BASE_URL = '/sim-screenshot';

function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . ($path !== '' ? '/' . ltrim($path, '/') : '/');
}

function redirect(string $path = ''): never
{
    header('Location: ' . url($path));
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Muat ulang halaman dan coba lagi.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function can_access(string $module): bool
{
    $user = current_user();
    if (!$user) return false;

    $permissions = [
        'dashboard'  => ['Admin', 'Ketua', 'Sekretaris', 'Bendahara'],
        'anggota'    => ['Admin', 'Ketua', 'Sekretaris'],
        'kegiatan'   => ['Admin', 'Ketua', 'Sekretaris'],
        'keuangan'   => ['Admin', 'Ketua', 'Bendahara'],
        'persuratan' => ['Admin', 'Ketua', 'Sekretaris'],
        'laporan'    => ['Admin', 'Ketua', 'Sekretaris', 'Bendahara'],
    ];

    return in_array($user['peran'] ?? '', $permissions[$module] ?? [], true);
}

function require_login(string $module = 'dashboard'): void
{
    if (!current_user()) {
        redirect();
    }
    if (!can_access($module)) {
        http_response_code(403);
        exit('<h1>403 - Akses ditolak</h1><p>Akun Anda tidak memiliki izin untuk membuka modul ini.</p><p><a href="' . e(url('dashboard.php')) . '">Kembali ke dashboard</a></p>');
    }
}

function rupiah(int|float|string|null $amount): string
{
    return 'Rp' . number_format((float)$amount, 0, ',', '.');
}

function tanggal_id(?string $date): string
{
    if (!$date || $date === '0000-00-00') return '-';
    $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
    $time = strtotime($date);
    if (!$time) return e($date);
    return date('d', $time) . ' ' . $months[(int)date('n', $time)] . ' ' . date('Y', $time);
}

function upload_file(string $field, string $folder, array $allowedMime, int $maxBytes = 10485760): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Unggah file gagal. Periksa ukuran file dan konfigurasi PHP.');
    }
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Ukuran file melebihi batas 10 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowedMime[$mime])) {
        throw new RuntimeException('Format file tidak didukung.');
    }

    $extension = $allowedMime[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetDir = __DIR__ . '/../uploads/' . trim($folder, '/');
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        throw new RuntimeException('Folder upload tidak dapat dibuat.');
    }
    if (!move_uploaded_file($file['tmp_name'], $targetDir . '/' . $filename)) {
        throw new RuntimeException('File tidak dapat disimpan.');
    }
    return trim($folder, '/') . '/' . $filename;
}

function remove_upload(?string $relativePath): void
{
    if (!$relativePath) return;
    $base = realpath(__DIR__ . '/../uploads');
    $target = realpath(__DIR__ . '/../uploads/' . $relativePath);
    if ($base && $target && str_starts_with($target, $base . DIRECTORY_SEPARATOR) && is_file($target)) {
        @unlink($target);
    }
}

function status_badge(string $status): string
{
    $class = match (strtolower($status)) {
        'aktif', 'terkirim', 'selesai', 'diarsipkan' => 'success',
        'tidak aktif', 'dibatalkan' => 'muted',
        'direncanakan', 'berlangsung', 'diproses' => 'warning',
        default => 'info',
    };
    return '<span class="badge badge-' . $class . '">' . e($status) . '</span>';
}

function page_query(array $overrides = []): string
{
    return http_build_query(array_merge($_GET, $overrides));
}
