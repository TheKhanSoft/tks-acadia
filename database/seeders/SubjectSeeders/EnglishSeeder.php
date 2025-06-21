<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\DepartmentProgram;
use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EnglishSeeder extends Seeder
{
    public function run()
    {
        // Refined and expanded subject list as per HEC 2024-2025 BS English Curriculum
        $subjects = [
            // Core/Compulsory Courses
            [ 'name' => 'Introduction to Literature', 'code' => 'ENG101', 'description' => 'Survey of literary genres, forms, and critical approaches to literature.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'English Composition and Comprehension', 'code' => 'ENG102', 'description' => 'Academic writing, essay structure, and comprehension skills.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'History of English Literature I', 'code' => 'ENG103', 'description' => 'English literature from Old English to the Renaissance period.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'History of English Literature II', 'code' => 'ENG104', 'description' => 'English literature from the Restoration to the Modern period.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'Academic Reading and Writing', 'code' => 'ENG105', 'description' => 'Advanced reading and writing skills for academic contexts.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'Introduction to Linguistics', 'code' => 'ENG106', 'description' => 'Fundamentals of linguistics, language structure, and analysis.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'Functional English', 'code' => 'ENG107', 'description' => 'Practical English usage in everyday and professional contexts.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'BS', ],
            [ 'name' => 'American Literature', 'code' => 'ENG204', 'description' => 'Survey of American literature from colonial period to present.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Shakespearean Studies', 'code' => 'ENG301', 'description' => 'Comprehensive study of Shakespeare\'s major plays and sonnets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Literary Criticism and Theory', 'code' => 'ENG302', 'description' => 'Major literary theories and critical approaches to textual analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Postcolonial Literature', 'code' => 'ENG303', 'description' => 'Literature from former colonies including South Asian and African writers.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Poetry and Poetics', 'code' => 'ENG304', 'description' => 'Study of poetic forms, techniques, and major poets across different periods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Modern Drama', 'code' => 'ENG305', 'description' => 'Contemporary dramatic literature and theatrical movements.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Research Methodology', 'code' => 'ENG497', 'description' => 'Research methods in literary and linguistic studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Final Research Project', 'code' => 'ENG499', 'description' => 'Independent research thesis in literature or linguistics.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'South Asian Literature in English', 'code' => 'ENG422', 'description' => 'Contemporary South Asian authors writing in English, cultural themes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Victorian Literature', 'code' => 'ENG411', 'description' => 'Literature of the Victorian era, social issues, and cultural contexts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Modernist Literature', 'code' => 'ENG412', 'description' => 'Early 20th century experimental literature and modernist movements.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Contemporary Fiction', 'code' => 'ENG413', 'description' => 'Recent developments in fiction writing and contemporary narrative techniques.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Linguistics', 'code' => 'ENG306', 'description' => 'Scientific study of language structure, phonetics, syntax, and semantics.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Phonetics and Phonology', 'code' => 'ENG211', 'description' => 'Study of speech sounds, sound systems, and pronunciation patterns.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Syntax and Semantics', 'code' => 'ENG212', 'description' => 'Sentence structure analysis, meaning relationships, and grammatical theory.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Sociolinguistics', 'code' => 'ENG307', 'description' => 'Language variation, social dialects, and language in society.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Psycholinguistics', 'code' => 'ENG308', 'description' => 'Language acquisition, processing, and cognitive aspects of language use.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Discourse Analysis', 'code' => 'ENG309', 'description' => 'Analysis of spoken and written discourse, conversation analysis, and text structure.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Applied Linguistics', 'code' => 'ENG310', 'description' => 'Application of linguistic theories to real-world language issues.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English for Academic Purposes', 'code' => 'ENG311', 'description' => 'Academic English skills for reading, writing, and research in higher education.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English for Professional Communication', 'code' => 'ENG312', 'description' => 'Professional writing, business communication, and workplace English.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English Language Testing and Assessment', 'code' => 'ENG313', 'description' => 'Principles of language testing, assessment design, and evaluation methods.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English for Specific Purposes (ESP)', 'code' => 'ENG314', 'description' => 'Specialized English teaching for professional and academic contexts.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Language Policy and Planning', 'code' => 'ENG315', 'description' => 'Language policy issues, planning, and implementation in multilingual contexts.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Rhetoric and Public Speaking', 'code' => 'ENG423', 'description' => 'Persuasive writing, oral communication, and presentation skills.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Creative Writing', 'code' => 'ENG402', 'description' => 'Workshop in fiction, poetry, and creative non-fiction writing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'English Language Teaching', 'code' => 'ENG403', 'description' => 'Methodology and techniques for teaching English as a second language.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Women\'s Literature', 'code' => 'ENG404', 'description' => 'Literature by and about women, feminist literary criticism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Translation Studies', 'code' => 'ENG405', 'description' => 'Theory and practice of translation between languages and cultures.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Media and Communication', 'code' => 'ENG406', 'description' => 'Language in media, journalism, and digital communication.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'Gothic Literature', 'code' => 'ENG414', 'description' => 'Gothic tradition in literature, horror elements, and psychological themes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Children\'s Literature', 'code' => 'ENG415', 'description' => 'Literature written for children, developmental psychology, and educational value.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Science Fiction Literature', 'code' => 'ENG416', 'description' => 'Speculative fiction, technological themes, and futuristic narratives.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Travel Writing', 'code' => 'ENG417', 'description' => 'Literary travel narratives, cultural encounters, and geographical writing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Biography and Autobiography', 'code' => 'ENG418', 'description' => 'Life writing genres, memoir, and personal narrative forms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Literary Journalism', 'code' => 'ENG419', 'description' => 'Creative non-fiction, narrative journalism, and documentary writing.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'Digital Humanities', 'code' => 'ENG420', 'description' => 'Technology applications in literary study, digital archives, and text analysis.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'Film and Literature', 'code' => 'ENG421', 'description' => 'Literary adaptations, screenplay writing, and comparative media studies.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'Technical Writing', 'code' => 'ENG424', 'description' => 'Professional writing, documentation, and workplace communication.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'English Language and Culture', 'code' => 'ENG431', 'description' => 'Exploration of cultural contexts in English language use and communication.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'English Language and Identity', 'code' => 'ENG432', 'description' => 'Language, identity formation, and cultural representation in English-speaking communities.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'English Language and Globalization', 'code' => 'ENG433', 'description' => 'Impact of globalization on English language, culture, and communication practices.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'World Literature in English', 'code' => 'ENG501', 'description' => 'Study of global literary texts written in English.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Pakistani Literature in English', 'code' => 'ENG502', 'description' => 'Exploration of Pakistani writers and themes in English.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Comparative Literature', 'code' => 'ENG503', 'description' => 'Comparative study of literature across cultures and languages.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'English Morphology', 'code' => 'ENG504', 'description' => 'Study of word formation and structure in English.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Stylistics', 'code' => 'ENG505', 'description' => 'Analysis of style in literary and non-literary texts.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Language and Gender', 'code' => 'ENG506', 'description' => 'Examination of gendered language use and representation.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'Language Acquisition', 'code' => 'ENG507', 'description' => 'Processes of first and second language acquisition.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English Pragmatics', 'code' => 'ENG508', 'description' => 'Study of language use in context and meaning in interaction.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Corpus Linguistics', 'code' => 'ENG509', 'description' => 'Use of corpora for language research and analysis.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Editing and Publishing', 'code' => 'ENG510', 'description' => 'Principles and practices of editing and publishing texts.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'English for Journalism', 'code' => 'ENG511', 'description' => 'Language skills and conventions for print and digital journalism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'Drama in Performance', 'code' => 'ENG512', 'description' => 'Study and enactment of dramatic texts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Environmental Literature', 'code' => 'ENG513', 'description' => 'Literature addressing environmental and ecological themes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS', ],
            [ 'name' => 'English for Business', 'code' => 'ENG514', 'description' => 'Business communication, correspondence, and report writing.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Language and Power', 'code' => 'ENG515', 'description' => 'Relationship between language, power, and society.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],
            [ 'name' => 'English for Law', 'code' => 'ENG516', 'description' => 'Legal English, contracts, and legal discourse.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English for Science and Technology', 'code' => 'ENG517', 'description' => 'Scientific writing and communication in English.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Language and Society', 'code' => 'ENG518', 'description' => 'Sociocultural aspects of language use.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'English for Tourism and Hospitality', 'code' => 'ENG519', 'description' => 'English language skills for tourism and hospitality industry.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS', ],
            [ 'name' => 'Multimodal Communication', 'code' => 'ENG520', 'description' => 'Communication across multiple modes: text, image, sound.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'BS', ],

            // MPhil Subjects
            [ 'name' => 'Advanced Literary Theory', 'code' => 'ENG601', 'description' => 'In-depth study of major literary theories from formalism to post-structuralism.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Research Methods in Linguistics', 'code' => 'ENG602', 'description' => 'Advanced research methodologies for linguistic analysis and investigation.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Postcolonial Studies: Theory and Practice', 'code' => 'ENG603', 'description' => 'Critical examination of postcolonial literature and theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Modernist Literature and Culture', 'code' => 'ENG604', 'description' => 'Study of modernist literature in its cultural and historical context.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Contemporary Critical Theory', 'code' => 'ENG605', 'description' => 'Exploration of current trends in critical theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Discourse Analysis: Text and Context', 'code' => 'ENG606', 'description' => 'Advanced analysis of written and spoken discourse.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Sociolinguistics: Language and Society', 'code' => 'ENG607', 'description' => 'Study of the relationship between language and society.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Psycholinguistics: Language and Mind', 'code' => 'ENG608', 'description' => 'Cognitive processes of language acquisition and use.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Shakespeare and His Contemporaries', 'code' => 'ENG609', 'description' => 'Advanced study of Shakespearean drama and its contemporaries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Romantic and Victorian Poetry', 'code' => 'ENG610', 'description' => 'In-depth analysis of poetry from the Romantic and Victorian eras.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'The Modern Novel', 'code' => 'ENG611', 'description' => 'Study of the development of the novel in the 20th century.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'American Literature: Major Authors', 'code' => 'ENG612', 'description' => 'Focused study on key figures in American literature.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'South Asian Diasporic Literature', 'code' => 'ENG613', 'description' => 'Literature of the South Asian diaspora.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Gender and Literature', 'code' => 'ENG614', 'description' => 'Exploration of gender representation in literature.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Stylistics: Analyzing Literary Language', 'code' => 'ENG615', 'description' => 'Advanced stylistics for literary analysis.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'World Englishes', 'code' => 'ENG616', 'description' => 'Study of different varieties of English worldwide.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Language, Ideology, and Power', 'code' => 'ENG617', 'description' => 'The role of language in constructing ideology and power relations.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'MS', ],
            [ 'name' => 'Translation Studies: Theories and Applications', 'code' => 'ENG618', 'description' => 'Advanced theories and practical applications of translation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Corpus Linguistics: Methods and Analysis', 'code' => 'ENG619', 'description' => 'Using corpora for advanced linguistic research.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'MS', ],
            [ 'name' => 'Critical Pedagogy in Language Teaching', 'code' => 'ENG620', 'description' => 'Critical approaches to language education.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Post-structuralism and Deconstruction', 'code' => 'ENG621', 'description' => 'Key concepts of post-structuralist and deconstructive thought.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Literature and Psychoanalysis', 'code' => 'ENG622', 'description' => 'Psychoanalytic readings of literary texts.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'MS', ],
            [ 'name' => 'Digital Humanities and Literary Studies', 'code' => 'ENG623', 'description' => 'Intersection of digital technology and literary research.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Academic Writing and Publishing', 'code' => 'ENG624', 'description' => 'Skills for publishing academic research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'ENG699', 'description' => 'Independent research thesis for MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Advanced Research Methodology', 'code' => 'ENG701', 'description' => 'Doctoral-level research design and methodology in humanities.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Postcolonial Theory', 'code' => 'ENG702', 'description' => 'Advanced seminar on contemporary issues in postcolonial studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Modern and Contemporary Literature', 'code' => 'ENG703', 'description' => 'In-depth study of key texts and movements in modern literature.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Critical Discourse Studies', 'code' => 'ENG704', 'description' => 'Advanced theories and methods in critical discourse analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Sociolinguistics', 'code' => 'ENG705', 'description' => 'Exploration of cutting-edge research in sociolinguistics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Language and Globalization', 'code' => 'ENG706', 'description' => 'Impact of globalization on language policies and practices.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'PhD', ],
            [ 'name' => 'Theories of Literary Criticism', 'code' => 'ENG707', 'description' => 'Comprehensive survey of major critical theories.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Cultural Studies and Literature', 'code' => 'ENG708', 'description' => 'Interdisciplinary approaches to literature through cultural studies.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'PhD', ],
            [ 'name' => 'Narrative Theory and Analysis', 'code' => 'ENG709', 'description' => 'Advanced study of narrative structures and theories.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Posthumanism and Literature', 'code' => 'ENG710', 'description' => 'Literary and theoretical explorations of posthumanism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Ecocriticism and Environmental Literature', 'code' => 'ENG711', 'description' => 'Study of literature and the physical environment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Seminar in Shakespearean Studies', 'code' => 'ENG712', 'description' => 'Focused research seminar on Shakespeare.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Language Policy and Planning', 'code' => 'ENG713', 'description' => 'Doctoral seminar on language policy formulation and implementation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Interdisciplinary Approaches to Literature', 'code' => 'ENG714', 'description' => 'Integrating methodologies from other disciplines in literary research.', 'credit_hours' => '3', 'category' => 'Interdisciplinary', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'ENG799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of English';    
         
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
