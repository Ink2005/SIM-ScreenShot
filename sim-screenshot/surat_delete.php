<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('persuratan');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
verify_csrf();$id=(int)($_POST['id']??0);$st=db()->prepare("SELECT jenis,berkas FROM surat WHERE id_surat=?");$st->execute([$id]);$row=$st->fetch();
db()->prepare("DELETE FROM surat WHERE id_surat=?")->execute([$id]);if($row)remove_upload($row['berkas']);
set_flash('success','Arsip surat berhasil dihapus.');redirect('persuratan.php?jenis='.($row['jenis']??'masuk'));
