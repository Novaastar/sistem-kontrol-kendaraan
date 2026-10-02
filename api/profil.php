<?php
require_once '_helpers.php';
$u = auth_user();
$stmt = mysqli_prepare($conn, "SELECT d.nama, u.username as nip, d.no_hp, d.status, u.foto FROM drivers d JOIN users u ON u.id = d.user_id WHERE d.user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $u['id']);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if(!$row) json_out(['success' => false, 'message' => 'Data driver tidak ditemukan'], 404);
json_out(['success' => true, 'data' => $row]);
