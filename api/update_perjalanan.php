<?php
require_once '_helpers.php';$u=auth_user();$d=body();$pid=(int)($d['penugasan_id']??0);
$stmt=mysqli_prepare($conn,"SELECT pr.id, pr.km_awal, p.status,p.kendaraan_id FROM perjalanan pr JOIN penugasan p ON p.id=pr.penugasan_id JOIN drivers dr ON dr.id=p.driver_id WHERE pr.penugasan_id=? AND dr.user_id=? LIMIT 1");mysqli_stmt_bind_param($stmt,'ii',$pid,$u['id']);mysqli_stmt_execute($stmt);$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));if(!$row)json_out(['success'=>false,'message'=>'Penugasan tidak ditemukan'],404);
$fields=[];$types='';$vals=[];
if(array_key_exists('jam_ambil',$d)){ $fields[]='jam_ambil=?';$types.='s';$vals[]=$d['jam_ambil']?:null;}
if(array_key_exists('km_awal',$d)){ $fields[]='km_awal=?';$types.='i';$vals[]=$d['km_awal']!==null?(int)$d['km_awal']:null;}
if(array_key_exists('jam_pulang',$d)){ $fields[]='jam_pulang=?';$types.='s';$vals[]=$d['jam_pulang']?:null;}
if(array_key_exists('km_akhir',$d)){ 
    $km_akhir = $d['km_akhir']!==null?(int)$d['km_akhir']:null;
    if ($km_akhir !== null && $row['km_awal'] !== null && $km_akhir < (int)$row['km_awal']) {
        json_out(['success'=>false,'message'=>'KM Akhir tidak boleh lebih kecil dari KM Awal'],422);
    }
    $fields[]='km_akhir=?';$types.='i';$vals[]=$km_akhir;
}
if(array_key_exists('keterangan',$d)){ $fields[]='keterangan=?';$types.='s';$vals[]=$d['keterangan'];}
if(!$fields)json_out(['success'=>false,'message'=>'Tidak ada data yang diubah'],422);
$sql="UPDATE perjalanan SET ".implode(',',$fields)." WHERE id=?";$types.='i';$vals[]=$row['id'];
$stmt=mysqli_prepare($conn,$sql);$refs=[];$refs[]=&$types;foreach($vals as $k=>$v)$refs[]=&$vals[$k];call_user_func_array([$stmt,'bind_param'],$refs);mysqli_stmt_execute($stmt);
if(!empty($d['jam_ambil']))mysqli_query($conn,"UPDATE penugasan SET status='berjalan' WHERE id=$pid");
if(!empty($d['jam_pulang'])){mysqli_query($conn,"UPDATE penugasan SET status='selesai' WHERE id=$pid");}
json_out(['success'=>true,'message'=>'Data perjalanan diperbarui']);
