<?php
require_once '_helpers.php';
$u = auth_user();
$d = body();
$password_lama = $d['password_lama'] ?? '';
$password_baru = $d['password_baru'] ?? '';

if(empty($password_lama) || empty($password_baru)) {
    json_out(['success' => false, 'message' => 'Password lama dan baru harus diisi'], 400);
}

$stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $u['id']);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row || !password_verify($password_lama, $row['password'])) {
    json_out(['success' => false, 'message' => 'Password lama tidak sesuai'], 400);
}

$hash = password_hash($password_baru, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'si', $hash, $u['id']);
if (mysqli_stmt_execute($stmt)) {
    json_out(['success' => true, 'message' => 'Password berhasil diubah']);
} else {
    json_out(['success' => false, 'message' => 'Gagal mengubah password'], 500);
}
