<?php

namespace Database\Factories;

use App\Models\JobNature;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobNatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobNature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(), // Simple unique word for factory state
        ];
    }
}
