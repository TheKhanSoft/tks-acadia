<?php

namespace Database\Factories;

use App\Models\SessionOffering;
use App\Models\AcademicSession; // Changed from Session
use App\Models\ProgramSubject;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionOfferingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SessionOffering::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $academicSession = AcademicSession::factory()->create() ?? AcademicSession::inRandomOrder()->first(); // Changed from Session
        $programSubject = ProgramSubject::factory()->create() ?? ProgramSubject::inRandomOrder()->first();
        $employee = Employee::factory()->create() ?? Employee::inRandomOrder()->first(); // Assumes EmployeeFactory exists

        return [
            'academic_session_id' => $academicSession->id, 
            'program_subject_id' => $programSubject->id,
            'employee_id' => $employee->id,
            'semester' => $this->faker->optional()->numberBetween(1, 8),
            'remarks' => $this->faker->optional()->sentence,
            'status' => $this->faker->randomElement(['Scheduled', 'Ongoing', 'Completed', 'Cancelled']),
        ];
    }
}
