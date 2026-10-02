<?php
require_once '_helpers.php';

$d = body();
$username = trim($d['username'] ?? '');
$password = $d['password'] ?? '';

if (empty($username) || empty($password)) {
    json_out(['success' => false, 'message' => 'NIP/Username dan password wajib diisi'], 400);
}

// Pencarian mendukung kolom username ATAU nip
$stmt = mysqli_prepare($conn, "SELECT id, username, password, role, nama, foto FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$u || !password_verify($password, $u['password'])) {
    json_out(['success' => false, 'message' => 'NIP/Username atau password salah'], 401);
}

if ($u['role'] !== 'driver') {
    json_out(['success' => false, 'message' => 'Akun ini bukan akun driver'], 403);
}

$token = bin2hex(random_bytes(32));
$hash = hash('sha256', $token);
$exp = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

$stmt = mysqli_prepare($conn, "INSERT INTO api_tokens(user_id, token_hash, expires_at) VALUES(?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iss', $u['id'], $hash, $exp);
mysqli_stmt_execute($stmt);

json_out([
    'success' => true,
    'token' => $token,
    'user' => [
        'id' => $u['id'],
        'nama' => $u['nama'],
        'username' => $u['username'],
        'foto' => $u['foto']
    ],
    'expires_at' => $exp
]);