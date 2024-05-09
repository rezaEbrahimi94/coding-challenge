<?php
namespace Tests\Unit;

use App\Models\Nurse;
use App\Models\Shift;
use App\Repositories\RosterBuilderRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RosterBuilderRepositoryTest extends TestCase
{
    public function testLoadNursesFromFile()
    {
        // Arrange
        Storage::fake('local');
        $filename = 'test.json';
        $nurses = ['Iskra', 'Andronicus', 'Tipene', 'Jaroslav'];
        Storage::put($filename, json_encode($nurses));

        // Act
        $result = RosterBuilderRepository::loadNursesFromFile($filename);

        // Assert
        $this->assertCount(4, $result);
        $this->assertInstanceOf(Nurse::class, $result->first());
        $this->assertEquals('Iskra', $result->first()->name);
    }

    public function testBuildRosterWithEmptyNurseList()
    {
        $startDate = Carbon::parse('2023-01-01');
        $endDate = Carbon::parse('2023-01-31');
        $nurses = collect();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Not enough nurses to fill one day.");

        RosterBuilderRepository::buildRoster($nurses, $startDate, $endDate);
    }


    public function testFullMonthRosterGeneration()
    {
        // Arrange
        $nurses = collect([
            new Nurse(['name' => 'Nurse1']),
            new Nurse(['name' => 'Nurse2']),
            new Nurse(['name' => 'Nurse3']),
            new Nurse(['name' => 'Nurse4']),
            new Nurse(['name' => 'Nurse5']),
            new Nurse(['name' => 'Nurse6']),
            new Nurse(['name' => 'Nurse7']),
            new Nurse(['name' => 'Nurse8']),
            new Nurse(['name' => 'Nurse9']),
            new Nurse(['name' => 'Nurse10']),
            new Nurse(['name' => 'Nurse11']),
            new Nurse(['name' => 'Nurse12']),
            new Nurse(['name' => 'Nurse13']),
            new Nurse(['name' => 'Nurse14']),
            new Nurse(['name' => 'Nurse15']),


        ]);
        $startDate = Carbon::parse('2023-01-01');
        $endDate = Carbon::parse('2023-01-31');

        // Act
        $roster = RosterBuilderRepository::buildRoster($nurses, $startDate, $endDate);

        // Assert
        // There should be 93 shifts (31 days * 3 shifts per day)
        $this->assertCount(93, $roster);

        // Check if each shift has exactly 5 nurses
        foreach ($roster as $shift) {
            $this->assertCount(5, $shift->nurses);
        }

        // Check for nurse rotation without repetition within a day
        $days = $roster->groupBy(function ($shift) {
            return $shift->date->toDateString();
        });

        foreach ($days as $dayShifts) {
            $dailyNurses = $dayShifts->flatMap(function ($shift) {
                return $shift->nurses->pluck('name');
            });

            $uniqueNurses = $dailyNurses->unique();

            // Each nurse should only appear once per day
            $this->assertEquals(15, $uniqueNurses->count());
        }
    }
}
