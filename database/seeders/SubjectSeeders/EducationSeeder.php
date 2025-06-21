<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Support\Facades\Log;

class EducationSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // BS Subjects
            [ 'name' => 'Foundations of Education', 'code' => 'EDU101', 'description' => 'Philosophical, historical, and sociological foundations of education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Child Development and Learning', 'code' => 'EDU102', 'description' => 'Theories of child development (cognitive, social, emotional, physical) and their implications for learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Psychology', 'code' => 'EDU103', 'description' => 'Application of psychological principles to teaching and learning processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Curriculum Development and Implementation', 'code' => 'EDU201', 'description' => 'Principles, processes, and models of curriculum design, development, and evaluation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'General Methods of Teaching', 'code' => 'EDU202', 'description' => 'Various teaching strategies, classroom management techniques, and instructional planning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Classroom Assessment and Evaluation', 'code' => 'EDU203', 'description' => 'Techniques for assessing student learning, test construction, and grading.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Technology and ICT in Education', 'code' => 'EDU204', 'description' => 'Integration of technology and ICT tools in teaching and learning.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'School Organization and Management', 'code' => 'EDU301', 'description' => 'Principles of school administration, leadership, and management of school resources.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Inclusive Education', 'code' => 'EDU302', 'description' => 'Principles and practices of creating inclusive learning environments for diverse learners.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methods in Education', 'code' => 'EDU401', 'description' => 'Introduction to educational research, methodologies, and data analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Teaching Practicum I (Observation)', 'code' => 'EDU402', 'description' => 'Supervised observation and participation in a school setting.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Teaching Practicum II (Supervised Teaching)', 'code' => 'EDU403', 'description' => 'Supervised teaching practice in a classroom setting.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Project/Thesis in Education', 'code' => 'EDU499', 'description' => 'Independent research project on a topic in education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Comparative Education', 'code' => 'EDU303', 'description' => 'A comparative study of educational systems across different countries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Guidance and Counseling in Schools', 'code' => 'EDU304', 'description' => 'Principles and techniques of guidance and counseling for students.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Philosophy of Education', 'code' => 'EDU104', 'description' => 'An introduction to the philosophical thought that has influenced education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Education', 'code' => 'EDU105', 'description' => 'The study of how social institutions and individual experiences affect education and its outcomes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'History of Education', 'code' => 'EDU106', 'description' => 'A survey of the historical development of educational theories and practices.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Leadership', 'code' => 'EDU305', 'description' => 'Theories and practices of leadership in educational settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'School, Community, and Teacher', 'code' => 'EDU205', 'description' => 'Examines the relationship between schools, the communities they serve, and the role of the teacher.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Language and Literacy Education', 'code' => 'EDU206', 'description' => 'Methods and theories for teaching language and literacy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematics Education', 'code' => 'EDU207', 'description' => 'Methods and theories for teaching mathematics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Science Education', 'code' => 'EDU208', 'description' => 'Methods and theories for teaching science.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Studies Education', 'code' => 'EDU209', 'description' => 'Methods and theories for teaching social studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Arts in Education', 'code' => 'EDU210', 'description' => 'The role and methods of teaching arts in the curriculum.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Health and Physical Education', 'code' => 'EDU211', 'description' => 'Methods and content for teaching health and physical education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Adolescent Development', 'code' => 'EDU306', 'description' => 'Study of the developmental changes and challenges during adolescence.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Classroom Management', 'code' => 'EDU307', 'description' => 'Strategies for creating a positive and effective learning environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Education for Sustainable Development', 'code' => 'EDU308', 'description' => 'Integrating principles of sustainability into education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Early Childhood Education', 'code' => 'EDU309', 'description' => 'Theories and practices for educating young children (ages 0-8).', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Special Needs Education', 'code' => 'EDU310', 'description' => 'Introduction to the field of special education and the needs of diverse learners.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Planning and Finance', 'code' => 'EDU404', 'description' => 'Principles of planning and financing educational systems and institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Professional Ethics in Education', 'code' => 'EDU405', 'description' => 'Ethical issues and professional responsibilities in the field of education.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Policy', 'code' => 'EDU406', 'description' => 'Analysis of educational policies and their impact on practice.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Learning', 'code' => 'EDU107', 'description' => 'An in-depth look at the psychological processes involved in learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Instructional Strategies', 'code' => 'EDU212', 'description' => 'A survey of various instructional strategies to meet diverse learning needs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Curriculum and Instruction', 'code' => 'EDU213', 'description' => 'The relationship between curriculum design and instructional practice.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Measurement', 'code' => 'EDU311', 'description' => 'The theory and technique of educational and psychological measurement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Human Development', 'code' => 'EDU108', 'description' => 'A lifespan perspective on human growth and development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cognitive Development', 'code' => 'EDU214', 'description' => 'The development of thought, language, and intelligence.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social and Emotional Learning', 'code' => 'EDU215', 'description' => 'Developing social and emotional competencies in students.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Motivation in Education', 'code' => 'EDU312', 'description' => 'Theories and strategies for enhancing student motivation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Governance', 'code' => 'EDU407', 'description' => 'The structures and processes of governance in education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'School Law', 'code' => 'EDU408', 'description' => 'Legal issues affecting schools, students, and teachers.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Action Research in Education', 'code' => 'EDU409', 'description' => 'Using research to improve teaching practices.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Multicultural Education', 'code' => 'EDU313', 'description' => 'Creating equitable educational experiences for students from diverse cultural backgrounds.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Urban Education', 'code' => 'EDU314', 'description' => 'The challenges and opportunities of education in urban settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Rural Education', 'code' => 'EDU315', 'description' => 'The challenges and opportunities of education in rural settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Anthropology', 'code' => 'EDU216', 'description' => 'The study of education from an anthropological perspective.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Learning Disabilities', 'code' => 'EDU316', 'description' => 'Understanding and supporting students with learning disabilities.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Behavioral Disorders in Children', 'code' => 'EDU317', 'description' => 'Understanding and managing behavioral challenges in the classroom.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Gifted and Talented Education', 'code' => 'EDU318', 'description' => 'Identifying and serving the needs of gifted and talented students.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Neuroscience', 'code' => 'EDU410', 'description' => 'The intersection of neuroscience and education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Play and Learning', 'code' => 'EDU109', 'description' => 'The role of play in child development and learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Digital Literacy and Citizenship', 'code' => 'EDU217', 'description' => 'Developing skills for navigating the digital world responsibly.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Assessment for Learning', 'code' => 'EDU319', 'description' => 'Using assessment to inform instruction and support student learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Teacher as a Reflective Practitioner', 'code' => 'EDU411', 'description' => 'Developing the skills of reflection to improve teaching.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Parent and Community Involvement', 'code' => 'EDU412', 'description' => 'Strategies for building strong partnerships with families and the community.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // MS/MPhil Subjects
            [ 'name' => 'Advanced Educational Psychology', 'code' => 'EDU501', 'description' => 'In-depth study of learning theories, motivation, and cognitive development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Curriculum Development and Evaluation', 'code' => 'EDU502', 'description' => 'Advanced theories and models of curriculum design, innovation, and assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Educational Leadership and Management', 'code' => 'EDU503', 'description' => 'Theories of educational leadership, strategic planning, and school improvement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Research Methods in Education (Quantitative)', 'code' => 'EDU504', 'description' => 'Advanced quantitative research designs, statistical analysis, and interpretation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Research Methods in Education (Qualitative)', 'code' => 'EDU505', 'description' => 'Advanced qualitative research methodologies, data collection, and analysis techniques.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Educational Policy and Planning', 'code' => 'EDU506', 'description' => 'Analysis of educational policies, planning processes, and policy implementation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Teacher Education and Professional Development', 'code' => 'EDU507', 'description' => 'Models and practices of pre-service and in-service teacher education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Education (Advanced)', 'code' => 'EDU508', 'description' => 'Advanced sociological perspectives on education, inequality, and social change.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Philosophy of Education (Advanced)', 'code' => 'EDU509', 'description' => 'In-depth examination of philosophical issues and theories in education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Educational Measurement and Testing (Advanced)', 'code' => 'EDU510', 'description' => 'Advanced psychometric theories, test development, and large-scale assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Instructional Design and Technology (Advanced)', 'code' => 'EDU511', 'description' => 'Advanced principles of instructional design and integration of emerging technologies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Economics of Education', 'code' => 'EDU512', 'description' => 'Economic principles applied to education, human capital theory, and financing education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Adult and Continuing Education', 'code' => 'EDU513', 'description' => 'Theories and practices of adult learning and lifelong education programs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Special Education: Assessment and Intervention (Advanced)', 'code' => 'EDU514', 'description' => 'Advanced assessment techniques and intervention strategies for students with special needs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Educational Supervision and Mentoring', 'code' => 'EDU515', 'description' => 'Techniques for supervising teachers and mentoring novice educators.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Higher Education: Issues and Trends', 'code' => 'EDU516', 'description' => 'Contemporary issues, governance, and trends in higher education systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Gender and Education', 'code' => 'EDU517', 'description' => 'Analysis of gender issues in educational access, participation, and outcomes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Peace Education and Conflict Resolution', 'code' => 'EDU518', 'description' => 'Principles and practices of peace education and conflict resolution in educational settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Environmental Education', 'code' => 'EDU519', 'description' => 'Approaches to teaching environmental awareness and sustainability.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Distance Education and E-Learning', 'code' => 'EDU520', 'description' => 'Theories, design, and delivery of distance education and online learning programs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Program Evaluation in Education', 'code' => 'EDU522', 'description' => 'Methods and models for evaluating educational programs and interventions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Cognitive Psychology in Education', 'code' => 'EDU523', 'description' => 'Advanced study of cognitive processes (memory, attention, problem-solving) in learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Critical Pedagogy', 'code' => 'EDU526', 'description' => 'Theories and practices of critical pedagogy for social justice in education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Human Resource Management in Education', 'code' => 'EDU536', 'description' => 'Principles of HRM applied to educational institutions, including recruitment, development, and retention of staff.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis', 'code' => 'EDU599', 'description' => 'Independent research thesis for MS in Education.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],

            // PhD Subjects
            [ 'name' => 'Doctoral Seminar in Educational Foundations', 'code' => 'EDU701', 'description' => 'Critical examination of philosophical, historical, and sociological foundations of education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Theories of Learning', 'code' => 'EDU702', 'description' => 'In-depth analysis of major learning theories and their application in educational research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Epistemology and Education', 'code' => 'EDU703', 'description' => 'Exploration of the nature of knowledge and its implications for educational practice and research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Quantitative Research Design', 'code' => 'EDU704', 'description' => 'Advanced experimental, quasi-experimental, and correlational research designs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Qualitative Research Design', 'code' => 'EDU705', 'description' => 'Advanced methodologies in qualitative research, including ethnography, phenomenology, and case study.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Mixed-Methods Research in Education', 'code' => 'EDU706', 'description' => 'Design and application of mixed-methods research in educational contexts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Policy Analysis in Higher Education', 'code' => 'EDU707', 'description' => 'Frameworks for analyzing higher education policies and their impact.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Leadership in Educational Organizations', 'code' => 'EDU708', 'description' => 'Advanced theories of leadership and organizational change in educational institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Curriculum Theory and Criticism', 'code' => 'EDU709', 'description' => 'Critical analysis of curriculum theories, ideologies, and development models.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Teacher Education', 'code' => 'EDU710', 'description' => 'Current research, policies, and practices in teacher education and professional development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Technology and Innovation in Education', 'code' => 'EDU711', 'description' => 'Critical examination of the role of technology and innovation in transforming education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Globalization and Education', 'code' => 'EDU712', 'description' => 'Analysis of the impact of globalization on educational policies and practices worldwide.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Educational Assessment', 'code' => 'EDU713', 'description' => 'Advanced topics in educational assessment, including psychometrics and policy issues.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Grant Writing and Research Funding', 'code' => 'EDU714', 'description' => 'Developing skills in writing research proposals and securing funding for educational research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation', 'code' => 'EDU799', 'description' => 'Original research dissertation contributing to the field of education.', 'credit_hours' => '12', 'category' => 'Major', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Education';

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
