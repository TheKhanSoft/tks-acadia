<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\DegreeLevel;
use App\Models\Office;
use App\Models\Program;
use App\Models\Subject;
use App\Models\ProgramSubject;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class SubjectSeederHelper
{
    public function RunSeeder($departmentName, $subjects, $programSemesters)
    {
        $departmentOffices = Office::where('name', $departmentName)->get();
        $command = ['type' => 'error', 'message' => ""];

        if ($departmentOffices->isEmpty()) {
            $command = ['type' => 'error', 'message' => "Department '$departmentName' not found. Skipping ComputerScienceSeeder."];
            //Log::warning("Department '$departmentName' not found in ComputerScienceSeeder.");
            return;
        }

        // Use the first department for subject creation, assuming subjects are common across campuses.
        $parent_department_id = $departmentOffices->first()->id;
        $departmentIds = $departmentOffices->pluck('id');

        // Helper array to manage semester assignments per program level

        foreach ($programSemesters as $level => &$info) {
            $programs = Program::whereIn('department_id', $departmentIds)
                              ->where('degree_level_id', $info['degree_level_id'])
                              ->get();

            if ($programs->isNotEmpty()) {
                $info['program_ids'] = $programs->pluck('id')->all();
                $command = ['type' => 'info', 'message' => "Found " . count($info['program_ids']) . " programs for level '$level'."];
                //Log::info("Found " . count($info['program_ids']) . " programs for level '$level' in ComputerScienceSeeder.");
                //$this->command->info("Found " . count($info['program_ids']) . " programs for level '$level'.");
            } else {
                $command = ['type' => 'warn', 'message' => "No programs found for level '$level' in any of the '$departmentName' departments. Skipping subjects for this level."];
                // $this->command->warn("No programs found for level '$level' in any of the '$departmentName' departments. Skipping subjects for this level.");
                //Log::warning("No programs found for level '$level' in ComputerScienceSeeder.");
            }
        }
        unset($info);

        foreach ($subjects as $subjectData) {
            $programLevel = $subjectData['program_level'];

            if (!isset($programSemesters[$programLevel]) || empty($programSemesters[$programLevel]['program_ids'])) {
                $command = ['type' => 'warn', 'message' => "Skipping subject '{$subjectData['name']}' as no programs for level '$programLevel' were found."];
                // $this->command->warn("Skipping subject '{$subjectData['name']}' as no programs for level '$programLevel' were found.");
                continue;
            }

            $currentProgramInfo = &$programSemesters[$programLevel];

            $new_subject = Subject::firstOrCreate(
                [
                    'code' => $subjectData['code'],
                    'name' => $subjectData['name'],
                    'description' => $subjectData['description'],
                    'credit_hours' => $subjectData['credit_hours'],
                    'subject_type_id' => 1,
                    'parent_department_id' => $parent_department_id,
                ]
            );

            if ($currentProgramInfo['subjects_in_semester'] >= 5) {
                if ($currentProgramInfo['current_semester'] < $currentProgramInfo['max_semesters']) {
                    $currentProgramInfo['current_semester']++;
                    $currentProgramInfo['subjects_in_semester'] = 0;
                } else {
                    $command = ['type' => 'warn', 'message' => "Max semesters ({$currentProgramInfo['max_semesters']}) reached for program level '$programLevel', but more subjects exist. Subject '{$new_subject->name}' assigned to semester {$currentProgramInfo['current_semester']}."];
                    //$this->command->warn("Max semesters ({$currentProgramInfo['max_semesters']}) reached for program level '$programLevel', but more subjects exist. Subject '{$new_subject->name}' assigned to semester {$currentProgramInfo['current_semester']}.");
                    //Log::warning("Max semesters ({$currentProgramInfo['max_semesters']}) reached for program level '$programLevel', but more subjects exist. Subject '{$new_subject->name}' assigned to semester {$currentProgramInfo['current_semester']} in ComputerScienceSeeder.");
                }
            }
            
            $assignedSemester = $currentProgramInfo['current_semester'];
            $currentProgramInfo['subjects_in_semester']++;

            foreach ($currentProgramInfo['program_ids'] as $programId) {
                ProgramSubject::firstOrCreate(
                    [
                        'program_id' => $programId,
                        'subject_id' => $new_subject->id,
                        'semester' => $assignedSemester,
                        'is_elective' => (isset($subjectData['category']) && $subjectData['category'] === 'Elective'),
                    ]
                );
            }
            // $this->command->info("Processed subject: {$new_subject->name} for {$programLevel} programs, semester {$assignedSemester}");
        }
        $command = ['type' => 'info', 'message' => "All subjects for the $departmentName have been processed."];
        // $this->command->info( '   -- ' . $departmentName . ' subjects seeded successfully.');
        return $command;
    }
}