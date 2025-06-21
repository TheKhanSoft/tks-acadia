<?php

namespace Database\Factories;

use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AcademicSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startYear = $this->faker->numberBetween(2020, 2025);
        $endYear = $startYear + $this->faker->numberBetween(1, 4); // AcademicSession duration 1 to 4 years

        return [
            'name' => $this->faker->unique()->numerify($startYear . '-' . $endYear . ' ###'),
            'start_date' => $this->faker->dateTimeBetween("$startYear-01-01", "$startYear-12-31")->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween("$endYear-01-01", "$endYear-12-31")->format('Y-m-d'),
            'type' => $this->faker->randomElement(['Academic Year', 'Semester', 'Trimester', 'Quarter']),
            'description' => $this->faker->optional()->sentence,
        ];
    }
}
