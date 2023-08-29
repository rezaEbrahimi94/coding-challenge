<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class GenerateRoster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-roster
                            {filename : the first date of the period to roster, in YYYY-MM-DD format}
                            {start-date : the first date of the period to roster, in YYYY-MM-DD format}
                            {end-date : a nurses file to import}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a nurses roster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = storage_path($this->argument('filename'));
        $startDate = $this->argument('start-date');
        $endDate = $this->argument('end-date');

        if (!$this->validateArguments($filename, $startDate, $endDate)) {
            return;
        }

        $this->info("Generating roster for {$this->argument('filename')} from {$startDate} to {$endDate}");
    }

    public function validateArguments(string $filename, string $startDate, string $endDate): bool
    {
        $validator = Validator::make([
            'filename' => $filename,
            'start-date' => $startDate,
            'end-date' => $endDate,
        ], [
            'filename' => 'required',
            'start-date' => 'required|date_format:Y-m-d',
            'end-date' => 'required|date_format:Y-m-d|after_or_equal:start-date',
        ]);

        // Check if file exists
        if (!file_exists($filename)) {
            $this->error("File {$filename} does not exist");
            return false;
        }

        if ($validator->fails()) {
            $this->info('Cannot start command, see errors below:');
        
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }
        return true;
    }
}
