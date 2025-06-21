<?php

namespace Database\Factories;

use App\Models\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EnrollmentStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Enrolled', 'Completed', 'Dropped Out', 'Cancelled', 'Pending']),
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean(70), // 70% chance of being true, adjust as needed
        ];
    }
}
