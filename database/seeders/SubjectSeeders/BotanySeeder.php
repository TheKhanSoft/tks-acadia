<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BotanySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects
            [ 'name' => 'Introduction to Botany', 'code' => 'BOT101', 'description' => 'Fundamental concepts of plant science, including structure, function, diversity, and evolution.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Morphology and Embryology', 'code' => 'BOT102', 'description' => 'Study of the external form, structure, and development of plants, including embryogenesis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Anatomy and Histology', 'code' => 'BOT201', 'description' => 'Microscopic study of plant cells, tissues, and organs.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Physiology I: Metabolism', 'code' => 'BOT202', 'description' => 'Study of fundamental metabolic processes in plants, including photosynthesis and respiration.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Physiology II: Growth and Development', 'code' => 'BOT203', 'description' => 'Study of plant growth, development, hormones, and responses to environmental stimuli.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Taxonomy and Systematics', 'code' => 'BOT204', 'description' => 'Principles of classification, identification, nomenclature, and evolutionary relationships of plants.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Genetics of Plants', 'code' => 'BOT301', 'description' => 'Principles of heredity, genetic variation, and gene function in plants.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Molecular Biology of Plants', 'code' => 'BOT302', 'description' => 'Study of the molecular mechanisms underlying plant life processes.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Ecology and Phytogeography', 'code' => 'BOT303', 'description' => 'Study of the interactions between plants and their environment, and their geographical distribution.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mycology and Lichenology', 'code' => 'BOT304', 'description' => 'Study of fungi and lichens, their biology, diversity, and ecological roles.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Phycology and Bryology', 'code' => 'BOT305', 'description' => 'Study of algae (phycology) and mosses, liverworts, and hornworts (bryology).', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Pteridology and Paleobotany', 'code' => 'BOT306', 'description' => 'Study of ferns and fern allies (pteridology) and fossil plants (paleobotany).', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Gymnosperms and Angiosperm Phylogeny', 'code' => 'BOT401', 'description' => 'Study of cone-bearing plants (gymnosperms) and the evolutionary relationships of flowering plants.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Economic Botany', 'code' => 'BOT402', 'description' => 'Study of plants of economic importance for food, fiber, medicine, and industry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Pathology', 'code' => 'BOT403', 'description' => 'Study of plant diseases, their causes, diagnosis, and management strategies.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biostatistics for Botanists', 'code' => 'BOT210', 'description' => 'Application of statistical methods to botanical research and data analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Cell Biology', 'code' => 'BOT211', 'description' => 'Detailed study of plant cell structure, organelles, and their functions.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Biochemistry', 'code' => 'BOT212', 'description' => 'Chemical processes and compounds in plants, including primary and secondary metabolites.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Reproductive Biology', 'code' => 'BOT310', 'description' => 'Mechanisms of sexual and asexual reproduction in plants, including pollination and seed development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Developmental Biology', 'code' => 'BOT311', 'description' => 'Molecular and cellular basis of plant growth, differentiation, and morphogenesis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Ethnobotany and Indigenous Knowledge', 'code' => 'BOT410', 'description' => 'Study of traditional plant uses by indigenous communities and their cultural significance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methodology in Botany', 'code' => 'BOT498', 'description' => 'Principles and techniques of scientific research in botany, including experimental design and scientific writing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Botany Seminar and Scientific Communication', 'code' => 'BOT497', 'description' => 'Presentation and discussion of current research topics in botany, and development of scientific communication skills.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project I (Botany)', 'code' => 'BOT499A', 'description' => 'Proposal development and initiation of an independent research project in a specialized area of botany.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project II (Botany)', 'code' => 'BOT499B', 'description' => 'Completion, analysis, and presentation of the independent research project in botany.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective Subjects for Botany
            [ 'name' => 'Plant Biotechnology', 'code' => 'BOT501', 'description' => 'Application of biotechnological tools for plant improvement and production.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Tissue Culture Techniques', 'code' => 'BOT502', 'description' => 'Methods and applications of growing plant cells, tissues, and organs in vitro.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Bioinformatics in Plant Sciences', 'code' => 'BOT503', 'description' => 'Computational approaches for analyzing biological data in plant research.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Medicinal Plants and Pharmacognosy', 'code' => 'BOT504', 'description' => 'Study of plants with medicinal properties, their active compounds, and traditional uses.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Conservation Biology of Plants', 'code' => 'BOT505', 'description' => 'Principles and strategies for the conservation of plant biodiversity.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Horticulture and Landscape Design', 'code' => 'BOT506', 'description' => 'Science and art of cultivating ornamental plants and designing landscapes.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Forest Ecology and Management', 'code' => 'BOT507', 'description' => 'Study of forest ecosystems and sustainable management practices.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Stress Physiology', 'code' => 'BOT508', 'description' => 'Physiological and molecular responses of plants to abiotic and biotic stresses.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Seed Science and Technology', 'code' => 'BOT509', 'description' => 'Biology, production, processing, and storage of seeds.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Aquatic Botany and Limnology', 'code' => 'BOT510', 'description' => 'Study of aquatic plants and freshwater ecosystems.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Virology and Mycoplasmology', 'code' => 'BOT511', 'description' => 'Study of plant viruses and mycoplasma-like organisms.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Soil Science and Plant Nutrition', 'code' => 'BOT512', 'description' => 'Properties of soil, nutrient uptake by plants, and fertilizer management.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Weed Science and Invasive Species', 'code' => 'BOT513', 'description' => 'Biology of weeds, their impact, and management of invasive plant species.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Genomics and Proteomics', 'code' => 'BOT514', 'description' => 'Study of plant genomes, gene expression, and protein functions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Mycology', 'code' => 'BOT515', 'description' => 'In-depth study of fungal diversity, genetics, and ecological roles.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Molecular Systematics', 'code' => 'BOT516', 'description' => 'Application of molecular data in understanding plant evolutionary relationships.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Defense Mechanisms', 'code' => 'BOT517', 'description' => 'Biochemical and molecular strategies used by plants to defend against pathogens and herbivores.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Industrial Applications of Botany', 'code' => 'BOT518', 'description' => 'Utilization of plants and plant products in various industries.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Breeding and Crop Improvement', 'code' => 'BOT519', 'description' => 'Principles and techniques for developing improved crop varieties.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Herbal Technology and Phytomedicine', 'code' => 'BOT520', 'description' => 'Processing of medicinal plants and development of herbal medicines.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Impact Assessment for Flora', 'code' => 'BOT521', 'description' => 'Methods for assessing the impact of developmental projects on plant life.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Biogeography and Climate Change', 'code' => 'BOT522', 'description' => 'Impact of climate change on plant distribution and adaptation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Restoration Ecology and Revegetation', 'code' => 'BOT523', 'description' => 'Principles and practices of restoring degraded ecosystems and revegetating disturbed lands.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Dendrology and Forest Mensuration', 'code' => 'BOT524', 'description' => 'Identification of trees and measurement of forest resources.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plant Nematology and Pest Management', 'code' => 'BOT525', 'description' => 'Study of plant-parasitic nematodes and integrated pest management strategies.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Plant Anatomy', 'code' => 'BOT526', 'description' => 'Specialized topics in plant internal structure and development.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cytogenetics of Plants', 'code' => 'BOT527', 'description' => 'Study of plant chromosomes, their structure, behavior, and evolution.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Level Subjects for Botany
            [ 'name' => 'Advanced Plant Taxonomy and Systematics', 'code' => 'BOT601', 'description' => 'Modern approaches in plant classification, including molecular systematics and phylogenetics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Plant Physiology', 'code' => 'BOT602', 'description' => 'In-depth study of metabolic pathways, hormonal regulation, and stress physiology in plants.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Plant Ecology', 'code' => 'BOT603', 'description' => 'Community dynamics, ecosystem processes, and conservation strategies in plant ecology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Plant Molecular Biology and Genomics', 'code' => 'BOT604', 'description' => 'Gene expression, regulation, and functional genomics in plants.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology and Biostatistics', 'code' => 'BOT605', 'description' => 'Experimental design, data analysis, and scientific writing for botanical research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Mycology and Plant Pathology', 'code' => 'BOT606', 'description' => 'Study of fungal biology, plant-pathogen interactions, and disease management.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Plant Developmental Biology', 'code' => 'BOT607', 'description' => 'Molecular and genetic control of plant development from embryogenesis to senescence.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Plant Biotechnology and Genetic Engineering', 'code' => 'BOT608', 'description' => 'Application of biotechnology for crop improvement and production of novel plant products.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Ethnobotany and Phytochemistry', 'code' => 'BOT701', 'description' => 'Scientific study of traditional plant knowledge and the chemistry of plant-derived compounds.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Conservation and Restoration Ecology', 'code' => 'BOT702', 'description' => 'Principles and practices of conserving biodiversity and restoring degraded ecosystems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant Stress Biology', 'code' => 'BOT703', 'description' => 'Molecular and physiological responses of plants to various environmental stresses.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Phycology', 'code' => 'BOT704', 'description' => 'Taxonomy, ecology, and biotechnology of algae.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant Anatomy and Morphogenesis', 'code' => 'BOT705', 'description' => 'Advanced topics in the structural development and evolution of plant form.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant Reproductive Ecology', 'code' => 'BOT706', 'description' => 'Evolutionary and ecological aspects of plant reproductive strategies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Bioinformatics for Plant Scientists', 'code' => 'BOT707', 'description' => 'Computational tools for analyzing genomic, transcriptomic, and proteomic data from plants.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Botany I', 'code' => 'BOT691', 'description' => 'Presentation and discussion of current research in botany.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Botany II', 'code' => 'BOT692', 'description' => 'Development and presentation of a research proposal.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research I', 'code' => 'BOT699A', 'description' => 'Independent research project conducted under the supervision of a faculty member.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research II', 'code' => 'BOT699B', 'description' => 'Continuation of research, data analysis, and writing of the thesis.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Plant Cell and Tissue Culture', 'code' => 'BOT708', 'description' => 'Advanced techniques and applications of plant in vitro culture.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Paleobotany and Palynology', 'code' => 'BOT709', 'description' => 'Study of fossil plants and pollen to understand past vegetation and climates.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant Secondary Metabolites', 'code' => 'BOT710', 'description' => 'Biosynthesis, regulation, and ecological functions of plant secondary compounds.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Invasive Plant Ecology', 'code' => 'BOT711', 'description' => 'Biology, impact, and management of invasive plant species.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant-Microbe Interactions', 'code' => 'BOT712', 'description' => 'Molecular and ecological aspects of symbiotic and pathogenic interactions between plants and microbes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Climate Change and Plant Biology', 'code' => 'BOT713', 'description' => 'Impacts of climate change on plant physiology, distribution, and evolution.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // PhD Level Subjects for Botany
            [ 'name' => 'Frontiers in Plant Science', 'code' => 'BOT801', 'description' => 'Critical analysis of recent breakthroughs and emerging areas in botany.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Research Design and Scientific Writing', 'code' => 'BOT802', 'description' => 'Principles of designing high-impact research and writing effective grants and manuscripts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Plant Molecular Systematics and Evolution', 'code' => 'BOT803', 'description' => 'Phylogenomic approaches to understanding plant evolution and biodiversity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Plant Functional Ecology', 'code' => 'BOT804', 'description' => 'Trait-based approaches to understanding plant community assembly and ecosystem function.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Plant-Environment Interactions', 'code' => 'BOT901', 'description' => 'Molecular and physiological mechanisms of plant adaptation to environmental challenges.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Plant Development and Morphogenesis', 'code' => 'BOT902', 'description' => 'Current research in the genetic and hormonal control of plant form and function.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Ethnobotany and Conservation', 'code' => 'BOT903', 'description' => 'Integrating traditional ecological knowledge with scientific approaches for conservation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching Practicum in Botany', 'code' => 'BOT990', 'description' => 'Supervised experience in teaching undergraduate botany courses.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Readings in Botany', 'code' => 'BOT991', 'description' => 'In-depth literature review on a specialized topic under faculty guidance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar I: Research Proposal Defense', 'code' => 'BOT992', 'description' => 'Development and defense of the doctoral research proposal.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar II: Research Progress Report', 'code' => 'BOT993', 'description' => 'Formal presentation of research progress to the department.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar III: Final Research Presentation', 'code' => 'BOT994', 'description' => 'Public seminar on the completed doctoral research prior to dissertation defense.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'BOT999', 'description' => 'Original and independent research that makes a significant contribution to the field of botany.', 'credit_hours' => '12', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Professionalism and Ethics in Science', 'code' => 'BOT805', 'description' => 'Discussion of ethical considerations, responsible conduct of research, and career development.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Genomic and Proteomic Data Analysis', 'code' => 'BOT806', 'description' => 'Advanced computational methods for the analysis and interpretation of large-scale biological data.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Botany';    
         
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
