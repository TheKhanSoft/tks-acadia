<?php

namespace Database\Factories;

use App\Models\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Active', 'Graduated', 'Withdrawn', 'On Leave', 'Suspended']),
            'description' => $this->faker->sentence,
            'is_active_status' => $this->faker->boolean(95), 
        ];
    }
}
