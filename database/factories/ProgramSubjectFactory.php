<?php

namespace Database\Factories;

use App\Models\ProgramSubject;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramSubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProgramSubject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Pakistani subject names
        $pakistaniSubjects = [
            'Introduction to Computing', 'Programming Fundamentals', 'Object Oriented Programming', 'Data Structures & Algorithms',
            'Calculus & Analytical Geometry', 'Linear Algebra', 'Differential Equations', 'Probability & Statistics',
            'Communication Skills', 'Islamic Studies', 'Pakistan Studies', 'Professional Ethics',
            'Digital Logic Design', 'Computer Organization & Assembly Language', 'Operating Systems', 'Database Systems',
            'Software Engineering', 'Web Engineering', 'Mobile Application Development', 'Artificial Intelligence',
            'Financial Accounting', 'Principles of Management', 'Marketing Management', 'Business Economics',
            'English Composition', 'Technical & Business Writing',
        ];
        
        $program = Program::inRandomOrder()->first();
        if (!$program) {
            // This fallback should ideally not be hit if ProgramSeeder runs first and creates programs.
            // If it is hit, it means ProgramSeeder might not have run or created enough data.
            $program = Program::factory()->create();
        }

        return [
            'program_id' => $program->id,
            'name' => $this->faker->randomElement($pakistaniSubjects) . ($this->faker->boolean(30) ? ' ' . $this->faker->randomElement(['I', 'II', 'Lab']) : ''),
            'subject_code' => strtoupper($this->faker->bothify('??-###')), // e.g., CS-101, MA-203
            'credits' => $this->faker->randomElement([1, 2, 3, 4]), // Credit hours
            'description' => $this->faker->optional()->sentence,
        ];
    }
}
