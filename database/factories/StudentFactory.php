<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\City;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $genderValues = array_column(Gender::cases(), 'value');

        return [
            'student_id' => 'S' . $this->faker->unique()->numerify('####-#####'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->optional()->phoneNumber,
            'phone_alternative' => $this->faker->optional()->phoneNumber,
            'gender' => $this->faker->randomElement($genderValues),
            'nic_no' => $this->faker->optional()->numerify(str_repeat('#', config('constants.nic_no_length', 15))),
            'date_of_birth' => $this->faker->optional()->date('Y-m-d', '2005-01-01'),
            'postal_address' => $this->faker->optional()->address,
            'permanent_address' => $this->faker->optional()->address,
            'city_id' => City::inRandomOrder()->first()?->id, // Use an existing city; ensure CitySeeder runs before StudentSeeder
            'photo_path' => null, // Or $this->faker->imageUrl(640, 480, 'people', true, 'students') if image generation is desired
            'bio' => $this->faker->optional()->paragraph,
            'student_status_id' => StudentStatus::inRandomOrder()->first()?->id, // Use an existing student status
        ];
    }
}
