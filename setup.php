<?php
require_once 'config/koneksi.php';
$messages=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $adminPass=$_POST['admin_password'] ?? '';
    $driverPass=$_POST['driver_password'] ?? '';
    if (strlen($adminPass)<6 || strlen($driverPass)<6) {
        $messages[]=['danger','Password minimal 6 karakter.'];
    } else {
        $conn->query("DELETE FROM users WHERE username IN ('admin','driver01')");
        $adminHash=password_hash($adminPass,PASSWORD_DEFAULT);
        $driverHash=password_hash($driverPass,PASSWORD_DEFAULT);
        $stmt=mysqli_prepare($conn,"INSERT INTO users(username,password,role,nama) VALUES (?,?,?,?)");
        $r='admin'; $n='Administrator'; $u='admin';
        mysqli_stmt_bind_param($stmt,'ssss',$u,$adminHash,$r,$n); mysqli_stmt_execute($stmt);
        $r='driver'; $n='Driver 01'; $u='driver01';
        mysqli_stmt_bind_param($stmt,'ssss',$u,$driverHash,$r,$n); mysqli_stmt_execute($stmt);
        $driverUserId=mysqli_insert_id($conn);
        $stmt2=mysqli_prepare($conn,"INSERT INTO drivers(user_id,nama,no_hp) VALUES (?,?,?)");
        $phone=''; $driverName='Driver 01';
        mysqli_stmt_bind_param($stmt2,'iss',$driverUserId,$driverName,$phone); mysqli_stmt_execute($stmt2);
        $messages[]=['success','Akun awal berhasil dibuat. Silakan hapus file setup.php setelah selesai.'];
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container py-5"><div class="card mx-auto p-4" style="max-width:520px"><h3>Setup Sistem Kontrol Kendaraan</h3><p class="text-muted">Jalankan setelah database.sql diimpor.</p>
<?php foreach($messages as $m): ?><div class="alert alert-<?=e($m[0])?>"><?=e($m[1])?></div><?php endforeach; ?>
<form method="post"><label class="form-label">Password Admin</label><input class="form-control mb-3" type="password" name="admin_password" value="admin123" required>
<label class="form-label">Password Driver</label><input class="form-control mb-3" type="password" name="driver_password" value="driver123" required>
<button class="btn btn-danger w-100">Buat Akun</button></form>
<hr><div class="small">Default bila tidak diubah: <b>admin / admin123</b> dan <b>driver01 / driver123</b>.</div></div></div></body></html>
