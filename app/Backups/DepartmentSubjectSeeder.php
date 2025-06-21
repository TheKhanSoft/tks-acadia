<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DepartmentSubject;
use App\Models\Office; // Assuming 'Department' is a type of 'Office'
use App\Models\Subject;

class DepartmentSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure OfficeSeeder and SubjectSeeder have run
        $csDepartment = Office::where('name', 'Computer Science Department')->first();
        $mathDepartment = Office::where('name', 'Mathematics Department')->first();
        // Add more departments if seeded by OfficeSeeder
        // $businessDepartment = Office::where('name', 'Business Department')->first();

        $cs101 = Subject::where('code', 'CS101')->first(); // Intro to Programming
        $cs201 = Subject::where('code', 'CS201')->first(); // Data Structures
        $cs305 = Subject::where('code', 'CS305')->first(); // Database Systems
        $ma101 = Subject::where('code', 'MA101')->first(); // Calculus I
        $ma202 = Subject::where('code', 'MA202')->first(); // Linear Algebra

        if (!($csDepartment && $mathDepartment && $cs101 && $cs201 && $cs305 && $ma101 && $ma202)) {
            $this->command->warn('Required Departments (Offices) or Subjects not found. Please ensure OfficeSeeder and SubjectSeeder have run. Skipping DepartmentSubjectSeeder.');
            return;
        }

        $departmentSubjects = [
            // Computer Science Department Subjects
            ['department_id' => $csDepartment->id, 'subject_id' => $cs101->id, 'is_active' => true],
            ['department_id' => $csDepartment->id, 'subject_id' => $cs201->id, 'is_active' => true],
            ['department_id' => $csDepartment->id, 'subject_id' => $cs305->id, 'is_active' => true],
            // CS department might also offer some math subjects or allow them as electives
            ['department_id' => $csDepartment->id, 'subject_id' => $ma101->id, 'is_active' => true], // e.g. Calculus for CS students

            // Mathematics Department Subjects
            ['department_id' => $mathDepartment->id, 'subject_id' => $ma101->id, 'is_active' => true],
            ['department_id' => $mathDepartment->id, 'subject_id' => $ma202->id, 'is_active' => true],
            // Math department might also offer foundational CS subjects
            // ['department_id' => $mathDepartment->id, 'subject_id' => $cs101->id, 'is_active' => true],
        ];

        foreach ($departmentSubjects as $dsData) {
            // Check if the combination already exists to prevent duplicate errors
            DepartmentSubject::firstOrCreate(
                ['department_id' => $dsData['department_id'], 'subject_id' => $dsData['subject_id']],
                ['is_active' => $dsData['is_active']]
            );
        }
    }
}
