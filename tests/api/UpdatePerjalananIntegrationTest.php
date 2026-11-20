<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Skk\Tests\TripUpdateTestContext;

final class UpdatePerjalananIntegrationTest extends TestCase
{
    private TripUpdateTestContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new TripUpdateTestContext();
        $this->context->setUp();
    }

    protected function tearDown(): void
    {
        $this->context->tearDown();
        parent::tearDown();
    }

    public function testIT01RejectsRequestWithoutBearerToken(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'km_akhir' => 15010,
        ], null);

        self::assertSame(401, $response['status']);
        self::assertSame('Token diperlukan', $response['body']['message']);
    }

    public function testIT02RejectsUnknownBearerToken(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'km_akhir' => 15010,
        ], $this->context->token . 'invalid');

        self::assertSame(401, $response['status']);
        self::assertFalse($response['body']['success']);
    }

    public function testIT03RejectsAssignmentOwnedByAnotherDriver(): void
    {
        $foreignToken = bin2hex(random_bytes(32));
        $foreignDriverId = $this->context->createDriver(
            bin2hex(random_bytes(5)),
            $foreignToken,
            2
        );
        $foreignAssignmentId = $this->context->createAssignment(
            bin2hex(random_bytes(5)),
            $foreignDriverId,
            2
        );

        $response = $this->context->post([
            'penugasan_id' => $foreignAssignmentId,
            'km_akhir' => 15010,
        ], $this->context->token);

        self::assertSame(404, $response['status']);
        self::assertSame('Penugasan tidak ditemukan', $response['body']['message']);
    }

    public function testIT04TripStartPersistsAndSetsAssignmentToRunning(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'jam_ambil' => '08:15',
            'km_awal' => 15010,
        ], $this->context->token);

        self::assertSame(200, $response['status']);
        self::assertTrue($response['body']['success']);
        $journey = $this->context->fetchJourney($this->context->assignmentId);
        self::assertSame('08:15:00', $journey['jam_ambil']);
        self::assertSame(15010, (int) $journey['km_awal']);
        self::assertSame('berjalan', $journey['status']);
    }

    public function testIT05RejectsLowerFinalOdometerWithoutPersistingIt(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'km_akhir' => 15009,
        ], $this->context->token);

        self::assertSame(422, $response['status']);
        self::assertFalse($response['body']['success']);
        self::assertSame(
            'KM Akhir tidak boleh lebih kecil dari KM Awal',
            $response['body']['message']
        );
        self::assertNull($this->context->fetchJourney($this->context->assignmentId)['km_akhir']);
    }

    public function testIT06RejectsUpdateWithoutEditableFields(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
        ], $this->context->token);

        self::assertSame(422, $response['status']);
        self::assertSame('Tidak ada data yang diubah', $response['body']['message']);
    }

    public function testIT07AcceptsEqualOdometerAndPersistsTripFinish(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'jam_pulang' => '16:00',
            'km_akhir' => 15010,
            'keterangan' => 'Pengujian PHPUnit',
        ], $this->context->token);

        self::assertSame(200, $response['status']);
        $journey = $this->context->fetchJourney($this->context->assignmentId);
        self::assertSame('16:00:00', $journey['jam_pulang']);
        self::assertSame(15010, (int) $journey['km_akhir']);
        self::assertSame('Pengujian PHPUnit', $journey['keterangan']);
        self::assertSame('selesai', $journey['status']);
    }

    public function testIT08PartialNoteUpdateDoesNotChangeAssignmentStatus(): void
    {
        $response = $this->context->post([
            'penugasan_id' => $this->context->assignmentId,
            'keterangan' => 'Catatan parsial',
        ], $this->context->token);

        self::assertSame(200, $response['status']);
        $journey = $this->context->fetchJourney($this->context->assignmentId);
        self::assertSame('Catatan parsial', $journey['keterangan']);
        self::assertSame('ditugaskan', $journey['status']);
    }
}
