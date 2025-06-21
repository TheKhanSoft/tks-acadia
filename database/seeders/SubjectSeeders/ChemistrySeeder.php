<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ChemistrySeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Chemistry
            [ 'name' => 'General Chemistry I', 'code' => 'CHM101', 'description' => 'Fundamental principles of chemistry: atomic structure, bonding, stoichiometry, states of matter, and solutions.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'General Chemistry Laboratory I', 'code' => 'CHM101L', 'description' => 'Practical experiments illustrating concepts from General Chemistry I.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'General Chemistry II', 'code' => 'CHM102', 'description' => 'Continuation of General Chemistry: thermodynamics, kinetics, equilibrium, electrochemistry, and descriptive chemistry.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'General Chemistry Laboratory II', 'code' => 'CHM102L', 'description' => 'Practical experiments illustrating concepts from General Chemistry II.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Organic Chemistry I', 'code' => 'CHM201', 'description' => 'Structure, bonding, nomenclature, properties, and reactions of alkanes, alkenes, alkynes, and alkyl halides.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Organic Chemistry Laboratory I', 'code' => 'CHM201L', 'description' => 'Basic techniques in organic chemistry: separation, purification, and synthesis.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Organic Chemistry II', 'code' => 'CHM202', 'description' => 'Continuation of Organic Chemistry: alcohols, ethers, aldehydes, ketones, carboxylic acids, amines, and spectroscopy.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Organic Chemistry Laboratory II', 'code' => 'CHM202L', 'description' => 'Advanced techniques in organic synthesis and qualitative organic analysis.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physical Chemistry I: Thermodynamics and Kinetics', 'code' => 'CHM301', 'description' => 'Principles of chemical thermodynamics, thermochemistry, chemical kinetics, and reaction mechanisms.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physical Chemistry Laboratory I', 'code' => 'CHM301L', 'description' => 'Experiments in chemical thermodynamics and kinetics.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physical Chemistry II: Quantum Chemistry and Spectroscopy', 'code' => 'CHM302', 'description' => 'Introduction to quantum mechanics, atomic and molecular structure, and spectroscopic methods.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physical Chemistry Laboratory II', 'code' => 'CHM302L', 'description' => 'Experiments in quantum chemistry and spectroscopy.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Inorganic Chemistry I: Principles and Main Group Elements', 'code' => 'CHM303', 'description' => 'Atomic structure, bonding theories, periodicity, and chemistry of main group elements.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Inorganic Chemistry Laboratory', 'code' => 'CHM303L', 'description' => 'Synthesis and characterization of inorganic compounds.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Inorganic Chemistry II: Transition Metals and Coordination Chemistry', 'code' => 'CHM401', 'description' => 'Chemistry of transition metals, coordination compounds, bonding, structure, and reactivity.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Analytical Chemistry I: Quantitative Analysis', 'code' => 'CHM210', 'description' => 'Principles of gravimetric, volumetric, and introductory instrumental methods of analysis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Analytical Chemistry Laboratory I', 'code' => 'CHM210L', 'description' => 'Practical quantitative chemical analysis techniques.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Analytical Chemistry II: Instrumental Analysis', 'code' => 'CHM310', 'description' => 'Principles and applications of instrumental methods: spectroscopy, chromatography, and electrochemistry.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Analytical Chemistry Laboratory II', 'code' => 'CHM310L', 'description' => 'Hands-on experience with modern analytical instruments.', 'credit_hours' => '0+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Biochemistry for Chemists', 'code' => 'CHM402', 'description' => 'Structure, function, and metabolism of biomolecules from a chemical perspective.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Spectroscopic Identification of Organic Compounds', 'code' => 'CHM403', 'description' => 'Application of NMR, IR, UV-Vis, and Mass Spectrometry for structure elucidation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Chemical Literature and Scientific Writing', 'code' => 'CHM497', 'description' => 'Accessing, evaluating, and communicating chemical information; scientific writing skills.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Research Methodology in Chemistry', 'code' => 'CHM498', 'description' => 'Experimental design, data analysis, safety, and ethics in chemical research.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Chemistry Seminar', 'code' => 'CHM496', 'description' => 'Presentation and discussion of current research topics in chemistry.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project I (Chemistry)', 'code' => 'CHM499A', 'description' => 'Literature survey and development of a research proposal for an independent chemistry project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Final Year Research Project II (Chemistry)', 'code' => 'CHM499B', 'description' => 'Execution, analysis, and presentation of the independent research project in chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Organic Chemistry: Reaction Mechanisms', 'code' => 'CHM501', 'description' => 'In-depth study of organic reaction mechanisms, stereochemistry, and reactive intermediates.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Organic Synthesis', 'code' => 'CHM502', 'description' => 'Modern synthetic methods, strategies, and retrosynthetic analysis in organic chemistry.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Polymer Chemistry', 'code' => 'CHM503', 'description' => 'Synthesis, characterization, properties, and applications of polymers.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Organometallic Chemistry', 'code' => 'CHM504', 'description' => 'Chemistry of compounds containing metal-carbon bonds, their structure, bonding, and reactivity.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Inorganic Chemistry: Bioinorganic Chemistry', 'code' => 'CHM505', 'description' => 'Role of metal ions in biological systems, metalloenzymes, and medicinal inorganic chemistry.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Solid State Chemistry', 'code' => 'CHM506', 'description' => 'Structure, bonding, properties, and synthesis of solid materials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Physical Chemistry: Statistical Thermodynamics', 'code' => 'CHM507', 'description' => 'Application of statistical mechanics to calculate thermodynamic properties of chemical systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Computational Chemistry', 'code' => 'CHM508', 'description' => 'Application of computational methods to study molecular structure, properties, and reactivity.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Chemistry', 'code' => 'CHM509', 'description' => 'Chemical processes in the environment, pollution, and remediation technologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Industrial Chemistry and Chemical Process Technology', 'code' => 'CHM510', 'description' => 'Principles of chemical process industries, unit operations, and reactor design.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Medicinal Chemistry', 'code' => 'CHM511', 'description' => 'Design, synthesis, and development of pharmaceutical agents.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Natural Products Chemistry', 'code' => 'CHM512', 'description' => 'Isolation, structure elucidation, synthesis, and biosynthesis of natural compounds.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Surface Chemistry and Catalysis', 'code' => 'CHM513', 'description' => 'Phenomena at surfaces and interfaces, and principles of homogeneous and heterogeneous catalysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Photochemistry', 'code' => 'CHM514', 'description' => 'Interaction of light with matter, photochemical reactions, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nuclear and Radiochemistry', 'code' => 'CHM515', 'description' => 'Principles of radioactivity, nuclear reactions, and applications of radioisotopes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Food Chemistry', 'code' => 'CHM516', 'description' => 'Chemical composition of foods, reactions during processing and storage, and food analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Green Chemistry and Sustainable Processes', 'code' => 'CHM517', 'description' => 'Principles of designing environmentally benign chemical products and processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Supramolecular Chemistry', 'code' => 'CHM518', 'description' => 'Chemistry beyond the molecule, focusing on non-covalent interactions and self-assembly.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Electroanalytical Chemistry', 'code' => 'CHM519', 'description' => 'Advanced electrochemical methods for chemical analysis.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Chromatographic Techniques', 'code' => 'CHM520', 'description' => 'Theory and practice of various chromatographic separation methods (GC, HPLC, etc.).', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quality Control and Assurance in Chemical Industry', 'code' => 'CHM521', 'description' => 'Statistical methods and procedures for quality management in chemical laboratories and industries.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Materials Chemistry', 'code' => 'CHM522', 'description' => 'Synthesis, characterization, and applications of advanced materials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Heterocyclic Chemistry', 'code' => 'CHM523', 'description' => 'Chemistry of cyclic compounds containing heteroatoms, their synthesis and reactions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Chemical Safety and Laboratory Management', 'code' => 'CHM524', 'description' => 'Safe handling of chemicals, waste disposal, and laboratory management practices.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nanochemistry and Nanomaterials', 'code' => 'CHM525', 'description' => 'Synthesis, properties, and applications of nanoscale materials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Level Subjects for Chemistry
            [ 'name' => 'Advanced Organic Chemistry', 'code' => 'CHM601', 'description' => 'In-depth study of reaction mechanisms, pericyclic reactions, and photochemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Inorganic Chemistry', 'code' => 'CHM602', 'description' => 'Symmetry, group theory, and advanced bonding theories in inorganic chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Physical Chemistry', 'code' => 'CHM603', 'description' => 'Advanced chemical kinetics, catalysis, and surface chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Analytical Chemistry', 'code' => 'CHM604', 'description' => 'Advanced separation techniques, mass spectrometry, and electroanalytical methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Quantum Chemistry', 'code' => 'CHM605', 'description' => 'Principles of quantum mechanics applied to chemical systems, including atomic and molecular structure.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Chemical Thermodynamics and Statistical Mechanics', 'code' => 'CHM606', 'description' => 'Classical and statistical thermodynamics of chemical systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Spectroscopic Techniques', 'code' => 'CHM607', 'description' => 'Advanced NMR, EPR, and other spectroscopic methods for structure elucidation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology in Chemistry', 'code' => 'CHM608', 'description' => 'Experimental design, data analysis, literature survey, and scientific writing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Modern Organic Synthesis', 'code' => 'CHM701', 'description' => 'Strategies and methodologies in modern organic synthesis, including asymmetric synthesis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Organometallic and Bioinorganic Chemistry', 'code' => 'CHM702', 'description' => 'Advanced topics in organometallic chemistry and the role of metals in biological systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Polymer Chemistry', 'code' => 'CHM703', 'description' => 'Kinetics and mechanisms of polymerization, polymer characterization, and special topics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Computational and Theoretical Chemistry', 'code' => 'CHM704', 'description' => 'Application of computational methods (ab initio, DFT) to chemical problems.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Materials Chemistry', 'code' => 'CHM705', 'description' => 'Design, synthesis, and characterization of advanced functional materials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Environmental Chemistry', 'code' => 'CHM706', 'description' => 'Atmospheric chemistry, aquatic chemistry, and soil chemistry in detail.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Medicinal Chemistry', 'code' => 'CHM707', 'description' => 'Drug design, SAR, and mechanism of action of various classes of drugs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Chemistry I', 'code' => 'CHM691', 'description' => 'Presentation of a literature review on a current topic in chemistry.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Chemistry II', 'code' => 'CHM692', 'description' => 'Presentation of research proposal or preliminary research findings.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research I', 'code' => 'CHM699A', 'description' => 'Independent research project under faculty supervision.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'MS/MPhil Thesis Research II', 'code' => 'CHM699B', 'description' => 'Continuation of research, data analysis, and thesis writing.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Supramolecular and Nanochemistry', 'code' => 'CHM708', 'description' => 'Principles of molecular recognition, self-assembly, and the chemistry of nanomaterials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Photochemistry and Laser Chemistry', 'code' => 'CHM709', 'description' => 'Principles of photochemical reactions and the application of lasers in chemistry.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Heterocyclic Chemistry', 'code' => 'CHM710', 'description' => 'Synthesis and reactivity of complex heterocyclic systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Chemical Kinetics and Reaction Dynamics', 'code' => 'CHM711', 'description' => 'Theoretical and experimental study of reaction rates and molecular dynamics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Industrial Catalysis', 'code' => 'CHM712', 'description' => 'Principles and applications of homogeneous and heterogeneous catalysis in industry.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Chemistry of Natural Products', 'code' => 'CHM713', 'description' => 'Advanced topics in the isolation, structure elucidation, and synthesis of natural products.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // PhD Level Subjects for Chemistry
            [ 'name' => 'Current Topics in Organic Chemistry', 'code' => 'CHM801', 'description' => 'Seminar-based course on recent literature and breakthroughs in organic chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Current Topics in Inorganic Chemistry', 'code' => 'CHM802', 'description' => 'Seminar-based course on recent literature and breakthroughs in inorganic chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Current Topics in Physical Chemistry', 'code' => 'CHM803', 'description' => 'Seminar-based course on recent literature and breakthroughs in physical chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Current Topics in Analytical Chemistry', 'code' => 'CHM804', 'description' => 'Seminar-based course on recent literature and breakthroughs in analytical chemistry.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Research Methodology and Ethics', 'code' => 'CHM805', 'description' => 'Advanced experimental design, grant writing, and ethical conduct in research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Synthetic Chemistry', 'code' => 'CHM901', 'description' => 'Specialized topics in total synthesis, asymmetric catalysis, and green synthesis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Materials and Nanoscience', 'code' => 'CHM902', 'description' => 'Cutting-edge research in functional materials, nanotechnology, and surface science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Chemical Biology', 'code' => 'CHM903', 'description' => 'Interface of chemistry and biology, including chemical genetics and proteomics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Theoretical and Computational Chemistry', 'code' => 'CHM904', 'description' => 'Advanced methods and applications in computational chemistry, including QM/MM and MD simulations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Studies in Chemistry', 'code' => 'CHM991', 'description' => 'In-depth study of a specialized area under the supervision of a faculty member.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar I: Proposal Defense', 'code' => 'CHM992', 'description' => 'Development and public defense of the doctoral research proposal.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar II: Research Update', 'code' => 'CHM993', 'description' => 'Presentation of significant research progress to the department.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar III: Final Presentation', 'code' => 'CHM994', 'description' => 'Public seminar on the complete body of doctoral research.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'CHM999', 'description' => 'Original and significant research in chemistry culminating in a doctoral dissertation.', 'credit_hours' => '12', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching and Mentoring in Chemistry', 'code' => 'CHM806', 'description' => 'Practicum in teaching undergraduate chemistry, including course design and mentoring.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Chemistry';    
         
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
