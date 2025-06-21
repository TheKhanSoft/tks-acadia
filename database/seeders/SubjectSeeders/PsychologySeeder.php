<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class PsychologySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Psychology
            [ 'name' => 'Introduction to Psychology I', 'code' => 'PSY101', 'description' => 'Foundations of psychology, history, major perspectives, and research methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Psychology II', 'code' => 'PSY102', 'description' => 'Continuation of PSY101, covering topics like learning, memory, motivation, emotion, and personality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Developmental Psychology (Lifespan Development)', 'code' => 'PSY201', 'description' => 'Physical, cognitive, and psychosocial development from conception to death.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Psychology', 'code' => 'PSY202', 'description' => 'How individuals\' thoughts, feelings, and behaviors are influenced by others and social situations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Abnormal Psychology', 'code' => 'PSY203', 'description' => 'Nature, causes, and treatment of psychological disorders.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cognitive Psychology', 'code' => 'PSY301', 'description' => 'Mental processes such as perception, attention, memory, language, problem-solving, and decision-making.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biological Psychology (Behavioral Neuroscience)', 'code' => 'PSY302', 'description' => 'Biological bases of behavior, including brain structures, neurotransmitters, and genetics.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Personality Theories', 'code' => 'PSY303', 'description' => 'Major theories of personality development and assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methods in Psychology I', 'code' => 'PSY304', 'description' => 'Principles of research design, data collection, and ethical considerations in psychological research.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistics for Psychology I', 'code' => 'PSY211', 'description' => 'Descriptive and inferential statistics used in psychological research.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Statistics for Psychology II', 'code' => 'PSY311', 'description' => 'Advanced statistical techniques, including ANOVA, regression, and non-parametric tests.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Psychological Testing and Assessment', 'code' => 'PSY305', 'description' => 'Principles of psychological measurement, test construction, administration, and interpretation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Experimental Psychology', 'code' => 'PSY401', 'description' => 'Design and execution of experiments in various areas of psychology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'History and Systems of Psychology', 'code' => 'PSY402', 'description' => 'Historical development of psychological thought and major schools of psychology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Clinical Psychology', 'code' => 'PSY403', 'description' => 'Assessment, diagnosis, and treatment of mental disorders; introduction to psychotherapy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Psychology Research Project I (Proposal)', 'code' => 'PSY498A', 'description' => 'Development of a research proposal for an independent psychology project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Psychology Research Project II (Thesis)', 'code' => 'PSY498B', 'description' => 'Execution, analysis, and write-up of the independent psychology research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sensation and Perception', 'code' => 'PSY212', 'description' => 'How sensory systems receive and process information from the environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Learning and Behavior', 'code' => 'PSY213', 'description' => 'Principles of classical conditioning, operant conditioning, and observational learning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Motivation and Emotion', 'code' => 'PSY312', 'description' => 'Theories and research on human motivation and emotional experiences.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cross-Cultural Psychology', 'code' => 'PSY313', 'description' => 'Influence of culture on psychological processes and behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Ethics in Psychology', 'code' => 'PSY411', 'description' => 'Ethical principles and dilemmas in psychological research and practice.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Counseling Psychology', 'code' => 'PSY314', 'description' => 'Theories and techniques of counseling for personal and interpersonal problems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Health Psychology', 'code' => 'PSY315', 'description' => 'Psychological factors in health, illness, and healthcare.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Educational Psychology', 'code' => 'PSY316', 'description' => 'Application of psychological principles to learning, teaching, and educational settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Industrial/Organizational Psychology', 'code' => 'PSY501', 'description' => 'Application of psychological principles to workplace behavior, personnel selection, and organizational development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Forensic Psychology', 'code' => 'PSY502', 'description' => 'Intersection of psychology and the legal system, including criminal profiling and eyewitness testimony.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Neuropsychology', 'code' => 'PSY503', 'description' => 'Relationship between brain function and behavior, assessment of brain damage.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychopharmacology', 'code' => 'PSY504', 'description' => 'Effects of drugs on behavior, mood, and mental processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Child Psychology', 'code' => 'PSY505', 'description' => 'In-depth study of cognitive, social, and emotional development in childhood.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Adolescent Psychology', 'code' => 'PSY506', 'description' => 'Psychological development and challenges during adolescence.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Aging (Geropsychology)', 'code' => 'PSY507', 'description' => 'Psychological aspects of aging, including cognitive changes and mental health in older adults.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Positive Psychology', 'code' => 'PSY508', 'description' => 'Study of human strengths, well-being, happiness, and optimal functioning.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Community Psychology', 'code' => 'PSY509', 'description' => 'Focus on social issues, prevention, and intervention at the community level.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Psychology', 'code' => 'PSY510', 'description' => 'Interaction between humans and their physical environments.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sports Psychology', 'code' => 'PSY511', 'description' => 'Psychological factors influencing performance in sports and exercise.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Gender', 'code' => 'PSY512', 'description' => 'Psychological perspectives on gender identity, roles, and differences.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Language (Psycholinguistics)', 'code' => 'PSY513', 'description' => 'Cognitive processes involved in language acquisition, comprehension, and production.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Human Factors Psychology (Ergonomics)', 'code' => 'PSY514', 'description' => 'Designing systems, products, and environments to optimize human well-being and performance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Trauma Psychology', 'code' => 'PSY515', 'description' => 'Psychological impact of traumatic events and interventions for trauma survivors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Group Dynamics and Psychotherapy', 'code' => 'PSY516', 'description' => 'Processes and theories of group behavior and group therapy techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Consumer Psychology', 'code' => 'PSY517', 'description' => 'Psychological principles underlying consumer behavior and marketing strategies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Religion and Spirituality', 'code' => 'PSY518', 'description' => 'Psychological study of religious beliefs, practices, and spiritual experiences.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Research Methods in Psychology', 'code' => 'PSY519', 'description' => 'Advanced research designs, qualitative methods, and program evaluation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Internship in Psychology', 'code' => 'PSY520', 'description' => 'Supervised practical experience in a psychological setting.', 'credit_hours' => '3-6', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cognitive Neuroscience', 'code' => 'PSY521', 'description' => 'Neural basis of cognitive functions, using methods like fMRI and EEG.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Art and Creativity', 'code' => 'PSY522', 'description' => 'Psychological processes involved in artistic creation and appreciation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Evolutionary Psychology', 'code' => 'PSY523', 'description' => 'Application of evolutionary principles to understand human behavior and cognition.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'School Psychology', 'code' => 'PSY524', 'description' => 'Psychological services in educational settings, including assessment, intervention, and consultation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Prejudice and Discrimination', 'code' => 'PSY525', 'description' => 'Causes and consequences of prejudice, stereotyping, and discrimination.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Behavior Modification Techniques', 'code' => 'PSY526', 'description' => 'Principles and application of behavior change techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Sleep and Dreams', 'code' => 'PSY527', 'description' => 'Scientific study of sleep stages, dreaming, and sleep disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Political Psychology', 'code' => 'PSY528', 'description' => 'Psychological factors influencing political behavior, leadership, and decision-making.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Psychology of Human Sexuality', 'code' => 'PSY529', 'description' => 'Biological, psychological, and social aspects of human sexuality.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Applied Behavior Analysis (ABA)', 'code' => 'PSY530', 'description' => 'Principles of behavior analysis and their application in various settings (e.g., autism, education).', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MPhil Subjects
            [ 'name' => 'Advanced Research Methods and Design', 'code' => 'PSY601', 'description' => 'Advanced quantitative, qualitative, and mixed-methods research designs.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Statistical Analysis in Psychology', 'code' => 'PSY602', 'description' => 'Multivariate statistics, structural equation modeling, and hierarchical linear modeling.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Psychopathology', 'code' => 'PSY603', 'description' => 'In-depth study of the classification, etiology, and diagnosis of psychological disorders.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Theories of Psychotherapy', 'code' => 'PSY604', 'description' => 'A critical review of major theoretical approaches to psychotherapy.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Cognitive and Affective Bases of Behavior', 'code' => 'PSY605', 'description' => 'Advanced study of cognitive and emotional processes underlying behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Social and Cultural Bases of Behavior', 'code' => 'PSY606', 'description' => 'Advanced topics in social psychology, including cultural influences on behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Biological Bases of Behavior', 'code' => 'PSY607', 'description' => 'Advanced topics in behavioral neuroscience and psychopharmacology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Psychological Assessment', 'code' => 'PSY608', 'description' => 'Advanced techniques in cognitive, personality, and neuropsychological assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Ethics and Professional Issues in Psychology', 'code' => 'PSY609', 'description' => 'Ethical codes, legal issues, and professional standards in psychology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Supervised Practicum in Psychotherapy', 'code' => 'PSY610', 'description' => 'Supervised clinical experience in providing psychotherapy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Supervised Practicum in Assessment', 'code' => 'PSY611', 'description' => 'Supervised clinical experience in psychological assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Developmental Psychology', 'code' => 'PSY612', 'description' => 'In-depth study of theories and research in lifespan development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Industrial/Organizational Psychology', 'code' => 'PSY613', 'description' => 'Topics in personnel psychology, organizational behavior, and leadership.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Health Psychology', 'code' => 'PSY614', 'description' => 'Psychosocial factors in chronic illness, health promotion, and disease prevention.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Educational Psychology', 'code' => 'PSY615', 'description' => 'Learning theories, instructional design, and assessment in educational settings.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Neuropsychological Assessment', 'code' => 'PSY616', 'description' => 'Techniques for assessing cognitive deficits resulting from brain injury or disease.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Cognitive Behavioral Therapy (CBT)', 'code' => 'PSY617', 'description' => 'Theory and practice of CBT for various psychological disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Psychodynamic Psychotherapy', 'code' => 'PSY618', 'description' => 'Theory and practice of psychodynamic and psychoanalytic therapies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Family and Couples Therapy', 'code' => 'PSY619', 'description' => 'Systems theory and therapeutic approaches for families and couples.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Trauma and Crisis Intervention', 'code' => 'PSY620', 'description' => 'Assessment and treatment of trauma-related disorders and crisis management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Child and Adolescent Psychopathology', 'code' => 'PSY621', 'description' => 'Diagnosis and treatment of psychological disorders in children and adolescents.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Program Evaluation', 'code' => 'PSY622', 'description' => 'Methods for evaluating the effectiveness of psychological interventions and programs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Multicultural Counseling', 'code' => 'PSY623', 'description' => 'Cultural competence and therapeutic approaches for diverse populations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Clinical Psychology', 'code' => 'PSY624', 'description' => 'Discussion of current issues and research in clinical psychology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'PSY699', 'description' => 'Independent research thesis for the MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Advanced Theories of Psychology', 'code' => 'PSY701', 'description' => 'Doctoral seminar on major theoretical systems in psychology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Quantitative Methods and Psychometrics', 'code' => 'PSY702', 'description' => 'Advanced topics in statistical modeling and measurement theory.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Qualitative Research Methods', 'code' => 'PSY703', 'description' => 'In-depth study of qualitative methodologies, data analysis, and interpretation.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'History and Philosophy of Psychology', 'code' => 'PSY704', 'description' => 'Philosophical underpinnings of psychological science.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Teaching of Psychology', 'code' => 'PSY705', 'description' => 'Pedagogy, curriculum development, and supervision in psychology education.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Seminar in Clinical Supervision', 'code' => 'PSY706', 'description' => 'Theories and models of clinical supervision.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Seminar in Cognitive Psychology', 'code' => 'PSY707', 'description' => 'Current research and theories in cognitive psychology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Seminar in Social Psychology', 'code' => 'PSY708', 'description' => 'Current research and theories in social psychology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Seminar in Developmental Psychology', 'code' => 'PSY709', 'description' => 'Current research and theories in developmental psychology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Neuropsychology', 'code' => 'PSY710', 'description' => 'Advanced topics in brain-behavior relationships and neuropsychological disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Psychology of Emotion', 'code' => 'PSY711', 'description' => 'Theories and research on the nature, functions, and regulation of emotions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Cultural and Cross-Cultural Psychology', 'code' => 'PSY712', 'description' => 'Advanced study of the role of culture in shaping psychological processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Grant Writing and Research Funding', 'code' => 'PSY713', 'description' => 'Skills for developing research proposals and securing funding.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Internship', 'code' => 'PSY714', 'description' => 'Doctoral-level supervised clinical or research internship.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'PSY799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Psychology';    
         
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
