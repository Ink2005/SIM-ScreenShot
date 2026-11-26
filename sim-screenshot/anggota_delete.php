<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('anggota');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
verify_csrf();
$id=(int)($_POST['id']??0);
db()->prepare("DELETE FROM anggota WHERE id_anggota=?")->execute([$id]);
set_flash('success','Data anggota berhasil dihapus.');
redirect('anggota.php');
