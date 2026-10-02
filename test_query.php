<?php
require 'config/koneksi.php';
$stmt=mysqli_prepare($conn,"SELECT p.id,p.tanggal,p.tujuan,p.status,k.id kendaraan_id,k.merk,k.nomor_polisi,k.foto,pr.id perjalanan_id,pr.jam_ambil,pr.km_awal,pr.jam_pulang,pr.km_akhir,pr.keterangan FROM penugasan p JOIN drivers d ON d.id=p.driver_id JOIN kendaraan k ON k.id=p.kendaraan_id LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id WHERE d.user_id=2 AND p.tanggal>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) ORDER BY p.tanggal DESC,p.id DESC");
mysqli_stmt_execute($stmt);
$r=mysqli_stmt_get_result($stmt);
$out=[];
while($x=mysqli_fetch_assoc($r)) $out[]=$x;
print_r($out);
