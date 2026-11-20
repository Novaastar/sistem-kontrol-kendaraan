<?php
declare(strict_types=1);

use Skk\Tests\TripUpdateTestContext;

beforeEach(function (): void {
    $this->context = new TripUpdateTestContext();
    $this->context->setUp();
});

afterEach(function (): void {
    $this->context->tearDown();
});

it('IT-01 rejects a request without a bearer token', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'km_akhir' => 15010,
    ], null);

    expect($response['status'])->toBe(401)
        ->and($response['body']['message'])->toBe('Token diperlukan');
});

it('IT-02 rejects an unknown bearer token', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'km_akhir' => 15010,
    ], $this->context->token . 'invalid');

    expect($response['status'])->toBe(401)
        ->and($response['body']['success'])->toBeFalse();
});

it('IT-03 rejects an assignment owned by another driver', function (): void {
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

    expect($response['status'])->toBe(404)
        ->and($response['body']['message'])->toBe('Penugasan tidak ditemukan');
});

it('IT-04 persists trip start and sets the assignment to running', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'jam_ambil' => '08:15',
        'km_awal' => 15010,
    ], $this->context->token);

    $journey = $this->context->fetchJourney($this->context->assignmentId);
    expect($response['status'])->toBe(200)
        ->and($response['body']['success'])->toBeTrue()
        ->and($journey['jam_ambil'])->toBe('08:15:00')
        ->and((int) $journey['km_awal'])->toBe(15010)
        ->and($journey['status'])->toBe('berjalan');
});

it('IT-05 rejects a lower final odometer without persisting it', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'km_akhir' => 15009,
    ], $this->context->token);

    expect($response['status'])->toBe(422)
        ->and($response['body']['success'])->toBeFalse()
        ->and($response['body']['message'])->toBe('KM Akhir tidak boleh lebih kecil dari KM Awal')
        ->and($this->context->fetchJourney($this->context->assignmentId)['km_akhir'])->toBeNull();
});

it('IT-06 rejects an update without editable fields', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
    ], $this->context->token);

    expect($response['status'])->toBe(422)
        ->and($response['body']['message'])->toBe('Tidak ada data yang diubah');
});

it('IT-07 accepts an equal odometer and persists trip finish', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'jam_pulang' => '16:00',
        'km_akhir' => 15010,
        'keterangan' => 'Pengujian Pest',
    ], $this->context->token);

    $journey = $this->context->fetchJourney($this->context->assignmentId);
    expect($response['status'])->toBe(200)
        ->and($journey['jam_pulang'])->toBe('16:00:00')
        ->and((int) $journey['km_akhir'])->toBe(15010)
        ->and($journey['keterangan'])->toBe('Pengujian Pest')
        ->and($journey['status'])->toBe('selesai');
});

it('IT-08 preserves assignment status for a partial note update', function (): void {
    $response = $this->context->post([
        'penugasan_id' => $this->context->assignmentId,
        'keterangan' => 'Catatan parsial',
    ], $this->context->token);

    $journey = $this->context->fetchJourney($this->context->assignmentId);
    expect($response['status'])->toBe(200)
        ->and($journey['keterangan'])->toBe('Catatan parsial')
        ->and($journey['status'])->toBe('ditugaskan');
});
