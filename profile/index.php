<?php
require_once '../config/koneksi.php';
require_once '../config/auth.php';
require_admin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    
    if (empty($nama)) {
        $error = "Nama tidak boleh kosong.";
    } else {
        $foto_query = "";
        
        // Handle file upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['foto']['tmp_name'];
            $name = basename($_FILES['foto']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($ext, $allowed)) {
                // Ensure upload directory exists
                $upload_dir = '../uploads/profiles/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $dest = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $dest)) {
                    $foto_query = ", foto='$new_filename'";
                } else {
                    $error = "Gagal mengupload foto.";
                }
            } else {
                $error = "Format foto tidak didukung. (Hanya JPG, JPEG, PNG, GIF)";
            }
        }
        
        if (empty($error)) {
            $stmt = mysqli_prepare($conn, "UPDATE users SET nama=?" . ($foto_query ? ", foto=?" : "") . " WHERE id=?");
            if ($foto_query) {
                mysqli_stmt_bind_param($stmt, 'ssi', $nama, $new_filename, $user_id);
            } else {
                mysqli_stmt_bind_param($stmt, 'si', $nama, $user_id);
            }
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Profil berhasil diperbarui.";
                $_SESSION['nama'] = $nama; // Update session
            } else {
                $error = "Gagal memperbarui profil: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch current user data
$stmt = mysqli_prepare($conn, "SELECT username, nama, foto FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$page_title = 'Edit Profil';
require '../partials/header.php';
?>
<div class="main-wrap">
  <?php require '../partials/sidebar.php'; ?>
  <main class="content">
    <?php require '../partials/navbar.php'; ?>
    <div class="content-body">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <h3 class="fw-bold mb-1">Edit Profil</h3>
          <p class="text-muted mb-0">Ubah informasi nama dan foto profil Anda</p>
        </div>
      </div>
      
      <div class="card card-stat">
        <div class="card-body p-4">
          <?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
          <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
          
          <form method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-4 text-center">
                <?php if (!empty($user['foto'])): ?>
                    <img src="../uploads/profiles/<?= e($user['foto']) ?>" alt="Foto Profil" class="rounded-circle mb-3 border" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-light d-flex justify-content-center align-items-center mx-auto mb-3 border" style="width: 150px; height: 150px; color: #aaa;">
                        <i class="bi bi-person-circle" style="font-size: 80px;"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <label for="foto" class="btn btn-outline-bj btn-sm"><i class="bi bi-upload"></i> Ganti Foto</label>
                    <input type="file" name="foto" id="foto" class="d-none" accept="image/jpeg,image/png,image/gif" onchange="previewImage(this)">
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                    <div class="form-text">Username tidak dapat diubah.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" value="<?= e($user['nama']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-bj"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        // Option to add immediate preview could go here if desired.
    }
}
</script>

<?php require '../partials/footer.php'; ?>
