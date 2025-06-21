<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Campus; // Assuming a Faculty belongs to a Campus
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Faculty::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Pakistani faculty/department names
        $pakistaniFaculties = [
            'Faculty of Arts & Humanities',
            'Faculty of Business & Economics',
            'Faculty of Chemical & Life Sciences',
            'Faculty of Physical & Numerical Sciences',
            'Faculty of Social Sciences',
        ];
        
        // Pakistani phone number formats
        $phoneFormats = [
            '03##-#######', // Mobile
            '021-########', // Karachi Landline
            '042-########', // Lahore Landline
            '051-########', // Islamabad Landline
        ];

        return [
            'name' => $this->faker->unique()->randomElement($pakistaniFaculties),
            'code' => $this->faker->unique()->bothify('FAC-###??'), // e.g., FAC-101CS
            'description' => $this->faker->optional()->sentence,
            // 'head_id' => Employee::inRandomOrder()->where('is_faculty_member', true)->first()?->id, // Assuming Employee model and a way to identify potential heads
            'head_id' => null, // Set to null for now, can be updated later or if EmployeeFactory is robust
            'head_appointment_date' => $this->faker->optional()->date('Y-m-d', '-5 years'),
            'contact_phone' => $this->faker->optional()->numerify($this->faker->randomElement($phoneFormats)),
            'contact_email' => $this->faker->optional()->unique()->safeEmailDomain('tks.edu.pk'),
            'established_year' => $this->faker->numberBetween(1950, date('Y')),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
