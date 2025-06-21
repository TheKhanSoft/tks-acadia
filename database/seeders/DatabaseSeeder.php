<?php

namespace Database\Seeders;

use App\Models\StudentProgramEnrollment;
use App\Models\Subject;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\JobNatureSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\ProgramSeeder;
use Database\Seeders\DepartmentProgramSeeder;
use Database\Seeders\ProgramSubjectSeeder; // Added
// Student Related Seeders
use Database\Seeders\StudentStatusSeeder;
use Database\Seeders\EnrollmentStatusSeeder;
use Database\Seeders\AcademicSessionSeeder;
use Database\Seeders\StudentSeeder;
use Database\Seeders\StudentProgramEnrollmentSeeder;
use Database\Seeders\SessionOfferingSeeder;
use Database\Seeders\SessionEnrollmentSeeder;
use Database\Seeders\AccreditationTypeSeeder; // Added
use Database\Seeders\SubjectTypeSeeder; // Added
use Database\Seeders\SubjectPrerequisiteSeeder; // Added
use Database\Seeders\LearningOutcomeSeeder; // Added
use Database\Seeders\DepartmentSubjectSeeder; // Added
use Database\Seeders\SubjectSeeders\BiochemistrySeeder;
use Database\Seeders\SubjectSeeders\BiotechnologySeeder;
use Database\Seeders\SubjectSeeders\BotanySeeder;
use Database\Seeders\SubjectSeeders\ChemistrySeeder;
use Database\Seeders\SubjectSeeders\ComputerScienceSeeder;
use Database\Seeders\SubjectSeeders\DataScienceSeeder;
use Database\Seeders\SubjectSeeders\EconomicsSeeder;
use Database\Seeders\SubjectSeeders\EducationSeeder;
use Database\Seeders\SubjectSeeders\EnglishSeeder;
use Database\Seeders\SubjectSeeders\EnvironmentalScienceSeeder;
use Database\Seeders\SubjectSeeders\IslamicStudiesSeeder;
use Database\Seeders\SubjectSeeders\ManagementSciencesSeeder;
use Database\Seeders\SubjectSeeders\MathematicsSeeder;
use Database\Seeders\SubjectSeeders\PakistanStudiesSeeder;
use Database\Seeders\SubjectSeeders\PhysicsSeeder;
use Database\Seeders\SubjectSeeders\PsychologySeeder;
use Database\Seeders\SubjectSeeders\SociologySeeder;
use Database\Seeders\SubjectSeeders\StatisticsSeeder;
use Database\Seeders\SubjectSeeders\ZoologySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            
           
            RolePermissionSeeder::class, // Added for Spatie roles and permissions

            // UserSeeder::class,
     
            // Location Seeders
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,

            CampusSeeder::class,
            FacultySeeder::class,
            OfficeTypeSeeder::class,
            OfficeSeeder::class,
            

            // //EMPLOYEE RELATED
            EmployeeWorkStatusSeeder::class,
            EmployeeTypeSeeder::class,
            JobNatureSeeder::class,
            PaymentMethodSeeder::class,
            EmployeeSeeder::class,
            EmployeeOfficeSeeder::class,

            // // PROGRAM RELATED
            AccreditationTypeSeeder::class,
            DeliveryModeSeeder::class, 
            DegreeLevelSeeder::class, 
            ProgramSeeder::class,
            ProgramSubjectSeeder::class, 
            //DepartmentProgramSeeder::class, 

             // // SUBJECT RELATED
             SubjectTypeSeeder::class,
             SubjectSeeder::class, // Main call to SubjectSeeder which handles specific subjects
             
             // SubjectPrerequisiteSeeder::class, 
             // LearningOutcomeSeeder::class, 
             // DepartmentSubjectSeeder::class, 

            // STUDENT RELATED
            StudentStatusSeeder::class,
            EnrollmentStatusSeeder::class,
            AcademicSessionSeeder::class,
          
            // Ensure CitySeeder, EmployeeSeeder are run if their factories are not self-sufficient or if specific prerequisite data is needed.
            // For now, factories will attempt to create them or use existing ones.
            StudentSeeder::class, // Depends on StudentStatusSeeder (and potentially CitySeeder)
            StudentProgramEnrollmentSeeder::class, // Depends on StudentSeeder, AcademicSessionSeeder, EnrollmentStatusSeeder, DepartmentProgramSeeder
            SessionOfferingSeeder::class, // Depends on AcademicSessionSeeder (and potentially ProgramSubjectSeeder, EmployeeSeeder)
            SessionEnrollmentSeeder::class, // Depends on SessionOfferingSeeder, StudentSeeder
            // StudentProgramEnrollmentSeeder::class, // Removed duplicate call

        ]);
    }
}
