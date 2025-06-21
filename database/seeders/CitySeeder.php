<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Disable foreign key checks

        $path = database_path('data/locations/cities.sql');

        if (File::exists($path)) {
            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info('Cities table seeded from SQL file!');
        } else {
            $this->command->warn('cities.sql file not found in database/data/locations/. Skipping CitySeeder.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Re-enable foreign key checks
            // Fallback or alternative seeding logic can be placed here if needed, e.g., using a CSV.
            // For CSV:
            /*
            $csvPath = database_path('data/locations/cities.csv');
            if (File::exists($csvPath)) {
                $file = fopen($csvPath, 'r');
                $header = fgetcsv($file); // Assuming first row is header

                while (($row = fgetcsv($file)) !== false) {
                    $data = array_combine($header, $row);
                    // Adjust keys according to your CSV header and table columns
                    // Ensure 'state_id' is correctly mapped or retrieved
                    DB::table('cities')->insert([
                        'name' => $data['name'],
                        'state_id' => $data['state_id'], // This needs to be valid ID from states table
                        // Add other fields as necessary
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                fclose($file);
                $this->command->info('Cities table seeded from CSV file!');
            } else {
                $this->command->warn('cities.csv file not found. Skipping CitySeeder.');
            }
            */
    } // This is the correct closing brace for the run() method
} // This is the correct closing brace for the CitySeeder class
