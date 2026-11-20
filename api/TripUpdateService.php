<?php
declare(strict_types=1);

namespace Skk\TripUpdate;

interface TripUpdateRepository
{
    public function findOwnedTrip(int $assignmentId, int $driverUserId): ?array;

    public function updateTrip(int $tripId, array $fields): void;

    public function updateAssignmentStatus(int $assignmentId, string $status): void;
}

final class TripUpdateService
{
    public function __construct(private TripUpdateRepository $repository)
    {
    }

    public function update(int $assignmentId, int $driverUserId, array $data): array
    {
        $trip = $this->repository->findOwnedTrip($assignmentId, $driverUserId);
        if ($trip === null) {
            return $this->result(false, 'Penugasan tidak ditemukan', 404);
        }

        $fields = [];
        if (array_key_exists('jam_ambil', $data)) {
            $fields['jam_ambil'] = $data['jam_ambil'] ?: null;
        }
        if (array_key_exists('km_awal', $data)) {
            $fields['km_awal'] = $data['km_awal'] !== null ? (int) $data['km_awal'] : null;
        }
        if (array_key_exists('jam_pulang', $data)) {
            $fields['jam_pulang'] = $data['jam_pulang'] ?: null;
        }
        if (array_key_exists('km_akhir', $data)) {
            $kmAkhir = $data['km_akhir'] !== null ? (int) $data['km_akhir'] : null;
            if ($kmAkhir !== null && $trip['km_awal'] !== null
                && $kmAkhir < (int) $trip['km_awal']) {
                return $this->result(
                    false,
                    'KM Akhir tidak boleh lebih kecil dari KM Awal',
                    422
                );
            }
            $fields['km_akhir'] = $kmAkhir;
        }
        if (array_key_exists('keterangan', $data)) {
            $fields['keterangan'] = $data['keterangan'];
        }
        if ($fields === []) {
            return $this->result(false, 'Tidak ada data yang diubah', 422);
        }

        $this->repository->updateTrip((int) $trip['id'], $fields);
        if (!empty($data['jam_ambil'])) {
            $this->repository->updateAssignmentStatus($assignmentId, 'berjalan');
        }
        if (!empty($data['jam_pulang'])) {
            $this->repository->updateAssignmentStatus($assignmentId, 'selesai');
        }

        return $this->result(true, 'Data perjalanan diperbarui', 200);
    }

    private function result(bool $success, string $message, int $status): array
    {
        return [
            'status' => $status,
            'body' => ['success' => $success, 'message' => $message],
        ];
    }
}
