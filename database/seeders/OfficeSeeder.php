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

        // Faculty IDs: 1: Arts & Humanities, 2: Business & Economics, 3: Chemical & Life Sciences, 4: Physical & Numerical Sciences, 5: Social Sciences
        $offices = [
            ['name' => 'Department of Agriculture', 'code_base' => 'AGRI', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'agriculture'],
            ['name' => 'Department of Biochemistry', 'code_base' => 'BIOCHEM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biochemistry'],
            ['name' => 'Department of Biotechnology', 'code_base' => 'BIOTEC', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biotechnology'],
            ['name' => 'Department of Botany', 'code_base' => 'BOT', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'botany'],
            ['name' => 'Department of Botany', 'code_base' => 'BOT', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'botany.timergara'], // Specific email for campus
            ['name' => 'Department of Chemistry', 'code_base' => 'CHEM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'chemistry'],
            ['name' => 'Department of Computer Science', 'code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'computerscience'],
            ['name' => 'Department of Computer Science', 'code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => 'P', 'email_prefix' => 'cs.pabbi'],
            ['name' => 'Department of Computer Science', 'code_base' => 'CS', 'faculty_id' => 4, 'campus_suffix' => 'T', 'email_prefix' => 'cs.timergara'],
            ['name' => 'Department of Digital Marketing (IBL)', 'code_base' => 'DGMKT', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'marketing'],
            ['name' => 'Department of Economics', 'code_base' => 'ECO', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'economics'],
            ['name' => 'Department of Economics', 'code_base' => 'ECO', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'economics.tmg'],
            ['name' => 'Department of Education', 'code_base' => 'EDU', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'education'],
            ['name' => 'Department of English', 'code_base' => 'ENG', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'english'],
            ['name' => 'Department of Entomology', 'code_base' => 'ENTO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'entomology'],
            ['name' => 'Department of Environmental Science', 'code_base' => 'ENVSCI', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'environmental'],
            ['name' => 'Department of Food Science & Technology', 'code_base' => 'FST', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'fst'],
            ['name' => 'Department of Geology', 'code_base' => 'GEO', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'geology'],
            ['name' => 'Department of HPE', 'code_base' => 'HPE', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'physicaleducation'],
            ['name' => 'Department of IR', 'code_base' => 'IR', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'ir'],
            ['name' => 'Department of Islamic Studies', 'code_base' => 'ISLSTU', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'islamicstudies'],
            ['name' => 'Department of Journalism and Mass Communication', 'code_base' => 'JMC', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'jmc'],
            ['name' => 'Department of Law', 'code_base' => 'LAW', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'law'],
            ['name' => 'Department of Management Science', 'code_base' => 'MGTSCI', 'faculty_id' => 2, 'campus_suffix' => 'P', 'email_prefix' => 'managementscience.pabbi'],
            ['name' => 'Department of Management Science', 'code_base' => 'MGTSCI', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'managementscience.timergara'],
            ['name' => 'Department of Mathematics', 'code_base' => 'MATH', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'mathematics'],
            ['name' => 'Department of Microbiology', 'code_base' => 'MICRO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'microbiology'],
            ['name' => 'Department of Microbiology', 'code_base' => 'MICRO', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'micro.tmg'],
            ['name' => 'Department of MLT', 'code_base' => 'MLT', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'mlt'],
            ['name' => 'Department of Pakistan Studies', 'code_base' => 'PAKSTU', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'pakstudy'],
            ['name' => 'Department of Pashto', 'code_base' => 'PASH', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'pashto'],
            ['name' => 'Department of Pharmacy', 'code_base' => 'PHARM', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'pharmacy'],
            ['name' => 'Department of Physics', 'code_base' => 'PHY', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'physics'],
            ['name' => 'Department of Political Science', 'code_base' => 'POLSCI', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'politicalscience'],
            ['name' => 'Department of Psychology', 'code_base' => 'PSY', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'psychology'],
            ['name' => 'Department of Sociology', 'code_base' => 'SOC', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'sociology'],
            ['name' => 'Department of Sociology', 'code_base' => 'SOC', 'faculty_id' => 5, 'campus_suffix' => 'T', 'email_prefix' => 'soc.tmg'],
            ['name' => 'Department of Statistics', 'code_base' => 'STAT', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'statistics'],
            ['name' => 'Department of Tourism & Hospitality', 'code_base' => 'TAH', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'tourism'],
            ['name' => 'Department of Zoology', 'code_base' => 'ZOO', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'zoology'],
            ['name' => 'Department of Zoology', 'code_base' => 'ZOO', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'zoology.tmg'],
            ['name' => 'College of Veterinary Sciences and Animal Husbandry', 'code_base' => 'CVSAH', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'cvs'],
            ['name' => 'Institute of Business Leadership', 'code_base' => 'IBL', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'ibl'],
            ['name' => 'Pakhtunkhwa College of Arts', 'code_base' => 'PCA', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'pca'], // Assuming 'arts' is desired prefix
        ];

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
            ['name' => 'Central Library', 'code' => 'LIB', 'campus_suffix' => null, 'email_prefix' => 'library'], // Default Garden Campus
            ['name' => 'Central Library - Main', 'code' => 'LIB-M', 'campus_suffix' => 'M', 'email_prefix' => 'library.main'],
            ['name' => 'Library - Timergara', 'code' => 'LIB-T', 'campus_suffix' => 'T', 'email_prefix' => 'library.tmg'],
            ['name' => 'Library - Pabbi', 'code' => 'LIB-P', 'campus_suffix' => 'P', 'email_prefix' => 'library.pabbi'],
            ['name' => 'Museum of Archaeology & Ethnology', 'code' => 'MUSEUM', 'campus_suffix' => null, 'email_prefix' => 'museum'],
            ['name' => 'Office of the Dean, Faculty of Arts & Humanities', 'code' => 'DFAH', 'campus_suffix' => null, 'email_prefix' => 'dean_ah'], // Using code as prefix seems better here
            ['name' => 'Office of the Dean, Faculty of Business & Economics', 'code' => 'DBEB', 'campus_suffix' => null, 'email_prefix' => 'dean_be'],
            ['name' => 'Office of the Dean, Faculty of Chemical & Life Sciences', 'code' => 'DCLE', 'campus_suffix' => null, 'email_prefix' => 'deanss'],
            ['name' => 'Office of the Dean, Faculty of Physical & Numerical Sciences', 'code' => 'DPNS', 'campus_suffix' => null, 'email_prefix' => 'dean_pns'],
            ['name' => 'Office of the Dean, Faculty of Social Sciences', 'code' => 'DSS', 'campus_suffix' => null, 'email_prefix' => 'dean_ss'],
            ['name' => 'Coordinator - Main', 'code' => 'COORD-M', 'campus_suffix' => 'M', 'email_prefix' => 'coordinator.main'],
            ['name' => 'Coordinator - Timergara', 'code' => 'COORD-T', 'campus_suffix' => 'T', 'email_prefix' => 'coordinator.tmg'],
            ['name' => 'Coordinator - Pabbi', 'code' => 'COORD-P', 'campus_suffix' => 'P', 'email_prefix' => 'coordinator.pabbi'],
        ];

         // Process Academic/College Offices (Type 2 or 3)
        $totalAcademicOffices = count($offices);
        foreach ($offices as $index => $officeData) {
            $campusId = 1; // Default: Garden Campus
            $code = $officeData['code_base'];

            if ($officeData['campus_suffix'] === 'T') {
                $campusId = 3; // Timergara Campus
                $code .= '-T';
            } elseif ($officeData['campus_suffix'] === 'P') {
                $campusId = 4; // Pabbi Campus
                $code .= '-P';
            }
            // Add other campus suffixes here if needed (e.g., Main Campus if different code needed)

            // Determine office_type_id (2 for departments/institutes, 3 for colleges/specific institutes)
            $officeTypeId = ($index >= $totalAcademicOffices - 3) ? 3 : 2;

            // Get location from fetched campuses
            $location = $campuses[$campusId] ?? null; // Use null if campus ID not found

            // Use pre-defined email prefix
            $emailPrefix = $officeData['email_prefix'];
            $email = $emailPrefix . '@awkum.edu.pk';
            $phone = fake()->phoneNumber; // Use fake() helper

            Office::updateOrCreate(
                ['code' => $code], // Unique key(s) to find existing record
                [
                    'name' => $officeData['name'],
                    'office_type_id' => $officeTypeId,
                    'campus_id' => $campusId,
                    'faculty_id' => $officeData['faculty_id'],
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

        // Process Administrative Offices (Type 1)
        foreach ($adminOffices as $officeData) {
            $campusId = 1; // Default: Garden Campus
            $code = $officeData['code']; // Use the provided code directly

            // Adjust campusId based on suffix
            if ($officeData['campus_suffix'] === 'T') {
                $campusId = 3; // Timergara Campus
            } elseif ($officeData['campus_suffix'] === 'P') {
                $campusId = 4; // Pabbi Campus
            } elseif ($officeData['campus_suffix'] === 'M') {
                $campusId = 2; // Main Campus
            }

            // Get location from fetched campuses
            $location = $campuses[$campusId] ?? null; // Use null if campus ID not found

            // Use pre-defined email prefix
            $emailPrefix = $officeData['email_prefix'];
            $email = $emailPrefix . '@awkum.edu.pk';
            $phone = fake()->phoneNumber; // Use fake() helper

            Office::updateOrCreate(
                ['code' => $code], // Unique key(s) to find existing record
                [
                    'name' => $officeData['name'],
                    'office_type_id' => 1, // Administrative Office Type
                    'campus_id' => $campusId,
                    'faculty_id' => null, // Admin offices typically don't belong to a faculty
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
    }
}
