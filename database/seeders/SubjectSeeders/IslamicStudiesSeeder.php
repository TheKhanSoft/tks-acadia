<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\DepartmentProgram;
use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IslamicStudiesSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            [ 'name' => 'Introduction to Islamic Studies', 'code' => 'ISL101', 'description' => 'Comprehensive introduction to Islamic faith, practices, and civilization.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Quranic Studies I', 'code' => 'ISL102', 'description' => 'Study of Quranic text, themes, and basic exegesis principles.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Hadith Studies I', 'code' => 'ISL103', 'description' => 'Introduction to Prophetic traditions, compilation, and authentication.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Seerah Studies', 'code' => 'ISL104', 'description' => 'Study of the life of Prophet Muhammad (PBUH), his character, and mission.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic History I', 'code' => 'ISL201', 'description' => 'Early Islamic history from Prophet Muhammad (PBUH) to the Umayyad period.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic History II', 'code' => 'ISL202', 'description' => 'Abbasid period, medieval Islamic civilizations, and later developments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Jurisprudence I (Usul al-Fiqh)', 'code' => 'ISL203', 'description' => 'Principles and sources of Islamic law (Shariah), methodology of legal reasoning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Jurisprudence II (Fiqh)', 'code' => 'ISL204', 'description' => 'Application of Islamic law in personal, civil, and criminal matters.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Quranic Studies II (Tafsir)', 'code' => 'ISL205', 'description' => 'Advanced Quranic interpretation, classical and modern exegesis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Hadith Studies II', 'code' => 'ISL206', 'description' => 'Advanced study of hadith literature, classification, and application.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Theology (Aqidah)', 'code' => 'ISL207', 'description' => 'Islamic beliefs, schools of theology, and contemporary issues.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Civilization', 'code' => 'ISL208', 'description' => 'Development of Islamic civilization, contributions to science, arts, and culture.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Philosophy', 'code' => 'ISL301', 'description' => 'Development of Islamic philosophical thought and major philosophers.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Ethics and Morality', 'code' => 'ISL302', 'description' => 'Islamic moral philosophy, character development, and social ethics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Comparative Study of Religions', 'code' => 'ISL303', 'description' => 'Comparative study of Islam and other major world religions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Mysticism (Tasawwuf)', 'code' => 'ISL304', 'description' => 'Development of Sufi thought, major Sufi orders, and spiritual practices.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Political System', 'code' => 'ISL305', 'description' => 'Islamic governance, political theory, and institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Economic System', 'code' => 'ISL306', 'description' => 'Principles of Islamic economics, zakat, and financial institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Education', 'code' => 'ISL307', 'description' => 'Educational philosophy in Islam, teaching methods, and curriculum development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Contemporary Islamic Thought', 'code' => 'ISL308', 'description' => 'Modern Islamic intellectual movements and reform thinkers.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Research Methodology in Islamic Studies', 'code' => 'ISL401', 'description' => 'Research methods, source analysis, and academic writing in Islamic studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Final Research Project/Thesis', 'code' => 'ISL499', 'description' => 'Independent research thesis on specialized topic in Islamic studies.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Family Laws', 'code' => 'ISL309', 'description' => 'Islamic laws regarding marriage, divorce, inheritance, and family matters.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Inheritance', 'code' => 'ISL310', 'description' => 'Detailed study of Islamic inheritance laws and their application.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Evidence', 'code' => 'ISL311', 'description' => 'Principles and practice of evidence in Islamic jurisprudence.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Crimes and Punishments', 'code' => 'ISL312', 'description' => 'Islamic criminal law, types of crimes, and prescribed punishments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic International Law', 'code' => 'ISL313', 'description' => 'Islamic principles of international relations, treaties, and diplomacy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Environmental Ethics', 'code' => 'ISL314', 'description' => 'Islamic teachings on environmental protection and sustainability.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Human Rights', 'code' => 'ISL315', 'description' => 'Islamic perspective on human rights, justice, and social equity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islam and Modern Science', 'code' => 'ISL316', 'description' => 'Relationship between Islam and modern scientific developments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Manuscript Studies', 'code' => 'ISL317', 'description' => 'Study of Islamic manuscripts, paleography, and textual criticism.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Calligraphy and Arts', 'code' => 'ISL318', 'description' => 'Islamic calligraphy, arts, and their role in Islamic civilization.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Banking and Finance', 'code' => 'ISL319', 'description' => 'Principles and practices of Islamic banking and financial systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Social System', 'code' => 'ISL320', 'description' => 'Islamic perspectives on social organization, welfare, and community.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Contracts', 'code' => 'ISL321', 'description' => 'Islamic legal principles governing contracts and commercial transactions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Penal System', 'code' => 'ISL322', 'description' => 'Islamic penal codes, punishments, and their application.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Movements in the Modern World', 'code' => 'ISL323', 'description' => 'Study of contemporary Islamic movements and their impact.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Torts', 'code' => 'ISL324', 'description' => 'Islamic legal principles regarding civil wrongs and liabilities.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Property', 'code' => 'ISL325', 'description' => 'Ownership, transfer, and management of property in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Partnership', 'code' => 'ISL326', 'description' => 'Islamic legal framework for business partnerships and joint ventures.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Trusts (Waqf)', 'code' => 'ISL327', 'description' => 'Concept and management of waqf (endowment) in Islam.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Succession', 'code' => 'ISL328', 'description' => 'Rules and procedures of succession in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Evidence and Procedure', 'code' => 'ISL329', 'description' => 'Procedural aspects of evidence in Islamic courts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Arbitration', 'code' => 'ISL330', 'description' => 'Islamic dispute resolution and arbitration mechanisms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Agency', 'code' => 'ISL331', 'description' => 'Agency and representation in Islamic commercial law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Gifts (Hibah)', 'code' => 'ISL332', 'description' => 'Legal framework for gifts and donations in Islam.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Endowment (Waqf)', 'code' => 'ISL333', 'description' => 'Establishment and administration of waqf in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Suretyship (Kafalah)', 'code' => 'ISL334', 'description' => 'Concept and application of suretyship in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Pledge (Rahn)', 'code' => 'ISL335', 'description' => 'Legal aspects of pledges and collateral in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Agency (Wakalah)', 'code' => 'ISL336', 'description' => 'Principles and practices of agency in Islamic transactions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Lease (Ijarah)', 'code' => 'ISL337', 'description' => 'Rules and regulations of leasing in Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Law of Partnership (Musharakah)', 'code' => 'ISL338', 'description' => 'Types and rules of partnership in Islamic commercial law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],

            // MPhil Subjects
            [ 'name' => 'Advanced Quranic Studies', 'code' => 'ISL601', 'description' => 'In-depth analysis of Quranic sciences and methodologies of Tafsir.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Hadith Studies', 'code' => 'ISL602', 'description' => 'Critical evaluation of Hadith literature and its sciences.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Methodology of Research in Islamic Studies', 'code' => 'ISL603', 'description' => 'Advanced research methods for Islamic studies scholars.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Theology and Modern Challenges', 'code' => 'ISL604', 'description' => 'Contemporary issues in Islamic theology and Kalam.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Principles of Islamic Jurisprudence (Usul al-Fiqh)', 'code' => 'ISL605', 'description' => 'Advanced study of the sources and principles of Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Comparative Fiqh', 'code' => 'ISL606', 'description' => 'A comparative study of different schools of Islamic law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Political and Economic Thought', 'code' => 'ISL607', 'description' => 'Classical and modern Islamic political and economic theories.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Sufism: History and Doctrines', 'code' => 'ISL608', 'description' => 'Historical development and key doctrines of Islamic mysticism.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Civilization and Culture', 'code' => 'ISL609', 'description' => 'Intellectual and cultural history of Islamic civilization.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islam and the West: A Historical Perspective', 'code' => 'ISL610', 'description' => 'Historical interactions between Islam and the Western world.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Fiqh of Minorities', 'code' => 'ISL611', 'description' => 'Jurisprudence concerning Muslim minorities living in non-Muslim societies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Bioethics', 'code' => 'ISL612', 'description' => 'Islamic perspectives on contemporary bioethical issues.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Gender Studies in Islam', 'code' => 'ISL613', 'description' => 'Role and status of women in Islam, and feminist interpretations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Art and Architecture', 'code' => 'ISL614', 'description' => 'A survey of the major developments in Islamic art and architecture.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Readings in Classical Arabic Texts', 'code' => 'ISL615', 'description' => 'Analysis of selected classical Arabic texts in various Islamic sciences.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Finance and Banking', 'code' => 'ISL616', 'description' => 'Advanced principles and practices of Islamic finance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Law of Contract and Business Ethics', 'code' => 'ISL617', 'description' => 'Advanced study of contracts and business ethics in Islam.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Contemporary Muslim Thinkers', 'code' => 'ISL618', 'description' => 'A study of influential Muslim thinkers of the 20th and 21st centuries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Da\'wah: Methods and Strategies', 'code' => 'ISL619', 'description' => 'The methodology and strategies of Islamic propagation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Thematic Study of the Seerah', 'code' => 'ISL620', 'description' => 'Thematic analysis of the life of Prophet Muhammad (PBUH).', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Criminology', 'code' => 'ISL621', 'description' => 'Islamic perspectives on crime, punishment, and justice.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Maqasid al-Shariah', 'code' => 'ISL622', 'description' => 'The higher objectives and intents of Islamic Law.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islamic Psychology', 'code' => 'ISL623', 'description' => 'An introduction to psychology from an Islamic perspective.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'History of Islamic Law', 'code' => 'ISL624', 'description' => 'The historical development of Islamic legal schools and doctrines.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'ISL699', 'description' => 'Independent research thesis for the MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Advanced Research Seminar in Islamic Studies', 'code' => 'ISL701', 'description' => 'Doctoral seminar on advanced research topics and methodologies.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar on Quranic Exegesis (Tafsir)', 'code' => 'ISL702', 'description' => 'Advanced seminar on classical and contemporary Tafsir literature.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar on Hadith Criticism', 'code' => 'ISL703', 'description' => 'In-depth study of the methodologies of Hadith criticism.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Islamic Jurisprudence', 'code' => 'ISL704', 'description' => 'Exploration of complex and contemporary issues in Fiqh.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Islamic Philosophy and Kalam', 'code' => 'ISL705', 'description' => 'Advanced studies in Islamic philosophy and speculative theology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Sufism and Islamic Spirituality', 'code' => 'ISL706', 'description' => 'Advanced research in the field of Tasawwuf and Islamic spirituality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Islam in the Modern World', 'code' => 'ISL707', 'description' => 'Critical analysis of Islam\'s engagement with modernity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Orientalism and Islamic Studies', 'code' => 'ISL708', 'description' => 'A critical review of Western scholarship on Islam.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Islamic Law and Human Rights', 'code' => 'ISL709', 'description' => 'A comparative study of Islamic and international human rights law.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Islamic Economics in a Global Context', 'code' => 'ISL710', 'description' => 'Advanced topics in Islamic economics and its global applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'The Sociology of the Muslim World', 'code' => 'ISL711', 'description' => 'Sociological approaches to the study of Muslim societies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Islamic Political Thought in the 20th Century', 'code' => 'ISL712', 'description' => 'A study of major Islamic political thinkers of the last century.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Inter-faith Dialogue and Relations', 'code' => 'ISL713', 'description' => 'Theological and practical dimensions of inter-faith dialogue.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Directed Readings for Comprehensive Exam', 'code' => 'ISL714', 'description' => 'Guided readings in preparation for the PhD comprehensive examination.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'ISL799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Islamic Studies';    
         
        $programSemesters = [
            'BS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 8, 'program_ids' => [], 'degree_level_id' => 1],
            'MS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 2],
            'PhD' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 4],
        ];

        $subSeederHelper = new SubjectSeederHelper();
        
        $info = $subSeederHelper->RunSeeder($department, $subjects, $programSemesters);

        switch ($info['type']) {
            case 'info':
                $this->command->info( '  ' . $info['message'] );
                // Log::info($info['message']);
                break;
            case 'warn':
                $this->command->warn( '  ' . $info['message'] );
                // Log::warning($info['message']);
                break;
            case 'error':
                $this->command->error( '  ' . $info['message'] );
                Log::error($info['message']);
                break;
            default:
                $this->command->error('  Unknown message type: ' . $info['type']);
                Log::error('  Unknown message type: ' . $info['type']);
                break;
        }
    }
}
