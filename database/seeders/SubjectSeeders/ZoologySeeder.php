<?php

namespace Database\Seeders\SubjectSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class ZoologySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // BS Zoology Subjects
            [ 'name' => 'Principles of Animal Life I (Invertebrate Zoology)', 'code' => 'ZOO101', 'description' => 'Diversity, classification, morphology, and physiology of invertebrate phyla.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Principles of Animal Life II (Vertebrate Zoology)', 'code' => 'ZOO102', 'description' => 'Diversity, classification, morphology, and physiology of vertebrate classes.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cell Biology', 'code' => 'ZOO201', 'description' => 'Structure and function of cells, organelles, cell cycle, and molecular processes.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Genetics', 'code' => 'ZOO202', 'description' => 'Principles of heredity, Mendelian genetics, molecular genetics, and population genetics.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Animal Physiology', 'code' => 'ZOO203', 'description' => 'Functions of organ systems in animals, including respiration, circulation, digestion, and excretion.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Developmental Biology (Embryology)', 'code' => 'ZOO301', 'description' => 'Processes of animal development from gametogenesis to organogenesis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Ecology', 'code' => 'ZOO302', 'description' => 'Interactions between organisms and their environment, population dynamics, and ecosystem structure.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Evolutionary Biology', 'code' => 'ZOO303', 'description' => 'Mechanisms of evolution, natural selection, speciation, and phylogeny.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry for Zoologists', 'code' => 'ZOO211', 'description' => 'Chemical processes and substances in living organisms relevant to zoology.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Biostatistics for Zoologists', 'code' => 'ZOO212', 'description' => 'Application of statistical methods to biological data in zoology.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Animal Behavior (Ethology)', 'code' => 'ZOO304', 'description' => 'Study of animal behavior, its causes, development, and evolution.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Immunology', 'code' => 'ZOO401', 'description' => 'Principles of the immune system, immune responses, and immunological techniques.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Molecular Biology', 'code' => 'ZOO402', 'description' => 'Structure and function of macromolecules, gene expression, and recombinant DNA technology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Parasitology', 'code' => 'ZOO403', 'description' => 'Biology of parasites, host-parasite interactions, and parasitic diseases.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Wildlife Conservation and Management', 'code' => 'ZOO404', 'description' => 'Principles of wildlife conservation, habitat management, and biodiversity protection.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Zoology Research Project I', 'code' => 'ZOO498A', 'description' => 'Development of research proposal and literature review for a zoology project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Zoology Research Project II', 'code' => 'ZOO498B', 'description' => 'Execution, data analysis, and presentation of the zoology research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Comparative Anatomy of Vertebrates', 'code' => 'ZOO311', 'description' => 'Comparative study of anatomical structures in different vertebrate groups.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Entomology', 'code' => 'ZOO312', 'description' => 'Study of insects, their diversity, biology, and economic importance.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Fisheries and Aquaculture', 'code' => 'ZOO313', 'description' => 'Biology of fishes, fishery management, and aquaculture techniques.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Paleontology', 'code' => 'ZOO314', 'description' => 'Study of fossils, history of life on Earth, and evolutionary patterns.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Zoogeography and Biogeography', 'code' => 'ZOO411', 'description' => 'Distribution of animal species across geographic regions and factors influencing it.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Endocrinology', 'code' => 'ZOO412', 'description' => 'Study of hormones, endocrine glands, and their role in regulating physiological processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methodology and Scientific Writing in Zoology', 'code' => 'ZOO315', 'description' => 'Principles of research design, data interpretation, and scientific communication.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Microbiology for Zoologists', 'code' => 'ZOO213', 'description' => 'Introduction to microorganisms, their biology, and relevance to animal health and disease.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'BS'],

            // Elective Subjects for Zoology
            [ 'name' => 'Advanced Cell Biology', 'code' => 'ZOO501', 'description' => 'In-depth study of cellular processes, signaling pathways, and cell differentiation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Molecular Genetics', 'code' => 'ZOO502', 'description' => 'Advanced topics in gene regulation, genomics, proteomics, and bioinformatics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Applied Entomology', 'code' => 'ZOO503', 'description' => 'Management of insect pests, beneficial insects, and forensic entomology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Fish Biology and Physiology', 'code' => 'ZOO504', 'description' => 'Detailed study of fish anatomy, physiology, and adaptation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Herpetology', 'code' => 'ZOO505', 'description' => 'Biology, diversity, and conservation of amphibians and reptiles.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Ornithology', 'code' => 'ZOO506', 'description' => 'Biology, diversity, behavior, and conservation of birds.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Mammalogy', 'code' => 'ZOO507', 'description' => 'Biology, diversity, evolution, and conservation of mammals.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Marine Biology', 'code' => 'ZOO508', 'description' => 'Study of marine organisms, ecosystems, and conservation of marine biodiversity.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Toxicology', 'code' => 'ZOO509', 'description' => 'Effects of pollutants on organisms and ecosystems, and risk assessment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Population Ecology', 'code' => 'ZOO510', 'description' => 'Dynamics of animal populations, life history strategies, and population regulation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Community Ecology', 'code' => 'ZOO511', 'description' => 'Structure, organization, and dynamics of ecological communities.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Behavioral Ecology', 'code' => 'ZOO512', 'description' => 'Evolutionary basis of animal behavior in relation to ecological factors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Conservation Genetics', 'code' => 'ZOO513', 'description' => 'Application of genetic principles to the conservation of endangered species.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Systematics and Phylogenetics', 'code' => 'ZOO514', 'description' => 'Principles of biological classification, phylogenetic analysis, and evolutionary relationships.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Neurobiology', 'code' => 'ZOO515', 'description' => 'Structure and function of the nervous system, neural signaling, and sensory processing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Reproductive Physiology', 'code' => 'ZOO516', 'description' => 'Physiological mechanisms of reproduction in animals.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Animal Biotechnology', 'code' => 'ZOO517', 'description' => 'Application of biotechnological techniques in animal science and improvement.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Bioinformatics for Zoologists', 'code' => 'ZOO518', 'description' => 'Computational tools and methods for analyzing biological data, especially genomic and proteomic data.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Wildlife Photography and Cinematography', 'code' => 'ZOO519', 'description' => 'Techniques and ethics of photographing and filming wildlife.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Zoo and Aquarium Management', 'code' => 'ZOO520', 'description' => 'Principles of managing captive animal populations in zoos and aquariums.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Human Genetics', 'code' => 'ZOO521', 'description' => 'Principles of human heredity, genetic disorders, and genetic counseling.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Medical Entomology', 'code' => 'ZOO522', 'description' => 'Insects and arthropods of medical importance as vectors of disease.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Aquatic Toxicology', 'code' => 'ZOO523', 'description' => 'Effects of toxic substances on aquatic organisms and ecosystems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Vertebrate Pest Management', 'code' => 'ZOO524', 'description' => 'Strategies for managing vertebrate pest populations in agricultural and urban environments.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Animal Nutrition', 'code' => 'ZOO525', 'description' => 'Principles of animal nutrition, feed formulation, and nutritional requirements.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Ichthyology', 'code' => 'ZOO526', 'description' => 'In-depth study of the biology, diversity, and evolution of fishes.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Wildlife Forensics', 'code' => 'ZOO527', 'description' => 'Application of scientific techniques to investigate wildlife crimes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Ecotourism and Sustainable Development', 'code' => 'ZOO528', 'description' => 'Principles of ecotourism and its role in conservation and sustainable development.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Climate Change and Animal Life', 'code' => 'ZOO529', 'description' => 'Impacts of climate change on animal physiology, distribution, and behavior.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Parasitology', 'code' => 'ZOO530', 'description' => 'Advanced topics in parasite biology, immunology, and epidemiology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Zoology Subjects
            [ 'name' => 'Advanced Research Methodology in Zoology', 'code' => 'ZOO601', 'description' => 'Advanced research design, biostatistics, and scientific writing for zoological research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advances in Molecular Biology', 'code' => 'ZOO602', 'description' => 'Current techniques and applications in genomics, proteomics, and bioinformatics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advances in Cell Biology', 'code' => 'ZOO603', 'description' => 'In-depth study of cell signaling, apoptosis, and cancer biology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advances in Ecology', 'code' => 'ZOO604', 'description' => 'Advanced concepts in population, community, and ecosystem ecology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Insect Physiology and Biochemistry', 'code' => 'ZOO611', 'description' => 'Detailed study of the physiological and biochemical systems of insects.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Fish Physiology and Endocrinology', 'code' => 'ZOO612', 'description' => 'Advanced topics in the physiology and hormonal control in fishes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Molecular Parasitology and Vector Biology', 'code' => 'ZOO613', 'description' => 'Molecular basis of host-parasite interactions and biology of disease vectors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Immunology', 'code' => 'ZOO614', 'description' => 'Molecular and cellular immunology, immunogenetics, and immunopathology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Developmental Biology', 'code' => 'ZOO615', 'description' => 'Molecular mechanisms of development, pattern formation, and organogenesis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Conservation Biology and Wildlife Management', 'code' => 'ZOO616', 'description' => 'Advanced principles of conservation, population viability analysis, and wildlife policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Insect Taxonomy and Systematics', 'code' => 'ZOO621', 'description' => 'Principles and practices of insect classification and phylogenetic analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Integrated Pest Management', 'code' => 'ZOO622', 'description' => 'Ecologically-based strategies for managing pest populations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Aquaculture and Fisheries Management', 'code' => 'ZOO623', 'description' => 'Sustainable aquaculture practices and modern fisheries management techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Molecular Systematics and Evolution', 'code' => 'ZOO624', 'description' => 'Use of molecular data to infer evolutionary relationships and study speciation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Animal Virology', 'code' => 'ZOO625', 'description' => 'Biology of animal viruses, viral pathogenesis, and host-virus interactions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Comparative Endocrinology', 'code' => 'ZOO626', 'description' => 'Comparative study of endocrine systems across different animal taxa.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Ecotoxicology and Risk Assessment', 'code' => 'ZOO627', 'description' => 'Advanced topics in the effects of toxins on wildlife and ecological risk assessment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Landscape Ecology and GIS', 'code' => 'ZOO628', 'description' => 'Application of GIS and remote sensing in analyzing spatial patterns in ecology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Animal Behavior', 'code' => 'ZOO629', 'description' => 'Current research in neuroethology, sociobiology, and cognitive ecology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Chronobiology', 'code' => 'ZOO630', 'description' => 'Study of biological rhythms and their underlying mechanisms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Stem Cell Biology', 'code' => 'ZOO631', 'description' => 'Biology of stem cells, their therapeutic potential, and ethical considerations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Epigenetics', 'code' => 'ZOO632', 'description' => 'Study of heritable changes in gene function that do not involve changes in the DNA sequence.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Ornithology and Mammalogy', 'code' => 'ZOO633', 'description' => 'Advanced study of the biology, ecology, and conservation of birds and mammals.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Protozoology', 'code' => 'ZOO634', 'description' => 'Biology, classification, and economic importance of protozoans.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'MPhil Thesis', 'code' => 'ZOO699', 'description' => 'Independent research thesis for the MPhil degree in Zoology.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS'],

            // PhD Zoology Subjects
            [ 'name' => 'Doctoral Seminar in Evolutionary Biology', 'code' => 'ZOO801', 'description' => 'Critical review and discussion of current research in evolutionary biology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar in Molecular and Cellular Zoology', 'code' => 'ZOO802', 'description' => 'Advanced topics and current literature in molecular and cellular zoology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar in Ecology and Environmental Biology', 'code' => 'ZOO803', 'description' => 'In-depth analysis of current research in ecology and environmental science.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Genomics and Bioinformatics', 'code' => 'ZOO811', 'description' => 'Cutting-edge research in comparative genomics, transcriptomics, and systems biology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Neuroethology', 'code' => 'ZOO812', 'description' => 'Neural basis of behavior, sensory ecology, and communication.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Conservation Science', 'code' => 'ZOO813', 'description' => 'Advanced theory and practice in conservation, including policy and global change biology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Host-Pathogen Interactions', 'code' => 'ZOO814', 'description' => 'Molecular and evolutionary dynamics of interactions between hosts and pathogens.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Physiological Ecology', 'code' => 'ZOO815', 'description' => 'Research on the physiological adaptations of animals to their environments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Scientific Communication and Grant Writing', 'code' => 'ZOO821', 'description' => 'Advanced skills in writing research articles, grant proposals, and scientific communication.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Pedagogy in Life Sciences', 'code' => 'ZOO822', 'description' => 'Training in teaching methods and curriculum development for university-level life sciences.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Biostatistical Modeling', 'code' => 'ZOO823', 'description' => 'Advanced statistical models for complex biological data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Macroecology and Biogeography', 'code' => 'ZOO824', 'description' => 'Research on large-scale ecological and biogeographical patterns.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Molecular Endocrinology of Vertebrates', 'code' => 'ZOO825', 'description' => 'Molecular mechanisms of hormone action and regulation in vertebrates.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Research in Zoology', 'code' => 'ZOO890', 'description' => 'Independent research on a specialized topic under the guidance of a faculty member.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'ZOO899', 'description' => 'Original research culminating in a PhD dissertation in Zoology.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Zoology';    
         
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
