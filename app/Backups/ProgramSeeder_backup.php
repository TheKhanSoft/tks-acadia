<?php

namespace Database\Seeders;

use App\Models\DegreeLevel;
use App\Models\Office;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProgramSeeder_backup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $programDetails = [
            ['id' => 1, 'title' => 'BS', 'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Science', 'equivalent' => '16', 'minCrHr' => 130, 'degreeLevel' => 1],
            ['id' => 2, 'title' => 'BSc (Hons)', 'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Science', 'equivalent' => '16', 'minCrHr' => 130, 'degreeLevel' => 1],
            
            ['id' => 3, 'title' => 'MPhil', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Philosophy', 'equivalent' => '18', 'minCrHr' => 48, 'degreeLevel' => 2],
            ['id' => 4, 'title' => 'MS', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science', 'equivalent' => '18', 'minCrHr' => 48, 'degreeLevel' => 2],
            ['id' => 5, 'title' => 'MSc (Hons)', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science', 'equivalent' => '18', 'minCrHr' => 48, 'degreeLevel' => 2],
            
            ['id' => 6, 'title' => 'MS Leading to PhD', 'minSemesters' => 6, 'maxSemesters' => 8, 'degreeTitle' => 'Master of Science Leading to Doctorate', 'equivalent' => '19', 'minCrHr' => 60, 'degreeLevel' => 3],
            
            ['id' => 7, 'title' => 'PhD', 'minSemesters' => 4, 'maxSemesters' => 12, 'degreeTitle' => 'Doctor of Philosophy', 'equivalent' => '20', 'minCrHr' => 60, 'degreeLevel' => 4],
            
            ['id' => 8, 'title' => 'AD', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Associate Degree', 'equivalent' => '14', 'minCrHr' => 60, 'degreeLevel' => 5],
            
            ['id' => 9, 'title' => 'Lateral Entry', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Bachelor of Science', 'equivalent' => '16', 'minCrHr' => 60, 'degreeLevel' => 6],
            
            ['id' => 10, 'title' => 'MA', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Art (16 years)', 'equivalent' => '16', 'minCrHr' => 60, 'degreeLevel' => 'Undergraduate'],
            ['id' => 11, 'title' => 'MSc', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science (16 years)', 'equivalent' => '16', 'minCrHr' => 60, 'degreeLevel' => 'Undergraduate'],
        ];

        $modeGroups = [
            'all'  => ['On-Campus', 'Online', 'Hybrid'], 
            'campus_online'  => ['On-Campus', 'Online'], 
            'hybrid'  => ['Hybrid'], 
            'campus'  => ['On-Campus'], 
            'online'  => [ 'Online'], 
            'no'  => [ 'Not Offer Anymore'], 
        ];

        $programNames = [
            ['title' => 'Accounting & Finance', 'departmentName' => 'Accounting & Finance', 'accreditationStatus' => '', 'accreditedBy' => '', 'departmentOfferings' => [1 => [1, 2, 3], 3 => [1], 7 => [1],  8 => [1, 2], 9 => [1, 2, 3], ] ], 
            ['title' => 'Artificial Intelligence', 'departmentName' =>'Computer Science',  'accreditationStatus' => '', 'accreditedBy' => '', 'departmentOfferings' => [1 => [1, 2, 3], 3 => [1], 7 => [1],  8 => [1, 2], 9 => [1, 2, 3], ] ],
            ['title' => 'Cyber Security', 'departmentName' =>'Computer Science',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Information Technology', 'departmentName' =>'Computer Science',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Software Engineering', 'departmentName' =>'Computer Science',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Chemistry', 'departmentName' =>  'Chemistry', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Data Science', 'departmentName' =>'Statistics',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Economics', 'departmentName' =>  'Economics', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'English Literature', 'departmentName' =>'English Literature', 'accreditationStatus' => '', 'accreditedBy' => '' ], 
            ['title' => 'International Relations', 'departmentName' =>'International Relations', 'accreditationStatus' => '', 'accreditedBy' => '' ], 
            ['title' => 'Islamic Studies', 'departmentName' =>'Islamic Studies',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Mathematics', 'departmentName' =>  'Mathematics', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Media Studies', 'departmentName' =>'JMC',  'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Physics', 'departmentName' =>  'Physics', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Psychology', 'departmentName' =>  'Psychology', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Sociology', 'departmentName' =>  'Sociology', 'accreditationStatus' => '', 'accreditedBy' => '' ],
            ['title' => 'Urdu', 'departmentName' =>  'Urdu', 'accreditationStatus' => '', 'accreditedBy' => '' ],
        ];
       
        $programWithDiscipline = [
            [ 'title' => 'BBA (Hons)', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Bachelor of Business Administration', 'equivalent' => '16'],
            [ 'title' => 'MBA', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Business Administration', 'equivalent' => '18'],
            [ 'title' => 'Executive MBA', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Executive Master of Business Administration', 'equivalent' => '18'],
            [ 'title' => 'Pharm-D', 'minSemesters' => 5, 'maxSemesters' => 14, 'degreeTitle' => 'Doctor of Pharmacy', 'equivalent' => '16'],
            [ 'title' => 'LLB', 'minSemesters' => 5, 'maxSemesters' => 14, 'degreeTitle' => 'Bachelor of Law', 'equivalent' => '16'],
            [ 'title' => 'DPT', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Doctor of Physical Therapy', 'equivalent' => '14'],
            [ 'title' => 'LLM', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Law', 'equivalent' => '18'],
            [ 'title' => 'Diploma in Web', 'minSemesters' => 2, 'maxSemesters' => 2, 'degreeTitle' => 'Diploma in Web Development', 'equivalent' => '14'],
            [ 'title' => 'Certificate in GD', 'minSemesters' => 1, 'maxSemesters' => 1, 'degreeTitle' => 'Certificate in Graphic Design', 'equivalent' => '14'],
        ];

        $allPrograms = [];
       
        foreach ($programNames as $programName) {
            foreach ($programName["departmentOfferings"] as $program => $deliver_mode) {
               
                foreach ($programDetails as $programDetail) {

                    $program_name = $programDetail['title'] . " " . $program['title'];
                    $program_duration = $programDetail['minSemesters']/2;

                    $office_id = Office::where('name', 'LIKE', "%$program[departmentName]%")->firstOrFail('id')->id;
                    
                    $degree_level_id = DegreeLevel::
                        where('name', 'LIKE', "%$program[departmentName]%")
                        ->where('duration', 'LIKE',$program_duration)
                        ->where('min_semester', 'LIKE',$programDetail['minSemesters'])
                        ->firstOrFail('id')->id;

                    $program =
                        [
                            'name' => $program_name, 
                            'code' => $faker->unique()->bothify(strtoupper(substr(str_replace(' ', '', $program_name), 0, 3)) . '-###'),
                            'description' => null,
                            'degree_title' => $programDetail['degreeTitle'],
                            
                            'department_id' => $office_id,
                            'degree_level_id' => $degree_level_id,
                            // 'delivery_mode_id',

                            'duration' => $program_duration,
                            'min_semester' => $programDetail['minSemesters'],
                            'max_semester' => $programDetail['maxSemesters'],
                            'total_credit_hours' => $programDetail['minCrHr'],
                            'equivalent' => $programDetail['equivalent'],
                            // 'program_level' => $programDetail['degreeLevel'],
                            'delivery_mode' => $value,
                            'department_name' => $program['departmentName'],
    
                            // $table->string('name');
                            // $table->string('code')->unique();
                            // $table->string('degree_title'); 
                            // $table->text('description')->nullable();
                            // $table->foreignId('department_id')->constrained("offices");
                            // $table->foreignId('degree_level_id')->constrained(); 
                            // $table->foreignId('delivery_mode_id')->nullable();            
                            // $table->string('duration')->nullable();
                            // $table->unsignedInteger('min_semester')->nullable();
                            // $table->unsignedInteger('max_semester')->nullable();
                            // $table->unsignedInteger('total_credit_hours')->nullable();
                            // $table->string('equivalent')->nullable(); 
                            // $table->string('accreditation_status')->nullable()->default('N/A'); 
                            // $table->date('start_date')->nullable();
                            // $table->json('prerequisites')->nullable(); 
                            // $table->json('learning_outcomes')->nullable(); 
                            // $table->boolean('is_active')->default(true);
                            
                            // 'accreditation' => $this->faker->optional()->randomElement($accreditations),
                            // 'start_date' => $this->faker->optional()->date('Y-m-d', '-10 years'),
                            // 'prerequisites' => $this->faker->optional()->words(3, true), // Example: "Intermediate, FSc Pre-Engineering"
                            // 'learning_outcomes' => $this->faker->optional()->sentences(2, true),
                            // // 'coordinator_id' => Employee::inRandomOrder()->first()?->id, // Assuming Employee model and seeder exist
                            // 'coordinator_id' => null, // Set to null for now
                            // 'is_active' => $this->faker->boolean(90),
    
                        ]
                    ;
                    Program::create($program);
                }
            
            }
            
        }
        // foreach ($programDetails as $programDetail) {
        //     foreach($programDetail['deliveryModes'] as $value){
        //         foreach ($programNames as $program) {
                    
        //             $program_name = $programDetail['title'] . " " . $program['title'];
        //             $program_duration = $programDetail['minSemesters']/2;

        //             $office_id = Office::where('name', 'LIKE', "%$program[departmentName]%")->firstOrFail('id')->id;
                    
        //             $degree_level_id = DegreeLevel::
        //                 where('name', 'LIKE', "%$program[departmentName]%")
        //                 ->where('duration', 'LIKE',$program_duration)
        //                 ->where('min_semester', 'LIKE',$programDetail['minSemesters'])
        //                 ->firstOrFail('id')->id;

        //             $program =
        //                 [
        //                     'name' => $program_name, 
        //                     'code' => $faker->unique()->bothify(strtoupper(substr(str_replace(' ', '', $program_name), 0, 3)) . '-###'),
        //                     'description' => null,
        //                     'degree_title' => $programDetail['degreeTitle'],
                            
        //                     'department_id' => $office_id,
        //                     'degree_level_id' => $degree_level_id,
        //                     // 'delivery_mode_id',

        //                     'duration' => $program_duration,
        //                     'min_semester' => $programDetail['minSemesters'],
        //                     'max_semester' => $programDetail['maxSemesters'],
        //                     'total_credit_hours' => $programDetail['minCrHr'],
        //                     'equivalent' => $programDetail['equivalent'],
        //                     // 'program_level' => $programDetail['degreeLevel'],
        //                     'delivery_mode' => $value,
        //                     'department_name' => $program['departmentName'],
    
        //                     // $table->string('name');
        //                     // $table->string('code')->unique();
        //                     // $table->string('degree_title'); 
        //                     // $table->text('description')->nullable();
        //                     // $table->foreignId('department_id')->constrained("offices");
        //                     // $table->foreignId('degree_level_id')->constrained(); 
        //                     // $table->foreignId('delivery_mode_id')->nullable();            
        //                     // $table->string('duration')->nullable();
        //                     // $table->unsignedInteger('min_semester')->nullable();
        //                     // $table->unsignedInteger('max_semester')->nullable();
        //                     // $table->unsignedInteger('total_credit_hours')->nullable();
        //                     // $table->string('equivalent')->nullable(); 
        //                     // $table->string('accreditation_status')->nullable()->default('N/A'); 
        //                     // $table->date('start_date')->nullable();
        //                     // $table->json('prerequisites')->nullable(); 
        //                     // $table->json('learning_outcomes')->nullable(); 
        //                     // $table->boolean('is_active')->default(true);
                            
        //                     // 'accreditation' => $this->faker->optional()->randomElement($accreditations),
        //                     // 'start_date' => $this->faker->optional()->date('Y-m-d', '-10 years'),
        //                     // 'prerequisites' => $this->faker->optional()->words(3, true), // Example: "Intermediate, FSc Pre-Engineering"
        //                     // 'learning_outcomes' => $this->faker->optional()->sentences(2, true),
        //                     // // 'coordinator_id' => Employee::inRandomOrder()->first()?->id, // Assuming Employee model and seeder exist
        //                     // 'coordinator_id' => null, // Set to null for now
        //                     // 'is_active' => $this->faker->boolean(90),
    
        //                 ]
        //             ;
        //             Program::create($program);
        //         }
        //     }
        // }

       // Program::insert($allPrograms);
    
    }
}
