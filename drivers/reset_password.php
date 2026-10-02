<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT d.nama, d.user_id FROM drivers d WHERE d.id=?");
mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$r) die('Driver tidak ditemukan.');

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass_baru = $_POST['password_baru'] ?? '';
    $pass_ulang = $_POST['password_ulang'] ?? '';
    if (strlen($pass_baru) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($pass_baru !== $pass_ulang) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (!$r['user_id']) {
        $error = 'Driver ini tidak memiliki akun login.';
    } else {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        $upd = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
        mysqli_stmt_bind_param($upd, 'si', $hash, $r['user_id']);
        if (mysqli_stmt_execute($upd)) {
            $success = 'Password berhasil direset!';
        } else {
            $error = 'Gagal mereset password.';
        }
    }
}
$page_title = 'Reset Password Driver'; require '../partials/header.php'; ?>
<div class="main-wrap"><?php require '../partials/sidebar.php'; ?><main class="content"><?php require '../partials/navbar.php'; ?>
<div class="content-body">
  <div class="d-flex align-items-center gap-2 mb-4">
    <a href="index.php" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i></a>
    <div>
      <h4 class="fw-bold mb-0">Reset Password Driver</h4>
      <p class="text-muted mb-0 small">Driver: <strong><?= e($r['nama']) ?></strong></p>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card card-stat">
        <div class="card-body p-4">
          <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 mb-3" style="width:64px;height:64px">
              <i class="bi bi-key-fill text-danger fs-3"></i>
            </div>
            <h5 class="fw-bold"><?= e($r['nama']) ?></h5>
            <p class="text-muted small mb-0">Buat password baru untuk driver ini</p>
          </div>
          <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
          <?php if ($success): ?><div class="alert alert-success py-2"><?= e($success) ?></div><?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label fw-semibold">Password Baru</label>
              <input class="form-control" type="password" name="password_baru" minlength="6" required placeholder="Minimal 6 karakter">
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Konfirmasi Password</label>
              <input class="form-control" type="password" name="password_ulang" minlength="6" required placeholder="Ulangi password baru">
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-bj"><i class="bi bi-key me-1"></i> Reset Password</button>
              <a class="btn btn-light" href="index.php">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</main></div>
<?php require '../partials/footer.php'; ?>
