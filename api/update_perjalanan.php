<?php
require_once '_helpers.php';
require_once 'TripUpdateService.php';
require_once 'MysqliTripUpdateRepository.php';

$user = auth_user();
$data = body();
$assignmentId = (int) ($data['penugasan_id'] ?? 0);
$service = new \Skk\TripUpdate\TripUpdateService(
    new \Skk\TripUpdate\MysqliTripUpdateRepository($conn)
);
$result = $service->update($assignmentId, (int) $user['id'], $data);

json_out($result['body'], $result['status']);
