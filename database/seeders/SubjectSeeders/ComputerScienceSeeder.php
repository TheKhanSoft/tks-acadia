<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\DegreeLevel;
use App\Models\Office;
use App\Models\Program;
use App\Models\Subject;
use App\Models\ProgramSubject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class ComputerScienceSeeder extends Seeder
{
    public function run()
    {
        $subjects = 
        [
            // --- BS Program Subjects ---
            [ 'name' => 'Introduction to Computing', 'code' => 'CS100', 'description' => 'A foundational course covering the basics of computing, including hardware, software, and internet concepts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Programming Fundamentals', 'code' => 'CS101', 'description' => 'Introduction to programming concepts and logic using modern programming languages.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Communication Skills', 'code' => 'ENG101', 'description' => 'Developing effective written and oral communication skills for academic and professional settings.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'BCS'],
            [ 'name' => 'Pakistan Studies', 'code' => 'HUM101', 'description' => 'A study of the history, culture, and political landscape of Pakistan.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'BCS'],
            [ 'name' => 'Islamic Studies', 'code' => 'HUM102', 'description' => 'An introduction to the fundamental principles and teachings of Islam.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'BCS'],
            [ 'name' => 'Differential Equations', 'code' => 'MATH202', 'description' => 'Solving ordinary and partial differential equations with applications in science and engineering.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Object Oriented Programming', 'code' => 'CS201', 'description' => 'Concepts of object-oriented programming including classes, objects, inheritance and polymorphism.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Data Structures and Algorithms', 'code' => 'CS202', 'description' => 'Study of fundamental data structures and algorithms for efficient problem solving.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Database Systems', 'code' => 'CS301', 'description' => 'Design and implementation of database systems including SQL and database management.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Computer Networks', 'code' => 'CS302', 'description' => 'Fundamentals of computer networking protocols, architecture and network security.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Operating Systems', 'code' => 'CS303', 'description' => 'Study of operating system concepts including process management, memory management and file systems.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Software Engineering', 'code' => 'CS304', 'description' => 'Software development lifecycle, project management and software quality assurance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Computer Architecture', 'code' => 'CS305', 'description' => 'Study of computer organization, processor design and memory hierarchy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Artificial Intelligence', 'code' => 'CS401', 'description' => 'Introduction to AI concepts including machine learning, expert systems and neural networks.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Web Technologies', 'code' => 'CS402', 'description' => 'Development of web applications using modern web technologies and frameworks.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Discrete Mathematics', 'code' => 'CS111', 'description' => 'Mathematical foundations for computer science including logic, sets and graph theory.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Computer Graphics', 'code' => 'CS403', 'description' => 'Fundamentals of computer graphics including 2D/3D graphics and visualization techniques.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Mobile Application Development', 'code' => 'CS404', 'description' => 'Development of mobile applications for Android and iOS platforms.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Information Security', 'code' => 'CS405', 'description' => 'Cybersecurity principles, cryptography and secure system design.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Human Computer Interaction', 'code' => 'CS406', 'description' => 'Design and evaluation of user interfaces and user experience principles.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Final Year Project I', 'code' => 'CS498', 'description' => 'Independent research project under faculty supervision - Phase I.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Final Year Project II', 'code' => 'CS499', 'description' => 'Completion and presentation of final year research project - Phase II.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Calculus and Analytical Geometry', 'code' => 'MATH101', 'description' => 'Differential and integral calculus with applications in computer science.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Digital Logic Design', 'code' => 'CS112', 'description' => 'Boolean algebra, logic gates, combinational and sequential circuits.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Assembly Language Programming', 'code' => 'CS205', 'description' => 'Low-level programming, processor architecture, and system programming.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Theory of Computation', 'code' => 'CS306', 'description' => 'Formal languages, automata theory, and computational complexity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Compiler Construction', 'code' => 'CS407', 'description' => 'Design and implementation of compilers, lexical analysis, and code generation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Distributed Systems', 'code' => 'CS408', 'description' => 'Design principles of distributed computing systems and fault tolerance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Machine Learning', 'code' => 'CS409', 'description' => 'Supervised and unsupervised learning algorithms and their applications.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Digital Image Processing', 'code' => 'CS410', 'description' => 'Image enhancement, filtering, segmentation, and pattern recognition techniques.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'], // Corrected duplicate code CS4171 to CS410
            [ 'name' => 'Parallel Computing', 'code' => 'CS411', 'description' => 'Parallel algorithms, multiprocessor systems, and GPU programming.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Bioinformatics', 'code' => 'CS412', 'description' => 'Computational methods for biological data analysis and sequence alignment.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BCS'],
            [ 'name' => 'Numerical Computing', 'code' => 'CS413', 'description' => 'Numerical methods for solving mathematical problems using computers.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Cloud Computing', 'code' => 'CS414', 'description' => 'Cloud service models, virtualization, and distributed cloud architectures.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Quantum Computing', 'code' => 'CS415', 'description' => 'Quantum algorithms, quantum gates, and quantum information processing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Embedded Systems', 'code' => 'CS416', 'description' => 'Microcontroller programming, real-time systems, and hardware interfaces.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Internet of Things', 'code' => 'CS417', 'description' => 'IoT architecture, sensor networks, and smart device programming.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Blockchain Technology', 'code' => 'CS418', 'description' => 'Cryptocurrency, smart contracts, and distributed ledger technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Natural Language Processing', 'code' => 'CS419', 'description' => 'Computational linguistics, text processing, and language understanding.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Robotics', 'code' => 'CS420', 'description' => 'Robot kinematics, motion planning, and autonomous navigation systems.', 'credit_hours' => '3+1', 'category' => 'Interdisciplinary', 'program_level' => 'BCS'],
            [ 'name' => 'Professional Ethics in Computing', 'code' => 'CS421', 'description' => 'Ethical issues in technology, privacy, intellectual property, and social responsibility.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'BCS'],
            [ 'name' => 'Software Project Management', 'code' => 'CS422', 'description' => 'Planning, executing, and managing software development projects.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Game Development', 'code' => 'CS423', 'description' => 'Principles of game design, game engines, and development for various platforms.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Data Warehousing', 'code' => 'CS424', 'description' => 'Design and implementation of data warehouses for business intelligence.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Network Security', 'code' => 'CS425', 'description' => 'Securing network infrastructure, firewalls, and intrusion detection systems.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Software Quality Assurance', 'code' => 'CS426', 'description' => 'Testing methodologies, quality standards, and automated testing tools.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'System Programming', 'code' => 'CS307', 'description' => 'Programming with system calls, process management, and inter-process communication.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Computer Organization and Assembly Language', 'code' => 'CS206', 'description' => 'A combined course on computer architecture and low-level programming.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Linear Algebra for CS', 'code' => 'MATH201', 'description' => 'Linear algebra concepts essential for computer graphics, machine learning, and data science.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Probability and Statistics for CS', 'code' => 'STAT201', 'description' => 'Probability theory and statistical methods with applications in computer science.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BCS'],
            [ 'name' => 'Technical and Business Writing', 'code' => 'ENG301', 'description' => 'Writing technical documents, reports, and business correspondence.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'BCS'],
            [ 'name' => 'Introduction to Data Science', 'code' => 'DS201', 'description' => 'Overview of the data science lifecycle, tools, and applications.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BCS'],
            [ 'name' => 'Design and Analysis of Algorithms', 'code' => 'CS308', 'description' => 'Advanced algorithm design strategies, complexity analysis, and NP-completeness.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BCS'],
            [ 'name' => 'Software Architecture and Design', 'code' => 'CS427', 'description' => 'Design patterns, architectural styles, and software modeling.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BCS'],
            [ 'name' => 'Virtual and Augmented Reality', 'code' => 'CS428', 'description' => 'Principles and technologies behind VR and AR systems and applications.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BCS'],

            // --- MS/MPhil Program Subjects ---
            [ 'name' => 'Advanced Algorithms', 'code' => 'CS501', 'description' => 'In-depth study of advanced algorithm design and analysis techniques.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology', 'code' => 'CS502', 'description' => 'Principles and practices of conducting research in computer science.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Operating Systems', 'code' => 'CS503', 'description' => 'Advanced topics in operating systems, including distributed and real-time systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Database Systems', 'code' => 'CS504', 'description' => 'Topics in distributed databases, data warehousing, and big data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Machine Learning Theory', 'code' => 'CS505', 'description' => 'Theoretical foundations of machine learning algorithms.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Deep Learning', 'code' => 'CS506', 'description' => 'Architectures and applications of deep neural networks.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Computer Networks', 'code' => 'CS507', 'description' => 'Advanced topics in network protocols, wireless networks, and network security.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Cryptography and Network Security', 'code' => 'CS508', 'description' => 'Modern cryptographic techniques and their application in securing networks and data.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Big Data Analytics', 'code' => 'CS509', 'description' => 'Techniques and tools for analyzing large-scale datasets.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Cloud Computing Security', 'code' => 'CS510', 'description' => 'Security challenges and solutions in cloud computing environments.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis I', 'code' => 'CS598', 'description' => 'Initial phase of MS thesis research.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis II', 'code' => 'CS599', 'description' => 'Completion and defense of MS thesis.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'Computer Vision', 'code' => 'CS511', 'description' => 'Image formation, feature detection, and 3D reconstruction.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Natural Language Processing', 'code' => 'CS512', 'description' => 'Text processing, language models, and machine translation.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Reinforcement Learning', 'code' => 'CS513', 'description' => 'Markov decision processes, Q-learning, and policy gradients.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Software Engineering', 'code' => 'CS514', 'description' => 'Software architecture, design patterns, and agile methodologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Distributed Database Systems', 'code' => 'CS515', 'description' => 'Distributed data storage, transaction management, and query processing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Wireless and Mobile Networks', 'code' => 'CS516', 'description' => 'Wireless communication, mobile IP, and ad-hoc networks.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Ethical Hacking and Penetration Testing', 'code' => 'CS517', 'description' => 'Vulnerability assessment, penetration testing techniques, and reporting.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Digital Forensics', 'code' => 'CS518', 'description' => 'Investigation of digital crimes, data recovery, and evidence analysis.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Data Warehousing and Data Mining', 'code' => 'CS519', 'description' => 'Data warehouse design, ETL processes, and data mining algorithms.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Information Retrieval', 'code' => 'CS520', 'description' => 'Indexing, retrieval models, and evaluation of search engines.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Human-Computer Interaction', 'code' => 'CS521', 'description' => 'User-centered design, usability testing, and emerging interaction technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Formal Methods in Software Engineering', 'code' => 'CS522', 'description' => 'Formal specification, verification, and model checking.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Computer Science', 'code' => 'CS590', 'description' => 'Presentation and discussion of current research topics.', 'credit_hours' => '1', 'category' => 'Core', 'program_level' => 'MS'],

            // --- PhD Program Subjects ---
            [ 'name' => 'Advanced Topics in AI', 'code' => 'CS701', 'description' => 'Cutting-edge research topics in Artificial Intelligence.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Computer Science Research', 'code' => 'CS702', 'description' => 'Presentation and critical discussion of current research papers and proposals.', 'credit_hours' => '1-3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Research Methodology for Doctoral Studies', 'code' => 'CS703', 'description' => 'Advanced research design, quantitative and qualitative data analysis, and academic writing for doctoral candidates.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Special Topics in Distributed and Parallel Computing', 'code' => 'CS704', 'description' => 'Exploration of advanced and emerging topics in distributed systems, parallel algorithms, and high-performance computing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Quantum Information Science', 'code' => 'CS705', 'description' => 'In-depth study of advanced principles of quantum computing, quantum error correction, quantum cryptography, and quantum information theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Theory of Computation for Advanced Studies', 'code' => 'CS706', 'description' => 'Advanced topics in computability, complexity theory, and formal models of computation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Machine Learning', 'code' => 'CS707', 'description' => 'Exploration of recent advancements and research frontiers in machine learning, including reinforcement learning and generative models.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Qualifying Examination Preparation', 'code' => 'CS790', 'description' => 'Guided study and preparation for doctoral qualifying examinations.', 'credit_hours' => '3', 'category' => 'Milestone', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research I', 'code' => 'CS798', 'description' => 'Initial phase of independent doctoral research leading to dissertation proposal.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research II', 'code' => 'CS799', 'description' => 'Continued independent doctoral research, dissertation writing, and defense.', 'credit_hours' => '6-12', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Computer Vision', 'code' => 'CS708', 'description' => 'Current research in object recognition, scene understanding, and video analysis.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Cybersecurity', 'code' => 'CS709', 'description' => 'Research on emerging threats, defensive technologies, and security policies.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Data Science', 'code' => 'CS710', 'description' => 'Research in scalable data analysis, data visualization, and data ethics.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching Practicum in Computer Science', 'code' => 'CS795', 'description' => 'Supervised teaching experience in undergraduate computer science courses.', 'credit_hours' => '1-3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Research in Computer Science', 'code' => 'CS797', 'description' => 'Independent research on a specialized topic under faculty guidance.', 'credit_hours' => '1-3', 'category' => 'Research', 'program_level' => 'PhD'],
        ];

        $departmentName = 'Department of Computer Science';

        $programSemesters = [
            'BCS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 8, 'program_ids' => [], 'degree_level_id' => 1],
            'MS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 2],
            'PhD' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 4],
        ];

        $subSeederHelper = new SubjectSeederHelper();
        
        $info = $subSeederHelper->RunSeeder($departmentName, $subjects, $programSemesters);

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
