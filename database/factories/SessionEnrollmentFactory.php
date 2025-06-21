<?php

namespace Database\Factories;

use App\Models\SessionEnrollment;
use App\Models\SessionOffering;
use App\Models\Student;
// use App\Models\EnrollmentStatus; // If you decide to use enrollment_status_id
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionEnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SessionEnrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sessionOffering = SessionOffering::factory()->create() ?? SessionOffering::inRandomOrder()->first();
        $student = Student::factory()->create() ?? Student::inRandomOrder()->first();
        // $enrollmentStatus = EnrollmentStatus::factory()->create() ?? EnrollmentStatus::inRandomOrder()->first(); // If using

        $enrollmentDate = $this->faker->dateTimeBetween(
            $sessionOffering->academicSession->start_date ?? '-1 year', 
            $sessionOffering->academicSession->end_date ?? 'now' 
        );

        return [
            'session_offering_id' => $sessionOffering->id,
            'student_id' => $student->id,
            'enrollment_date' => $enrollmentDate->format('Y-m-d'),
            'grades' => $this->faker->optional(0.4)->randomFloat(2, 50, 100), // Grades between 50-100, 40% chance
            'remarks' => $this->faker->optional()->sentence,
            'status' => $this->faker->randomElement(['Enrolled', 'Completed', 'Withdrawn', 'Failed']),
            // 'enrollment_status_id' => $enrollmentStatus->id, // If using
        ];
    }
}
