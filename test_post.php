<?php
$url = 'http://localhost/sistem_kontrol_kendaraan_bank_jatim/api/update_perjalanan.php';
$token = '50dc6f0d11a23ad24c299014b45819545d99c0f57666eebcc055541ae571a6db'; // from my earlier test_api
$data = json_encode(['penugasan_id' => 1, 'jam_pulang' => '15:01', 'km_akhir' => 12890, 'keterangan' => 'Dinas ke Surabaya']);
$opts = ['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\nAuthorization: Bearer $token\r\n", 'content' => $data]];
$context = stream_context_create($opts);
$resp = file_get_contents($url, false, $context);
echo $resp;
