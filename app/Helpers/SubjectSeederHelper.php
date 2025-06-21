<?php

namespace App\Helpers;

use App\Models\Office;
use App\Models\Program;
use App\Models\Subject;
use App\Models\ProgramSubject;
use Illuminate\Support\Collection;

class SubjectSeederHelper
{
    private const SUBJECTS_PER_SEMESTER = 5;
    private const DEFAULT_SUBJECT_TYPE_ID = 1;

    /**
     * Run the subject seeder for specified departments
     *
     * @param string|array $departmentName
     * @param array $subjects
     * @param array $programSemesters
     * @return array
     */
    public function RunSeeder($departmentName, array $subjects, array $programSemesters): array
    {
        // Normalize department name to array for consistent handling
        $departmentNames = is_array($departmentName) ? $departmentName : [$departmentName];
        
        $departmentOffices = $this->getDepartmentOffices($departmentNames);
        
        if ($departmentOffices->isEmpty()) {
            return $this->createResponse('error', "Department(s) '" . implode(', ', $departmentNames) . "' not found. Skipping seeder.");
        }

        $parentDepartmentId = $departmentOffices->first()->id;
        $departmentIds = $departmentOffices->pluck('id');

        $this->setupProgramSemesters($programSemesters, $departmentIds);
        $this->processSubjects($subjects, $programSemesters, $parentDepartmentId);

        return $this->createResponse('info', "All subjects for " . implode(', ', $departmentNames) . " have been processed.");
    }

    /**
     * Get department offices by names
     *
     * @param array $departmentNames
     * @return Collection
     */
    private function getDepartmentOffices(array $departmentNames): Collection
    {
        return Office::whereIn('name', $departmentNames)->get();
    }

    /**
     * Setup program semesters with program IDs
     *
     * @param array $programSemesters
     * @param Collection $departmentIds
     * @return void
     */
    private function setupProgramSemesters(array &$programSemesters, Collection $departmentIds): void
    {
        foreach ($programSemesters as $level => &$info) {
            $programs = Program::whereIn('department_id', $departmentIds)
                              ->where('degree_level_id', $info['degree_level_id'])
                              ->get();

            if ($programs->isNotEmpty()) {
                $info['program_ids'] = $programs->pluck('id')->toArray();
            } else {
                // Log warning but continue processing
                $info['program_ids'] = [];
            }
        }
        unset($info); // Clean up reference
    }

    /**
     * Process all subjects and assign them to programs
     *
     * @param array $subjects
     * @param array $programSemesters
     * @param int $parentDepartmentId
     * @return void
     */
    private function processSubjects(array $subjects, array &$programSemesters, int $parentDepartmentId): void
    {
        foreach ($subjects as $subjectData) {
            $programLevel = $subjectData['program_level'];

            if (!$this->hasValidPrograms($programSemesters, $programLevel)) {
                continue;
            }

            $subject = $this->createOrUpdateSubject($subjectData, $parentDepartmentId);
            $assignedSemester = $this->assignSemester($programSemesters[$programLevel]);
            $this->createProgramSubjectRelations($subject, $programSemesters[$programLevel], $assignedSemester, $subjectData);
        }
    }

    /**
     * Check if valid programs exist for the given level
     *
     * @param array $programSemesters
     * @param string $programLevel
     * @return bool
     */
    private function hasValidPrograms(array $programSemesters, string $programLevel): bool
    {
        return isset($programSemesters[$programLevel]) && 
               !empty($programSemesters[$programLevel]['program_ids']);
    }

    /**
     * Create or update a subject
     *
     * @param array $subjectData
     * @param int $parentDepartmentId
     * @return Subject
     */
    private function createOrUpdateSubject(array $subjectData, int $parentDepartmentId): Subject
    {
        return Subject::updateOrCreate(
            [
                'code' => $subjectData['code'],
                'name' => $subjectData['name'],
                'description' => $subjectData['description'],
                'credit_hours' => $subjectData['credit_hours'],
                'subject_type_id' => $subjectData['subject_type_id'] ?? self::DEFAULT_SUBJECT_TYPE_ID,
                'parent_department_id' => $parentDepartmentId,
            ]
        );
    }

    /**
     * Assign semester for the subject
     *
     * @param array $currentProgramInfo
     * @return int
     */
    private function assignSemester(array &$currentProgramInfo): int
    {
        if ($currentProgramInfo['subjects_in_semester'] >= self::SUBJECTS_PER_SEMESTER) {
            if ($currentProgramInfo['current_semester'] < $currentProgramInfo['max_semesters']) {
                $currentProgramInfo['current_semester']++;
                $currentProgramInfo['subjects_in_semester'] = 0;
            }
            // If max semesters reached, continue with the last semester
        }
        
        $assignedSemester = $currentProgramInfo['current_semester'];
        $currentProgramInfo['subjects_in_semester']++;

        return $assignedSemester;
    }

    /**
     * Create program-subject relations
     *
     * @param Subject $subject
     * @param array $programInfo
     * @param int $semester
     * @param array $subjectData
     * @return void
     */
    private function createProgramSubjectRelations(Subject $subject, array $programInfo, int $semester, array $subjectData): void
    {
        $isElective = isset($subjectData['category']) && $subjectData['category'] === 'Elective';

        foreach ($programInfo['program_ids'] as $programId) {
            ProgramSubject::firstOrCreate([
                'program_id' => $programId,
                'subject_id' => $subject->id,
                'semester' => $semester,
                'is_elective' => $isElective,
            ]);
        }
    }

    /**
     * Create a response array
     *
     * @param string $type
     * @param string $message
     * @return array
     */
    private function createResponse(string $type, string $message): array
    {
        return [
            'type' => $type,
            'message' => $message
        ];
    }
}
