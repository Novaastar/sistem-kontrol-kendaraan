<?php
declare(strict_types=1);

namespace Skk\Tests;

use mysqli;
use RuntimeException;

final class TripUpdateTestContext
{
    public mysqli $db;
    public string $apiUrl;
    public string $token;
    public int $driverId;
    public int $assignmentId;

    private array $fixtureIds = [
        'assignments' => [],
        'drivers' => [],
        'users' => [],
        'vehicles' => [],
    ];

    public function setUp(): void
    {
        date_default_timezone_set('Asia/Jakarta');
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $port = filter_var(getenv('SKK_TEST_DB_PORT'), FILTER_VALIDATE_INT);
        if ($port === false || $port === null || $port === 3306) {
            throw new RuntimeException(
                'Set SKK_TEST_DB_PORT to an isolated test MySQL port other than 3306.'
            );
        }

        $this->apiUrl = getenv('SKK_TEST_API_URL')
            ?: 'http://127.0.0.1:8083/api/update_perjalanan.php';
        $this->db = new mysqli('127.0.0.1', 'root', '', 'jsdp_core', $port);
        $this->db->set_charset('utf8mb4');

        $instance = $this->db->query(
            'SELECT @@port AS port, @@datadir AS datadir'
        )->fetch_assoc();
        if ((int) $instance['port'] !== $port
            || !str_contains($instance['datadir'], 'skk-whitebox-mysql')) {
            throw new RuntimeException(
                'Refusing to run against a database outside the isolated SKK test instance.'
            );
        }

        $suffix = bin2hex(random_bytes(5));
        $this->token = bin2hex(random_bytes(32));
        $this->driverId = $this->createDriver($suffix, $this->token, 1);
        $this->assignmentId = $this->createAssignment($suffix, $this->driverId, 1);
    }

    public function tearDown(): void
    {
        if (!isset($this->db) || !$this->db instanceof mysqli) {
            return;
        }

        $tables = [
            'assignments' => 'penugasan',
            'drivers' => 'drivers',
            'users' => 'driver_users',
            'vehicles' => 'kendaraan',
        ];

        foreach ($tables as $type => $table) {
            $statement = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
            foreach ($this->fixtureIds[$type] as $id) {
                $statement->bind_param('i', $id);
                $statement->execute();
            }
        }

        $this->db->close();
    }

    public function post(array $payload, ?string $bearerToken): array
    {
        $headers = ['Content-Type: application/json'];
        if ($bearerToken !== null) {
            $headers[] = 'Authorization: Bearer ' . $bearerToken;
        }

        $curl = curl_init($this->apiUrl);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);

        $body = curl_exec($curl);
        if ($body === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new RuntimeException('API request failed: ' . $error);
        }

        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        return [
            'status' => $status,
            'body' => json_decode($body, true, 512, JSON_THROW_ON_ERROR),
        ];
    }

    public function createDriver(string $suffix, string $token, int $sequence): int
    {
        $username = 'wb_' . $suffix . '_' . $sequence;
        $name = 'White Box Driver ' . $sequence;
        $statement = $this->db->prepare(
            "INSERT INTO driver_users (username, password, role, nama)
             VALUES (?, 'unused-test-password', 'driver', ?)"
        );
        $statement->bind_param('ss', $username, $name);
        $statement->execute();
        $userId = $this->db->insert_id;
        $this->fixtureIds['users'][] = $userId;

        $hash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $statement = $this->db->prepare(
            'INSERT INTO driver_api_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        );
        $statement->bind_param('iss', $userId, $hash, $expires);
        $statement->execute();

        $statement = $this->db->prepare(
            'INSERT INTO drivers (user_id, nama) VALUES (?, ?)'
        );
        $statement->bind_param('is', $userId, $name);
        $statement->execute();
        $driverId = $this->db->insert_id;
        $this->fixtureIds['drivers'][] = $driverId;

        return $driverId;
    }

    public function createAssignment(string $suffix, int $driverId, int $sequence): int
    {
        $brand = 'White Box Test Vehicle';
        $plate = 'WB' . strtoupper(substr($suffix, 0, 6)) . $sequence;
        $statement = $this->db->prepare(
            'INSERT INTO kendaraan (merk, nomor_polisi) VALUES (?, ?)'
        );
        $statement->bind_param('ss', $brand, $plate);
        $statement->execute();
        $vehicleId = $this->db->insert_id;
        $this->fixtureIds['vehicles'][] = $vehicleId;

        $date = date('Y-m-d');
        $status = 'ditugaskan';
        $statement = $this->db->prepare(
            'INSERT INTO penugasan (tanggal, kendaraan_id, driver_id, status)
             VALUES (?, ?, ?, ?)'
        );
        $statement->bind_param('siis', $date, $vehicleId, $driverId, $status);
        $statement->execute();
        $assignmentId = $this->db->insert_id;
        $this->fixtureIds['assignments'][] = $assignmentId;

        $statement = $this->db->prepare(
            'INSERT INTO perjalanan (penugasan_id, km_awal) VALUES (?, 15010)'
        );
        $statement->bind_param('i', $assignmentId);
        $statement->execute();

        return $assignmentId;
    }

    public function fetchJourney(int $assignmentId): array
    {
        $statement = $this->db->prepare(
            'SELECT pr.jam_ambil, pr.km_awal, pr.jam_pulang, pr.km_akhir,
                    pr.keterangan, p.status
             FROM perjalanan pr
             JOIN penugasan p ON p.id = pr.penugasan_id
             WHERE pr.penugasan_id = ?'
        );
        $statement->bind_param('i', $assignmentId);
        $statement->execute();
        $journey = $statement->get_result()->fetch_assoc();
        if (!is_array($journey)) {
            throw new RuntimeException('Journey fixture was not found.');
        }

        return $journey;
    }
}
