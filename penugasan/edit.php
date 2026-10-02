<?php
require_once '../config/koneksi.php';require_once '../config/auth.php';require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$kendaraan=mysqli_query($conn,"SELECT * FROM kendaraan ORDER BY merk");
$drivers=mysqli_query($conn,"SELECT * FROM drivers ORDER BY nama");
$error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $tanggal=$_POST['tanggal'];
    $kid=(int)$_POST['kendaraan_id'];
    $did=(int)$_POST['driver_id'];
    $status=$_POST['status'];
    $tujuan=trim($_POST['tujuan']);
    
    $stmt=mysqli_prepare($conn,"UPDATE penugasan SET tanggal=?, kendaraan_id=?, driver_id=?, status=?, tujuan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,'siissi',$tanggal,$kid,$did,$status,$tujuan,$id);
    
    if(mysqli_stmt_execute($stmt)){
        header('Location:index.php');
        exit;
    } else {
        $error='Gagal mengubah penugasan.';
    }
}

$p_res = mysqli_query($conn, "SELECT * FROM penugasan WHERE id=$id");
$p_data = mysqli_fetch_assoc($p_res);
if (!$p_data) { header('Location: index.php'); exit; }

$page_title='Edit Penugasan';require '../partials/header.php';require '../partials/navbar.php';
?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<div class="card card-stat">
<div class="card-body">
<h4>Edit Penugasan</h4>
<?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?>
<form method="post">
<div class="row g-3">
<div class="col-md-3">
<label class="form-label">Tanggal</label>
<input class="form-control" type="date" name="tanggal" value="<?=e($p_data['tanggal'])?>" required>
</div>
<div class="col-md-3">
<label class="form-label">Kendaraan</label>
<select class="form-select" name="kendaraan_id" required>
<?php while($r=mysqli_fetch_assoc($kendaraan)):?>
<option value="<?=$r['id']?>" <?= $p_data['kendaraan_id']==$r['id']?'selected':'' ?>><?=e($r['merk'].' - '.$r['nomor_polisi'])?></option>
<?php endwhile;?>
</select>
</div>
<div class="col-md-3">
<label class="form-label">Driver</label>
<select class="form-select" name="driver_id" required>
<?php while($r=mysqli_fetch_assoc($drivers)):?>
<option value="<?=$r['id']?>" <?= $p_data['driver_id']==$r['id']?'selected':'' ?>><?=e($r['nama'])?></option>
<?php endwhile;?>
</select>
</div>
<div class="col-md-3">
<label class="form-label">Status</label>
<select class="form-select" name="status" required>
<option value="ditugaskan" <?= $p_data['status']=='ditugaskan'?'selected':'' ?>>Ditugaskan</option>
<option value="berjalan" <?= $p_data['status']=='berjalan'?'selected':'' ?>>Berjalan</option>
<option value="selesai" <?= $p_data['status']=='selesai'?'selected':'' ?>>Selesai</option>
<option value="dibatalkan" <?= $p_data['status']=='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
</select>
</div>
<div class="col-md-12">
<label class="form-label">Tujuan</label>
<input class="form-control" name="tujuan" value="<?=e($p_data['tujuan'])?>">
</div>
</div>
<div class="mt-4"><button class="btn btn-bj">Simpan Perubahan</button> <a class="btn btn-secondary" href="index.php">Batal</a></div>
</form>
</div>
</div>
</main>
</div>
<?php require '../partials/footer.php';?>
