<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LearningOutcome;
use App\Models\Subject;

class LearningOutcomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure SubjectSeeder has run
        $cs101 = Subject::where('code', 'CS101')->first();
        $cs201 = Subject::where('code', 'CS201')->first();
        $ma101 = Subject::where('code', 'MA101')->first();

        if (!($cs101 && $cs201 && $ma101)) {
            $this->command->warn('One or more subjects not found. Please ensure SubjectSeeder has run. Skipping LearningOutcomeSeeder.');
            return;
        }

        $learningOutcomes = [
            // Learning Outcomes for CS101 - Introduction to Programming
            [
                'outcomeable_id' => $cs101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Understand basic programming concepts like variables, control structures, and functions.'
            ],
            [
                'outcomeable_id' => $cs101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Be able to write simple programs to solve problems.'
            ],
            [
                'outcomeable_id' => $cs101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Develop debugging skills to identify and fix errors in code.'
            ],

            // Learning Outcomes for CS201 - Data Structures and Algorithms
            [
                'outcomeable_id' => $cs201->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Understand and implement fundamental data structures (arrays, lists, stacks, queues, trees, graphs).'
            ],
            [
                'outcomeable_id' => $cs201->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Analyze the time and space complexity of algorithms.'
            ],
            [
                'outcomeable_id' => $cs201->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Apply appropriate algorithms to solve complex computational problems.'
            ],

            // Learning Outcomes for MA101 - Calculus I
            [
                'outcomeable_id' => $ma101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Understand the concepts of limits, continuity, and derivatives.'
            ],
            [
                'outcomeable_id' => $ma101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Be able to compute derivatives of various functions.'
            ],
            [
                'outcomeable_id' => $ma101->id,
                'outcomeable_type' => Subject::class,
                'outcomes' => 'Apply differentiation to solve optimization and related rates problems.'
            ],
        ];

        foreach ($learningOutcomes as $lo_data) {
            LearningOutcome::create($lo_data);
        }
    }
}
