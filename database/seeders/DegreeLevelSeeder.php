<?php

namespace Database\Seeders;

use App\Models\Degree;
use App\Models\DegreeLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DegreeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $faker = Faker::create();
        
        $degreeLevels = [
            ['id' => 1, 'name' => 'Undergraduate', 'description' => 'BS/LLB/PharmD etc' ,  'equivalent' => '16', 'minCrHr' => 130, 'minSemesters' => 8, 'maxSemesters' => 12 ],
            ['id' => 2, 'name' => 'Graduate',      'description' => 'MS/MPhil/MBA/LLM etc' ,  'equivalent' => '18', 'minCrHr' => 48, 'minSemesters' => 3, 'maxSemesters' => 6, ] ,
            ['id' => 3, 'name' => 'Graduate2PhD',      'description' => 'MPhil Leading to PhD' ,  'equivalent' => '19', 'minCrHr' => 60, 'minSemesters' => 5, 'maxSemesters' => 8, 'isActive' => false] ,
            ['id' => 4, 'name' => 'Postgraduate',  'description' => 'PhD' ,  'equivalent' => '20', 'minCrHr' => 60, 'minSemesters' => 6, 'maxSemesters' => 12, ],
            ['id' => 5, 'name' => 'Associate Degree', 'description' => 'AD' ,  'equivalent' => '14', 'minCrHr' => 60, 'minSemesters' => 4, 'maxSemesters' => 6,  ],
            ['id' => 6, 'name' => 'Lateral Entry' ,'description' => 'Lateral Entry in 5th Semester' ,  'equivalent' => '16', 'minCrHr' => 60, 'minSemesters' => 4, 'maxSemesters' => 6 ],
            ['id' => 7, 'name' => 'Diploma' ,'description' => 'Diploma courses' ,  'equivalent' => '-1', 'minCrHr' => 12, 'minSemesters' => 2, 'maxSemesters' => 3 ],
            ['id' => 8, 'name' => 'Certificate' ,'description' => 'Certificate courses' ,  'equivalent' => '-1', 'minCrHr' => 6, 'minSemesters' => 1, 'maxSemesters' => 2 ],
            ['id' => 9, 'name' => 'Undergraduate - BA/BSc' ,'description' => 'Bachelor of Arts/Master of Science (14-year degree) - not offering anymore' ,  'equivalent' => '14', 'minCrHr' => 60, 'minSemesters' => 4, 'maxSemesters' => 6, 'isActive' => false ],
            ['id' => 10, 'name' => 'Undergraduate - MA/MSc' ,'description' => 'Master of Arts/Master of Science (16-year degree) - not offering anymore' ,  'equivalent' => '16', 'minCrHr' => 60, 'minSemesters' => 4, 'maxSemesters' => 6, 'isActive' => false ],
        ];
       
        foreach ($degreeLevels as $level) {
            $level =
                [
                    'id' => $level['id'], 
                    'name' => $level['name'], 
                    'description' => $level['description'],
                    'equivalent' => $level['equivalent'],
                    'min_semester' => $level['minSemesters'],
                    'max_semester' => $level['maxSemesters'],
                    'total_credit_hours' => $level['minCrHr'],
                    'duration' => $level['minSemesters']/2,
                    'is_active' => $level['isActive'] ?? true,
                ]
            ;
            
            DegreeLevel::create($level);
        }
        $this->command->info('');
        $this->command->info('  Degree Level table Seeded Successfully!');
    }
}
