<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectPrerequisite;

class SubjectPrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure SubjectSeeder has run
        $cs101 = Subject::where('code', 'CS101')->first();
        $cs201 = Subject::where('code', 'CS201')->first();
        $cs305 = Subject::where('code', 'CS305')->first();
        $ma101 = Subject::where('code', 'MA101')->first();
        $ma202 = Subject::where('code', 'MA202')->first();

        if (!($cs101 && $cs201 && $cs305 && $ma101 && $ma202)) {
            $this->command->warn('One or more subjects not found. Please ensure SubjectSeeder has run and created the expected subjects. Skipping SubjectPrerequisiteSeeder.');
            return;
        }

        $prerequisites = [
            // CS201 (Data Structures) requires CS101 (Intro to Programming)
            ['subject_id' => $cs201->id, 'prerequisite_subject_id' => $cs101->id],
            // CS305 (Database Systems) requires CS201 (Data Structures)
            ['subject_id' => $cs305->id, 'prerequisite_subject_id' => $cs201->id],
            // MA202 (Linear Algebra) requires MA101 (Calculus I)
            ['subject_id' => $ma202->id, 'prerequisite_subject_id' => $ma101->id],
            // Example: CS305 (Database Systems) could also require MA101 (Calculus I) indirectly or directly
            // For simplicity, we'll stick to direct prerequisites based on common curriculum flow.
        ];

        foreach ($prerequisites as $prerequisite) {
            SubjectPrerequisite::create($prerequisite);
        }

        // Note: The 'prerequisite_subject_id' column in the 'subjects' table itself
        // is for a single, primary prerequisite. The 'subject_prerequisites' table
        // allows for multiple prerequisites for a single subject.
        // The SubjectSeeder already handles the primary prerequisite for CS201.
        // This seeder can be used to add more, or manage them exclusively here.
        // For consistency, it might be better to manage all prerequisites
        // through the SubjectPrerequisite model and table.

        // If SubjectSeeder already set $cs201->prerequisite_subject_id = $cs101->id;
        // then the entry in $prerequisites for CS201 might be redundant if that field
        // is meant to be the *only* way to define a prerequisite.
        // However, the `subject_prerequisites` table is more flexible for multiple prerequisites.
        // Let's assume `subjects.prerequisite_subject_id` is for a quick reference / main prerequisite
        // and `subject_prerequisites` is for the comprehensive list.
    }
}
