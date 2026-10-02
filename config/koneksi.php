<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bank_jatim_kendaraan';

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . mysqli_connect_error()]);
    exit;
}
mysqli_set_charset($conn, 'utf8mb4');

date_default_timezone_set('Asia/Jakarta');

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
