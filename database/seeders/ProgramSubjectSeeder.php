<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramSubject;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProgramSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->command->warn('No programs found to assign subjects to. Skipping ProgramSubjectSeeder.');
            return;
        }

        if (ProgramSubject::count() > 0) {
            $this->command->info('ProgramSubjects table already has data. Skipping ProgramSubjectSeeder.');
            return;
        }

        // foreach ($programs as $program) {
        //     // Create a random number of subjects (e.g., 5 to 15) for each program
        //     $numberOfSubjects = rand(5, 15);
        //     ProgramSubject::factory()->count($numberOfSubjects)->create([
        //         'program_id' => $program->id, // Ensure subjects are linked to this specific program
        //     ]);
        // }
        $this->command->info('ProgramSubjects table seeded for existing programs.');
    }
}
