<?php
require 'config/koneksi.php';
$pid = 1;
$u = ['id' => 2];
$d = ['penugasan_id' => 1, 'jam_pulang' => '15:01', 'km_akhir' => null, 'keterangan' => 'Dinas ke Surabaya'];

$stmt=mysqli_prepare($conn,"SELECT pr.id,p.status,p.kendaraan_id FROM perjalanan pr JOIN penugasan p ON p.id=pr.penugasan_id JOIN drivers dr ON dr.id=p.driver_id WHERE pr.penugasan_id=? AND dr.user_id=? LIMIT 1");
mysqli_stmt_bind_param($stmt,'ii',$pid,$u['id']);
mysqli_stmt_execute($stmt);
$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
var_dump($row);

$fields=[];$types='';$vals=[];
if(array_key_exists('jam_ambil',$d)){ $fields[]='jam_ambil=?';$types.='s';$vals[]=$d['jam_ambil']?:null;}
if(array_key_exists('km_awal',$d)){ $fields[]='km_awal=?';$types.='i';$vals[]=$d['km_awal']!==null?(int)$d['km_awal']:null;}
if(array_key_exists('jam_pulang',$d)){ $fields[]='jam_pulang=?';$types.='s';$vals[]=$d['jam_pulang']?:null;}
if(array_key_exists('km_akhir',$d)){ $fields[]='km_akhir=?';$types.='i';$vals[]=$d['km_akhir']!==null?(int)$d['km_akhir']:null;}
if(array_key_exists('keterangan',$d)){ $fields[]='keterangan=?';$types.='s';$vals[]=$d['keterangan'];}
if(!$fields) die("no fields");

$sql="UPDATE perjalanan SET ".implode(',',$fields)." WHERE id=?";$types.='i';$vals[]=$row['id'];
var_dump($sql);
var_dump($types);
var_dump($vals);

$stmt=mysqli_prepare($conn,$sql);
var_dump($stmt);
if (!$stmt) echo mysqli_error($conn);

$refs=[];$refs[]=&$types;foreach($vals as $k=>$v)$refs[]=&$vals[$k];
call_user_func_array([$stmt,'bind_param'],$refs);
mysqli_stmt_execute($stmt);
echo mysqli_stmt_error($stmt);
echo "\nDONE";
