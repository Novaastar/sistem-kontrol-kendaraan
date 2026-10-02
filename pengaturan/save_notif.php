<?php
require_once '../config/koneksi.php';
require_once '../config/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notif_pagi_aktif = isset($_POST['notif_pagi_aktif']) ? '1' : '0';
    $notif_pagi_jam = (int)$_POST['notif_pagi_jam'];
    $notif_pagi_menit = (int)$_POST['notif_pagi_menit'];
    
    $notif_sore_aktif = isset($_POST['notif_sore_aktif']) ? '1' : '0';
    $notif_sore_jam = (int)$_POST['notif_sore_jam'];
    $notif_sore_menit = (int)$_POST['notif_sore_menit'];

    $settings = [
        'notif_pagi_aktif' => $notif_pagi_aktif,
        'notif_pagi_jam' => $notif_pagi_jam,
        'notif_pagi_menit' => $notif_pagi_menit,
        'notif_sore_aktif' => $notif_sore_aktif,
        'notif_sore_jam' => $notif_sore_jam,
        'notif_sore_menit' => $notif_sore_menit
    ];

    foreach ($settings as $key => $value) {
        $stmt = mysqli_prepare($conn, "INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        mysqli_stmt_bind_param($stmt, 'ss', $key, $value);
        mysqli_stmt_execute($stmt);
    }
    
    $_SESSION['success'] = 'Pengaturan notifikasi berhasil disimpan.';
    header('Location: index.php');
    exit;
}
?>
