<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeType;
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
        $establishedYear = $this->faker->optional(80)->year(max: now()->year); // 80% chance of having a year
        $hasHead = $this->faker->boolean(70); // 70% chance of having a head
        // 'employee_id', 'first_name', 'last_name', 'email', 'phone',
        // 'designation', 'employee_type_id', 'hire_date', 'termination_date',
        // 'qualification', 'specialization', 'photo_path', 'bio', 'is_active'

        return [
            'employee_id' => strtoupper(string: $this->faker->unique()->bothify('???####')),
            'first_name' => $this->faker->first_name(),
            'last_name' => $this->faker->last_name(),
            'employee_type_id' => EmployeeType::inRandomOrder()->value('id') ?? EmployeeType::factory(), // Use existing EmployeeType id or create a new one
            'description' => $this->faker->optional()->paragraph(), // Optional description
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'employee_location' => $this->faker->optional()->address(),
            'established_year' => $establishedYear,
            'parent_employee_id' => null, // Default to null, can be set via state or later logic
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
