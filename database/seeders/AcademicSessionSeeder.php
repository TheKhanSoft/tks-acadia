<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicSession;

use function PHPSTORM_META\type;

class AcademicSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ["Spring", "Summer", "Fall"];
        $startYear = 2009;
        $endYear = 2025;
        $academicYear = '';
        for ($year = $startYear; $year < $endYear; $year++) { 
            foreach ($types as $type) {
                AcademicSession::createOrFirst(
                    [
                        'name' => $type . " " . $year,
                        'year' => $year,
                        'type' => $type
                    ]
                );
            }
        }

        //AcademicSession::factory()->count(5)->create();

        $this->command->info('Academic Sessions seeded successfully!');
    }
}
