<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $uid = $_SESSION['user_id'];

    $q = mysqli_query($conn, "SELECT password FROM users WHERE id=$uid");
    $user = mysqli_fetch_assoc($q);

    if (password_verify($current_pass, $user['password'])) {
        if ($new_pass === $confirm_pass) {
            if (strlen($new_pass) >= 6) {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$uid");
                $success = 'Password berhasil diperbarui!';
            } else {
                $error = 'Password baru minimal 6 karakter.';
            }
        } else {
            $error = 'Konfirmasi password baru tidak cocok.';
        }
    } else {
        $error = 'Password saat ini salah.';
    }
}

// Ambil pengaturan notifikasi
$q_set = mysqli_query($conn, "SELECT setting_key, setting_value FROM app_settings");
$app_settings = [];
while ($row = mysqli_fetch_assoc($q_set)) {
    $app_settings[$row['setting_key']] = $row['setting_value'];
}
$notif_pagi_aktif = isset($app_settings['notif_pagi_aktif']) && $app_settings['notif_pagi_aktif'] == '1';
$notif_pagi_jam = isset($app_settings['notif_pagi_jam']) ? (int)$app_settings['notif_pagi_jam'] : 6;
$notif_pagi_menit = isset($app_settings['notif_pagi_menit']) ? (int)$app_settings['notif_pagi_menit'] : 30;

$notif_sore_aktif = isset($app_settings['notif_sore_aktif']) && $app_settings['notif_sore_aktif'] == '1';
$notif_sore_jam = isset($app_settings['notif_sore_jam']) ? (int)$app_settings['notif_sore_jam'] : 16;
$notif_sore_menit = isset($app_settings['notif_sore_menit']) ? (int)$app_settings['notif_sore_menit'] : 0;

$page_title='Pengaturan Sistem'; require '../partials/header.php';
?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<?php require '../partials/navbar.php';?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-1">Pengaturan</h3>
    <p class="text-muted mb-0">Konfigurasi keamanan dan sistem</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-stat">
      <div class="card-body">
        <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock me-2 text-danger"></i>Keamanan Akun</h5>
        
        <?php if($success): ?>
            <div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i> <?= e($success) ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
          <input type="hidden" name="action" value="change_password">
          <div class="mb-3">
            <label class="form-label text-muted">Password Saat Ini</label>
            <input type="password" name="current_pass" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-muted">Password Baru</label>
            <input type="password" name="new_pass" class="form-control" required minlength="6">
            <div class="form-text">Minimal 6 karakter.</div>
          </div>
          <div class="mb-4">
            <label class="form-label text-muted">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_pass" class="form-control" required minlength="6">
          </div>
          <button type="submit" class="btn btn-bj w-100"><i class="bi bi-save"></i> Simpan Password Baru</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card card-stat mb-4">
      <div class="card-body">
        <h5 class="fw-bold mb-4"><i class="bi bi-bell me-2 text-warning"></i>Pengaturan Notifikasi Driver</h5>
        <form method="post" action="save_notif.php">
          
          <!-- Notif Pagi -->
          <div class="mb-4 p-3 border rounded bg-light">
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" role="switch" id="notif_pagi_aktif" name="notif_pagi_aktif" <?= $notif_pagi_aktif ? 'checked' : '' ?>>
              <label class="form-check-label fw-bold" for="notif_pagi_aktif">Reminder KM Awal (Pagi)</label>
            </div>
            <div class="row g-2 align-items-center">
              <div class="col-auto"><span class="text-muted small">Pukul</span></div>
              <div class="col-auto">
                <select class="form-select form-select-sm" name="notif_pagi_jam">
                  <option value="6" <?= $notif_pagi_jam == 6 ? 'selected' : '' ?>>06</option>
                  <option value="7" <?= $notif_pagi_jam == 7 ? 'selected' : '' ?>>07</option>
                  <option value="8" <?= $notif_pagi_jam == 8 ? 'selected' : '' ?>>08</option>
                  <option value="9" <?= $notif_pagi_jam == 9 ? 'selected' : '' ?>>09</option>
                  <option value="10" <?= $notif_pagi_jam == 10 ? 'selected' : '' ?>>10</option>
                  <option value="11" <?= $notif_pagi_jam == 11 ? 'selected' : '' ?>>11</option>
                  <option value="12" <?= $notif_pagi_jam == 12 ? 'selected' : '' ?>>12</option>
                </select>
              </div>
              <div class="col-auto"><span class="text-muted">:</span></div>
              <div class="col-auto">
                <select class="form-select form-select-sm" name="notif_pagi_menit">
                  <?php for($i = 0; $i < 60; $i += 5): ?>
                    <option value="<?= $i ?>" <?= $notif_pagi_menit == $i ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Notif Sore -->
          <div class="mb-4 p-3 border rounded bg-light">
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" role="switch" id="notif_sore_aktif" name="notif_sore_aktif" <?= $notif_sore_aktif ? 'checked' : '' ?>>
              <label class="form-check-label fw-bold" for="notif_sore_aktif">Reminder KM Akhir (Sore)</label>
            </div>
            <div class="row g-2 align-items-center">
              <div class="col-auto"><span class="text-muted small">Pukul</span></div>
              <div class="col-auto">
                <select class="form-select form-select-sm" name="notif_sore_jam">
                  <option value="14" <?= $notif_sore_jam == 14 ? 'selected' : '' ?>>14</option>
                  <option value="15" <?= $notif_sore_jam == 15 ? 'selected' : '' ?>>15</option>
                  <option value="16" <?= $notif_sore_jam == 16 ? 'selected' : '' ?>>16</option>
                  <option value="17" <?= $notif_sore_jam == 17 ? 'selected' : '' ?>>17</option>
                  <option value="18" <?= $notif_sore_jam == 18 ? 'selected' : '' ?>>18</option>
                </select>
              </div>
              <div class="col-auto"><span class="text-muted">:</span></div>
              <div class="col-auto">
                <select class="form-select form-select-sm" name="notif_sore_menit">
                  <?php for($i = 0; $i < 60; $i += 5): ?>
                    <option value="<?= $i ?>" <?= $notif_sore_menit == $i ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-bj w-100"><i class="bi bi-save"></i> Simpan Pengaturan Notif</button>
        </form>
      </div>
    </div>
    
    <div class="card card-stat">
      <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
        <div class="mb-3"><img src="/sistem_kontrol_kendaraan_bank_jatim/assets/img/logo.png" alt="Bank Jatim" style="max-height: 40px;"></div>
        <h6 class="fw-bold">Sistem Kontrol Kendaraan</h6>
        <p class="text-muted small mb-0">Versi 1.0.0<br>Bank Jatim Cabang Tuban</p>
      </div>
    </div>
  </div>
</div>

</div>
</main>
</div>
<?php require '../partials/footer.php';?>
