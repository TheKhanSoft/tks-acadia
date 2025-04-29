<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Office;
use App\Models\OfficeType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $establishedYear = $this->faker->optional(80)->year(max: now()->year); // 80% chance of having a year
        $hasHead = $this->faker->boolean(70); // 70% chance of having a head

        return [
            'name' => $this->faker->unique()->company() . ' ' . $this->faker->randomElement(['Department', 'Section', 'Hostel', 'Guest House']),
            'code' => strtoupper($this->faker->unique()->bothify('???###')),
            'office_type_id' => OfficeType::inRandomOrder()->value('id') ?? OfficeType::factory(), // Use existing OfficeType id or create a new one
            'description' => $this->faker->optional()->paragraph(), // Optional description
            // 'head_id' => $hasHead ? Employee::factory() : null, // Assumes EmployeeFactory exists
            // 'head_appointment_date' => $hasHead && $establishedYear
            //                             ? $this->faker->dateTimeBetween($establishedYear . '-01-01', 'now')->format('Y-m-d')
            //                             : ($hasHead ? $this->faker->date() : null),
            'office_location' => $this->faker->optional()->address(),
            'contact_email' => $this->faker->unique()->companyEmail(),
            'contact_phone' => $this->faker->optional()->phoneNumber(),
            'established_year' => $establishedYear,
            'parent_office_id' => null, // Default to null, can be set via state or later logic
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            // 'deleted_at' => null, // Handled by SoftDeletes trait
        ];
    }

    /**
     * Indicate that the office is active.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the office is inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the office has a parent office.
     * Avoids creating a parent if one already exists in the attributes.
     *
     * @param int|Office|Factory|null $parent
     */
    public function withParent(int|Office|Factory|null $parent = null): Factory
    {
        return $this->state(function (array $attributes) use ($parent) {
            // Only create a parent if one isn't already set and no specific parent is provided
            if (!isset($attributes['parent_office_id']) && $parent === null) {
                 // Try to find an existing office first to reduce infinite loops
                 $existingParent = Office::inRandomOrder()->first();
                 if ($existingParent) {
                     // Ensure we don't assign the office as its own parent if the factory is resolving itself
                     if (!isset($attributes['id']) || $attributes['id'] !== $existingParent->id) {
                         return ['parent_office_id' => $existingParent->id];
                     }
                 }
                 // If no suitable existing parent, create a new one
                 return ['parent_office_id' => Office::factory()];
            }
            // If a specific parent is provided, use it
            elseif ($parent !== null) {
                 return ['parent_office_id' => $parent];
            }
            // Otherwise, keep the existing parent_office_id or null
            return [];
        });
    }


     /**
     * Indicate that the office has a specific head.
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
