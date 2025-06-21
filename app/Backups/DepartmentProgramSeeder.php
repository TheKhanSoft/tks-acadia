<?php

namespace Database\Seeders;

use App\Models\DepartmentProgram;
use App\Models\Office;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DepartmentProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
       // DepartmentProgram::factory(50)->create();

        $allPrograms = Program::get(['id', 'name' ]);
        $program_prefixes = [ 'AD ', 'BS ', 'MS ', 'MPhil ', 'PhD ','MA ', 'MSc (Hons) ', 'MSc ' ];
        $allDepartments = Office::departments()->select(['id', 'short_name'])->get();
        

        $departmentProgram = [];
        foreach ($allPrograms as $program) {
            $prog_name = $program->name . "";
            foreach ($program_prefixes as $program_prefix) {
                $prog_name = str_replace($program_prefix, '', $prog_name);
            }
            var_dump($prog_name);
            
            foreach ($allDepartments->where('short_name', $prog_name) as $department) {
                $departmentProgram = [
                    'office_id' => $department->id, 
                    'program_id' => $program->id,
                    'offered_since' => $faker->optional()->year($max = 'now'),
                    'annual_intake' => $faker->optional()->numberBetween(30, 200),
                    'is_flagship_program' => $faker->boolean(20), // 20% chance of being a flagship program
                    'is_active' => $faker->boolean(90),
                ];

                //DepartmentProgram::create($departmentProgram);
            }
        }
    }
}
