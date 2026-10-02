<?php
require_once '_helpers.php';
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Metode tidak diizinkan'], 405);
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    json_out(['success' => false, 'message' => 'Gagal mengupload foto'], 400);
}

$tmp_name = $_FILES['foto']['tmp_name'];
$name = basename($_FILES['foto']['name']);
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($ext, $allowed)) {
    json_out(['success' => false, 'message' => 'Format tidak didukung. (JPG, JPEG, PNG, GIF)'], 400);
}

$upload_dir = '../uploads/profiles/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$new_filename = 'user_' . $u['id'] . '_' . time() . '.' . $ext;
$dest = $upload_dir . $new_filename;

if (move_uploaded_file($tmp_name, $dest)) {
    $stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $u['id']);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($row && !empty($row['foto']) && file_exists($upload_dir . $row['foto'])) {
        @unlink($upload_dir . $row['foto']);
    }

    $stmt = mysqli_prepare($conn, "UPDATE users SET foto=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $new_filename, $u['id']);
    mysqli_stmt_execute($stmt);

    json_out(['success' => true, 'message' => 'Foto berhasil diubah', 'foto' => $new_filename]);
} else {
    json_out(['success' => false, 'message' => 'Gagal menyimpan foto'], 500);
}
