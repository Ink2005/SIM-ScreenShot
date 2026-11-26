<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('kegiatan');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
verify_csrf();$id=(int)($_POST['id']??0);db()->prepare("DELETE FROM kegiatan WHERE id_kegiatan=?")->execute([$id]);set_flash('success','Kegiatan berhasil dihapus.');redirect('kegiatan.php');
