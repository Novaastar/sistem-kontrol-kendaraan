<?php
require_once '../config/koneksi.php';require_once '../config/auth.php';require_admin();
$tanggal=$_GET['tanggal']??date('Y-m-d');
$stmt=mysqli_prepare($conn,"SELECT p.*,k.merk,k.nomor_polisi,d.nama driver,pr.jam_ambil,pr.km_awal,pr.jam_pulang,pr.km_akhir,pr.keterangan FROM penugasan p JOIN kendaraan k ON k.id=p.kendaraan_id JOIN drivers d ON d.id=p.driver_id LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id WHERE p.tanggal=? ORDER BY p.id");mysqli_stmt_bind_param($stmt,'s',$tanggal);mysqli_stmt_execute($stmt);$rows=mysqli_stmt_get_result($stmt);
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Laporan <?=e($tanggal)?></title><style>
@page{size:A4 landscape;margin:8mm}body{font-family:Arial,sans-serif;font-size:9px;color:#111}.head{text-align:center;line-height:1.25;margin-bottom:8px}.head b{font-size:11px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #555;padding:4px;text-align:center}th{background:#eee}td:last-child{text-align:left}.sign{margin-top:35px;width:100%;display:flex;justify-content:space-between}.sign div{width:40%;text-align:center}
</style></head><body onload="window.print()"><div class="head"><b>LAPORAN HARIAN</b><br><b>KONTROL KE KENDARAAN</b><br><b>BANK JATIM CABANG TUBAN</b><br>HARI/TANGGAL: <b><?=e(date('d-m-Y',strtotime($tanggal)))?></b></div>
<table><thead><tr><th>KENDARAAN</th><th>PLAT NOMOR</th><th>DRIVER</th><th>JAM AMBIL</th><th>KM AWAL</th><th>JAM PULANG</th><th>KM AKHIR</th><th>CK SATPAM 1</th><th>CK SATPAM 2</th><th>CK SATPAM 3</th><th>KETERANGAN</th></tr></thead><tbody>
<?php while($r=mysqli_fetch_assoc($rows)):?><tr><td><?=e($r['merk'])?></td><td><?=e($r['nomor_polisi'])?></td><td><?=e($r['driver'])?></td><td><?=e($r['jam_ambil']?:'-')?></td><td><?=e($r['km_awal']?:'-')?></td><td><?=e($r['jam_pulang']?:'-')?></td><td><?=e($r['km_akhir']?:'-')?></td><td></td><td></td><td></td><td><?=e($r['keterangan']?:'-')?></td></tr><?php endwhile;?>
</tbody></table><div class="sign"><div>CONTROLLER UMUM<br><br><br>________________________</div><div>CONTROLLER SATPAM<br><br><br>________________________</div></div></body></html>
