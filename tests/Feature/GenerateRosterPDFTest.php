<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateRosterPDFTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');  // Mock the storage system
    }

    public function testPdfGeneration()
    {
        // Arrange
        $filename = 'nurses.json';
        $startDate = '2023-01-01';
        $endDate = '2023-01-07';
        $expectedPath = 'rosters/' . $startDate . '_to_' . $endDate . '.pdf';

        // Simulate file with nurses data
        Storage::put($filename, json_encode(['Iskra', 'Andronicus', 'Tipene', 'Jaroslav','Reza', 'Miroslav','Milica','Tomislav','Rey','Matt','Batman','Bruce','Wayne','Clark','Simon']));

        // Act
        Artisan::call('app:generate-roster', [
            'filename' => $filename,
            'start-date' => $startDate,
            'end-date' => $endDate
        ]);

        // Assert
        Storage::disk('local')->assertExists($expectedPath);
    }
}
