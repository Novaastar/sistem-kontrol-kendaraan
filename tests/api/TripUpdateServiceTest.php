<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Skk\TripUpdate\TripUpdateRepository;
use Skk\TripUpdate\TripUpdateService;

require_once __DIR__ . '/../../api/TripUpdateService.php';

final class TripUpdateRepositoryStub implements TripUpdateRepository
{
    public function __construct(private ?array $trip)
    {
    }

    public function findOwnedTrip(int $assignmentId, int $driverUserId): ?array
    {
        return $this->trip;
    }

    public function updateTrip(int $tripId, array $fields): void
    {
        throw new LogicException('A rejected update must not write trip data.');
    }

    public function updateAssignmentStatus(int $assignmentId, string $status): void
    {
        throw new LogicException('A rejected update must not change assignment status.');
    }
}

final class TripUpdateRepositorySpy implements TripUpdateRepository
{
    public array $calls = [];

    public function findOwnedTrip(int $assignmentId, int $driverUserId): ?array
    {
        $this->calls[] = ['findOwnedTrip', $assignmentId, $driverUserId];

        return ['id' => 17, 'km_awal' => 15010];
    }

    public function updateTrip(int $tripId, array $fields): void
    {
        $this->calls[] = ['updateTrip', $tripId, $fields];
    }

    public function updateAssignmentStatus(int $assignmentId, string $status): void
    {
        $this->calls[] = ['updateAssignmentStatus', $assignmentId, $status];
    }
}

final class TripUpdateServiceTest extends TestCase
{
    public function testStubSuppliesTripDataAndRejectsLowerFinalOdometerBeforeWrites(): void
    {
        $service = new TripUpdateService(
            new TripUpdateRepositoryStub(['id' => 17, 'km_awal' => 15010])
        );

        $result = $service->update(29, 41, ['km_akhir' => 15009]);

        self::assertSame(422, $result['status']);
        self::assertSame(
            'KM Akhir tidak boleh lebih kecil dari KM Awal',
            $result['body']['message']
        );
    }

    public function testSpyRecordsTripStartPersistenceAndStatusTransition(): void
    {
        $repository = new TripUpdateRepositorySpy();
        $service = new TripUpdateService($repository);

        $result = $service->update(29, 41, [
            'jam_ambil' => '08:15',
            'km_awal' => 15010,
        ]);

        self::assertSame(200, $result['status']);
        self::assertSame([
            ['findOwnedTrip', 29, 41],
            ['updateTrip', 17, ['jam_ambil' => '08:15', 'km_awal' => 15010]],
            ['updateAssignmentStatus', 29, 'berjalan'],
        ], $repository->calls);
    }

    public function testStubReturningNoOwnedTripProducesNotFound(): void
    {
        $service = new TripUpdateService(new TripUpdateRepositoryStub(null));

        $result = $service->update(29, 41, ['km_akhir' => 15010]);

        self::assertSame(404, $result['status']);
        self::assertFalse($result['body']['success']);
    }
}
