<?php
require_once __DIR__ . '/includes/bootstrap.php';require_login('keuangan');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
verify_csrf();$id=(int)($_POST['id']??0);
$st=db()->prepare("SELECT bukti FROM transaksi_kas WHERE id_transaksi=?");$st->execute([$id]);$row=$st->fetch();
db()->prepare("DELETE FROM transaksi_kas WHERE id_transaksi=?")->execute([$id]);
if($row)remove_upload($row['bukti']);
set_flash('success','Transaksi berhasil dihapus.');redirect('keuangan.php');
