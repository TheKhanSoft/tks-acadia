<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeType>
 */
class EmployeeTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dateTime = $this->faker->numberBetween(1, 100) <= 30 ? Carbon::now() : null;
        return [
            'type' => Str::ucfirst($this->faker->unique()->word() ),
            'code' => strtoupper($this->faker->unique()->bothify('????##')),
            'description' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(70), // 70% chance of being active
            'deleted_at' => $dateTime,
        ];
    }

    /**
     * Indicate that the campus is active.
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the campus is inactive.
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
