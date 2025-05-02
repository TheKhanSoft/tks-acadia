<?php

namespace Database\Seeders;

use App\Models\EmployeeType; // Use EmployeeType model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade if needed
use Carbon\Carbon; // Import Carbon for timestamps

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Define common employee types with descriptions
        $types = [
            ['name' => 'Administrative Staff', 'code' => 'ADM', 'description' => 'Employees involved in the management and administrative operations.', 'is_active' => true],
            ['name' => 'Faculty', 'code' => 'FAC', 'description' => 'Employees primarily involved in teaching and research.', 'is_active' => true],
            ['name' => 'Technical Staff', 'code' => 'TEC', 'description' => 'Employees providing technical support (e.g., IT, lab technicians).', 'is_active' => true],
            ['name' => 'Support Staff', 'code' => 'SUP', 'description' => 'Employees providing general support services (e.g., clerical, maintenance).', 'is_active' => true],
            ['name' => 'Contractual Staff', 'code' => 'CON', 'description' => 'Employees hired on a fixed-term contract basis.', 'is_active' => true],
            ['name' => 'Visiting Faculty', 'code' => 'VIS', 'description' => 'Faculty members employed on a temporary or visiting basis.', 'is_active' => true],
            ['name' => 'Research Staff', 'code' => 'RES', 'description' => 'Employees primarily focused on research projects.', 'is_active' => true],
            ['name' => 'Project Staff', 'code' => 'PRO', 'description' => 'Employees hired specifically for externally funded projects.', 'is_active' => true],
            ['name' => 'Consultant', 'code' => 'CSL', 'description' => 'External experts hired for specific advisory roles.', 'is_active' => true],
            // Add other relevant types as needed
            // ['name' => 'Adjunct Faculty', 'code' => 'ADJ', 'description' => 'Part-time faculty members.', 'is_active' => true],
        ];

        // Use updateOrCreate to seed the data
        foreach ($types as $typeData) {
            EmployeeType::updateOrCreate(
                ['name' => $typeData['name']], // Unique key to find existing record
                [
                    'code' => $typeData['code'],
                    'description' => $typeData['description'],
                    'is_active' => $typeData['is_active'],
                    'created_at' => $now, // Set timestamps only if creating
                    'updated_at' => $now,
                ]
            );
        }
    }
}
