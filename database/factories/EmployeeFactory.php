<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\EmployeeWorkStatus; // Added import
use App\Models\JobNature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Removed unused variables $establishedYear, $hasHead
        $postalAddress = $this->faker->address(); // Define postal address once
        $appointmentDate = $this->faker->dateTimeBetween('-10 years', 'now'); // Define appointment date once

        return [
            'employee_id' => strtoupper(string: $this->faker->unique()->bothify('???####')),
            'first_name' => $this->faker->firstName(), // Use pk_PK provider
            'last_name' => $this->faker->lastName(), // Use pk_PK provider
            'email' => $this->faker->unique()->email(), // Use pk_PK provider (assuming standard 'email')
            'phone' => $this->faker->optional()->phoneNumber(), // Use pk_PK provider
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']), // Added gender
            'nic_no' => $this->faker->optional()->numerify('#####-#######-#'), // Added nic_no (optional)
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'), // Added date_of_birth
            'employee_type_id' => EmployeeType::inRandomOrder()->value('id') ?? EmployeeType::factory(),
            'job_nature_id' => JobNature::inRandomOrder()->value('id') ?? JobNature::factory(),
            'appointment_date' => $appointmentDate->format('Y-m-d'), // Use defined variable
            'termination_date' => ($termDate = $this->faker->optional(0.1)->dateTimeBetween($appointmentDate, '+5 years')) ? $termDate->format('Y-m-d') : null, // Use defined variable, check null
            'postal_address' => $postalAddress, // Added postal_address
            'permanent_address' => $this->faker->boolean(80) ? $postalAddress : $this->faker->address(), // Added permanent_address (often same as postal)
            'qualification' => $this->faker->randomElement(['BSc', 'MSc', 'PhD', 'Diploma', 'High School']),
            'specialization' => $this->faker->optional()->word(),
            'photo_path' => $this->faker->optional()->imageUrl(640, 480, 'people'),
            'bio' => $this->faker->optional()->paragraph(),
            'employee_work_status_id' => EmployeeWorkStatus::inRandomOrder()->value('id') ?? EmployeeWorkStatus::factory(), // Added employee_work_status_id
            // 'is_active' => $this->faker->boolean(90),
            // Removed created_at, updated_at (handled by Eloquent)
            // 'deleted_at' => null, // Handled by SoftDeletes trait
        ];
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the employee is inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the employee has a parent employee.
     * Avoids creating a parent if one already exists in the attributes.
     *
     * @param int|Employee|Factory|null $parent
     */
    public function withParent(int|Employee|Factory|null $parent = null): Factory
    {
        return $this->state(function (array $attributes) use ($parent) {
            // Only create a parent if one isn't already set and no specific parent is provided
            if (!isset($attributes['parent_employee_id']) && $parent === null) {
                 // Try to find an existing employee first to reduce infinite loops
                 $existingParent = Employee::inRandomOrder()->first();
                 if ($existingParent) {
                     // Ensure we don't assign the employee as its own parent if the factory is resolving itself
                     if (!isset($attributes['id']) || $attributes['id'] !== $existingParent->id) {
                         return ['parent_employee_id' => $existingParent->id];
                     }
                 }
                 // If no suitable existing parent, create a new one
                 return ['parent_employee_id' => Employee::factory()];
            }
            // If a specific parent is provided, use it
            elseif ($parent !== null) {
                 return ['parent_employee_id' => $parent];
            }
            // Otherwise, keep the existing parent_employee_id or null
            return [];
        });
    }


     /**
     * Indicate that the employee has a specific head.
     *
     * @param int|Employee|Factory|null $head
     */
    public function withHead(int|Employee|Factory|null $head = null): Factory
    {
        return $this->state(function (array $attributes) use ($head) {
            $headId = $head ?? Employee::factory(); // Assumes EmployeeFactory exists
            $establishedYear = $attributes['established_year'] ?? $this->faker->optional(80)->year(max: now()->year);

            return [
                'head_id' => $headId,
                'head_appointment_date' => $establishedYear
                                            ? $this->faker->dateTimeBetween($establishedYear . '-01-01', 'now')->format('Y-m-d')
                                            : $this->faker->date(),
            ];
        });
    }
}
