<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login sebagai admin, redirect ke dashboard
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: ../dashboard/'); exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validasi
    if (empty($nama) || empty($username) || empty($password) || empty($confirm)) {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($username) < 4 || strlen($username) > 50) {
        $error = 'Username harus terdiri dari 4–50 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh mengandung huruf, angka, dan underscore.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Cek apakah username sudah dipakai
        $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($cek, 's', $username);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan. Pilih username lain.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role   = 'admin';
            $ins = mysqli_prepare($conn, "INSERT INTO users (username, password, role, nama) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($ins, 'ssss', $username, $hashed, $role, $nama);
            if (mysqli_stmt_execute($ins)) {
                $success = 'Akun admin berhasil dibuat! Silakan <a href="login.php">login di sini</a>.';
            } else {
                $error = 'Terjadi kesalahan saat membuat akun. Coba lagi.';
            }
        }
    }
}

$page_title = 'Daftar Akun Admin';
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
  <div class="fw-semibold text-dark fs-5 mb-1">Daftar Akun Admin</div>
  <div class="text-muted small">Cabang Tuban • Web Admin</div>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if (!$success): ?>
<form method="post" novalidate>
  <label class="form-label">Nama Lengkap</label>
  <input class="form-control form-control-lg mb-3" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required autofocus placeholder="Masukkan nama lengkap">
  <label class="form-label">Username</label>
  <input class="form-control form-control-lg mb-3" name="username" value="<?= e($_POST['username'] ?? '') ?>" required placeholder="Minimal 4 karakter">
  <label class="form-label">Password</label>
  <input class="form-control form-control-lg mb-3" type="password" name="password" required placeholder="Minimal 8 karakter">
  <label class="form-label">Konfirmasi Password</label>
  <input class="form-control form-control-lg mb-4" type="password" name="confirm_password" required placeholder="Ulangi password">
  <button class="btn btn-bj btn-lg w-100" type="submit">Buat Akun</button>
</form>
<?php endif; ?>
<div class="text-center mt-3">
  <span class="small text-muted">Sudah punya akun? </span>
  <a href="login.php" class="small fw-semibold" style="color: var(--bj-blue, #003d7a); text-decoration: none;">Masuk di sini</a>
</div>
</div>
</div>
</body></html>
