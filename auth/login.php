<?php
session_start();
require_once '../config/koneksi.php';
if (!empty($_SESSION['user_id'])) { 
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: ../dashboard/'); exit; 
    } else {
        session_unset();
        session_destroy();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, "SELECT id,username,password,role,nama FROM users WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] !== 'admin') {
            $error = 'Akun Driver hanya dapat login melalui Aplikasi Mobile Android.';
        } else {
            $_SESSION['user_id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['role']=$user['role'];
            $_SESSION['nama']=$user['nama'];
            header('Location: ../dashboard/'); exit;
        }
    } else {
        $error='Username atau password salah.';
    }
}
$page_title='Login';
require_once '../partials/header.php';
?>
<div class="login-page">
<div class="car-background" aria-hidden="true">
    <div class="car-track">
        <div class="car-sequence">
            <img src="../assets/img/dashboard-cars/mobil-01.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-03.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-04.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-05.jpg" alt="">
            <img src="../uploads/kendaraan/k_1789444256.png" alt="">
            <img src="../uploads/kendaraan/k_1789444102.png" alt="">
        </div>
        <div class="car-sequence" aria-hidden="true">
            <img src="../assets/img/dashboard-cars/mobil-01.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-03.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-04.jpg" alt="">
            <img src="../assets/img/dashboard-cars/mobil-05.jpg" alt="">
            <img src="../uploads/kendaraan/k_1789444256.png" alt="">
            <img src="../uploads/kendaraan/k_1789444102.png" alt="">
        </div>
    </div>
</div>
<div class="card login-card p-4">
<div class="text-center mb-4 mt-2">
  <img src="../assets/img/logo.png" alt="Bank Jatim" style="width: 240px; height: auto; transform: scale(1.15);" class="mb-4">
  <div class="fw-semibold text-dark fs-5 mb-1">Sistem Kontrol Kendaraan</div>
  <div class="text-muted small">Cabang Tuban • Web Admin</div>
</div>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
  <label class="form-label">Username</label>
  <input class="form-control form-control-lg mb-3" name="username" required autofocus>
  <label class="form-label">Password</label>
  <input class="form-control form-control-lg mb-3" type="password" name="password" required>
  <button class="btn btn-bj btn-lg w-100">Masuk</button>
</form>
<div class="text-center mt-3">
  <span class="small text-muted">Anda belum punya akun? </span>
  <a href="register.php" class="small fw-semibold" style="color: var(--bj-blue, #003d7a); text-decoration: none;">Buat akun dulu</a>
</div>
</div>
</div>
</body></html>
