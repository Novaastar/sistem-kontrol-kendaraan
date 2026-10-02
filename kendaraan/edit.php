<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$id=(int)($_GET['id']??0);$stmt=mysqli_prepare($conn,"SELECT * FROM kendaraan WHERE id=?");mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);$r=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));if(!$r)die('Data tidak ditemukan');
if($_SERVER['REQUEST_METHOD']==='POST'){
 $merk=trim($_POST['merk']);$plat=strtoupper(trim($_POST['nomor_polisi']));$tahun=$_POST['tahun']!==''?(int)$_POST['tahun']:null;$status=$_POST['status'];
 $foto_sql = "";
 $bind_types = 'ssisi';
 $bind_params = [&$merk, &$plat, &$tahun, &$status, &$id];
 if(isset($_FILES['foto']) && $_FILES['foto']['error']===UPLOAD_ERR_OK) {
     $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
     $foto_name = 'k_' . time() . '.' . $ext;
     if(move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/kendaraan/' . $foto_name)){
         $foto_sql = ", foto=?";
         $bind_types = 'ssissi';
         $bind_params = [&$merk, &$plat, &$tahun, &$status, &$foto_name, &$id];
     }
 }
 $s=mysqli_prepare($conn,"UPDATE kendaraan SET merk=?,nomor_polisi=?,tahun=?,status=? $foto_sql WHERE id=?");
 call_user_func_array('mysqli_stmt_bind_param', array_merge([$s, $bind_types], $bind_params));
 mysqli_stmt_execute($s);header('Location:index.php');exit;
}
$page_title='Edit Kendaraan';require '../partials/header.php';?>
<div class="main-wrap"><?php require '../partials/sidebar.php';?><main class="content"><?php require '../partials/navbar.php';?><div class="card card-stat"><div class="card-body"><h4>Edit Kendaraan</h4><form method="post" enctype="multipart/form-data"><div class="row g-3"><div class="col-md-6"><label class="form-label">Merk</label><input class="form-control" name="merk" value="<?=e($r['merk'])?>" required></div><div class="col-md-6"><label class="form-label">Nomor Polisi</label><input class="form-control" name="nomor_polisi" value="<?=e($r['nomor_polisi'])?>" required></div><div class="col-md-4"><label class="form-label">Tahun</label><input class="form-control" type="number" name="tahun" value="<?=e($r['tahun'])?>"></div><div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach(['aktif','tidak aktif'] as $x):?><option <?=$r['status']===$x?'selected':''?>><?=ucwords($x)?></option><?php endforeach;?></select></div><div class="col-md-4"><label class="form-label">Ganti Foto (Opsional)</label><input class="form-control" type="file" name="foto" accept="image/*"></div></div><div class="mt-4"><button class="btn btn-bj">Update</button> <a class="btn btn-secondary" href="index.php">Batal</a></div></form></div></div></main></div><?php require '../partials/footer.php';?>
