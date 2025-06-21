<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/locations/countries.sql');

        if (File::exists($path)) {
            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info('Countries table seeded from SQL file!');
        } else {
            $this->command->warn('countries.sql file not found in database/data/locations/. Skipping CountrySeeder.');
            // Fallback or alternative seeding logic can be placed here if needed, e.g., using a CSV.
            // For CSV:
            /*
            $csvPath = database_path('data/locations/countries.csv');
            if (File::exists($csvPath)) {
                $file = fopen($csvPath, 'r');
                $header = fgetcsv($file); // Assuming first row is header

                while (($row = fgetcsv($file)) !== false) {
                    $data = array_combine($header, $row);
                    // Adjust keys according to your CSV header and table columns
                    DB::table('countries')->insert([
                        'name' => $data['name'],
                        'iso2' => $data['iso2'] ?? null,
                        'iso3' => $data['iso3'] ?? null,
                        // Add other fields as necessary
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                fclose($file);
                $this->command->info('Countries table seeded from CSV file!');
            } else {
                $this->command->warn('countries.csv file not found. Skipping CountrySeeder.');
            }
            */
        }
    }
}
