<?php

namespace App\Repositories;

use App\Interfaces\RosterBuilderInterface;
use App\Models\Nurse;
use App\Models\Shift;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class RosterBuilderRepository implements RosterBuilderInterface
{
    public static function loadNursesFromFile(string $filename): Collection
    {
        // Check if file exists
        if (!Storage::exists($filename)) {
            throw new Exception("File {$filename} does not exist");
        }

        // Get the file content
        $content = Storage::get($filename);

        // Decode the JSON content to array
        $nurses = json_decode($content, true);

        // Map the array to Nurse objects
        $nurses = collect($nurses)->map(function ($nurseName) {
            return new Nurse(['name' => $nurseName]);
        });

        return $nurses;
    }

    /**
     * Generates a roster for a specified period.
     *
     * @param Collection $nurses Collection of nurses.
     * @param Carbon $startDate Start date of the period.
     * @param Carbon $endDate End date of the period.
     * @return Collection Collection of Shift models.
     */
    public static function buildRoster(Collection $nurses, Carbon $startDate, Carbon $endDate): Collection
    {
        $allShifts = collect();
        $shiftTypes = [Shift::SHIFT_TYPE_MORNING, Shift::SHIFT_TYPE_EVENING, Shift::SHIFT_TYPE_NIGHT];
        $nurseCount = $nurses->count();

        if ($nurseCount < 5) {
            throw new Exception("Not enough nurses to fill one shift.");
        }

        // Prepare a rotating index to cycle through nurses
        $index = 0;

        for ($date = $startDate->copy(); $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            foreach ($shiftTypes as $shiftType) {
                $shiftNurses = collect();
                for ($i = 0; $i < 5; $i++) { // Always pick 5 nurses
                    $shiftNurses->push($nurses[($index + $i) % $nurseCount]);
                }
                $index = ($index + 5) % $nurseCount; // Move index by 5 for the next shift

                $shift = new Shift([
                    'date' => $date->copy(),
                    'type' => $shiftType,
                    'nurses' => $shiftNurses
                ]);
                $allShifts->push($shift);
            }
        }

        return $allShifts;
    }
}
