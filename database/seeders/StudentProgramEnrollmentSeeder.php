<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\DepartmentProgram;
use App\Models\Student;
use Illuminate\Database\Seeder;
use App\Models\StudentProgramEnrollment;
use App\Models\EnrollmentStatus; // Import EnrollmentStatus
use Illuminate\Support\Facades\Log; // For logging if needed

class StudentProgramEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $departmentPrograms = DepartmentProgram::all();
        $academicSessions = AcademicSession::all();
        $enrollmentStatuses = EnrollmentStatus::all(); // Fetch all enrollment statuses

        if ($students->isEmpty() || $departmentPrograms->isEmpty() || $academicSessions->isEmpty() || $enrollmentStatuses->isEmpty()) {
            $this->command->warn('Not enough prerequisite data (Students, DepartmentPrograms, AcademicSessions, or EnrollmentStatuses) to seed Student Program Enrollments. Skipping.');
            Log::warning('StudentProgramEnrollmentSeeder: Prerequisite data missing. Students: ' . $students->count() .
                         ', DeptPrograms: ' . $departmentPrograms->count() .
                         ', AcadSessions: ' . $academicSessions->count() .
                         ', EnrollStatuses: ' . $enrollmentStatuses->count());
            return;
        }

        $createdCount = 0;
        $desiredCount = 100; // How many unique enrollments you want to attempt to create
        $usedCombinations = [];

        // Shuffle collections to get more varied combinations if we don't iterate through all possibilities
        $students = $students->shuffle();
        $departmentPrograms = $departmentPrograms->shuffle();
        $academicSessions = $academicSessions->shuffle();

        // Attempt to create unique enrollments
        // This nested loop tries to find unique combinations.
        // It might not be the most efficient for very large N, but for a few hundred/thousand it's usually acceptable.
        foreach ($students as $student) {
            foreach ($departmentPrograms as $dp) {
                foreach ($academicSessions as $session) {
                    if ($createdCount >= $desiredCount) {
                        break 3; // Break all 3 loops
                    }

                    $combinationKey = $student->id . '-' . $dp->id . '-' . $session->id;

                    if (!isset($usedCombinations[$combinationKey])) {
                        // Get a random enrollment status for this enrollment
                        $randomEnrollmentStatus = $enrollmentStatuses->random();

                        StudentProgramEnrollment::factory()->create([
                            'student_id' => $student->id,
                            'department_program_id' => $dp->id,
                            'academic_session_id' => $session->id,
                            'enrollment_status_id' => $randomEnrollmentStatus->id, // Assign specific status
                            // Factory will fill other details like dates, remarks, grades
                        ]);
                        $usedCombinations[$combinationKey] = true;
                        $createdCount++;
                    }
                }
            }
        }

        if ($createdCount > 0) {
            $this->command->info("Successfully created {$createdCount} unique student program enrollments.");
        } else {
            $this->command->warn("Could not create any unique student program enrollments. Check data and logic.");
        }
        
        if ($createdCount < $desiredCount) {
            $this->command->warn("Attempted to create {$desiredCount} enrollments, but only {$createdCount} unique combinations were possible with the current data.");
        }
    }
}
