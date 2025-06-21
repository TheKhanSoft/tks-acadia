<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BiotechnologySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Biotechnology
            ['name' => 'Introduction to Biotechnology', 'code' => 'BTE101', 'description' => 'Overview of biotechnology, its applications, and ethical considerations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Cell Biology for Biotechnologists', 'code' => 'BTE102', 'description' => 'Fundamental concepts of cell structure, function, and molecular organization relevant to biotechnology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Microbiology for Biotechnologists', 'code' => 'BTE201', 'description' => 'Study of microorganisms (bacteria, viruses, fungi, protists) and their applications in biotechnology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Biochemistry for Biotechnologists', 'code' => 'BTE202', 'description' => 'Principles of biomolecules, enzymes, metabolism, and their relevance in biotechnological processes.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Genetics for Biotechnologists', 'code' => 'BTE203', 'description' => 'Principles of heredity, gene expression, and genetic manipulation in the context of biotechnology.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Molecular Biology Techniques', 'code' => 'BTE301', 'description' => 'Hands-on training in fundamental molecular biology techniques used in biotechnology.', 'credit_hours' => '2+2', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Recombinant DNA Technology', 'code' => 'BTE302', 'description' => 'Principles and applications of gene cloning, genetic engineering, and DNA manipulation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Immunology and Immunotechnology', 'code' => 'BTE303', 'description' => 'Study of the immune system and development of immunological tools and therapies.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Bioinformatics and Computational Biology', 'code' => 'BTE304', 'description' => 'Application of computational tools to analyze biological data, including genomics and proteomics.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Bioprocess Engineering and Technology', 'code' => 'BTE401', 'description' => 'Principles of designing, optimizing, and scaling up biological processes for industrial applications.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Plant Biotechnology', 'code' => 'BTE402', 'description' => 'Application of biotechnological tools for crop improvement, plant tissue culture, and genetic modification of plants.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Animal Biotechnology', 'code' => 'BTE403', 'description' => 'Application of biotechnological tools in animal breeding, diagnostics, and production of therapeutics.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Environmental Biotechnology', 'code' => 'BTE404', 'description' => 'Use of biological systems for bioremediation, waste treatment, and sustainable environmental management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Food Biotechnology', 'code' => 'BTE310', 'description' => 'Application of biotechnology in food production, processing, preservation, and quality improvement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Pharmaceutical Biotechnology', 'code' => 'BTE410', 'description' => 'Development and production of biopharmaceuticals, vaccines, and diagnostic tools.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Industrial Biotechnology', 'code' => 'BTE411', 'description' => 'Application of enzymes and microorganisms in industrial processes for producing chemicals, fuels, and materials.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Biosafety and Bioethics', 'code' => 'BTE412', 'description' => 'Ethical, legal, and social implications of biotechnology, and biosafety regulations.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Biostatistics for Biotechnologists', 'code' => 'BTE210', 'description' => 'Statistical methods for designing experiments and analyzing data in biotechnology research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Enzyme Technology', 'code' => 'BTE311', 'description' => 'Production, purification, immobilization, and applications of enzymes in biotechnology.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Genomics and Proteomics', 'code' => 'BTE420', 'description' => 'Study of whole genomes and proteomes, and their applications in biotechnology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Research Methodology in Biotechnology', 'code' => 'BTE498', 'description' => 'Principles of scientific inquiry, experimental design, data interpretation, and scientific communication.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Biotechnology Seminar and Scientific Writing', 'code' => 'BTE497', 'description' => 'Presentation of research findings and development of scientific writing skills.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Final Year Research Project I (Biotechnology)', 'code' => 'BTE499A', 'description' => 'Literature review, proposal development, and initiation of an independent research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Final Year Research Project II (Biotechnology)', 'code' => 'BTE499B', 'description' => 'Execution, analysis, and presentation of the independent research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Quality Control and Quality Assurance in Biotechnology', 'code' => 'BTE421', 'description' => 'Principles and practices of maintaining quality standards in biotechnological products and processes.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            ['name' => 'Medical Biotechnology', 'code' => 'BTE501', 'description' => 'Application of biotechnology in diagnostics, therapeutics, and personalized medicine.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Agricultural Biotechnology', 'code' => 'BTE502', 'description' => 'Biotechnological approaches for crop improvement, pest resistance, and sustainable agriculture.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Nanobiotechnology', 'code' => 'BTE503', 'description' => 'Interface of nanotechnology and biotechnology, with applications in medicine and diagnostics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Forensic Biotechnology', 'code' => 'BTE504', 'description' => 'Application of DNA fingerprinting and other biotechnological tools in forensic science.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Marine Biotechnology', 'code' => 'BTE505', 'description' => 'Exploration and application of marine organisms and their products in biotechnology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Biofuels and Bioenergy', 'code' => 'BTE506', 'description' => 'Production of renewable energy from biological sources using biotechnological methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Stem Cell Technology and Regenerative Medicine', 'code' => 'BTE507', 'description' => 'Biology of stem cells and their potential applications in tissue repair and regeneration.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Vaccinology', 'code' => 'BTE508', 'description' => 'Development, production, and evaluation of vaccines against infectious diseases.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Protein Engineering', 'code' => 'BTE509', 'description' => 'Design and modification of proteins for novel functions and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Metabolic Engineering', 'code' => 'BTE510', 'description' => 'Modification of metabolic pathways in organisms for enhanced production of desired compounds.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Bioremediation Technologies', 'code' => 'BTE511', 'description' => 'Advanced techniques for using biological agents to clean up environmental pollutants.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Intellectual Property Rights in Biotechnology', 'code' => 'BTE512', 'description' => 'Patenting, copyrights, and other intellectual property issues relevant to biotechnology innovations.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Drug Discovery and Development', 'code' => 'BTE513', 'description' => 'Process of identifying, developing, and testing new pharmaceutical drugs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Cancer Biology and Therapy', 'code' => 'BTE514', 'description' => 'Molecular basis of cancer and biotechnological approaches for its diagnosis and treatment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Aquaculture Biotechnology', 'code' => 'BTE515', 'description' => 'Application of biotechnology to improve aquaculture practices and production.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Biomaterials and Tissue Engineering', 'code' => 'BTE516', 'description' => 'Development of biocompatible materials and their use in creating artificial tissues and organs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Systems Biology in Biotechnology', 'code' => 'BTE517', 'description' => 'Integrative approaches to study complex biological systems for biotechnological applications.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Fermentation Technology and Downstream Processing', 'code' => 'BTE518', 'description' => 'Large-scale cultivation of microorganisms and purification of biotechnological products.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Molecular Diagnostics', 'code' => 'BTE519', 'description' => 'Development and application of molecular techniques for disease diagnosis.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Gene Therapy', 'code' => 'BTE520', 'description' => 'Therapeutic delivery of genetic material to treat or cure diseases.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Bioinstrumentation', 'code' => 'BTE521', 'description' => 'Principles and applications of instruments used in biotechnological research and industry.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Computational Drug Design', 'code' => 'BTE522', 'description' => 'Use of computational methods to design and discover new drugs.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Biotechnology Entrepreneurship', 'code' => 'BTE523', 'description' => 'Principles of starting and managing a biotechnology-based business.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Advanced Immunology', 'code' => 'BTE524', 'description' => 'In-depth topics in immune system function, regulation, and disorders.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            ['name' => 'Synthetic Biology', 'code' => 'BTE525', 'description' => 'Design and construction of new biological parts, devices, and systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Program Subjects
            ['name' => 'Advanced Molecular Biology', 'code' => 'BTE601', 'description' => 'In-depth study of gene regulation, epigenetics, and advanced molecular techniques.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Advanced Cell Culture Techniques', 'code' => 'BTE602', 'description' => 'Principles and practices of animal and plant cell culture for research and production.', 'credit_hours' => '2+1', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Research Methodology in Biotechnology', 'code' => 'BTE603', 'description' => 'Experimental design, data analysis, scientific writing, and ethical considerations in research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Advanced Bioinformatics', 'code' => 'BTE604', 'description' => 'Advanced computational tools for genomics, proteomics, and systems biology.', 'credit_hours' => '2+1', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Advanced Bioprocess Engineering', 'code' => 'BTE605', 'description' => 'Optimization of bioreactors, downstream processing, and scale-up of biotechnological processes.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Genomics and Functional Genomics', 'code' => 'BTE610', 'description' => 'High-throughput sequencing, genome analysis, and functional annotation.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Proteomics and Protein Engineering', 'code' => 'BTE611', 'description' => 'Mass spectrometry, protein interaction analysis, and rational protein design.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Advanced Immunology and Immunotherapy', 'code' => 'BTE612', 'description' => 'Molecular mechanisms of immunity and development of novel immunotherapies.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Stem Cell and Regenerative Medicine', 'code' => 'BTE613', 'description' => 'Biology of stem cells, tissue engineering, and therapeutic applications.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Advanced Plant Biotechnology', 'code' => 'BTE614', 'description' => 'Metabolic engineering in plants, molecular farming, and synthetic biology in plants.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Environmental Biotechnology and Bioremediation', 'code' => 'BTE615', 'description' => 'Advanced strategies for waste treatment, pollution control, and environmental monitoring.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Drug Discovery and Development', 'code' => 'BTE616', 'description' => 'From target identification to clinical trials, focusing on biopharmaceuticals.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Nanobiotechnology and Bionanomaterials', 'code' => 'BTE617', 'description' => 'Synthesis and application of nanomaterials in diagnostics, drug delivery, and imaging.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Advanced Food Biotechnology', 'code' => 'BTE618', 'description' => 'Functional foods, nutraceuticals, and advanced techniques in food processing.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            ['name' => 'Biostatistics and Experimental Design', 'code' => 'BTE619', 'description' => 'Advanced statistical methods for complex biological datasets.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Journal Club and Seminar I', 'code' => 'BTE691', 'description' => 'Critical analysis and presentation of current research articles.', 'credit_hours' => '1', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'Journal Club and Seminar II', 'code' => 'BTE692', 'description' => 'Advanced discussion on research frontiers and proposal development.', 'credit_hours' => '1', 'category' => 'Core', 'program_level' => 'MS'],
            ['name' => 'MS Thesis Research I', 'code' => 'BTE698', 'description' => 'Independent research project under faculty supervision - Phase I.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'MS'],
            ['name' => 'MS Thesis Research II', 'code' => 'BTE699', 'description' => 'Completion and defense of MS thesis - Phase II.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'MS'],
            ['name' => 'Cancer Biotechnology', 'code' => 'BTE701', 'description' => 'Molecular mechanisms of cancer and development of targeted therapies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            ['name' => 'Virology and Viral Biotechnology', 'code' => 'BTE702', 'description' => 'Molecular biology of viruses and their use as vectors and therapeutic agents.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            ['name' => 'Metabolic Engineering and Synthetic Biology', 'code' => 'BTE703', 'description' => 'Design and construction of novel biological pathways and organisms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            ['name' => 'Bioethics and Intellectual Property Rights', 'code' => 'BTE704', 'description' => 'Advanced topics in bioethics, patent law, and technology transfer.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'MS'],
            ['name' => 'Advanced Diagnostic Techniques', 'code' => 'BTE705', 'description' => 'Development and application of next-generation diagnostic tools.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            ['name' => 'Marine Biotechnology', 'code' => 'BTE706', 'description' => 'Exploitation of marine resources for novel products and processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // --- PhD Program Subjects ---
            ['name' => 'Advanced Topics in Biotechnology', 'code' => 'BTE801', 'description' => 'Critical review of cutting-edge research in selected areas of biotechnology.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            ['name' => 'Advanced Research Methodology', 'code' => 'BTE802', 'description' => 'Advanced experimental design, grant writing, and scientific communication for doctoral students.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            ['name' => 'Seminar in Biotechnology Research', 'code' => 'BTE803', 'description' => 'Presentation and discussion of doctoral research progress and current literature.', 'credit_hours' => '1-3', 'category' => 'Core', 'program_level' => 'PhD'],
            ['name' => 'Advanced Systems Biology', 'code' => 'BTE810', 'description' => 'Modeling and analysis of complex biological networks and systems.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Advanced Synthetic Biology', 'code' => 'BTE811', 'description' => 'Design and synthesis of artificial biological systems and organisms.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Advanced Topics in Gene Therapy', 'code' => 'BTE812', 'description' => 'Development of viral and non-viral vectors for therapeutic gene delivery.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Advanced Topics in Cancer Biotechnology', 'code' => 'BTE813', 'description' => 'Novel approaches for cancer diagnosis, targeted therapy, and immunotherapy.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Advanced Topics in Bioremediation', 'code' => 'BTE814', 'description' => 'Genomic and metagenomic approaches for enhancing bioremediation processes.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Advanced Topics in Pharmaceutical Biotechnology', 'code' => 'BTE815', 'description' => 'Development of next-generation biopharmaceuticals, including antibodies and vaccines.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            ['name' => 'Directed Studies in Biotechnology', 'code' => 'BTE901', 'description' => 'In-depth study of a specialized topic under the supervision of a faculty member.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            ['name' => 'PhD Qualifying Examination', 'code' => 'BTE990', 'description' => 'Comprehensive examination of the student\'s knowledge in their field of study.', 'credit_hours' => '3', 'category' => 'Milestone', 'program_level' => 'PhD'],
            ['name' => 'Doctoral Dissertation Proposal', 'code' => 'BTE991', 'description' => 'Development and defense of the doctoral research proposal.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'PhD'],
            ['name' => 'Doctoral Dissertation Research I', 'code' => 'BTE998', 'description' => 'Independent doctoral research leading to the dissertation.', 'credit_hours' => '9', 'category' => 'Research', 'program_level' => 'PhD'],
            ['name' => 'Doctoral Dissertation Research II', 'code' => 'BTE999', 'description' => 'Continuation of doctoral research, dissertation writing, and defense.', 'credit_hours' => '9', 'category' => 'Research', 'program_level' => 'PhD'],
            ['name' => 'Teaching Practicum in Biotechnology', 'code' => 'BTE950', 'description' => 'Supervised teaching experience in an undergraduate biotechnology course.', 'credit_hours' => '1-3', 'category' => 'Elective', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Biotechnology';

        $programSemesters = [
            'BS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 8, 'program_ids' => [], 'degree_level_id' => 1],
            'MS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 2],
            'PhD' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 4],
        ];

        $subSeederHelper = new SubjectSeederHelper();

        $info = $subSeederHelper->RunSeeder($department, $subjects, $programSemesters);

        switch ($info['type']) {
            case 'info':
                $this->command->info('  ' . $info['message']);
                // Log::info($info['message']);
                break;
            case 'warn':
                $this->command->warn('  ' . $info['message']);
                // Log::warning($info['message']);
                break;
            case 'error':
                $this->command->error('  ' . $info['message']);
                Log::error($info['message']);
                break;
            default:
                $this->command->error('  Unknown message type: ' . $info['type']);
                Log::error('  Unknown message type: ' . $info['type']);
                break;
        }
    }
}