<?php

namespace Database\Seeders\SubjectSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class SociologySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // BS Sociology Subjects
            [ 'name' => 'Introduction to Sociology I', 'code' => 'SOC101', 'description' => 'Basic concepts, theories, and perspectives in sociology; social structure and institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Sociology II', 'code' => 'SOC102', 'description' => 'Continuation of SOC101, covering social processes, social change, and contemporary social issues.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Theory I (Classical)', 'code' => 'SOC201', 'description' => 'Major classical sociological theories (Comte, Spencer, Marx, Durkheim, Weber).', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Theory II (Contemporary)', 'code' => 'SOC202', 'description' => 'Major contemporary sociological theories (functionalism, conflict theory, symbolic interactionism, feminist theory).', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methods in Sociology I (Quantitative)', 'code' => 'SOC203', 'description' => 'Principles of quantitative research design, data collection, and analysis in sociology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methods in Sociology II (Qualitative)', 'code' => 'SOC204', 'description' => 'Principles of qualitative research design, data collection (interviews, ethnography), and analysis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Statistics I', 'code' => 'SOC211', 'description' => 'Descriptive statistics and basic inferential statistics for sociological data.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Social Statistics II', 'code' => 'SOC311', 'description' => 'Advanced inferential statistics, regression analysis, and multivariate techniques.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Development', 'code' => 'SOC301', 'description' => 'Theories and issues of social and economic development, globalization, and inequality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Culture', 'code' => 'SOC302', 'description' => 'Cultural norms, values, symbols, subcultures, and cultural change.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Stratification and Inequality', 'code' => 'SOC303', 'description' => 'Systems of social inequality based on class, race, gender, and other factors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Family', 'code' => 'SOC304', 'description' => 'Structure, functions, and changes in family systems across cultures and time.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Criminology', 'code' => 'SOC305', 'description' => 'Nature, causes, and control of crime; theories of criminal behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Political Sociology', 'code' => 'SOC401', 'description' => 'Relationship between social structures and political processes, power, and the state.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Gender', 'code' => 'SOC402', 'description' => 'Social construction of gender, gender roles, inequality, and feminist perspectives.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Urban Sociology', 'code' => 'SOC403', 'description' => 'Social life in cities, urbanization processes, urban problems, and planning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology Research Project I (Proposal)', 'code' => 'SOC498A', 'description' => 'Development of a research proposal for an independent sociological study.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology Research Project II (Thesis)', 'code' => 'SOC498B', 'description' => 'Execution, analysis, and write-up of the independent sociological research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Problems in Pakistan', 'code' => 'SOC212', 'description' => 'Analysis of major social problems in Pakistan, such as poverty, unemployment, and illiteracy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Rural Sociology', 'code' => 'SOC312', 'description' => 'Social structures, processes, and changes in rural communities.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Population Dynamics (Social Demography)', 'code' => 'SOC313', 'description' => 'Study of population size, composition, distribution, and change.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Education', 'code' => 'SOC314', 'description' => 'Social factors influencing education systems, processes, and outcomes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Health and Medicine', 'code' => 'SOC411', 'description' => 'Social aspects of health, illness, healthcare systems, and medical practices.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Psychology (Sociological Perspective)', 'code' => 'SOC213', 'description' => 'Sociological approaches to understanding individual behavior in social contexts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Religion', 'code' => 'SOC315', 'description' => 'Role of religion in society, religious institutions, beliefs, and practices.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective Subjects for Sociology
            [ 'name' => 'Sociology of Globalization', 'code' => 'SOC501', 'description' => 'Social, economic, political, and cultural dimensions of globalization.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Sociology', 'code' => 'SOC502', 'description' => 'Interaction between society and the natural environment, environmental problems, and movements.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Deviance and Social Control', 'code' => 'SOC503', 'description' => 'Theories of deviance, social reactions to deviance, and mechanisms of social control.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Work and Occupations', 'code' => 'SOC504', 'description' => 'Social organization of work, labor markets, occupational cultures, and workplace dynamics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Aging', 'code' => 'SOC505', 'description' => 'Social aspects of aging, challenges faced by older adults, and policies related to aging.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Race and Ethnicity', 'code' => 'SOC506', 'description' => 'Social construction of race and ethnicity, prejudice, discrimination, and intergroup relations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Law', 'code' => 'SOC507', 'description' => 'Relationship between law and society, legal institutions, and social change.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Knowledge', 'code' => 'SOC508', 'description' => 'Social construction of knowledge, ideologies, and the role of intellectuals.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Mass Communication and Media', 'code' => 'SOC509', 'description' => 'Impact of mass media on society, media institutions, and cultural production.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Human Rights', 'code' => 'SOC510', 'description' => 'Sociological perspectives on human rights, violations, and advocacy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Social Movements', 'code' => 'SOC511', 'description' => 'Theories of social movements, collective action, and social change.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Organizations', 'code' => 'SOC512', 'description' => 'Structure, processes, and dynamics of formal organizations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Conflict and Peace', 'code' => 'SOC513', 'description' => 'Causes of social conflict, conflict resolution, and peacebuilding processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Food and Agriculture', 'code' => 'SOC514', 'description' => 'Social aspects of food production, consumption, food systems, and agricultural policies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Science and Technology', 'code' => 'SOC515', 'description' => 'Social shaping of science and technology, and their impact on society.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Art and Aesthetics', 'code' => 'SOC516', 'description' => 'Social context of art production, consumption, and aesthetic judgments.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Sport', 'code' => 'SOC517', 'description' => 'Role of sport in society, social issues in sport, and sport as a social institution.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Emotions', 'code' => 'SOC518', 'description' => 'Social construction and management of emotions in everyday life.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Migration and Diaspora', 'code' => 'SOC519', 'description' => 'Causes and consequences of international migration, and experiences of diaspora communities.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Applied Sociology and Social Policy', 'code' => 'SOC520', 'description' => 'Application of sociological knowledge to address social problems and inform social policy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Childhood and Youth', 'code' => 'SOC521', 'description' => 'Social construction of childhood and youth, and experiences of young people in society.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Tourism', 'code' => 'SOC522', 'description' => 'Social impacts of tourism, tourist behavior, and the tourism industry.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Disaster', 'code' => 'SOC523', 'description' => 'Social responses to natural and human-made disasters, and disaster management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of the Body', 'code' => 'SOC524', 'description' => 'Social and cultural meanings of the human body, embodiment, and body modification.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Comparative Sociology', 'code' => 'SOC525', 'description' => 'Cross-cultural and historical comparison of social structures and processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Mental Health', 'code' => 'SOC526', 'description' => 'Social factors influencing mental health, illness, and treatment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Consumption', 'code' => 'SOC527', 'description' => 'Social patterns of consumption, consumer culture, and materialism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Cyberspace and Digital Society', 'code' => 'SOC528', 'description' => 'Social interactions, communities, and culture in online environments.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sociology of Terrorism', 'code' => 'SOC529', 'description' => 'Social causes, dynamics, and consequences of terrorism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Internship in Sociology', 'code' => 'SOC530', 'description' => 'Supervised practical experience in a sociological or community-based setting.', 'credit_hours' => '3-6', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Sociology Subjects
            [ 'name' => 'Advanced Sociological Theory', 'code' => 'SOC601', 'description' => 'In-depth analysis of classical, contemporary, and postmodern sociological theories.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Quantitative Research Methods', 'code' => 'SOC602', 'description' => 'Advanced statistical techniques, survey design, and data analysis using software like SPSS/Stata.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Qualitative Research Methods', 'code' => 'SOC603', 'description' => 'Advanced techniques in ethnography, grounded theory, case studies, and discourse analysis.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Development and Globalization', 'code' => 'SOC604', 'description' => 'Critical examination of development theories, global inequalities, and transnational processes.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Criminological Theory', 'code' => 'SOC611', 'description' => 'Comprehensive study of major theories of crime and deviance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Gender, Power, and Social Change', 'code' => 'SOC612', 'description' => 'Advanced feminist theories, intersectionality, and analysis of gendered institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Political Sociology and the State', 'code' => 'SOC613', 'description' => 'Advanced analysis of power, social movements, civil society, and state-society relations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Urban and Community Studies', 'code' => 'SOC614', 'description' => 'Theories of urbanism, community development, and social problems in urban settings.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Health, Illness, and Medicine', 'code' => 'SOC615', 'description' => 'Critical perspectives on health disparities, medicalization, and healthcare systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Social Stratification: Class, Race, and Gender', 'code' => 'SOC616', 'description' => 'In-depth analysis of systems of social inequality and their intersections.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Environmental Sociology and Policy', 'code' => 'SOC621', 'description' => 'Theories of human-environment interaction, environmental justice, and policy analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Education and Policy', 'code' => 'SOC622', 'description' => 'Analysis of educational inequality, policy reforms, and the role of education in society.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of the Family and Kinship', 'code' => 'SOC623', 'description' => 'Comparative and theoretical analysis of family structures, policies, and social change.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Religion and Secularism', 'code' => 'SOC624', 'description' => 'Advanced topics in the study of religion, secularization, and religious movements.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Law and Society', 'code' => 'SOC625', 'description' => 'Socio-legal theories, the role of law in social control, and legal consciousness.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Demography and Population Studies', 'code' => 'SOC626', 'description' => 'Advanced demographic methods and analysis of population trends and policies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Culture and Media', 'code' => 'SOC627', 'description' => 'Theories of cultural production, media effects, and digital culture.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Work and Organizations', 'code' => 'SOC628', 'description' => 'Analysis of labor markets, organizational theory, and the future of work.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Social Movements and Collective Behavior', 'code' => 'SOC629', 'description' => 'Advanced theories and research on social movements, protest, and political change.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Rural Development and Agrarian Change', 'code' => 'SOC630', 'description' => 'Analysis of rural social structures, agrarian transitions, and rural development policies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Project Planning and Management', 'code' => 'SOC631', 'description' => 'Skills for designing, implementing, and evaluating social projects and programs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Migration and Transnationalism', 'code' => 'SOC632', 'description' => 'Theories of migration, diaspora studies, and transnational social fields.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of Human Rights', 'code' => 'SOC633', 'description' => 'Sociological analysis of human rights discourse, institutions, and social justice.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Sociology of the Global South', 'code' => 'SOC634', 'description' => 'Postcolonial theories and sociological analysis of societies in the Global South.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'MPhil Thesis', 'code' => 'SOC699', 'description' => 'Independent research thesis for the MPhil degree in Sociology.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS'],

            // PhD Sociology Subjects
            [ 'name' => 'Doctoral Seminar in Sociological Theory', 'code' => 'SOC801', 'description' => 'Advanced critical engagement with foundational and contemporary sociological theories.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Epistemology and Philosophy of Social Science', 'code' => 'SOC802', 'description' => 'Philosophical foundations of social inquiry, including positivism, interpretivism, and critical realism.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Professional Development and Ethics in Sociology', 'code' => 'SOC803', 'description' => 'Seminar on academic publishing, grant writing, teaching, and ethical conduct in research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Advanced Research Design', 'code' => 'SOC811', 'description' => 'Intensive study of mixed-methods, comparative-historical, and longitudinal research designs.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Social Inequality', 'code' => 'SOC812', 'description' => 'Current research and debates on the intersections of class, race, gender, and other forms of inequality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Globalization and Transnational Sociology', 'code' => 'SOC813', 'description' => 'Cutting-edge research on global processes, transnational networks, and cosmopolitanism.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Cultural Sociology', 'code' => 'SOC814', 'description' => 'Advanced topics in the sociology of culture, including meaning-making, cultural fields, and performance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Political and Economic Sociology', 'code' => 'SOC815', 'description' => 'Current research at the intersection of states, markets, and societies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching Sociology Practicum', 'code' => 'SOC821', 'description' => 'Supervised experience in teaching undergraduate sociology courses.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Criminology and Social Justice', 'code' => 'SOC822', 'description' => 'Specialized seminar on current issues in criminology, criminal justice, and restorative justice.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Environmental Sociology', 'code' => 'SOC823', 'description' => 'In-depth study of a specialized area, such as climate change, food systems, or environmental movements.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Medical Sociology', 'code' => 'SOC824', 'description' => 'Specialized seminar on topics like global health, mental illness, or health technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Historical and Comparative Sociology', 'code' => 'SOC825', 'description' => 'Methods and theories for conducting historical and cross-national sociological research.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Readings in Sociology', 'code' => 'SOC890', 'description' => 'In-depth study of a specialized research area under faculty supervision, leading to a comprehensive exam field.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'SOC899', 'description' => 'Original research culminating in a PhD dissertation in Sociology.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Sociology';    
         
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
