<?php

namespace Database\Seeders;

use App\Models\JobNature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobNatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('job_natures')->truncate(); // Clear the table first

        $jobNatures = [
            ['name' => 'Permanent', 'code'=> 'PER', 'description' => 'Regular, ongoing employment with no predetermined end date.'],
            ['name' => 'Contractual', 'code'=> 'CONT', 'description' => 'Employment for a specific, fixed term or project.'],
            ['name' => 'Temporary', 'code'=> 'TEMP', 'description' => 'Short-term employment, often covering absences or seasonal peaks.'],
            ['name' => 'Fixed Pay', 'code'=> 'FIXED', 'description' => 'Employment with a set salary for a defined scope of work or period.'],
            ['name' => 'Internship', 'code'=> 'INTERN', 'description' => 'A temporary position focused on training and gaining work experience.'],
            ['name' => 'Daily Wages', 'code'=> 'DPL', 'description' => 'Compensation is calculated and paid based on the number of days worked.'],
            ['name' => 'Visiting', 'code'=> 'VISIT', 'description' => 'Short-term or intermittent role, often for specialized expertise.'],
        ];

        foreach ($jobNatures as $nature) {
            JobNature::create($nature);
        }
    }
}
