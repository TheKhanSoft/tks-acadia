<?php

namespace Database\Factories;

use App\Models\DepartmentProgram;
use App\Models\Program;
use App\Models\Faculty; // Assuming Department is represented by Faculty
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepartmentProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Prefer existing faculty, then create if none found (requires FacultySeeder to run first)
        $faculty = Office::departments()->inRandomOrder()->firstOrFail() ?? Faculty::factory()->create();
        
        // For Program, it's often tied to a faculty.
        // The Program model does not have a direct faculty_id. Association is via DepartmentProgram.
        $program = Program::inRandomOrder()->firstOrFail() ?? Program::factory()->create(); // Removed ['faculty_id' => $faculty->id]

        // Attempt to find an existing unique pair or create one
        // This is a simplified approach; a more robust solution might involve checking the database
        // for existing unique pairs before attempting to create a new one.
        // The 'office_id' in DepartmentProgram model seems to be the faculty/department itself.
        // The 'program_id' links to a Program model.
        // The original factory had 'faculty_id' and 'program_id'.
        // Your DepartmentProgram model has 'office_id' and 'program_id'.
        // Assuming 'office_id' in DepartmentProgram refers to a record in 'faculties' table (acting as an office/department).

        return [
            'office_id' => $faculty->id, // Assuming office_id in DepartmentProgram is the ID from faculties table
            'program_id' => $program->id,
            'offered_since' => $this->faker->optional()->year($max = 'now'),
            'annual_intake' => $this->faker->optional()->numberBetween(30, 200),
            'is_flagship_program' => $this->faker->boolean(20), // 20% chance of being a flagship program
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
