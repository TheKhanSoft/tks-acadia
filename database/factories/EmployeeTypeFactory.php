<?php

namespace Database\Factories;

use App\Models\EmployeeType; // Use EmployeeType model
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeType>
 */
class EmployeeTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define common employee types
        $types = [
            'Faculty',
            'Administrative Staff',
            'Technical Staff',
            'Support Staff',
            'Contractual Staff',
            'Visiting Faculty',
            'Research Staff',
            'Project Staff',
            'Consultant',
        ];

        return [
            // Use unique() to ensure we don't try to create the same type twice during testing runs
            'name' => $this->faker->unique()->randomElement($types),
            'description' => $this->faker->optional()->sentence(), // Optional description
            'is_active' => $this->faker->boolean(95), // Most types are likely active
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            // 'deleted_at' => null, // Handled by SoftDeletes trait if used
        ];
    }

    /**
     * Indicate that the employee type is active.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the employee type is inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
