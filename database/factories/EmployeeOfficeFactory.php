<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeOffice;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeOffice>
 */
class EmployeeOfficeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeOffice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = $this->faker->dateTimeBetween('-5 years', 'now');
        $isPrimary = $this->faker->boolean(80); // 80% chance of being primary

        return [
            'office_id' => Office::inRandomOrder()->value('id') ?? Office::factory(),
            'employee_id' => Employee::inRandomOrder()->value('id') ?? Employee::factory(),
            'role' => $this->faker->jobTitle(),
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $this->faker->optional(0.05)->dateTimeBetween($start_date, '+3 years')?->format('Y-m-d'), // 5% chance of having an end date
            'is_primary_office' => $isPrimary,
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Indicate that the office assignment is primary.
     */
    public function primary(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_primary_office' => true,
        ]);
    }

    /**
     * Indicate that the office assignment is not primary.
     */
    public function notPrimary(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_primary_office' => false,
        ]);
    }

    /**
     * Indicate that the office assignment is active.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the office assignment is inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
