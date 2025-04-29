<?php

namespace Database\Seeders;

use App\Models\OfficeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //OfficeType::factory(20)->create();
        $officeTypes = [
            ['id' => 1, 'name' => 'Section', 'code' => 'SEC', 'description' => 'A major administrative or academic unit.'],
            ['id' => 2, 'name' => 'Department', 'code' => 'DEPT', 'description' => 'A specific division within a department.'],
            ['id' => 3, 'name' => 'Constituent College', 'code' => 'C-COL', 'description' => 'A college forming part of a university.'],
            ['id' => 4, 'name' => 'Hostel', 'code' => 'HOSTEL', 'description' => 'Student residential accommodation.'],
            ['id' => 5, 'name' => 'Guest House', 'code' => 'G-HOUSE', 'description' => 'Temporary accommodation for visitors.'],
            ['id' => 6, 'name' => 'Affiliated College', 'code' => 'A-COL', 'description' => 'A college associated with a university but largely independent.'],
        ];

        foreach ($officeTypes as $type) {
            OfficeType::create($type);
        }
        
    }
}
