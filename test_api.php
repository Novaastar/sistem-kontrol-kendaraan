<?php
// Simulate Api.login
$url = 'http://localhost/sistem_kontrol_kendaraan_bank_jatim/api/login.php';
$data = json_encode(['username' => '89344432', 'password' => 'driver123']);
$opts = ['http' => ['method' => 'POST', 'header' => 'Content-Type: application/json', 'content' => $data]];
$context = stream_context_create($opts);
$login_resp = file_get_contents($url, false, $context);
echo "Login: " . $login_resp . "\n\n";

$login_data = json_decode($login_resp, true);
$token = $login_data['token'];

// Simulate Api.tasks
$url2 = 'http://localhost/sistem_kontrol_kendaraan_bank_jatim/api/penugasan.php';
$opts2 = ['http' => ['method' => 'GET', 'header' => "Authorization: Bearer $token\r\n"]];
$context2 = stream_context_create($opts2);
$tasks_resp = file_get_contents($url2, false, $context2);
echo "Tasks: " . $tasks_resp . "\n";
