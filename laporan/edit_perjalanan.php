<?php
require_once '../config/koneksi.php';
require_once '../config/auth.php';
require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid.']);
    exit;
}

$penugasan_id = (int)($_POST['penugasan_id'] ?? 0);
if (!$penugasan_id) {
    echo json_encode(['success' => false, 'message' => 'ID penugasan tidak valid.']);
    exit;
}

// Ambil & sanitasi nilai field
$jam_ambil  = trim($_POST['jam_ambil']  ?? '') ?: null;
$km_awal    = strlen(trim($_POST['km_awal'] ?? '')) ? (int)$_POST['km_awal'] : null;
$jam_pulang = trim($_POST['jam_pulang'] ?? '') ?: null;
$km_akhir   = strlen(trim($_POST['km_akhir'] ?? '')) ? (int)$_POST['km_akhir'] : null;
$keterangan = trim($_POST['keterangan'] ?? '') ?: null;

// Validasi km_akhir >= km_awal jika keduanya terisi
if ($km_awal !== null && $km_akhir !== null && $km_akhir < $km_awal) {
    echo json_encode(['success' => false, 'message' => 'KM akhir tidak boleh lebih kecil dari KM awal.']);
    exit;
}

// Cek apakah baris perjalanan sudah ada
$cek = mysqli_prepare($conn, "SELECT id FROM perjalanan WHERE penugasan_id=?");
mysqli_stmt_bind_param($cek, 'i', $penugasan_id);
mysqli_stmt_execute($cek);
$exists = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

if ($exists) {
    // UPDATE
    $stmt = mysqli_prepare($conn,
        "UPDATE perjalanan SET jam_ambil=?, km_awal=?, jam_pulang=?, km_akhir=?, keterangan=? WHERE penugasan_id=?");
    mysqli_stmt_bind_param($stmt, 'siissi',
        $jam_ambil, $km_awal, $jam_pulang, $km_akhir, $keterangan, $penugasan_id);
} else {
    // INSERT baru
    $stmt = mysqli_prepare($conn,
        "INSERT INTO perjalanan (penugasan_id, jam_ambil, km_awal, jam_pulang, km_akhir, keterangan) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'isiisi',
        $penugasan_id, $jam_ambil, $km_awal, $jam_pulang, $km_akhir, $keterangan);
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Data perjalanan berhasil disimpan.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
}
