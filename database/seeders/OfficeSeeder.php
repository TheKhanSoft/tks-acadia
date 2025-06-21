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
            ['name' => 'Vice-Chancellor Secretariat', 'code' => 'VC', 'campus_suffix' => null, 'email_prefix' => 'vc', 'description' => 'Supports the Vice-Chancellor in administrative and executive duties.'],
            ['name' => 'Registrar Office', 'code' => 'REG', 'campus_suffix' => null, 'email_prefix' => 'registrar', 'description' => 'Manages academic administration, student records, and official university documentation.'],
            ['name' => 'Public Information Office', 'code' => 'PIO', 'campus_suffix' => null, 'email_prefix' => 'pio', 'description' => 'Handles public relations, media communications, and information dissemination.'],
            ['name' => 'Directorate of Academics and Research', 'code' => 'ACAD', 'campus_suffix' => null, 'email_prefix' => 'academic', 'description' => 'Oversees academic programs, curriculum development, and research activities.'],
            ['name' => 'Treasurer Office', 'code' => 'TR', 'campus_suffix' => null, 'email_prefix' => 'tr', 'description' => 'Manages the university\'s financial operations, budgeting, and accounting.'],
            ['name' => 'Pay & Pension Section', 'code' => 'PAY', 'campus_suffix' => null, 'email_prefix' => 'paypension', 'description' => 'Administers payroll processing and pension benefits for university employees.'],
            ['name' => 'Directorate of Admissions', 'code' => 'ADM', 'campus_suffix' => null, 'email_prefix' => 'admissions', 'description' => 'Manages the student admission process, applications, and enrollment.'],
            ['name' => 'Controller of Examinations', 'code' => 'COE', 'campus_suffix' => null, 'email_prefix' => 'controller', 'description' => 'Administers and oversees all university examinations and assessments.'],
            ['name' => 'Office of Research, Innovation & Commercialization', 'code' => 'ORIC', 'campus_suffix' => null, 'email_prefix' => 'oric', 'description' => 'Promotes research, innovation, and the commercialization of university research outcomes.'],
            ['name' => 'Provost', 'code' => 'PROVST', 'campus_suffix' => null, 'email_prefix' => 'provost', 'description' => 'Oversees student affairs, including discipline, welfare, and residential life.'],
            ['name' => 'Proctors Office', 'code' => 'PROCTOR', 'campus_suffix' => null, 'email_prefix' => 'proctor', 'description' => 'Maintains student discipline and enforces university regulations.'],
            ['name' => 'Quality Enhancement Cell', 'code' => 'QEC', 'campus_suffix' => null, 'email_prefix' => 'qec', 'description' => 'Monitors and enhances the quality of academic programs and administrative processes.'],
            ['name' => 'Legal Cell', 'code' => 'LC', 'campus_suffix' => null, 'email_prefix' => 'legal', 'description' => 'Provides legal advice and handles legal matters for the university.'],
            ['name' => 'Directorate of Administration', 'code' => 'ADMIN', 'campus_suffix' => null, 'email_prefix' => 'administration', 'description' => 'Manages general administrative functions and support services.'],
            ['name' => 'Human Resource Development', 'code' => 'HRD', 'campus_suffix' => null, 'email_prefix' => 'hrd', 'description' => 'Manages employee recruitment, training, development, and relations.'],
            ['name' => 'University Advancement Cell & Career Development Center', 'code' => 'UAC&CDC', 'campus_suffix' => null, 'email_prefix' => 'uaccdc', 'description' => 'Focuses on fundraising, alumni relations, and student career services.'],
            ['name' => 'Procurement', 'code' => 'PROCUREMENT', 'campus_suffix' => null, 'email_prefix' => 'procurement', 'description' => 'Manages the purchasing of goods and services for the university.'],
            ['name' => 'Security Section', 'code' => 'SECURITY', 'campus_suffix' => null, 'email_prefix' => 'security', 'description' => 'Ensures the safety and security of the university campus, staff, and students.'],
            ['name' => 'Planning & Development', 'code' => 'P&D', 'campus_suffix' => null, 'email_prefix' => 'pandd', 'description' => 'Oversees strategic planning, project management, and infrastructure development.'],
            ['name' => 'Directorate of Works', 'code' => 'WORK', 'campus_suffix' => null, 'email_prefix' => 'works', 'description' => 'Manages the maintenance, construction, and repair of university buildings and infrastructure.'],
            ['name' => 'Directorate of IT', 'code' => 'IT', 'campus_suffix' => null, 'email_prefix' => 'it', 'description' => 'Manages information technology infrastructure, services, and support.'],
            ['name' => 'Directorate of Sports', 'code' => 'SPORT', 'campus_suffix' => null, 'email_prefix' => 'sports', 'description' => 'Organizes and manages university sports programs and facilities.'],
            ['name' => 'Transport Section', 'code' => 'TRANSPORT', 'campus_suffix' => null, 'email_prefix' => 'transport', 'description' => 'Manages the university\'s transportation fleet and services.'],
            ['name' => 'Meeting Section', 'code' => 'MEETINGS', 'campus_suffix' => null, 'email_prefix' => 'meetings', 'description' => 'Coordinates and facilitates official university meetings and events.'],
            ['name' => 'Media Section', 'code' => 'Media', 'campus_suffix' => null, 'email_prefix' => 'media', 'description' => 'Handles media production, coverage, and communication activities.'],
            ['name' => 'University Medical Center', 'code' => 'UMC', 'campus_suffix' => null, 'email_prefix' => 'medical.center', 'description' => 'Provides medical services and healthcare support to students and staff.'],
            ['name' => 'Central Library', 'code' => 'LIB', 'campus_suffix' => null, 'email_prefix' => 'library', 'description' => 'Main library providing access to academic resources, books, and journals for Garden Campus.'],
            ['name' => 'Central Library - Main', 'code' => 'LIB-M', 'campus_suffix' => 'M', 'email_prefix' => 'library.main', 'description' => 'Library services specifically for the Main Campus.'],
            ['name' => 'Library - Timergara', 'code' => 'LIB-T', 'campus_suffix' => 'T', 'email_prefix' => 'library.tmg', 'description' => 'Library services specifically for the Timergara Campus.'],
            ['name' => 'Library - Pabbi', 'code' => 'LIB-P', 'campus_suffix' => 'P', 'email_prefix' => 'library.pabbi', 'description' => 'Library services specifically for the Pabbi Campus.'],
            ['name' => 'Museum of Archaeology & Ethnology', 'code' => 'MUSEUM', 'campus_suffix' => null, 'email_prefix' => 'museum', 'description' => 'Manages the university museum, showcasing archaeological and ethnological collections.'],
            ['name' => 'Office of the Dean, Faculty of Arts & Humanities', 'code' => 'DFAH', 'campus_suffix' => null, 'email_prefix' => 'dean_ah', 'description' => 'Administrative office supporting the Dean of the Faculty of Arts & Humanities.'],
            ['name' => 'Office of the Dean, Faculty of Business & Economics', 'code' => 'DBEB', 'campus_suffix' => null, 'email_prefix' => 'dean_be', 'description' => 'Administrative office supporting the Dean of the Faculty of Business & Economics.'],
            ['name' => 'Office of the Dean, Faculty of Chemical & Life Sciences', 'code' => 'DCLE', 'campus_suffix' => null, 'email_prefix' => 'deanss', 'description' => 'Administrative office supporting the Dean of the Faculty of Chemical & Life Sciences.'], // Note: email_prefix seems mismatched, kept as is.
            ['name' => 'Office of the Dean, Faculty of Physical & Numerical Sciences', 'code' => 'DPNS', 'campus_suffix' => null, 'email_prefix' => 'dean_pns', 'description' => 'Administrative office supporting the Dean of the Faculty of Physical & Numerical Sciences.'],
            ['name' => 'Office of the Dean, Faculty of Social Sciences', 'code' => 'DSS', 'campus_suffix' => null, 'email_prefix' => 'dean_ss', 'description' => 'Administrative office supporting the Dean of the Faculty of Social Sciences.'],
            ['name' => 'Coordinator - Main', 'code' => 'COORD-M', 'campus_suffix' => 'M', 'email_prefix' => 'coordinator.main', 'description' => 'Coordinates administrative and academic activities for the Main Campus.'],
            ['name' => 'Coordinator - Timergara', 'code' => 'COORD-T', 'campus_suffix' => 'T', 'email_prefix' => 'coordinator.tmg', 'description' => 'Coordinates administrative and academic activities for the Timergara Campus.'],
            ['name' => 'Coordinator - Pabbi', 'code' => 'COORD-P', 'campus_suffix' => 'P', 'email_prefix' => 'coordinator.pabbi', 'description' => 'Coordinates administrative and academic activities for the Pabbi Campus.'],
            ['name' => "Estate Office, Garden Campus", 'code' => "Estate", 'campus_suffix' => null, 'email_prefix' => 'estate', 'description' => 'Manages university property, land, and facilities maintenance for the Garden Campus.'],
            ['name' => "Foreign Faculty Hostel", 'code' => "ForeignFHostel", 'campus_suffix' => 'NA', 'email_prefix' => 'provost', 'description' => 'Provides accommodation facilities for visiting foreign faculty members.'],
            ['name' => "Gaju Khan Tomb", 'code' => "GKTomb", 'campus_suffix' => 'NA', 'email_prefix' => 'admin', 'description' => 'Manages the historical site of Gaju Khan Tomb.'],
            ['name' => "Ground & Garden", 'code' => "G&G", 'campus_suffix' => 'NA', 'email_prefix' => 'admin', 'description' => 'Responsible for the maintenance and landscaping of university grounds and gardens.'],
            ['name' => "Guest House, Abbottabad", 'code' => "GHouseAbbotabad", 'campus_suffix' => 'NA', 'email_prefix' => 'ps', 'description' => 'Manages the university guest house facility located in Abbottabad.'],
            ['name' => "Guest House, Ayubia", 'code' => "GHouseAyubia", 'campus_suffix' => 'NA', 'email_prefix' => 'ps', 'description' => 'Manages the university guest house facility located in Ayubia.'],
            ['name' => "Guest House, Islamabad", 'code' => "GHouseIsb", 'campus_suffix' => 'NA', 'email_prefix' => 'ps', 'description' => 'Manages the university guest house facility located in Islamabad.'],
            ['name' => "Guest House, Project Office", 'code' => "GHouseProject", 'campus_suffix' => 'NA', 'email_prefix' => 'ps', 'description' => 'Manages the guest house associated with the university project office.'],
        ];

        // Faculty IDs: 1: Arts & Humanities, 2: Business & Economics, 3: Chemical & Life Sciences, 4: Physical & Numerical Sciences, 5: Social Sciences
        $departments = [
            ['name' => 'Department of Agriculture', 'short_name' => 'Agriculture','code_base' => 'AGRI', 'description' => 'Focuses on agricultural sciences, crop production, and sustainable farming practices.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'agriculture'],
            ['name' => 'Department of Biochemistry', 'short_name' => 'Biochemistry','code_base' => 'BIOCHEM', 'description' => 'Studies the chemical processes and substances occurring within living organisms.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biochemistry'],
            ['name' => 'Department of Biotechnology', 'short_name' => 'Biotechnology','code_base' => 'BIOTEC', 'description' => 'Specializes in technological applications using biological systems and organisms for developing products and processes.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'biotechnology'],
            ['name' => 'Department of Botany', 'short_name' => 'Botany','code_base' => 'BOT', 'description' => 'Dedicated to the scientific study of plants, including their physiology, structure, genetics, ecology, and classification.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'botany'],
            ['name' => 'Department of Botany', 'short_name' => 'Botany','code_base' => 'BOT', 'description' => 'Timergara campus branch focused on botanical sciences and plant biology research.', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'botany.timergara'],
            ['name' => 'Department of Chemistry', 'short_name' => 'Chemistry','code_base' => 'CHEM', 'description' => 'Explores the composition, structure, properties, and change of matter through chemical processes and experiments.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'chemistry'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science','code_base' => 'CS', 'description' => 'Focuses on the study of computers and computational systems, including programming languages, algorithms, and software development.', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'computerscience'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science Pabbi','code_base' => 'CS-P', 'description' => 'Pabbi campus branch offering computer science education and research opportunities for local students.', 'faculty_id' => 4, 'campus_suffix' => 'P', 'email_prefix' => 'cs.pabbi'],
            ['name' => 'Department of Computer Science', 'short_name' => 'Computer Science Timergara','code_base' => 'CS-T', 'description' => 'Timergara campus division providing computer science education and IT skills development.', 'faculty_id' => 4, 'campus_suffix' => 'T', 'email_prefix' => 'cs.timergara'],
            ['name' => 'Department of Digital Marketing (IBL)', 'short_name' => 'Digital Marketing (IBL)','code_base' => 'DGMKT', 'description' => 'Specializes in digital marketing strategies, social media management, and online business promotion techniques.', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'marketing'],
            ['name' => 'Department of Economics', 'short_name' => 'Economics','code_base' => 'ECO', 'description' => 'Studies economic systems, policies, and their impact on society, businesses, and government.', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'economics'],
            ['name' => 'Department of Economics', 'short_name' => 'Economics','code_base' => 'ECO', 'description' => 'Timergara campus division focused on economic theory, development economics, and regional economic issues.', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'economics.tmg'],
            ['name' => 'Department of Education', 'short_name' => 'Education','code_base' => 'EDU', 'description' => 'Concentrates on educational theory, teaching methodologies, curriculum development, and educational psychology.', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'education'],
            ['name' => 'Department of English', 'short_name' => 'English','code_base' => 'ENG', 'description' => 'Focuses on English language, literature, linguistics, and communication skills development.', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'english'],
            ['name' => 'Department of Entomology', 'short_name' => 'Entomology','code_base' => 'ENTO', 'description' => 'Specializes in the scientific study of insects, their classification, biology, and impact on agriculture and health.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'entomology'],
            ['name' => 'Department of Environmental Science', 'short_name' => 'Environmental Science','code_base' => 'ENVSCI', 'description' => 'Studies environmental systems, conservation, pollution control, and sustainable resource management.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'environmental'],
            ['name' => 'Department of Food Science & Technology', 'short_name' => 'Food Science & Technology','code_base' => 'FST', 'description' => 'Focuses on food processing, preservation, safety, and development of new food products and technologies.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'fst'],
            ['name' => 'Department of Geology', 'short_name' => 'Geology','code_base' => 'GEO', 'description' => 'Studies the Earth\'s physical structure, substances, history, and the processes that shape the landscape.', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'geology'],
            ['name' => 'Department of Health and Physical Education', 'short_name' => 'HPE','code_base' => 'HPE', 'description' => 'Focuses on health and physical education, promoting fitness, sports sciences, and healthy lifestyle practices.', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'physicaleducation'],
            ['name' => 'Department of International Relations', 'short_name' => 'IR','code_base' => 'IR', 'description' => 'Studies international relations, global politics, diplomacy, and interactions between nations and international organizations.', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'ir'],
            ['name' => 'Department of Islamic Studies', 'short_name' => 'Islamic Studies','code_base' => 'ISLSTU', 'description' => 'Focuses on Islamic theology, jurisprudence, history, culture, and contemporary Islamic thought.', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'islamicstudies'],
            ['name' => 'Department of Journalism and Mass Communication', 'short_name' => 'Journalism and Mass Communication','code_base' => 'JMC', 'description' => 'Trains students in journalism, media production, broadcasting, and communication theory and ethics.', 'faculty_id' => 5, 'campus_suffix' => null, 'email_prefix' => 'jmc'],
            ['name' => 'Department of Law', 'short_name' => 'Law','code_base' => 'LAW', 'description' => 'Provides legal education covering constitutional, civil, criminal, and international law, preparing students for legal practice.', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'law'],
            ['name' => 'Department of Management Science', 'short_name' => 'Management Science','code_base' => 'MGTSCI', 'description' => 'Pabbi campus division offering programs in business management, leadership, and organizational behavior.', 'faculty_id' => 2, 'campus_suffix' => 'P', 'email_prefix' => 'managementscience.pabbi'],
            ['name' => 'Department of Management Science', 'short_name' => 'Management Science','code_base' => 'MGTSCI', 'description' => 'Timergara campus branch focusing on business administration, strategic management, and entrepreneurship.', 'faculty_id' => 2, 'campus_suffix' => 'T', 'email_prefix' => 'managementscience.timergara'],
            ['name' => 'Department of Mathematics', 'short_name' => 'Mathematics','code_base' => 'MATH', 'description' => 'Studies advanced mathematical concepts, theories, and applications in pure and applied mathematics.', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'mathematics'],
            ['name' => 'Department of Microbiology', 'short_name' => 'Microbiology','code_base' => 'MICRO', 'description' => 'Focuses on microorganisms, their classification, structure, function, and applications in medicine and industry.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'microbiology'],
            ['name' => 'Department of Microbiology', 'short_name' => 'Microbiology','code_base' => 'MICRO', 'description' => 'Timergara campus branch specializing in microbial sciences and their applications in regional healthcare and agriculture.', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'micro.tmg'],
            ['name' => 'Department of Pakistan Studies', 'short_name' => 'Pakistan Studies','code_base' => 'PAKSTU', 'description' => 'Studies Pakistan\'s history, culture, politics, geography, and socio-economic development.', 'faculty_id' => 1, 'campus_suffix' => "M", 'email_prefix' => 'pakstudy'],
            ['name' => 'Department of Pashto', 'short_name' => 'Pashto','code_base' => 'PASH', 'description' => 'Focuses on Pashto language, literature, poetry, and cultural studies of Pashtun regions.', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'pashto'],
            ['name' => 'Department of Pharmacy', 'short_name' => 'Pharmacy','code_base' => 'PHARM', 'description' => 'Offers education in pharmaceutical sciences, drug development, pharmacology, and clinical pharmacy practices.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'pharmacy'],
            ['name' => 'Department of Physics', 'short_name' => 'Physics','code_base' => 'PHY', 'description' => 'Studies matter, energy, forces, and their interaction in the universe through theoretical and experimental approaches.', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'physics'],
            ['name' => 'Department of Political Science', 'short_name' => 'Political Science','code_base' => 'POLSCI', 'description' => 'Analyzes political systems, governance, public policy, and political theory at local, national, and international levels.', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'politicalscience'],
            ['name' => 'Department of Psychology', 'short_name' => 'Psychology','code_base' => 'PSY', 'description' => 'Studies human behavior, mental processes, and psychological development through research and clinical practice.', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'psychology'],
            ['name' => 'Department of Sociology', 'short_name' => 'Sociology','code_base' => 'SOC', 'description' => 'Examines social relationships, institutions, and the structure and functioning of human society.', 'faculty_id' => 5, 'campus_suffix' => "M", 'email_prefix' => 'sociology'],
            ['name' => 'Department of Sociology', 'short_name' => 'Sociology','code_base' => 'SOC', 'description' => 'Timergara campus branch focusing on sociological research relevant to local communities and regional development.', 'faculty_id' => 5, 'campus_suffix' => 'T', 'email_prefix' => 'soc.tmg'],
            ['name' => 'Department of Statistics', 'short_name' => 'Statistics','code_base' => 'STAT', 'description' => 'Specializes in statistical methods, data analysis, probability theory, and their applications in various fields.', 'faculty_id' => 4, 'campus_suffix' => null, 'email_prefix' => 'statistics'],
            ['name' => 'Department of Tourism & Hospitality', 'short_name' => 'Tourism & Hospitality','code_base' => 'TAH', 'description' => 'Focuses on tourism management, hospitality services, and the promotion of local and international travel industries.', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'tourism'],
            ['name' => 'Department of Zoology', 'short_name' => 'Zoology','code_base' => 'ZOO', 'description' => 'Studies animal biology, behavior, evolution, and ecology through research and laboratory investigations.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'zoology'],
            ['name' => 'Department of Zoology', 'short_name' => 'Zoology','code_base' => 'ZOO', 'description' => 'Timergara campus division focusing on zoological sciences with emphasis on local fauna and biodiversity.', 'faculty_id' => 3, 'campus_suffix' => 'T', 'email_prefix' => 'zoology.tmg'],
            ['name' => 'Department of Accounting & Finance', 'short_name' => 'Accounting & Finance','code_base' => 'ACFIN', 'description' => '', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'accounting'],
            
            ['name' => 'College of Veterinary Sciences and Animal Husbandry', 'short_name' => 'CVS & AH', 'code_base' => 'CVSAH', 'description' => 'Provides education in veterinary medicine, animal health, livestock management, and related agricultural sciences.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'cvs'],
            ['name' => 'Institute of Business Leadership', 'short_name' => 'IBL', 'code_base' => 'IBL', 'description' => 'Offers advanced business education, leadership training, and executive development programs for future business leaders.', 'faculty_id' => 2, 'campus_suffix' => null, 'email_prefix' => 'ibl'],
            ['name' => 'Pakhtunkhwa College of Arts', 'short_name' => 'PCA', 'code_base' => 'PCA', 'description' => 'Focuses on fine arts, visual arts, performing arts, and cultural studies with emphasis on regional artistic traditions.', 'faculty_id' => 1, 'campus_suffix' => null, 'email_prefix' => 'principal_pca'], 
            ['name' => 'University College for Women', 'short_name' => 'UCW', 'code_base' => 'UCW', 'description' => 'Provides higher education specifically for women across various disciplines in a supportive learning environment.', 'faculty_id' => 1, 'campus_suffix' => "M", 'email_prefix' => 'principal_ucw'], 
            // ['name' => 'Department of Agriculture', 'short_name' => 'Agriculture','code_base' => 'AGRI', 'description' => 'Focuses on agricultural sciences, crop production, and sustainable farming practices.', 'faculty_id' => 3, 'campus_suffix' => null, 'email_prefix' => 'agriculture'],

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
                    'description' =>  $sectionData['description'],
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
                    'description' => $departmentData['description'], 
                    'office_location' => $location, 
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
