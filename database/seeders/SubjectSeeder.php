<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\SubjectSeeders\BiochemistrySeeder;
use Database\Seeders\SubjectSeeders\BiotechnologySeeder;
use Database\Seeders\SubjectSeeders\BotanySeeder;
use Database\Seeders\SubjectSeeders\ChemistrySeeder;
use Database\Seeders\SubjectSeeders\ComputerScienceSeeder;
use Database\Seeders\SubjectSeeders\DataScienceSeeder;
use Database\Seeders\SubjectSeeders\EconomicsSeeder;
use Database\Seeders\SubjectSeeders\EnglishSeeder;
use Database\Seeders\SubjectSeeders\IslamicStudiesSeeder;
use Database\Seeders\SubjectSeeders\MathematicsSeeder;
use Database\Seeders\SubjectSeeders\PakistanStudiesSeeder;
use Database\Seeders\SubjectSeeders\PhysicsSeeder;
use Database\Seeders\SubjectSeeders\PsychologySeeder;
use Database\Seeders\SubjectSeeders\SociologySeeder;
use Database\Seeders\SubjectSeeders\StatisticsSeeder;
use Database\Seeders\SubjectSeeders\ZoologySeeder;
use Database\Seeders\SubjectSeeders\ManagementSciencesSeeder; 
use Database\Seeders\SubjectSeeders\EducationSeeder;
use Database\Seeders\SubjectSeeders\EnvironmentalScienceSeeder;
use Database\Seeders\SubjectSeeders\PharmacySeeder;

// use Database\Seeders\SubjectSeeders\GeographySeeder;
// use Database\Seeders\SubjectSeeders\HistorySeeder;
// use Database\Seeders\SubjectSeeders\InternationalRelationsSeeder;
// use Database\Seeders\SubjectSeeders\LawSeeder;
// use Database\Seeders\SubjectSeeders\MediaStudiesSeeder;
// use Database\Seeders\SubjectSeeders\PhilosophySeeder;
// use Database\Seeders\SubjectSeeders\PoliticalScienceSeeder;
// use Database\Seeders\SubjectSeeders\SocialWorkSeeder;
// use Database\Seeders\SubjectSeeders\SoftwareEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\FineArtsSeeder;
// use Database\Seeders\SubjectSeeders\ArchitectureSeeder;
// use Database\Seeders\SubjectSeeders\PharmacySeeder;
// use Database\Seeders\SubjectSeeders\AgricultureSeeder;
// use Database\Seeders\SubjectSeeders\VeterinarySciencesSeeder;
// use Database\Seeders\SubjectSeeders\ElectricalEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\MechanicalEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\CivilEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\ChemicalEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\PetroleumEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\TextileEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\AerospaceEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\BiomedicalEngineeringSeeder;
// use Database\Seeders\SubjectSeeders\FoodScienceTechnologySeeder;
// use Database\Seeders\SubjectSeeders\LibraryInformationScienceSeeder;
// use Database\Seeders\SubjectSeeders\PublicHealthSeeder;
// use Database\Seeders\SubjectSeeders\SportsSciencesSeeder;
// use Database\Seeders\SubjectSeeders\TourismHospitalitySeeder;
// use Database\Seeders\SubjectSeeders\GeneralCoursesSeeder; // For common/university-wide courses

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ComputerScienceSeeder::class,
            BiochemistrySeeder::class,
            BiotechnologySeeder::class,
            BotanySeeder::class,
            ChemistrySeeder::class,
            DataScienceSeeder::class,
            EconomicsSeeder::class,
            EducationSeeder::class,
            EnglishSeeder::class,
            IslamicStudiesSeeder::class,
            MathematicsSeeder::class,
            PakistanStudiesSeeder::class,
            PhysicsSeeder::class,
            PsychologySeeder::class,
            SociologySeeder::class,
            StatisticsSeeder::class,
            ZoologySeeder::class,
            ManagementSciencesSeeder::class,
            EnvironmentalScienceSeeder::class,
            PharmacySeeder::class,
            
            // GeographySeeder::class,
            // InternationalRelationsSeeder::class,
            // LawSeeder::class,
            // JMCSeeder::class,
            // PoliticalScienceSeeder::class,

            // SoftwareEngineeringSeeder::class,
            // FineArtsSeeder::class,
            // ArchitectureSeeder::class,
            // AgricultureSeeder::class,
            // VeterinarySciencesSeeder::class,
            // TextileEngineeringSeeder::class,
            // AerospaceEngineeringSeeder::class,
            // BiomedicalEngineeringSeeder::class,
            // FoodScienceTechnologySeeder::class,
            // LibraryInformationScienceSeeder::class,
            // PublicHealthSeeder::class,
            // SportsSciencesSeeder::class,
            // TourismHospitalitySeeder::class,
            // GeneralCoursesSeeder::class,
        ]);
    }
}
