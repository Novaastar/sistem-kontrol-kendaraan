<?php
declare(strict_types=1);

namespace Skk\TripUpdate;

use mysqli;
use RuntimeException;

final class MysqliTripUpdateRepository implements TripUpdateRepository
{
    public function __construct(private mysqli $connection)
    {
    }

    public function findOwnedTrip(int $assignmentId, int $driverUserId): ?array
    {
        $statement = mysqli_prepare(
            $this->connection,
            'SELECT pr.id, pr.km_awal
             FROM perjalanan pr
             JOIN penugasan p ON p.id = pr.penugasan_id
             JOIN drivers dr ON dr.id = p.driver_id
             WHERE pr.penugasan_id = ? AND dr.user_id = ?
             LIMIT 1'
        );
        mysqli_stmt_bind_param($statement, 'ii', $assignmentId, $driverUserId);
        mysqli_stmt_execute($statement);
        $trip = mysqli_fetch_assoc(mysqli_stmt_get_result($statement));

        return $trip ?: null;
    }

    public function updateTrip(int $tripId, array $fields): void
    {
        $fieldTypes = [
            'jam_ambil' => 's',
            'km_awal' => 'i',
            'jam_pulang' => 's',
            'km_akhir' => 'i',
            'keterangan' => 's',
        ];
        $assignments = [];
        $types = '';
        $values = [];

        foreach ($fieldTypes as $field => $type) {
            if (array_key_exists($field, $fields)) {
                $assignments[] = "{$field} = ?";
                $types .= $type;
                $values[] = $fields[$field];
            }
        }
        if ($assignments === []) {
            throw new RuntimeException('Trip update requires at least one supported field.');
        }

        $sql = 'UPDATE perjalanan SET ' . implode(', ', $assignments) . ' WHERE id = ?';
        $types .= 'i';
        $values[] = $tripId;
        $statement = mysqli_prepare($this->connection, $sql);
        $references = [&$types];
        foreach ($values as $index => $value) {
            $references[] = &$values[$index];
        }
        call_user_func_array([$statement, 'bind_param'], $references);
        mysqli_stmt_execute($statement);
    }

    public function updateAssignmentStatus(int $assignmentId, string $status): void
    {
        $statement = mysqli_prepare(
            $this->connection,
            'UPDATE penugasan SET status = ? WHERE id = ?'
        );
        mysqli_stmt_bind_param($statement, 'si', $status, $assignmentId);
        mysqli_stmt_execute($statement);
    }
}
