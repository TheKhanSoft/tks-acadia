<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeOffice;
use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        $offices = Office::all();

        if ($employees->isEmpty() || $offices->isEmpty()) {
            $this->command->warn('Cannot run EmployeeOfficeSeeder: Employees or Offices table is empty.');
            return;
        }

        // Ensure every employee has at least one primary office
        foreach ($employees as $employee) {
            // Check if the employee already has a primary office from previous seeding attempts
            if (!$employee->offices()->wherePivot('is_primary_office', true)->exists()) {
                EmployeeOffice::factory()
                    ->for($employee)
                    ->for($offices->random())
                    ->primary() // Use the primary state
                    ->create();
            }
        }

        // Create additional random, non-primary assignments (e.g., 400 more)
        $additionalAssignments = 400;
        for ($i = 0; $i < $additionalAssignments; $i++) {
            EmployeeOffice::factory()
                ->for($employees->random())
                ->for($offices->random())
                ->notPrimary() // Use the notPrimary state
                ->create();
        }
    }
}
