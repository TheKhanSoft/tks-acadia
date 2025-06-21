<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/locations/states.sql');

        if (File::exists($path)) {
            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info('States table seeded from SQL file!');
        } else {
            $this->command->warn('states.sql file not found in database/data/locations/. Skipping StateSeeder.');
            // Fallback or alternative seeding logic can be placed here if needed, e.g., using a CSV.
            // For CSV:
            /*
            $csvPath = database_path('data/locations/states.csv');
            if (File::exists($csvPath)) {
                $file = fopen($csvPath, 'r');
                $header = fgetcsv($file); // Assuming first row is header

                while (($row = fgetcsv($file)) !== false) {
                    $data = array_combine($header, $row);
                    // Adjust keys according to your CSV header and table columns
                    // Ensure 'country_id' is correctly mapped or retrieved
                    DB::table('states')->insert([
                        'name' => $data['name'],
                        'country_id' => $data['country_id'], // This needs to be valid ID from countries table
                        // Add other fields as necessary
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                fclose($file);
                $this->command->info('States table seeded from CSV file!');
            } else {
                $this->command->warn('states.csv file not found. Skipping StateSeeder.');
            }
            */
        }
    }
}
