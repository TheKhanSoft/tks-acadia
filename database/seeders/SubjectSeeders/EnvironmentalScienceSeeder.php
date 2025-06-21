<?php

namespace Database\Seeders\SubjectSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class EnvironmentalScienceSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // BS Environmental Science Subjects
            [ 'name' => 'Introduction to Environmental Science', 'code' => 'ENV101', 'description' => 'Fundamental concepts of environmental systems, human-environment interactions, and major environmental issues.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Chemistry I', 'code' => 'ENV102', 'description' => 'Basic chemical principles applied to environmental systems, including water, air, and soil chemistry.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Biology (Ecology)', 'code' => 'ENV103', 'description' => 'Principles of ecology, ecosystems, biodiversity, population dynamics, and conservation biology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Earth System Science (Geology & Climatology)', 'code' => 'ENV104', 'description' => 'Introduction to Earth\'s physical systems, geological processes, weather, and climate.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Physics', 'code' => 'ENV201', 'description' => 'Physical principles relevant to environmental phenomena, including energy, radiation, and fluid dynamics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Statistics and Data Analysis', 'code' => 'ENV202', 'description' => 'Statistical methods for analyzing environmental data, experimental design, and data interpretation.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Pollution (Air, Water, Soil)', 'code' => 'ENV203', 'description' => 'Sources, types, effects, and control of pollutants in air, water, and soil.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Microbiology', 'code' => 'ENV204', 'description' => 'Role of microorganisms in environmental processes, bioremediation, and public health.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Impact Assessment (EIA)', 'code' => 'ENV301', 'description' => 'Methodologies for assessing the environmental impacts of development projects.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Law and Policy', 'code' => 'ENV302', 'description' => 'National and international environmental laws, regulations, and policy-making processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Waste Management and Recycling', 'code' => 'ENV303', 'description' => 'Principles and techniques for solid and hazardous waste management, reduction, and recycling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Geographic Information Systems (GIS) for Environment', 'code' => 'ENV304', 'description' => 'Application of GIS and remote sensing in environmental analysis and management.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Climate Change: Science, Impacts, and Adaptation', 'code' => 'ENV305', 'description' => 'Scientific basis of climate change, its impacts, and strategies for mitigation and adaptation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Toxicology', 'code' => 'ENV401', 'description' => 'Effects of toxic substances on organisms and ecosystems, risk assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Natural Resource Management', 'code' => 'ENV402', 'description' => 'Sustainable management of water, forests, fisheries, and other natural resources.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Economics and Management', 'code' => 'ENV403', 'description' => 'Economic principles applied to environmental problems, valuation, and policy instruments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Research Methods and Thesis I', 'code' => 'ENV498A', 'description' => 'Research design, literature review, and proposal development for environmental research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Research Methods and Thesis II', 'code' => 'ENV498B', 'description' => 'Data collection, analysis, and writing of the BS environmental science thesis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Chemistry II (Organic Pollutants)', 'code' => 'ENV211', 'description' => 'Chemistry of organic pollutants, their fate, and transport in the environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Hydrology and Water Resources Management', 'code' => 'ENV311', 'description' => 'Hydrological cycle, groundwater and surface water hydrology, and water resource planning.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Soil Science and Conservation', 'code' => 'ENV212', 'description' => 'Soil formation, properties, classification, and conservation practices.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biodiversity and Conservation', 'code' => 'ENV312', 'description' => 'Principles of biodiversity, threats to biodiversity, and conservation strategies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Health and Safety', 'code' => 'ENV313', 'description' => 'Impacts of environmental factors on human health and occupational safety.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Renewable Energy Resources', 'code' => 'ENV411', 'description' => 'Technologies and potential of solar, wind, hydro, and other renewable energy sources.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Biotechnology', 'code' => 'ENV412', 'description' => 'Application of biotechnological tools for environmental monitoring and remediation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sustainable Development', 'code' => 'ENV213', 'description' => 'Concepts, principles, and indicators of sustainable development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Ethics and Justice', 'code' => 'ENV314', 'description' => 'Ethical dimensions of environmental issues and concepts of environmental justice.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Field Techniques in Environmental Science', 'code' => 'ENV315', 'description' => 'Practical field methods for sampling and analyzing environmental parameters.', 'credit_hours' => '1+2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Atmospheric Science and Air Pollution Control', 'code' => 'ENV413', 'description' => 'Atmospheric composition, dynamics, and technologies for air pollution control.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Oceanography and Coastal Zone Management', 'code' => 'ENV414', 'description' => 'Physical, chemical, and biological aspects of oceans and coastal management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective BS/MS Environmental Science Subjects
            [ 'name' => 'Environmental Modeling', 'code' => 'ENV501', 'description' => 'Development and application of mathematical models for environmental systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Remote Sensing and Image Analysis for Environment', 'code' => 'ENV502', 'description' => 'Advanced remote sensing techniques and image processing for environmental applications.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Water Quality Management and Treatment', 'code' => 'ENV503', 'description' => 'Water quality standards, monitoring, and advanced water treatment technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Soil Pollution and Remediation', 'code' => 'ENV504', 'description' => 'Sources of soil contamination and techniques for soil remediation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Hazardous Waste Management', 'code' => 'ENV505', 'description' => 'Characterization, treatment, and disposal of hazardous wastes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Industrial Ecology and Cleaner Production', 'code' => 'ENV506', 'description' => 'Principles of industrial ecology and strategies for cleaner production processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Geochemistry', 'code' => 'ENV507', 'description' => 'Geochemical processes influencing the fate and transport of contaminants.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Urban Environmental Management', 'code' => 'ENV508', 'description' => 'Environmental challenges in urban areas and strategies for sustainable urban development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Forest Ecology and Management', 'code' => 'ENV509', 'description' => 'Ecological principles of forest ecosystems and sustainable forest management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Wildlife Ecology and Management', 'code' => 'ENV510', 'description' => 'Principles of wildlife population dynamics, habitat management, and conservation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Risk Assessment', 'code' => 'ENV511', 'description' => 'Methods for assessing risks to human health and ecosystems from environmental hazards.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Climate Modeling and Prediction', 'code' => 'ENV512', 'description' => 'Techniques for modeling climate systems and predicting future climate scenarios.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Forensics', 'code' => 'ENV513', 'description' => 'Scientific methods to identify sources and timing of pollution events.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Agroecology and Sustainable Agriculture', 'code' => 'ENV514', 'description' => 'Ecological principles applied to agricultural systems for sustainability.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Education and Communication', 'code' => 'ENV515', 'description' => 'Strategies for effective environmental education and public communication.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Energy Policy and Management', 'code' => 'ENV516', 'description' => 'Policies related to energy production, consumption, and conservation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Auditing and Management Systems (ISO 14001)', 'code' => 'ENV517', 'description' => 'Principles of environmental auditing and implementation of EMS.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Aquatic Ecology', 'code' => 'ENV518', 'description' => 'Ecology of freshwater and marine ecosystems, including lakes, rivers, and oceans.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Restoration Ecology', 'code' => 'ENV519', 'description' => 'Principles and practices of restoring degraded ecosystems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Sociology', 'code' => 'ENV520', 'description' => 'Social dimensions of environmental problems and human-environment interactions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'GIS for Water Resources Management', 'code' => 'ENV521', 'description' => 'Application of GIS in hydrological modeling and water resource management.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Air Quality Modeling', 'code' => 'ENV522', 'description' => 'Mathematical models for simulating air pollutant dispersion and transport.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Monitoring Techniques', 'code' => 'ENV523', 'description' => 'Advanced instrumental and field techniques for environmental monitoring.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Ecotoxicology and Biomonitoring', 'code' => 'ENV524', 'description' => 'Effects of pollutants on biological systems and use of bioindicators.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Green Chemistry and Technology', 'code' => 'ENV525', 'description' => 'Principles of green chemistry and development of environmentally benign technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Management in Developing Countries', 'code' => 'ENV526', 'description' => 'Specific environmental challenges and management approaches in developing nations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Disaster Risk Management and Environment', 'code' => 'ENV527', 'description' => 'Linkages between environmental degradation and disaster risk, and management strategies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Carbon Management and Sequestration', 'code' => 'ENV528', 'description' => 'Strategies for managing carbon emissions and technologies for carbon sequestration.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Environmental Nanotechnology', 'code' => 'ENV529', 'description' => 'Applications and implications of nanotechnology in environmental science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Seminar in Environmental Science', 'code' => 'ENV530', 'description' => 'Presentation and discussion of current research topics in environmental science.', 'credit_hours' => '1-2', 'category' => 'Elective', 'program_level' => 'BS/MS'],
            [ 'name' => 'Internship in Environmental Science', 'code' => 'ENV490', 'description' => 'Supervised practical experience in an environmental organization or industry.', 'credit_hours' => '3-6', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Environmental Chemistry', 'code' => 'ENV601', 'description' => 'In-depth study of chemical processes in various environmental compartments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Ecology and Ecosystem Dynamics', 'code' => 'ENV602', 'description' => 'Advanced ecological theories, ecosystem modeling, and global ecological change.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Environmental Policy and Governance', 'code' => 'ENV603', 'description' => 'Complexities of environmental policy-making, international agreements, and governance structures.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Research Design and Methodology for Environmental Science (MS)', 'code' => 'ENV604', 'description' => 'Advanced research design, proposal writing, and ethical considerations for MS level research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis Research I', 'code' => 'ENV698A', 'description' => 'Initial research work, literature synthesis, and methodology development for MS thesis.', 'credit_hours' => '3-6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis Research II', 'code' => 'ENV698B', 'description' => 'Completion of research, data analysis, thesis writing, and defense for MS degree.', 'credit_hours' => '3-6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Environmental Systems Analysis', 'code' => 'ENV605', 'description' => 'Holistic analysis of complex environmental systems using modeling and simulation tools.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Pollution Prevention and Control Technologies', 'code' => 'ENV606', 'description' => 'Advanced technologies for preventing and controlling air, water, and soil pollution.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Global Environmental Change', 'code' => 'ENV607', 'description' => 'Scientific understanding of global change phenomena (climate change, biodiversity loss, etc.) and their interconnections.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Environmental Data Science', 'code' => 'ENV608', 'description' => 'Application of data science techniques, machine learning, and big data analytics to environmental problems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],

            // PhD Environmental Science Subjects
            [ 'name' => 'Doctoral Seminar in Environmental Science', 'code' => 'ENV801', 'description' => 'Critical review and discussion of seminal and current literature in environmental science.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Philosophy and Ethics of Environmental Inquiry', 'code' => 'ENV802', 'description' => 'Epistemological foundations and ethical dimensions of environmental research and practice.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Research Design and Grant Writing', 'code' => 'ENV803', 'description' => 'Developing fundable research proposals and advanced experimental designs.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Earth System Governance', 'code' => 'ENV811', 'description' => 'Advanced theories and research on the governance of complex earth systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Climate Change Science and Policy', 'code' => 'ENV812', 'description' => 'Cutting-edge research in climate science, impacts, and international policy debates.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Ecotoxicology and Chemical Fate', 'code' => 'ENV813', 'description' => 'Advanced topics in the transport, fate, and effects of contaminants in ecosystems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Conservation Science and Sustainability', 'code' => 'ENV814', 'description' => 'Current research in conservation biology, landscape ecology, and sustainability science.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Environmental Systems Modeling', 'code' => 'ENV815', 'description' => 'Development and application of complex, integrated models for environmental systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Pedagogy in Environmental Science', 'code' => 'ENV821', 'description' => 'Training in teaching methods and curriculum development for university-level environmental science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Water Science and Policy', 'code' => 'ENV822', 'description' => 'Specialized seminar on current issues in hydrology, water quality, and water governance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Atmospheric Science', 'code' => 'ENV823', 'description' => 'In-depth study of atmospheric chemistry, physics, and air quality management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Socio-Ecological Systems Theory and Application', 'code' => 'ENV824', 'description' => 'Theoretical frameworks and case studies of coupled human-natural systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Environmental and Resource Economics', 'code' => 'ENV825', 'description' => 'Advanced economic theories and methods for environmental valuation and policy analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Research in Environmental Science', 'code' => 'ENV890', 'description' => 'Independent research on a specialized topic under faculty guidance, in preparation for dissertation.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'ENV899', 'description' => 'Original research culminating in a PhD dissertation in Environmental Science.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Environmental Science';    
         
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
