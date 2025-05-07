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
       // DB::table('job_natures')->truncate(); // Clear the table first

        $jobNatures = [
            ['name' => 'Permanent', 'code'=> 'PER', 'description' => 'Regular, ongoing employment with no predetermined end date.'],
            ['name' => 'Contractual', 'code'=> 'CONT', 'description' => 'Employment for a specific, fixed term or project.'],
            ['name' => 'Temporary', 'code'=> 'TEMP', 'description' => 'Short-term employment, often covering absences or seasonal peaks.'],
            ['name' => 'Fixed Pay', 'code'=> 'FIXED', 'description' => 'Employment with a set salary for a defined scope of work or period.'],
            ['name' => 'Internship', 'code'=> 'INTERN', 'description' => 'A temporary position focused on training and gaining work experience.'],
            ['name' => 'Daily Wages', 'code'=> 'DPL', 'description' => 'Compensation is calculated and paid based on the number of days worked.'],
            ['name' => 'Visiting', 'code'=> 'VISIT', 'description' => 'Short-term or intermittent role, often for specialized expertise.'],
            ['name' => 'Part-time', 'code'=> 'PT', 'description' => 'Employment with fewer hours than full-time work, typically on a regular schedule.'],
            ['name' => 'Freelance', 'code'=> 'FREE', 'description' => 'Self-employed worker who provides services on a project basis.'],
            ['name' => 'Remote', 'code'=> 'REM', 'description' => 'Work performed primarily from outside the company premises.'],
            ['name' => 'Seasonal', 'code'=> 'SEAS', 'description' => 'Employment limited to specific periods of the year based on business cycles.'],
            ['name' => 'Probationary', 'code'=> 'PROB', 'description' => 'Initial trial period before potentially becoming a permanent employee.'],
            ['name' => 'Consultant', 'code'=> 'CONS', 'description' => 'External professional providing expert advice or services.'],
            ['name' => 'Apprenticeship', 'code'=> 'APPR', 'description' => 'Training position combining on-the-job experience with formal education.'],
        ];

        foreach ($jobNatures as $nature) {
            JobNature::create($nature);
        }
    }
}
