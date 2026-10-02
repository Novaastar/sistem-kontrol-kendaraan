<?php
require_once '_helpers.php';
$u = auth_user(); // Validate token and get user

// Fetch settings from database
$q = mysqli_query($conn, "SELECT setting_key, setting_value FROM app_settings");
$settings = [];
while ($row = mysqli_fetch_assoc($q)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$data = [
    'pagi' => [
        'aktif' => isset($settings['notif_pagi_aktif']) ? (bool)$settings['notif_pagi_aktif'] : false,
        'jam' => isset($settings['notif_pagi_jam']) ? (int)$settings['notif_pagi_jam'] : 6,
        'menit' => isset($settings['notif_pagi_menit']) ? (int)$settings['notif_pagi_menit'] : 30
    ],
    'sore' => [
        'aktif' => isset($settings['notif_sore_aktif']) ? (bool)$settings['notif_sore_aktif'] : false,
        'jam' => isset($settings['notif_sore_jam']) ? (int)$settings['notif_sore_jam'] : 16,
        'menit' => isset($settings['notif_sore_menit']) ? (int)$settings['notif_sore_menit'] : 0
    ]
];

echo json_encode([
    'success' => true,
    'data' => $data
]);
?>
