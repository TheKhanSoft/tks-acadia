<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Campus; // Import Campus model
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
// No need for direct Faker import when using fake() helper

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove $faker = Faker::create(); - use fake() helper instead

        // Fetch campus addresses from the database, keyed by ID
        $campuses = Campus::pluck('address', 'id')->all();

        
        // Administrative Offices (Type 1)
        $adminOffices = [
            ['name' => 'Vice-Chancellor Secretariat', 'code' => 'VC', 'campus_suffix' => null, 'email_prefix' => 'vc'],
            ['name' => 'Registrar Office', 'code' => 'REG', 'campus_suffix' => null, 'email_prefix' => 'registrar'],
            ['name' => 'Public Information Office', 'code' => 'PIO', 'campus_suffix' => null, 'email_prefix' => 'pio'],
            ['name' => 'Directorate of Academics and Research', 'code' => 'ACAD', 'campus_suffix' => null, 'email_prefix' => 'academic'],
            ['name' => 'Treasurer Office', 'code' => 'TR', 'campus_suffix' => null, 'email_prefix' => 'tr'],
            ['name' => 'Pay & Pension Section', 'code' => 'PAY', 'campus_suffix' => null, 'email_prefix' => 'paypension'],
            ['name' => 'Directorate of Admissions', 'code' => 'ADM', 'campus_suffix' => null, 'email_prefix' => 'admissions'],
            ['name' => 'Controller of Examinations', 'code' => 'COE', 'campus_suffix' => null, 'email_prefix' => 'controller'],
            ['name' => 'Office of Research, Innovation & Commercialization', 'code' => 'ORIC', 'campus_suffix' => null, 'email_prefix' => 'oric'],
            ['name' => 'Provost', 'code' => 'PROVST', 'campus_suffix' => null, 'email_prefix' => 'provost'],
            ['name' => 'Proctors Office', 'code' => 'PROCTOR', 'campus_suffix' => null, 'email_prefix' => 'proctor'],
            ['name' => 'Quality Enhancement Cell', 'code' => 'QEC', 'campus_suffix' => null, 'email_prefix' => 'qec'],
            ['name' => 'Legal Cell', 'code' => 'LC', 'campus_suffix' => null, 'email_prefix' => 'legal'],
            ['name' => 'Directorate of Administration', 'code' => 'ADMIN', 'campus_suffix' => null, 'email_prefix' => 'administration'],
            ['name' => 'Human Resource Development', 'code' => 'HRD', 'campus_suffix' => null, 'email_prefix' => 'hrd'],
            ['name' => 'University Advancement Cell & Career Development Center', 'code' => 'UAC&CDC', 'campus_suffix' => null, 'email_prefix' => 'uaccdc'],
            ['name' => 'Procurement', 'code' => 'PROCUREMENT', 'campus_suffix' => null, 'email_prefix' => 'procurement'],
            ['name' => 'Security Section', 'code' => 'SECURITY', 'campus_suffix' => null, 'email_prefix' => 'security'],
            ['name' => 'Planning & Development', 'code' => 'P&D', 'campus_suffix' => null, 'email_prefix' => 'pandd'],
            ['name' => 'Directorate of Works', 'code' => 'WORK', 'campus_suffix' => null, 'email_prefix' => 'works'],
            ['name' => 'Directorate of IT', 'code' => 'IT', 'campus_suffix' => null, 'email_prefix' => 'it'],
            ['name' => 'Directorate of Sports', 'code' => 'SPORT', 'campus_suffix' => null, 'email_prefix' => 'sports'],
            ['name' => 'Transport Section', 'code' => 'TRANSPORT', 'campus_suffix' => null, 'email_prefix' => 'transport'],            
            ['name' => 'Meeting Section', 'code' => 'MEETINGS', 'campus_suffix' => null, 'email_prefix' => 'meetings'],
            ['name' => 'Media Section', 'code' => 'Media', 'campus_suffix' => null, 'email_prefix' => 'media'],
            ['name' => 'University Medical Center', 'code' => 'UMC', 'campus_suffix' => null, 'email_prefix' => 'medical.center'],
            ['name' => 'Central Library', 'code' => 'LIB', 'campus_suffix' => null, 'email_prefix' => 'library'], 
            ['name' => 'Central Library - Main', 'code' => 'LIB-M', 'campus_suffix' => 'M', 'email_prefix' => 'library.main'],
            ['name' => 'Library - Timergara', 'code' => 'LIB-T', 'campus_suffix' => 'T', 'email_prefix' => 'library.tmg'],
            ['name' => 'Library - Pabbi', 'code' => 'LIB-P', 'campus_suffix' => 'P', 'email_prefix' => 'library.pabbi'],
            ['name' => 'Museum of Archaeology & Ethnology', 'code' => 'MUSEUM', 'campus_suffix' => null, 'email_prefix' => 'museum'],
            ['name' => 'Office of the Dean, Faculty of Arts & Humanities', 'code' => 'DFAH', 'campus_suffix' => null, 'email_prefix' => 'dean_ah'], 
            ['name' => 'Office of the Dean, Faculty of Business & Economics', 'code' => 'DBEB', 'campus_suffix' => null, 'email_prefix' => 'dean_be'],
            ['name' => 'Office of the Dean, Faculty of Chemical & Life Sciences', 'code' => 'DCLE', 'campus_suffix' => null, 'email_prefix' => 'deanss'],
            ['name' => 'Office of the Dean, Faculty of Physical & Numerical Sciences', 'code' => 'DPNS', 'campus_suffix' => null, 'email_prefix' => 'dean_pns'],
            ['name' => 'Office of the Dean, Faculty of Social Sciences', 'code' => 'DSS', 'campus_suffix' => null, 'email_prefix' => 'dean_ss'],
            ['name' => 'Coordinator - Main', 'code' => 'COORD-M', 'campus_suffix' => 'M', 'email_prefix' => 'coordinator.main'],
            ['name' => 'Coordinator - Timergara', 'code' => 'COORD-T', 'campus_suffix' => 'T', 'email_prefix' => 'coordinator.tmg'],
            ['name' => 'Coordinator - Pabbi', 'code' => 'COORD-P', 'campus_suffix' => 'P', 'email_prefix' => 'coordinator.pabbi'],

            ['name' => "Estate Office, Garden Campus", 'code' => "Estate", 'campus_suffix' => null, 'email_prefix' => 'estate'],

            ['name' => "Foreign Faculty Hostel", 'code' => "ForeignFHostel", 'campus_suffix' => 'NA', 'email_prefix' => 'provost' ],
            ['name' => "Gaju Khan Tomb", 'code' => "GKTomb", 'campus_suffix' => 'NA', 'email_prefix' => 'admin'],
            ['name' => "Ground & Garden", 'code' => "G&G", 'campus_suffix' => 'NA', 'email_prefix' => 'admin'],
            ['name' => "Guest House, Abbottabad", 'code' => "GHouseAbbotabad", 'campus_suffix' => 'NA', 'email_prefix' => 'ps'],
            ['name' => "Guest House, Ayubia", 'code' => "GHouseAyubia", 'campus_suffix' => 'NA', 'email_prefix' => 'ps'],
            ['name' => "Guest House, Islamabad", 'code' => "GHouseIsb", 'campus_suffix' => 'NA', 'email_prefix' => 'ps'],
            ['name' => "Guest House, Project Office", 'code' => "GHouseProject", 'campus_suffix' => 'NA', 'email_prefix' => 'ps'],
        ];

        // Faculty IDs: 1: Arts & Humanities, 2: Business & Economics, 3: Chemical & Life Sciences, 4: Physical & Numerical Sciences, 5: Social Sciences
        $departments = [
            ['name' => 'Department of Agriculture', 'short_name' => 'Agriculture','code_base' => 'AGRI', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'agriculture'],
            ['name' => 'Department of Biochemistry', 'short_name' => 'Biochemistry','code_base' => 'BIOCHEM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biochemistry'],
            ['name' => 'Department of Biotechnology', 'short_name' => 'Biotechnology','code_base' => 'BIOTEC', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biotechnology'],
            ['name' => 'Department of Botany', 'short_name' => 'Botany','code_base' => 'BOT', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'botany'],
            ['name' => 'Department of Botany', 'short_name' => 'Botany','code_base' => 'BOT', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'botany.timergara'], // Specific email for campus
            ['name' => 'Department of Chemistry', 'short_name' => 'Chemistry','code_base' => 'CHEM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'chemistry'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science','code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'computerscience'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science','code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => 'P', 'email_prefix' => 'cs.pabbi'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science','code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => 'T', 'email_prefix' => 'cs.timergara'],
            ['name' => 'Department of Digital Marketing (IBL)', 'short_name' => 'Digital Marketing (IBL)','code_base' => 'DGMKT', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'marketing'],
            ['name' => 'Department of Economics', 'short_name' => 'Economics','code_base' => 'ECO', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'economics'],
            ['name' => 'Department of Economics', 'short_name' => 'Economics','code_base' => 'ECO', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'economics.tmg'],
            ['name' => 'Department of Education', 'short_name' => 'Education','code_base' => 'EDU', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'education'],
            ['name' => 'Department of English', 'short_name' => 'English','code_base' => 'ENG', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'english'],
            ['name' => 'Department of Entomology', 'short_name' => 'Entomology','code_base' => 'ENTO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'entomology'],
            ['name' => 'Department of Environmental Science', 'short_name' => 'Environmental Science','code_base' => 'ENVSCI', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'environmental'],
            ['name' => 'Department of Food Science & Technology', 'short_name' => 'Food Science & Technology','code_base' => 'FST', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'fst'],
            ['name' => 'Department of Geology', 'short_name' => 'Geology','code_base' => 'GEO', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'geology'],
            ['name' => 'Department of HPE', 'short_name' => 'HPE','code_base' => 'HPE', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'physicaleducation'],
            ['name' => 'Department of IR', 'short_name' => 'IR','code_base' => 'IR', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'ir'],
            ['name' => 'Department of Islamic Studies', 'short_name' => 'Islamic Studies','code_base' => 'ISLSTU', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'islamicstudies'],
            ['name' => 'Department of Journalism and Mass Communication', 'short_name' => 'Journalism and Mass Communication','code_base' => 'JMC', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'jmc'],
            ['name' => 'Department of Law', 'short_name' => 'Law','code_base' => 'LAW', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'law'],
            ['name' => 'Department of Management Science', 'short_name' => 'Management Science','code_base' => 'MGTSCI', 'faculty_id' => 2, 'campus_suffix' => 'P', 'email_prefix' => 'managementscience.pabbi'],
            ['name' => 'Department of Management Science', 'short_name' => 'Management Science','code_base' => 'MGTSCI', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'managementscience.timergara'],
            ['name' => 'Department of Mathematics', 'short_name' => 'Mathematics','code_base' => 'MATH', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'mathematics'],
            ['name' => 'Department of Microbiology', 'short_name' => 'Microbiology','code_base' => 'MICRO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'microbiology'],
            ['name' => 'Department of Microbiology', 'short_name' => 'Microbiology','code_base' => 'MICRO', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'micro.tmg'],
            ['name' => 'Department of MLT', 'short_name' => 'MLT','code_base' => 'MLT', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'mlt'],
            ['name' => 'Department of Pakistan Studies', 'short_name' => 'Pakistan Studies','code_base' => 'PAKSTU', 'faculty_id' => 1, 'campus_suffix' => "M", 'email_prefix' => 'pakstudy'],
            ['name' => 'Department of Pashto', 'short_name' => 'Pashto','code_base' => 'PASH', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'pashto'],
            ['name' => 'Department of Pharmacy', 'short_name' => 'Pharmacy','code_base' => 'PHARM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'pharmacy'],
            ['name' => 'Department of Physics', 'short_name' => 'Physics','code_base' => 'PHY', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'physics'],
            ['name' => 'Department of Political Science', 'short_name' => 'Political Science','code_base' => 'POLSCI', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'politicalscience'],
            ['name' => 'Department of Psychology', 'short_name' => 'Psychology','code_base' => 'PSY', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'psychology'],
            ['name' => 'Department of Sociology', 'short_name' => 'Sociology','code_base' => 'SOC', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'sociology'],
            ['name' => 'Department of Sociology', 'short_name' => 'Sociology','code_base' => 'SOC', 'faculty_id' => 5, 'campus_suffix' => 'T', 'email_prefix' => 'soc.tmg'],
            ['name' => 'Department of Statistics', 'short_name' => 'Statistics','code_base' => 'STAT', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'statistics'],
            ['name' => 'Department of Tourism & Hospitality', 'short_name' => 'Tourism & Hospitality','code_base' => 'TAH', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'tourism'],
            ['name' => 'Department of Zoology', 'short_name' => 'Zoology','code_base' => 'ZOO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'zoology'],
            ['name' => 'Department of Zoology', 'short_name' => 'Zoology','code_base' => 'ZOO', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'zoology.tmg'],
            ['name' => 'College of Veterinary Sciences and Animal Husbandry', 'short_name' => 'CVS & AH', 'code_base' => 'CVSAH', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'cvs'],
            ['name' => 'Institute of Business Leadership', 'short_name' => 'IBL', 'code_base' => 'IBL', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'ibl'],
            ['name' => 'Pakhtunkhwa College of Arts', 'short_name' => 'PCA', 'code_base' => 'PCA', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'principal_pca'], 
            ['name' => 'University College for Women', 'short_name' => 'UCW', 'code_base' => 'UCW', 'faculty_id' => 1, 'campus_suffix' => "M", 'email_prefix' => 'principal_ucw'], 
        ];

        // Process Administrative Offices (Type 1)
        foreach ($adminOffices as $sectionData) {
            $campusId = 1; // Default: Garden Campus
            $short_name = $sectionData['code'];
            $code = $sectionData['code']; // Use the provided code directly

            // Adjust campusId based on suffix
            if ($sectionData['campus_suffix'] === 'T') {
                $campusId = 3; // Timergara Campus
                $short_name .= ' - Timergara';
            } elseif ($sectionData['campus_suffix'] === 'P') {
                $campusId = 4; // Pabbi Campus
                $short_name .= ' - Pabbi';
            } elseif ($sectionData['campus_suffix'] === 'M') {
                $campusId = 2; // Main Campus
                $short_name .= ' - Main';
            }
            elseif ($sectionData['campus_suffix'] === 'NA') {
                $campusId = null; // NO campus
            }

            // Get location from fetched campuses
            $location = $campuses[$campusId] ?? null; // Use null if campus ID not found

            // Use pre-defined email prefix
            $emailPrefix = $sectionData['email_prefix'];
            $email = $emailPrefix . '@awkum.edu.pk';
            $phone = fake()->phoneNumber; // 

            Office::updateOrCreate(
                ['code' => $code], // Unique key(s) to find existing record
                [
                    'name' => $sectionData['name'],
                    'short_name' => $short_name,
                    'office_type_id' => 1, // Administrative Office Type
                    'campus_id' => $campusId,
                    'faculty_id' => null, // Admin departments typically don't belong to a faculty
                    'description' => null,
                    'head_id' => null,
                    'head_appointment_date' => null,
                    'office_location' => $location, // Use dynamically fetched location
                    'contact_email' => $email,
                    'contact_phone' => $phone,
                    'established_year' => null,
                    'parent_office_id' => null,
                    'is_active' => true,
                ]
            );
        }

        
         // Process Academic/College Offices (Type 2 or 3)
        $totalAcademicOffices = count($departments);
        foreach ($departments as $index => $departmentData) {
            $campusId = 1; // Default: Garden Campus
            $code = $departmentData['code_base'];
            $short_name = $departmentData['short_name'];

            if ($departmentData['campus_suffix'] === 'T') {
                $campusId = 3; // Timergara Campus
                $code .= '-T';
                $short_name .= ' - Timergara';
            } elseif ($departmentData['campus_suffix'] === 'P') {
                $campusId = 4; // Pabbi Campus
                $code .= '-P';
                $short_name .= ' - Pabbi';
            }
            elseif ($departmentData['campus_suffix'] === 'M') {
                $campusId = 2; // Main Campus
                $short_name .= ' - Main';
            }
            // Add other campus suffixes here if needed (e.g., Main Campus if different code needed)

            // Determine office_type_id (2 for departments/institutes, 3 for colleges/specific institutes)
            $officeTypeId = ($index >= $totalAcademicOffices - 3) ? 3 : 2;

            // Get location from fetched campuses
            $location = $campuses[$campusId] ?? null; // Use null if campus ID not found

            // Use pre-defined email prefix
            $emailPrefix = $departmentData['email_prefix'];
            $email = $emailPrefix . '@awkum.edu.pk';
            $phone = fake()->phoneNumber; // Use fake() helper

            Office::updateOrCreate(
                ['code' => $code], 
                [
                    'name' => $departmentData['name'],
                    'short_name' => $short_name,
                    'office_type_id' => $officeTypeId,
                    'campus_id' => $campusId,
                    'faculty_id' => $departmentData['faculty_id'],
                    'description' => null, // Set as null per request
                    'head_id' => null, // Set as null per request
                    'head_appointment_date' => null,
                    'office_location' => $location, // Use dynamically fetched location
                    'contact_email' => $email,
                    'contact_phone' => $phone,
                    'established_year' => null,
                    'parent_office_id' => null, // Set as null per request
                    'is_active' => true,
                ]
            );
        }
    }
}
