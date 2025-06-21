<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BiochemistrySeeder extends Seeder
{
    public function run()
    {
        $subjects = 
        [
            // Core Major Subjects for Biochemistry
            [ 'name' => 'Principles of Biochemistry', 'code' => 'BCH101', 'description' => 'Fundamental concepts of biochemistry, including biomolecules, enzymes, and metabolic pathways.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cell Biology and Biomembranes', 'code' => 'BCH102', 'description' => 'Structure and function of cells, organelles, and biological membranes.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Techniques in Biochemistry', 'code' => 'BCH201', 'description' => 'Laboratory methods and instrumentation used in biochemical research.', 'credit_hours' => '2+2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Enzymology', 'code' => 'BCH202', 'description' => 'Study of enzyme structure, kinetics, mechanisms, and regulation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Metabolism I: Carbohydrates and Lipids', 'code' => 'BCH203', 'description' => 'Metabolic pathways of carbohydrates and lipids, and their regulation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Metabolism II: Proteins and Nucleic Acids', 'code' => 'BCH204', 'description' => 'Metabolic pathways of amino acids, proteins, and nucleic acids.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Molecular Biology I: Gene Structure and Function', 'code' => 'BCH301', 'description' => 'Structure of DNA and RNA, DNA replication, transcription, and translation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Molecular Biology II: Gene Regulation', 'code' => 'BCH302', 'description' => 'Mechanisms of gene expression regulation in prokaryotes and eukaryotes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Immunology', 'code' => 'BCH303', 'description' => 'Principles of the immune system, immune responses, and immunological techniques.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemical Genetics', 'code' => 'BCH304', 'description' => 'Genetic basis of biochemical processes and inherited metabolic disorders.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Clinical Biochemistry', 'code' => 'BCH401', 'description' => 'Biochemical basis of human diseases and diagnostic laboratory methods.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Nutritional Biochemistry', 'code' => 'BCH402', 'description' => 'Biochemical aspects of nutrients, their metabolism, and impact on health.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biophysical Chemistry', 'code' => 'BCH310', 'description' => 'Physical principles underlying biological systems and biomolecular interactions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Bioenergetics and Thermodynamics', 'code' => 'BCH311', 'description' => 'Energy transformations in biological systems and thermodynamic principles.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Protein Structure and Function', 'code' => 'BCH312', 'description' => 'Detailed study of protein architecture, folding, and functional diversity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Recombinant DNA Technology', 'code' => 'BCH410', 'description' => 'Principles and applications of genetic engineering and DNA manipulation.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Bioinformatics for Biochemists', 'code' => 'BCH411', 'description' => 'Computational tools for analyzing biochemical and molecular biological data.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methodology in Biochemistry', 'code' => 'BCH498', 'description' => 'Experimental design, data analysis, and scientific writing in biochemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry Seminar', 'code' => 'BCH497', 'description' => 'Presentation and critical discussion of current research in biochemistry.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project I (Biochemistry)', 'code' => 'BCH499A', 'description' => 'Development of a research proposal and initial work on an independent biochemical project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project II (Biochemistry)', 'code' => 'BCH499B', 'description' => 'Completion, analysis, and presentation of the independent research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biostatistics for Life Sciences', 'code' => 'BCH210', 'description' => 'Statistical methods relevant to biological and biochemical research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Microbiology for Biochemists', 'code' => 'BCH211', 'description' => 'Fundamentals of microbial life, diversity, and their biochemical significance.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Plant Biochemistry', 'code' => 'BCH320', 'description' => 'Biochemical processes unique to plants, including photosynthesis and secondary metabolism.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Animal Biochemistry', 'code' => 'BCH321', 'description' => 'Biochemical pathways and regulatory mechanisms in animal systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Disease', 'code' => 'BCH322', 'description' => 'Molecular basis of diseases and biochemical markers for diagnosis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biotechnology and Biochemical Engineering', 'code' => 'BCH323', 'description' => 'Application of biochemistry in biotechnology and industrial processes.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Reproduction', 'code' => 'BCH324', 'description' => 'Hormonal and biochemical regulation of reproductive processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Aging and Longevity', 'code' => 'BCH325', 'description' => 'Molecular mechanisms of aging and interventions for longevity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Stress Responses', 'code' => 'BCH326', 'description' => 'Cellular responses to stress and their biochemical implications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Enzymology', 'code' => 'BCH501', 'description' => 'In-depth study of enzyme mechanisms, regulation, and industrial applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Protein Engineering and Design', 'code' => 'BCH502', 'description' => 'Methods for modifying protein structure and function for specific applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Genomics and Proteomics', 'code' => 'BCH503', 'description' => 'High-throughput analysis of genomes and proteomes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Signal Transduction Pathways', 'code' => 'BCH504', 'description' => 'Molecular mechanisms of cellular communication and signaling.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cancer Biochemistry', 'code' => 'BCH505', 'description' => 'Biochemical alterations in cancer cells and development of targeted therapies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Neurobiochemistry', 'code' => 'BCH506', 'description' => 'Biochemical processes in the nervous system and neurological disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Endocrinology', 'code' => 'BCH507', 'description' => 'Biochemistry of hormones, their receptors, and endocrine disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Xenobiotic Metabolism and Toxicology', 'code' => 'BCH508', 'description' => 'Biochemical pathways for metabolizing foreign compounds and principles of toxicology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Membrane Biochemistry and Transport', 'code' => 'BCH509', 'description' => 'Structure and function of biological membranes and transport mechanisms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Lipids and Lipoproteins', 'code' => 'BCH510', 'description' => 'Advanced topics in lipid metabolism, transport, and related diseases.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Structural Biochemistry', 'code' => 'BCH511', 'description' => 'Techniques for determining the three-dimensional structure of biomolecules.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Industrial Biochemistry and Fermentation Technology', 'code' => 'BCH512', 'description' => 'Application of biochemical principles in industrial processes and fermentation.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Vitamins and Minerals', 'code' => 'BCH513', 'description' => 'Roles of vitamins and minerals in metabolic processes and health.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Developmental Biochemistry', 'code' => 'BCH514', 'description' => 'Biochemical changes during embryonic development and differentiation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Aging', 'code' => 'BCH515', 'description' => 'Molecular and biochemical mechanisms underlying the aging process.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Free Radicals in Biology and Medicine', 'code' => 'BCH516', 'description' => 'Role of reactive oxygen species in health and disease.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Glycobiology', 'code' => 'BCH517', 'description' => 'Structure, function, and metabolism of carbohydrates and glycoconjugates.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biochemical Pharmacology', 'code' => 'BCH518', 'description' => 'Molecular mechanisms of drug action and metabolism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Biochemistry', 'code' => 'BCH519', 'description' => 'Biochemical processes in the environment and bioremediation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Food Biochemistry and Technology', 'code' => 'BCH520', 'description' => 'Biochemical aspects of food composition, processing, and preservation.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Molecular Biology Techniques', 'code' => 'BCH521', 'description' => 'Cutting-edge techniques in molecular biology research.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'RNA Biochemistry and Technology', 'code' => 'BCH522', 'description' => 'Diverse roles of RNA molecules and RNA-based technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Epigenetics and Chromatin Biology', 'code' => 'BCH523', 'description' => 'Mechanisms of epigenetic regulation and chromatin structure.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Systems Biology and Network Analysis', 'code' => 'BCH524', 'description' => 'Integrative approaches to study biological systems and networks.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry of Infectious Diseases', 'code' => 'BCH525', 'description' => 'Molecular mechanisms of host-pathogen interactions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Current Topics in Biochemistry', 'code' => 'BCH526', 'description' => 'Discussion of recent advancements and emerging areas in biochemistry.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Level Subjects for Biochemistry
            [ 'name' => 'Advanced Molecular Biology', 'code' => 'BCH601', 'description' => 'In-depth analysis of gene structure, replication, transcription, and translation mechanisms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Cell Biology', 'code' => 'BCH602', 'description' => 'Comprehensive study of cellular structures, functions, and signaling pathways at the molecular level.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology & Scientific Writing', 'code' => 'BCH603', 'description' => 'Principles of experimental design, data analysis, and effective scientific communication.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Enzymology and Enzyme Kinetics', 'code' => 'BCH604', 'description' => 'Detailed study of enzyme mechanisms, kinetics, inhibition, and allosteric regulation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Biochemical Techniques', 'code' => 'BCH605', 'description' => 'Hands-on experience with modern biochemical and molecular biology laboratory techniques.', 'credit_hours' => '1+2', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Proteomics and Genomics', 'code' => 'BCH606', 'description' => 'Study of the entire set of proteins and genes, their structures, functions, and interactions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Signal Transduction Mechanisms', 'code' => 'BCH607', 'description' => 'Exploration of intracellular and intercellular signaling pathways and their roles in cellular processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Metabolic Regulation and Integration', 'code' => 'BCH608', 'description' => 'An integrated view of metabolic pathways and their regulation in health and disease.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Structural Biochemistry and Biophysics', 'code' => 'BCH609', 'description' => 'Principles of macromolecular structure determination and biophysical characterization.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Computational Biology and Bioinformatics', 'code' => 'BCH610', 'description' => 'Application of computational tools to analyze biological data, including sequence, structure, and expression data.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Biochemistry I', 'code' => 'BCH691', 'description' => 'Presentation and discussion of current research literature in biochemistry.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Biochemistry II', 'code' => 'BCH692', 'description' => 'Advanced seminar focusing on critical analysis of scientific papers and research proposals.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research I', 'code' => 'BCH699A', 'description' => 'Independent research work towards the MS/MPhil thesis.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research II', 'code' => 'BCH699B', 'description' => 'Continuation of independent research, data analysis, and thesis writing.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Biostatistics', 'code' => 'BCH611', 'description' => 'Statistical methods and their application in the design and analysis of biological experiments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Cancer Biology and Genetics', 'code' => 'BCH701', 'description' => 'Molecular and cellular basis of cancer, including oncogenes, tumor suppressors, and signaling pathways.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Molecular Neurobiology', 'code' => 'BCH702', 'description' => 'Biochemical and molecular aspects of nervous system development, function, and disease.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Immunology', 'code' => 'BCH703', 'description' => 'In-depth study of the molecular and cellular components of the immune system.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Plant Biochemistry and Biotechnology', 'code' => 'BCH704', 'description' => 'Biochemical pathways in plants and their manipulation for agricultural and industrial applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Industrial and Environmental Biochemistry', 'code' => 'BCH705', 'description' => 'Application of biochemical principles in industrial processes, bioremediation, and environmental monitoring.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Glycobiology', 'code' => 'BCH706', 'description' => 'Study of the structure, biosynthesis, and function of glycans and glycoconjugates.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Lipid and Lipoprotein Biochemistry', 'code' => 'BCH707', 'description' => 'Advanced topics in lipid metabolism, transport, and their role in diseases like atherosclerosis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Epigenetics and Chromatin Dynamics', 'code' => 'BCH708', 'description' => 'Mechanisms of epigenetic inheritance and the role of chromatin structure in gene regulation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Drug Design and Development', 'code' => 'BCH709', 'description' => 'Principles of rational drug design, from target identification to lead optimization.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Nutritional Biochemistry', 'code' => 'BCH710', 'description' => 'Molecular mechanisms by which nutrients impact metabolism and gene expression.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // PhD Level Subjects for Biochemistry
            [ 'name' => 'Advanced Topics in Gene Regulation', 'code' => 'BCH801', 'description' => 'Cutting-edge research in transcriptional, post-transcriptional, and epigenetic control of gene expression.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Current Trends in Structural Biology', 'code' => 'BCH802', 'description' => 'Exploration of recent advances in cryo-EM, NMR, and X-ray crystallography for studying macromolecular complexes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Signal Transduction and Cellular Regulation', 'code' => 'BCH803', 'description' => 'In-depth analysis of complex signaling networks and their dysregulation in disease.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Molecular Basis of Human Disease', 'code' => 'BCH804', 'description' => 'A research-oriented course on the molecular mechanisms underlying genetic and complex diseases.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Proteomics and Metabolomics', 'code' => 'BCH805', 'description' => 'Advanced techniques and data analysis strategies in mass spectrometry-based proteomics and metabolomics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Systems Biology and Modeling', 'code' => 'BCH806', 'description' => 'Computational modeling of biological networks to understand emergent properties of complex systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Neurobiochemistry', 'code' => 'BCH901', 'description' => 'Seminar-style course covering the latest research in molecular and cellular neurobiology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Biochemistry of Cancer and Metastasis', 'code' => 'BCH902', 'description' => 'Focus on the biochemical changes that drive tumor progression, invasion, and metastasis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Frontiers in Epigenetic Regulation', 'code' => 'BCH903', 'description' => 'Discussion of novel epigenetic mechanisms and their implications in development and disease.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Readings in Biochemistry', 'code' => 'BCH991', 'description' => 'In-depth literature review on a specialized topic under the guidance of a faculty member.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar I', 'code' => 'BCH992', 'description' => 'Presentation of ongoing doctoral research and critical feedback from peers and faculty.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar II', 'code' => 'BCH993', 'description' => 'Advanced presentation skills and defense of research progress.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar III', 'code' => 'BCH994', 'description' => 'Final seminar before thesis submission, focusing on research outcomes and impact.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Research Proposal and Qualifying Exam', 'code' => 'BCH998', 'description' => 'Development and defense of the doctoral research proposal.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'BCH999', 'description' => 'Original, independent research culminating in the doctoral dissertation.', 'credit_hours' => '12', 'category' => 'Major', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Biochemistry';    
         
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
