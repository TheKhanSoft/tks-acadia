<?php

namespace Database\Factories;

use App\Models\AcademicSession;
use App\Models\StudentProgramEnrollment;
use App\Models\DepartmentProgram;
use App\Models\Student;
use App\Models\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentProgramEnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentProgramEnrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // This factory now assumes that Student, DepartmentProgram, AcademicSession, and EnrollmentStatus
        // tables have been populated by their respective seeders.
        // It will throw an error if it can't find related records, which is a signal
        // that prerequisite seeders need to create more data.

        $student = Student::inRandomOrder()->firstOrFail();
        $departmentProgram = DepartmentProgram::inRandomOrder()->firstOrFail();
        $academicSession = AcademicSession::inRandomOrder()->firstOrFail();
        $enrollmentStatus = EnrollmentStatus::inRandomOrder()->firstOrFail();

        $enrollmentDate = $this->faker->dateTimeBetween($academicSession->start_date ?? '-2 years', $academicSession->end_date ?? 'now');
        $expectedCompletionDate = $this->faker->optional(0.8)->dateTimeBetween($enrollmentDate, $academicSession->end_date ?? '+4 years');

        return [
            'department_program_id' => $departmentProgram->id,
            'academic_session_id' => $academicSession->id,
            'student_id' => $student->id,
            'enrollment_date' => $enrollmentDate->format('Y-m-d'),
            'expected_completion_date' => $expectedCompletionDate ? $expectedCompletionDate->format('Y-m-d') : null,
            'actual_completion_date' => null, // Typically null on creation
            'grades' => $this->faker->optional(0.3)->randomFloat(2, 1, 4), // Optional grades
            'remarks' => $this->faker->optional()->sentence,
            'enrollment_status_id' => $enrollmentStatus->id,
        ];
    }
}
