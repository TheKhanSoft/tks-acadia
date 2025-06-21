<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class PakistanStudiesSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Pakistan Studies
            [ 'name' => 'Ideology of Pakistan', 'code' => 'PST101', 'description' => 'Historical and philosophical foundations of the ideology of Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Geography of Pakistan', 'code' => 'PST102', 'description' => 'Physical and human geography of Pakistan, its regions, resources, and environmental challenges.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Constitutional Development in Pakistan (1947-Present)', 'code' => 'PST103', 'description' => 'Evolution of constitutional frameworks in Pakistan, key documents, and political implications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Political History of Pakistan I (1947-1971)', 'code' => 'PST201', 'description' => 'Early political developments, challenges, and events leading to the separation of East Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Political History of Pakistan II (1971-Present)', 'code' => 'PST202', 'description' => 'Political landscape, democratic transitions, military interventions, and contemporary issues.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Foreign Policy of Pakistan', 'code' => 'PST203', 'description' => 'Determinants, objectives, and evolution of Pakistan\'s foreign relations with major powers and neighbors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Economy of Pakistan', 'code' => 'PST204', 'description' => 'Structure, challenges, and development strategies of Pakistan\'s economy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Society and Culture of Pakistan', 'code' => 'PST301', 'description' => 'Social structures, cultural diversity, languages, traditions, and contemporary social issues in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methodology for Pakistan Studies', 'code' => 'PST302', 'description' => 'Introduction to research methods, data collection, and analysis in the context of Pakistan Studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Muslim Struggle for Independence (1857-1947)', 'code' => 'PST303', 'description' => 'Key events, personalities, and movements leading to the creation of Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Local Government System in Pakistan', 'code' => 'PST304', 'description' => 'Evolution, structure, and functioning of local governance in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Human Rights in Pakistan', 'code' => 'PST305', 'description' => 'Status of human rights, challenges, and legal frameworks in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Media and Politics in Pakistan', 'code' => 'PST401', 'description' => 'Role of media in shaping political discourse and public opinion in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan\'s Relations with Muslim World', 'code' => 'PST402', 'description' => 'Analysis of Pakistan\'s diplomatic, economic, and cultural ties with Muslim countries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan Studies Thesis I', 'code' => 'PST498A', 'description' => 'Development of research proposal and initial work on an independent project in Pakistan Studies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan Studies Thesis II', 'code' => 'PST498B', 'description' => 'Completion, analysis, and presentation of the independent research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Political Thought of Muslim Leaders in South Asia', 'code' => 'PST211', 'description' => 'Ideas and contributions of key Muslim political thinkers in the subcontinent.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Ethnic Issues and National Integration in Pakistan', 'code' => 'PST311', 'description' => 'Challenges of ethnicity and strategies for national cohesion in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Development Planning and Policy in Pakistan', 'code' => 'PST312', 'description' => 'Analysis of development plans, policies, and their impact on Pakistan\'s socio-economic landscape.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan Movement: Key Personalities', 'code' => 'PST212', 'description' => 'Biographical studies of prominent leaders of the Pakistan Movement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Judicial System of Pakistan', 'code' => 'PST313', 'description' => 'Structure, functioning, and role of the judiciary in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Civil-Military Relations in Pakistan', 'code' => 'PST411', 'description' => 'Dynamics of civil-military relations and their impact on governance in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Regional Languages and Literature of Pakistan', 'code' => 'PST314', 'description' => 'Overview of major regional languages and their literary traditions in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Issues in Pakistan', 'code' => 'PST213', 'description' => 'Key environmental challenges, policies, and conservation efforts in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Social Stratification in Pakistan', 'code' => 'PST315', 'description' => 'Class, caste, and gender-based social inequalities in Pakistani society.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective Subjects for Pakistan Studies
            [ 'name' => 'Pakistan and International Law', 'code' => 'PST501', 'description' => 'Pakistan\'s engagement with international legal frameworks and institutions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Political Parties and Electoral Politics in Pakistan', 'code' => 'PST502', 'description' => 'Role and dynamics of political parties and election processes in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sufism in South Asia with Special Reference to Pakistan', 'code' => 'PST503', 'description' => 'Historical development and contemporary relevance of Sufi traditions in the region.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Gender Studies in Pakistan', 'code' => 'PST504', 'description' => 'Issues of gender, women\'s rights, and feminist movements in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Urbanization and Development in Pakistan', 'code' => 'PST505', 'description' => 'Challenges and opportunities of urbanization and its impact on development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Rural Development in Pakistan', 'code' => 'PST506', 'description' => 'Strategies, policies, and challenges of rural development in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan\'s Nuclear Policy', 'code' => 'PST507', 'description' => 'Evolution, rationale, and implications of Pakistan\'s nuclear program and policy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Water Issues in Pakistan: National and Regional Perspectives', 'code' => 'PST508', 'description' => 'Analysis of water scarcity, management, and transboundary water disputes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Art and Architecture of Pakistan', 'code' => 'PST509', 'description' => 'Historical and contemporary trends in Pakistani art and architecture.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Education System in Pakistan: Challenges and Reforms', 'code' => 'PST510', 'description' => 'Analysis of the education sector, its problems, and reform initiatives.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan\'s Relations with Major Powers (USA, China, Russia)', 'code' => 'PST511', 'description' => 'In-depth study of Pakistan\'s bilateral relations with key global actors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan\'s Relations with South Asian Countries', 'code' => 'PST512', 'description' => 'Dynamics of Pakistan\'s foreign policy towards its South Asian neighbors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Terrorism and Counter-Terrorism in Pakistan', 'code' => 'PST513', 'description' => 'Causes, impact, and strategies related to terrorism and counter-terrorism efforts in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Public Administration and Governance in Pakistan', 'code' => 'PST514', 'description' => 'Structure and functioning of public administration and governance mechanisms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Demography and Population Dynamics of Pakistan', 'code' => 'PST515', 'description' => 'Study of population trends, demographic challenges, and policies in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'History of Science and Technology in Pakistan', 'code' => 'PST516', 'description' => 'Development of scientific and technological institutions and achievements in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Intellectual History of Muslims in South Asia', 'code' => 'PST517', 'description' => 'Major intellectual trends and contributions of Muslim thinkers in the subcontinent.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Comparative Politics of South Asia', 'code' => 'PST518', 'description' => 'A comparative analysis of political systems and processes in South Asian countries.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Foreign Aid and Development in Pakistan', 'code' => 'PST519', 'description' => 'Role and impact of foreign aid on Pakistan\'s economic development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Political Economy of Corruption in Pakistan', 'code' => 'PST520', 'description' => 'Analysis of the causes, consequences, and control of corruption in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'National Security Policy of Pakistan', 'code' => 'PST521', 'description' => 'Formulation, challenges, and dimensions of Pakistan\'s national security policy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan and Regional Organizations (SAARC, ECO, SCO)', 'code' => 'PST522', 'description' => 'Pakistan\'s role and engagement in regional cooperation frameworks.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'History of Press in Pakistan', 'code' => 'PST523', 'description' => 'Evolution of print media and its role in Pakistani society and politics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cultural Heritage of Pakistan', 'code' => 'PST524', 'description' => 'Preservation and promotion of tangible and intangible cultural heritage of Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Land Reforms in Pakistan', 'code' => 'PST525', 'description' => 'History, impact, and challenges of land reform policies in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Political Movements in Pakistan', 'code' => 'PST526', 'description' => 'Study of significant political movements and their impact on Pakistan\'s history.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'State and Society in Pakistan: A Critical Analysis', 'code' => 'PST527', 'description' => 'Theoretical perspectives on the relationship between state and society in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Geopolitics of Pakistan', 'code' => 'PST528', 'description' => 'Analysis of Pakistan\'s strategic location and its geopolitical significance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Contemporary Issues in Pakistan\'s Foreign Policy', 'code' => 'PST529', 'description' => 'Discussion of current challenges and debates in Pakistan\'s foreign relations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'The Role of Ulema in Pakistan Politics', 'code' => 'PST530', 'description' => 'Historical and contemporary role of religious scholars in the political landscape of Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Federalism in Pakistan: Issues and Prospects', 'code' => 'PST531', 'description' => 'Study of federal structure, provincial autonomy, and inter-governmental relations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Social Work and Community Development in Pakistan', 'code' => 'PST532', 'description' => 'Principles and practices of social work and community development initiatives in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Pakistan\'s Maritime Affairs and Blue Economy', 'code' => 'PST533', 'description' => 'Exploration of Pakistan\'s maritime resources, challenges, and potential of the blue economy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Disaster Management in Pakistan', 'code' => 'PST534', 'description' => 'Policies, frameworks, and challenges related to disaster preparedness and management in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sports and Society in Pakistan', 'code' => 'PST535', 'description' => 'The role of sports in Pakistani culture, national identity, and social development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MPhil Subjects
            [ 'name' => 'Theories of State and Nation Building', 'code' => 'PST601', 'description' => 'Advanced theories of state formation and nation-building with a focus on Pakistan.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Research Methodology', 'code' => 'PST602', 'description' => 'Qualitative and quantitative research methods for advanced studies in Pakistan.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Historiography of Pakistan', 'code' => 'PST603', 'description' => 'A critical examination of the historical writings on Pakistan.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'State, Society, and Governance in Pakistan', 'code' => 'PST604', 'description' => 'In-depth analysis of the dynamics between state, society, and governance structures.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Political Economy of Pakistan', 'code' => 'PST605', 'description' => 'Advanced study of the interplay between politics and economy in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Pakistan\'s Foreign Policy Analysis', 'code' => 'PST606', 'description' => 'A critical analysis of Pakistan\'s foreign policy decision-making process.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Constitutional and Political Development in Pakistan', 'code' => 'PST607', 'description' => 'An advanced course on the constitutional and political evolution of Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'National Security of Pakistan: Issues and Challenges', 'code' => 'PST608', 'description' => 'Comprehensive study of traditional and non-traditional security threats to Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Pakistan Movement: A Critical Perspective', 'code' => 'PST609', 'description' => 'A critical re-evaluation of the historical events and narratives of the Pakistan Movement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Islam and Politics in Pakistan', 'code' => 'PST610', 'description' => 'The role of Islam in the political and constitutional history of Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Regionalism and Federalism in Pakistan', 'code' => 'PST611', 'description' => 'A study of regional dynamics and the functioning of federalism in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Social Change and Development in Pakistan', 'code' => 'PST612', 'description' => 'Theories and processes of social change and development in the context of Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Geopolitics of South Asia and Pakistan', 'code' => 'PST613', 'description' => 'An analysis of the geopolitical trends in South Asia and their implications for Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Human Security in Pakistan', 'code' => 'PST614', 'description' => 'A focus on human security challenges, including food, health, and environmental security.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Media, Culture, and Society in Pakistan', 'code' => 'PST615', 'description' => 'The role of media in shaping cultural norms and societal values in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Pakistan\'s Relations with Afghanistan and Iran', 'code' => 'PST616', 'description' => 'An in-depth study of Pakistan\'s bilateral relations with its western neighbors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'The Modern Middle East and Pakistan', 'code' => 'PST617', 'description' => 'Pakistan\'s engagement with the political and strategic dynamics of the Middle East.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Local Government and Devolution of Power in Pakistan', 'code' => 'PST618', 'description' => 'A critical analysis of local government systems and decentralization policies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Sectarianism in Pakistan: Causes and Consequences', 'code' => 'PST619', 'description' => 'An investigation into the roots and impact of sectarian conflict in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'The Economy of Modern Pakistan: Issues and Prospects', 'code' => 'PST620', 'description' => 'Advanced analysis of contemporary economic issues and future prospects for Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Pakistan\'s Diaspora Communities', 'code' => 'PST621', 'description' => 'A study of Pakistani diaspora communities and their role in the homeland.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Water Politics in Pakistan', 'code' => 'PST622', 'description' => 'An examination of inter-provincial and transboundary water disputes affecting Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Education Policy and Practice in Pakistan', 'code' => 'PST623', 'description' => 'A critical review of education policies and their implementation in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar on Contemporary Issues in Pakistan', 'code' => 'PST624', 'description' => 'A seminar course discussing current political, social, and economic issues in Pakistan.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'PST699', 'description' => 'Independent research thesis for the MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Advanced Theories in Pakistan Studies', 'code' => 'PST701', 'description' => 'Doctoral-level seminar on theoretical frameworks for studying Pakistan.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Quantitative and Qualitative Research', 'code' => 'PST702', 'description' => 'Advanced research design, data analysis, and interpretation for doctoral research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar on the State in Post-Colonial Societies', 'code' => 'PST703', 'description' => 'A comparative study of the state in post-colonial contexts, with a focus on Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Pakistan\'s Strategic Culture and Foreign Policy', 'code' => 'PST704', 'description' => 'An in-depth analysis of the strategic culture shaping Pakistan\'s foreign policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Democracy and Authoritarianism in Pakistan', 'code' => 'PST705', 'description' => 'A doctoral seminar on the cycles of democracy and authoritarianism in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Political Economy of Development and Underdevelopment in Pakistan', 'code' => 'PST706', 'description' => 'Advanced analysis of the political and economic factors of development in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Nationalism, Ethnicity, and Identity Politics in Pakistan', 'code' => 'PST707', 'description' => 'A critical study of nationalism, ethnicity, and identity-based conflicts in Pakistan.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Religion, State, and Society in Pakistan', 'code' => 'PST708', 'description' => 'An advanced seminar on the complex interactions between religion, state, and society.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'South Asia in International Relations', 'code' => 'PST709', 'description' => 'A study of South Asia\'s role in the international system and its major foreign policy challenges.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Conflict and Peace Studies in South Asia', 'code' => 'PST710', 'description' => 'An examination of major conflicts and peace processes in the South Asian region.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Civil Society and Social Movements in Pakistan', 'code' => 'PST711', 'description' => 'A study of the role of civil society organizations and social movements in Pakistan.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Readings in Pakistan Studies: Primary Sources', 'code' => 'PST712', 'description' => 'A guided reading course focusing on primary source materials in Pakistan Studies.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar on Research in Pakistan Studies', 'code' => 'PST713', 'description' => 'A forum for doctoral candidates to present and discuss their ongoing research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Directed Research for Comprehensive Examination', 'code' => 'PST714', 'description' => 'Supervised research and reading in preparation for the PhD comprehensive examination.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'PST799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Pakistan Studies';    
         
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
