<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $merk=trim($_POST['merk']);$plat=strtoupper(trim($_POST['nomor_polisi']));$tahun=$_POST['tahun']!==''?(int)$_POST['tahun']:null;
 $foto_name = null;
 if(isset($_FILES['foto']) && $_FILES['foto']['error']===UPLOAD_ERR_OK) {
     $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
     $foto_name = 'k_' . time() . '.' . $ext;
     move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/kendaraan/' . $foto_name);
 }
 $stmt=mysqli_prepare($conn,"INSERT INTO kendaraan(merk,nomor_polisi,tahun,foto) VALUES(?,?,?,?)");
 mysqli_stmt_bind_param($stmt,'ssis',$merk,$plat,$tahun,$foto_name);
 if(mysqli_stmt_execute($stmt)){header('Location:index.php');exit;} $error='Nomor polisi mungkin sudah terdaftar.';
}
$page_title='Tambah Kendaraan';require '../partials/header.php';?>
<div class="main-wrap"><?php require '../partials/sidebar.php';?><main class="content"><?php require '../partials/navbar.php';?><div class="card card-stat"><div class="card-body"><h4>Tambah Kendaraan</h4><?php if(isset($error)):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><div class="row g-3"><div class="col-md-6"><label class="form-label">Merk Kendaraan</label><input class="form-control" name="merk" required></div><div class="col-md-6"><label class="form-label">Nomor Polisi</label><input class="form-control" name="nomor_polisi" data-uppercase required></div><div class="col-md-4"><label class="form-label">Tahun</label><input class="form-control" type="number" name="tahun" min="1990" max="2100"></div><div class="col-md-8"><label class="form-label">Foto Kendaraan (Opsional)</label><input class="form-control" type="file" name="foto" accept="image/jpeg,image/png,image/webp"></div></div><div class="mt-4"><button class="btn btn-bj">Simpan</button> <a class="btn btn-secondary" href="index.php">Batal</a></div></form></div></div></main></div><?php require '../partials/footer.php';?>
