<?php

namespace Database\Factories;

use App\Models\EmployeeWorkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeWorkStatus>
 */
class EmployeeWorkStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeWorkStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use specific statuses as requested
        $statuses = ['Working', 'Earned Leave', 'Suspended', 'Terminated', 'Resigned', 'Retired', 'On Probation'];

        return [
            // Use unique() to ensure we don't try to create the same status twice during testing runs
            'name' => $this->faker->unique()->randomElement($statuses),
            'description' => $this->faker->optional()->sentence(), // Optional description
            'is_active' => $this->faker->boolean(90), // Most statuses are likely 'active' in the sense they are usable
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            // 'deleted_at' => null, // Handled by SoftDeletes trait if used
        ];
    }

    /**
     * Indicate that the employee work status is active.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the employee work status is inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
