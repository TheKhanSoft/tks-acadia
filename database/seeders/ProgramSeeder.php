<?php

namespace Database\Seeders;

use App\Models\DegreeLevel;
use App\Models\DeliveryMode;
use App\Models\DepartmentProgram;
use App\Models\Office;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Generator as FakerGenerator;

class ProgramSeeder extends Seeder
{
    protected FakerGenerator $faker;

    protected array $programDetails = [
        // BS/BSc
        ['id' => 1, 'title' => 'BS', 'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Science', 'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 1],
        ['id' => 2, 'title' => 'BSc (Hons)', 'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Science (Honours)', 'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 1],
        // MPhil/MS/MSc (18 years)
        ['id' => 3, 'title' => 'MPhil', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Philosophy', 'equivalent' => '18 Years', 'minCrHr' => 30, 'degreeLevel' => 2],
        ['id' => 4, 'title' => 'MS', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science', 'equivalent' => '18 Years', 'minCrHr' => 30, 'degreeLevel' => 2],
        ['id' => 5, 'title' => 'MSc (Hons)', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science (Honours)', 'equivalent' => '18 Years', 'minCrHr' => 30, 'degreeLevel' => 2],
        // MS Leading to PhD
        ['id' => 6, 'title' => 'MS Leading to PhD', 'minSemesters' => 6, 'maxSemesters' => 10, 'degreeTitle' => 'Master of Science Leading to Doctorate', 'equivalent' => '18+ Years', 'minCrHr' => 60, 'degreeLevel' => 3],
        // PhD
        ['id' => 7, 'title' => 'PhD', 'minSemesters' => 6, 'maxSemesters' => 12, 'degreeTitle' => 'Doctor of Philosophy', 'equivalent' => '18+ Years', 'minCrHr' => 48, 'degreeLevel' => 4],
        // Associate Degree
        ['id' => 8, 'title' => 'AD', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Associate Degree', 'equivalent' => '14 Years', 'minCrHr' => 60, 'degreeLevel' => 5],
        // Lateral Entry
        ['id' => 9, 'title' => 'Lateral Entry', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Bachelor of Science (Lateral Entry)', 'equivalent' => '16 Years', 'minCrHr' => 60, 'degreeLevel' => 1],
        // MA/MSc (16 years)
        ['id' => 10, 'title' => 'MA', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Arts (16 years)', 'equivalent' => '16 Years', 'minCrHr' => 60, 'degreeLevel' => 'Undergraduate'],
        ['id' => 11, 'title' => 'MSc', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science (16 years)', 'equivalent' => '16 Years', 'minCrHr' => 60, 'degreeLevel' => 'Undergraduate'],
        // Diploma & Certificate
        ['id' => 12, 'title' => 'Diploma', 'minSemesters' => 2, 'maxSemesters' => 4, 'degreeTitle' => 'Diploma', 'equivalent' => '13 Years', 'minCrHr' => 30, 'degreeLevel' => 7],
        ['id' => 13, 'title' => 'Certificate', 'minSemesters' => 1, 'maxSemesters' => 2, 'degreeTitle' => 'Certificate', 'equivalent' => '12+ Years', 'minCrHr' => 15, 'degreeLevel' => 8],
    ];

    protected array $programs = 
    [
        // Discipline-based entries
        ['title' => 'Artificial Intelligence', 'departmentName' =>'Department of Computer Science', 'campuses' => ['Garden Campus'], 'accreditedBy' => 'NCEAC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSAI', 'MSAI', 'PHDAI', 'LATAI']],
        ['title' => 'Cyber Security', 'departmentName' =>'Department of Computer Science', 'campuses' => ['Garden Campus'], 'accreditedBy' => 'NCEAC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSCSEC', 'MSCSEC', 'PHDCSEC', 'LATCSEC']],
        ['title' => 'Software Engineering', 'departmentName' =>'Department of Computer Science', 'campuses' => ['Garden Campus'], 'accreditedBy' => 'NCEAC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSSE', 'MSSE', 'PHDSE', 'LATSE']],
        ['title' => 'Accounting & Finance', 'departmentName' => 'Department of Accounting & Finance', 'campuses' => ['Garden Campus'], 'accreditationStatus' => 'Applied', 'accreditedBy' => 'NACTEAC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSAF', 'MSAF', 'PHDAF', 'LATAF']],
        ['title' => 'Chemistry', 'departmentName' =>  'Department of Chemistry', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSCHEM', 'MSCHEM', 'PHDCHEM', 'LATCHEM']],
        ['title' => 'Data Science', 'departmentName' =>'Department of Statistics',  'campuses' => ['Garden Campus'], 'accreditationStatus' => 'Applied', 'accreditedBy' => 'NCEAC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSDS', 'MSDS', 'PHDDS', 'LATDS']],
        ['title' => 'Economics', 'departmentName' =>  'Department of Economics', 'campuses' => ['Garden Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSECO', 'MSECO', 'PHDECO', 'LATECO', 'BSECO-T', 'MSECO-T', 'PHDECO-T', 'LATECO-T']],
        ['title' => 'English', 'departmentName' =>'Department of English', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSENG', 'MSENG', 'PHDENG', 'LATENG']],
        ['title' => 'International Relations', 'departmentName' =>'Department of International Relations', 'campuses' => ['Main Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSIR-M', 'MSIR-M', 'PHDIR-M', 'LATIR-M']],
        ['title' => 'Islamic Studies', 'departmentName' =>'Department of Islamic Studies',  'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSIS', 'MSIS', 'PHDIS', 'LATIS']],
        ['title' => 'Mathematics', 'departmentName' =>  'Department of Mathematics', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSMATH', 'MSMATH', 'PHDMATH', 'LATMATH']],
        ['title' => 'Media Studies', 'departmentName' =>'Department of Journalism and Mass Communication',  'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSMS', 'MSMS', 'PHDMS', 'LATMS']],
        ['title' => 'Physics', 'departmentName' =>  'Department of Physics', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPHY', 'MSPHY', 'PHDPHY', 'LATPHY']],
        ['title' => 'Psychology', 'departmentName' =>  'Department of Psychology', 'campuses' => ['Main Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPSY-M', 'MSPSY-M', 'PHDPSY-M', 'LATPSY-M']],
        ['title' => 'Sociology', 'departmentName' =>  'Department of Sociology', 'campuses' => ['Main Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSSOC-M', 'MSSOC-M', 'PHDSOC-M', 'LATSOC-M', 'BSSOC-T', 'MSSOC-T', 'PHDSOC-T', 'LATSOC-T']],
        ['title' => 'Agriculture', 'departmentName' => 'Department of Agriculture', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSAGRI', 'MSAGRI', 'PHDAGRI', 'LATAGRI']],
        ['title' => 'Biochemistry', 'departmentName' => 'Department of Biochemistry', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSBC', 'MSBC', 'PHDBC', 'LATBC']],
        ['title' => 'Biotechnology', 'departmentName' => 'Department of Biotechnology', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSBT', 'MSBT', 'PHDBT', 'LATBT']],
        ['title' => 'Botany', 'departmentName' => 'Department of Botany', 'campuses' => ['Garden Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSBOT', 'MSBOT', 'PHDBOT', 'LATBOT', 'BSBOT-T', 'MSBOT-T', 'PHDBOT-T', 'LATBOT-T']],
        ['title' => 'Digital Marketing', 'departmentName' => 'Department of Digital Marketing (IBL)', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSDM', 'MSDM', 'PHDDM', 'LATDM']],
        ['title' => 'Education', 'departmentName' => 'Department of Education', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSED', 'MSED', 'PHDED', 'LATED']],
        ['title' => 'Entomology', 'departmentName' => 'Department of Entomology', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSENTO', 'MSENTO', 'PHDENTO', 'LATENTO']],
        ['title' => 'Environmental Science', 'departmentName' => 'Department of Environmental Science', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSES', 'MSES', 'PHDES', 'LATES']],
        ['title' => 'Food Science & Technology', 'departmentName' => 'Department of Food Science & Technology', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSFST', 'MSFST', 'PHDFST', 'LATFST']],
        ['title' => 'Geology', 'departmentName' => 'Department of Geology', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSGEO', 'MSGEO', 'PHDGEO', 'LATGEO']],
        ['title' => 'Health & Physical Education', 'departmentName' => 'Department of Health and Physical Education', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSHPE', 'MSHPE', 'PHDHPE', 'LATHPE']],
        ['title' => 'Management Science', 'departmentName' => 'Department of Management Science', 'campuses' => ['Pabbi Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSMS-P', 'MSMS-P', 'PHDMS-P', 'LATMS-P', 'BSMS-T', 'MSMS-T', 'PHDMS-T', 'LATMS-T']],
        ['title' => 'Microbiology', 'departmentName' => 'Department of Microbiology', 'campuses' => ['Garden Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSMB', 'MSMB', 'PHDMB', 'LATMB', 'BSMB-T', 'MSMB-T', 'PHDMB-T', 'LATMB-T']],
        ['title' => 'Medical Laboratory Technology', 'departmentName' => 'Department of Microbiology', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1]], 'codes' => ['BSMLT']],
        ['title' => 'Pakistan Studies', 'departmentName' => 'Department of Pakistan Studies', 'campuses' => ['Main Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPS-M', 'MSPS-M', 'PHDPS-M', 'LATPS-M']],
        ['title' => 'Pashto', 'departmentName' => 'Department of Pashto', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPA', 'MSPA', 'PHDPA', 'LATPA']],
        ['title' => 'Political Science', 'departmentName' => 'Department of Political Science', 'campuses' => ['Main Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPSC-M', 'MSPSC-M', 'PHDPSC-M', 'LATPSC-M']],
        ['title' => 'Tourism & Hospitality', 'departmentName' => 'Department of Tourism & Hospitality', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSTH', 'MSTH', 'PHDTH', 'LATTH']],
        ['title' => 'Zoology', 'departmentName' => 'Department of Zoology', 'campuses' => ['Garden Campus', 'Timergara Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSZOO', 'MSZOO', 'PHDZOO', 'LATZOO', 'BSZOO-T', 'MSZOO-T', 'PHDZOO-T', 'LATZOO-T']],
        ['title' => 'Fine Arts', 'departmentName' =>  'Pakhtunkhwa College of Arts', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSFA', 'MSFA', 'PHDFA', 'LATFA']],
        ['title' => 'Graphic Design', 'departmentName' =>  'Pakhtunkhwa College of Arts', 'campuses' => ['Garden Campus'], 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSGD', 'MSGD', 'PHDGD', 'LATGD']],
        ['title' => 'Animal Husbandry', 'departmentName' => 'College of Veterinary Sciences and Animal Husbandry', 'campuses' => ['Garden Campus'], 'accreditationStatus' => 'Pending', 'accreditedBy' => 'PVMC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSAH', 'MSAH', 'PHDAH', 'LATAH']],
        ['title' => 'Poultry Science', 'departmentName' => 'College of Veterinary Sciences and Animal Husbandry', 'campuses' => ['Garden Campus'], 'accreditationStatus' => 'Pending', 'accreditedBy' => 'PVMC', 'departmentOfferings' => [1 => [1], 4 => [1], 7 => [1], 9 => [1]], 'codes' => ['BSPS', 'MSPS', 'PHDPS', 'LATPS']],

        // Fixed Programs
        [
            'isFixedProgram' => true, 'name' => 'BCS (Hons)', 'departmentName' =>'Department of Computer Science',  'campuses' => ['Garden Campus', 'Pabbi Campus', 'Timergara Campus'],
            'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Computer Science (Honours)',
            'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 1,
            'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NCEAC',
            'codes' => ['BCS', 'BCS-P', 'BCS-T']
        ],
        [
            'isFixedProgram' => true, 'name' => 'Lateral Entry to BCS (Hons)', 'departmentName' =>'Department of Computer Science',  'campuses' => ['Garden Campus', 'Pabbi Campus', 'Timergara Campus'],
            'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Lateral Entry to Bachelor of Computer Science (Honours)',
            'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 9,
            'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NCEAC',
            'codes' => ['LATBCS', 'LATCS-P', 'LATCS-T']
        ],
        [
            'isFixedProgram' => true, 'name' => 'BBA', 'departmentName' => 'Institute of Business Leadership', 'campuses' => ['Garden Campus'],
            'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Business Administration (Honours)',
            'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 1,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NACTEAC',
            'codes' => ['BBA', 'BBA-P', 'BBA-T']
        ],
        [
            'isFixedProgram' => true, 'name' => 'BBA', 'departmentName' => 'Department of Management Science', 'campuses' => [ 'Pabbi Campus', 'Timergara Campus'],
            'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Business Administration (Honours)',
            'equivalent' => '16 Years', 'minCrHr' => 124, 'degreeLevel' => 1,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NACTEAC',
            'codes' => ['BBA-P', 'BBA-T']
        ],
        [
            'isFixedProgram' => true, 'name' => 'MBA', 'departmentName' => 'Institute of Business Leadership', 'campuses' => ['Garden Campus'],
            'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Business Administration',
            'equivalent' => '18 Years', 'minCrHr' => 60, 'degreeLevel' => 2,
            'deliveryModeIds' => [1, 2], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NACTEAC', 'codes' => ['MBA']
        ],
        [
            'isFixedProgram' => true, 'name' => 'Executive MBA', 'departmentName' => 'Institute of Business Leadership', 'campuses' => ['Garden Campus'],
            'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Executive Master of Business Administration',
            'equivalent' => '18 Years', 'minCrHr' => 48, 'degreeLevel' => 2,
            'deliveryModeIds' => [1, 2], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'NACTEAC', 'codes' => ['EMBA']
        ],
        [
            'isFixedProgram' => true, 'name' => 'PharmD', 'departmentName' => 'Department of Pharmacy', 'campuses' => ['Garden Campus'],
            'minSemesters' => 10, 'maxSemesters' => 14, 'degreeTitle' => 'Doctor of Pharmacy',
            'equivalent' => '17 Years', 'minCrHr' => 180, 'degreeLevel' => 6,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'Pharmacy Council', 'codes' => ['PHARMD']
        ],
        [
            'isFixedProgram' => true, 'name' => 'LLB', 'departmentName' => 'Department of Law', 'campuses' => ['Main Campus'],
            'minSemesters' => 10, 'maxSemesters' => 14, 'degreeTitle' => 'Bachelor of Laws',
            'equivalent' => '16 Years', 'minCrHr' => 150, 'degreeLevel' => 1,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'Pakistan Bar Council', 'codes' => ['LLB-M']
        ],
        [
            'isFixedProgram' => true, 'name' => 'LLM', 'departmentName' => 'Department of Law', 'campuses' => ['Main Campus'],
            'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Laws',
            'equivalent' => '18 Years', 'minCrHr' => 30, 'degreeLevel' => 2,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Accredited', 'accreditedBy' => 'HEC', 'codes' => ['LLM-M']
        ],
        [
            'isFixedProgram' => true, 'name' => 'Diploma in Web Development', 'departmentName' => 'Department of Computer Science', 'campuses' => ['Garden Campus'],
            'minSemesters' => 2, 'maxSemesters' => 2, 'degreeTitle' => 'Diploma in Web Development',
            'equivalent' => '14 Years', 'minCrHr' => 30, 'degreeLevel' => 7,
            'deliveryModeIds' => [1, 2], 'accreditationStatus' => 'N/A', 'accreditedBy' => '', 'codes' => ['DIPWD']
        ],
        [
            'isFixedProgram' => true, 'name' => 'Certificate in Graphic Design', 'departmentName' => 'Department of Computer Science', 'campuses' => ['Garden Campus'],
            'minSemesters' => 1, 'maxSemesters' => 1, 'degreeTitle' => 'Certificate in Graphic Design',
            'equivalent' => '12+ Years', 'minCrHr' => 15, 'degreeLevel' => 8,
            'deliveryModeIds' => [1, 2], 'accreditationStatus' => 'N/A', 'accreditedBy' => '', 'codes' => ['CERTGD']
        ],
        [
            'isFixedProgram' => true, 'name' => 'DVM', 'departmentName' => 'College of Veterinary Sciences and Animal Husbandry', 'campuses' => ['Garden Campus'],
            'minSemesters' => 10, 'maxSemesters' => 14, 'degreeTitle' => 'Doctor of Veterinary Medicine',
            'equivalent' => '17 Years', 'minCrHr' => 190, 'degreeLevel' => 6,
            'deliveryModeIds' => [1], 'accreditationStatus' => 'Pending', 'accreditedBy' => 'PVMC', 'codes' => ['DVM']
        ],
    ];

    private array $degreeLevelsByIdCache;
    private array $degreeLevelsByNameCache;
    private array $deliveryModesCache;
    private array $campusMap;
    private array $degreeCodePrefixMap;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    private function initializeCaches(): void
    {
        $this->degreeLevelsByIdCache = DegreeLevel::pluck('id', 'id')->all();
        $this->degreeLevelsByNameCache = DegreeLevel::pluck('id', 'name')->all();
        $this->deliveryModesCache = DeliveryMode::pluck('id', 'id')->all();

        $this->campusMap = [
            'Garden Campus'    => ['id' => 1, 'suffix' => ''],
            'Main Campus'      => ['id' => 2, 'suffix' => '-M'],
            'Timergara Campus' => ['id' => 3, 'suffix' => '-T'],
            'Pabbi Campus'     => ['id' => 4, 'suffix' => '-P'],
        ];

        $this->degreeCodePrefixMap = collect($this->programDetails)->mapWithKeys(function ($detail) {
            $title = $detail['title'];
            $prefix = strtoupper(str_replace([' (Hons)', ' '], ['', ''], $title));
            if ($title === 'Lateral Entry') $prefix = 'LAT';
            return [$detail['id'] => $prefix];
        })->all();
    }

    private function getDegreeLevelId(string|int $degreeLevelInput): ?int
    {
        if (is_numeric($degreeLevelInput)) {
            return $this->degreeLevelsByIdCache[$degreeLevelInput] ?? null;
        }
        if (is_string($degreeLevelInput)) {
            $normalizedName = Str::title($degreeLevelInput);
            return $this->degreeLevelsByNameCache[$normalizedName]
                ?? $this->degreeLevelsByNameCache[$degreeLevelInput]
                ?? null;
        }
        return null;
    }

    private function getPrimaryDeliveryModeId(array $deliveryModeIds): ?int
    {
        if (empty($deliveryModeIds)) {
            return $this->deliveryModesCache[1] ?? null;
        }
        foreach ($deliveryModeIds as $dmId) {
            if (isset($this->deliveryModesCache[$dmId])) {
                return $dmId;
            }
        }
        return $this->deliveryModesCache[1] ?? null;
    }

    public function run(): void
    {
        $this->initializeCaches();
        DB::transaction(function () {
            $this->seedProgramsFromArray();
        });
        $this->command->info('ProgramSeeder completed successfully.');
    }

    private function seedProgramsFromArray(): void
    {
        foreach ($this->programs as $programEntry) {
            foreach ($programEntry['campuses'] as $campusName) {
                $campusInfo = $this->campusMap[$campusName] ?? null;
                if (!$campusInfo) {
                    $this->command->warn("Campus '{$campusName}' not found in map. Skipping.");
                    continue;
                }

                $department = Office::where('name', $programEntry['departmentName'])
                                    ->where('campus_id', $campusInfo['id'])
                                    ->first();

                if (!$department) {
                    $this->command->warn("Department '{$programEntry['departmentName']}' not found for campus '{$campusName}'. Skipping.");
                    continue;
                }

                if (isset($programEntry['isFixedProgram']) && $programEntry['isFixedProgram']) {
                    $this->createFixedProgram($programEntry, $department, $campusInfo, $campusName);
                } else {
                    $this->createDisciplinePrograms($programEntry, $department, $campusInfo, $campusName);
                }
            }
        }
    }

    private function createFixedProgram(array $programEntry, Office $department, array $campusInfo, string $campusName): void
    {
        $programData = $programEntry;
        $programData['deliveryModeIds'] = $programData['deliveryModeIds'] ?? [1];
        
        $programData['name'] = $programEntry['name'] . ($campusInfo['suffix'] ? " ({$campusName})" : '');

        $code = collect($programEntry['codes'])->first(function ($c) use ($campusInfo) {
            return $campusInfo['suffix'] === ''
                ? !Str::contains($c, '-')
                : Str::endsWith($c, $campusInfo['suffix']);
        });

        if (!$code) {
            $this->command->warn("No matching code found for '{$programData['name']}' on campus '{$department->campus->name}'. Using first available.");
            $code = $programEntry['codes'][0] ?? null;
        }
        
        $programData['code'] = $code;

        $this->createProgramEntry($programData, $department->id);
    }

    private function createDisciplinePrograms(array $programEntry, Office $department, array $campusInfo, string $campusName): void
    {
        $offerings = $programEntry['departmentOfferings'] ?? [];

        foreach ($offerings as $programDetailId => $deliveryModeIds) {
            $detailTemplate = collect($this->programDetails)->firstWhere('id', $programDetailId);
            if (!$detailTemplate) {
                $this->command->warn("ProgramDetail template ID '{$programDetailId}' not found. Skipping.");
                continue;
            }

            $baseName = $detailTemplate['title'] . ' ' . $programEntry['title'];
            $programName = $baseName . ($campusInfo['suffix'] ? " ({$campusName})" : '');

            $degreePrefix = $this->degreeCodePrefixMap[$programDetailId] ?? '';
            
            $code = collect($programEntry['codes'])->first(function ($c) use ($degreePrefix, $campusInfo) {
                return Str::startsWith($c, $degreePrefix) && ($campusInfo['suffix'] === '' ? !Str::contains($c, '-') : Str::endsWith($c, $campusInfo['suffix']));
            });

            if (!$code) {
                $this->command->warn("No matching code found for '{$baseName}' on campus '{$department->campus->name}'. Skipping.");
                continue;
            }

            $generatedProgramData = array_merge($detailTemplate, [
                'name' => $programName,
                'code' => $code,
                'accreditationStatus' => $programEntry['accreditationStatus'] ?? 'N/A',
                'accreditedBy' => $programEntry['accreditedBy'] ?? null,
                'deliveryModeIds' => (is_array($deliveryModeIds) && !empty($deliveryModeIds)) ? $deliveryModeIds : [1],
            ]);

            $this->createProgramEntry($generatedProgramData, $department->id);
        }
    }

    private function createProgramEntry(array $programData, int $departmentId): void
    {
        $resolvedDegreeLevelId = $this->getDegreeLevelId($programData['degreeLevel']);
        if (!$resolvedDegreeLevelId) {
            $this->command->warn("DegreeLevel '{$programData['degreeLevel']}' for program '{$programData['name']}' could not be resolved. Skipping.");
            return;
        }

        $primaryDeliveryModeId = $this->getPrimaryDeliveryModeId($programData['deliveryModeIds']);

        $program = Program::updateOrCreate(
            [
                'name' => $programData['name'],
                'department_id' => $departmentId,
            ],
            [
                'code' => $programData['code'] ?? null,
                'degree_level_id' => $resolvedDegreeLevelId,
                'degree_title' => $programData['degreeTitle'],
                'description' => $programData['description'] ?? "Standard {$programData['name']} program.",
                'delivery_mode_id' => $primaryDeliveryModeId,
                'duration' => is_numeric($programData['minSemesters']) ? ($programData['minSemesters'] / 2) . ' Years' : ($programData['duration'] ?? null),
                'min_semester' => $programData['minSemesters'],
                'max_semester' => $programData['maxSemesters'],
                'total_credit_hours' => $programData['minCrHr'],
                'equivalent' => $programData['equivalent'],
                'accreditation_status' => $programData['accreditationStatus'] ?? 'N/A',
                'is_active' => $programData['is_active'] ?? true,
            ]
        );

        // DepartmentProgram::firstOrCreate(
        //     [
        //         'department_id' => $departmentId,
        //         'program_id' => $program->id,
        //     ],
        //     [
        //         'is_active' => true,
        //     ]
        // );
    }
}
