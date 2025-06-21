<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\DegreeLevel;
use App\Models\Office;
use App\Models\Program;
use App\Models\Subject;
use App\Models\ProgramSubject;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class PharmacySeeder extends Seeder
{
    public function run()
    {
        $subjects = 
        [
            // --- PharmD Program Subjects ---
            [ 'name' => 'Introduction to Pharmacy', 'code' => 'PHARM100', 'description' => 'Overview of pharmacy profession, pharmaceutical sciences, and healthcare systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Chemistry I', 'code' => 'PHARM101', 'description' => 'Basic principles of organic chemistry and drug structure-activity relationships.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Human Anatomy and Physiology I', 'code' => 'PHARM102', 'description' => 'Structure and function of human body systems including cardiovascular and respiratory systems.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Medical Terminology', 'code' => 'PHARM103', 'description' => 'Medical vocabulary, prefixes, suffixes, and terminology used in healthcare.', 'credit_hours' => '2', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Mathematics for Pharmacy', 'code' => 'MATH111', 'description' => 'Mathematical concepts and calculations essential for pharmaceutical practice.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Communication Skills', 'code' => 'ENG101', 'description' => 'Developing effective written and oral communication skills for healthcare professionals.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Pakistan Studies', 'code' => 'HUM101', 'description' => 'A study of the history, culture, and political landscape of Pakistan.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Islamic Studies', 'code' => 'HUM102', 'description' => 'An introduction to the fundamental principles and teachings of Islam.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Chemistry II', 'code' => 'PHARM201', 'description' => 'Advanced organic chemistry concepts and drug synthesis pathways.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Human Anatomy and Physiology II', 'code' => 'PHARM202', 'description' => 'Advanced study of nervous, endocrine, and reproductive systems.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacognosy I', 'code' => 'PHARM203', 'description' => 'Study of natural products and their pharmaceutical applications.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Microbiology and Immunology', 'code' => 'PHARM204', 'description' => 'Basic principles of microbiology and immune system functions.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Biochemistry', 'code' => 'PHARM205', 'description' => 'Biochemical processes and metabolic pathways in living organisms.', 'credit_hours' => '3+1', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutics I', 'code' => 'PHARM301', 'description' => 'Physical pharmacy principles and dosage form design.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacology I', 'code' => 'PHARM302', 'description' => 'Basic principles of drug action, absorption, distribution, metabolism, and excretion.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pathophysiology', 'code' => 'PHARM303', 'description' => 'Study of disease processes and their effects on normal physiological functions.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacognosy II', 'code' => 'PHARM304', 'description' => 'Advanced study of medicinal plants and their bioactive compounds.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Analysis', 'code' => 'PHARM305', 'description' => 'Analytical methods for drug identification and quantification.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutics II', 'code' => 'PHARM401', 'description' => 'Advanced pharmaceutical formulations and drug delivery systems.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacology II', 'code' => 'PHARM402', 'description' => 'Advanced pharmacology including drug interactions and adverse effects.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Pharmacy I', 'code' => 'PHARM403', 'description' => 'Introduction to pharmaceutical care and patient counseling.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Biotechnology', 'code' => 'PHARM404', 'description' => 'Biotechnology applications in drug development and production.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacokinetics and Biopharmaceutics', 'code' => 'PHARM405', 'description' => 'Drug absorption, distribution, metabolism, and elimination kinetics.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Pharmacy II', 'code' => 'PHARM501', 'description' => 'Advanced clinical pharmacy practice and therapeutic drug monitoring.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Hospital and Clinical Pharmacy', 'code' => 'PHARM502', 'description' => 'Hospital pharmacy operations and clinical pharmacy services.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Jurisprudence', 'code' => 'PHARM503', 'description' => 'Pharmacy laws, regulations, and professional ethics.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacoeconomics', 'code' => 'PHARM504', 'description' => 'Economic evaluation of pharmaceutical interventions and healthcare costs.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Community Pharmacy Practice', 'code' => 'PHARM505', 'description' => 'Community pharmacy operations and patient care services.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Rotations I', 'code' => 'PHARM590', 'description' => 'Supervised clinical practice in hospital and community settings.', 'credit_hours' => '6', 'category' => 'Clinical', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Rotations II', 'code' => 'PHARM591', 'description' => 'Advanced clinical rotations in specialized pharmacy practice areas.', 'credit_hours' => '6', 'category' => 'Clinical', 'program_level' => 'PharmD'],
            [ 'name' => 'Research Project', 'code' => 'PHARM599', 'description' => 'Independent research project in pharmaceutical sciences.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'PharmD'],
            [ 'name' => 'Toxicology', 'code' => 'PHARM406', 'description' => 'Study of adverse effects of chemicals on living organisms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Cosmeceuticals', 'code' => 'PHARM407', 'description' => 'Formulation and evaluation of cosmetic and dermatological products.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Veterinary Pharmacy', 'code' => 'PHARM408', 'description' => 'Pharmaceutical care for animals and veterinary drug regulations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Nuclear Pharmacy', 'code' => 'PHARM409', 'description' => 'Preparation and use of radioactive materials in diagnosis and therapy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Drug Information and Literature Evaluation', 'code' => 'PHARM410', 'description' => 'Skills for retrieving, evaluating, and disseminating drug information.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Public Health Pharmacy', 'code' => 'PHARM506', 'description' => 'Role of pharmacists in public health initiatives and disease prevention.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Geriatric Pharmacy', 'code' => 'PHARM507', 'description' => 'Pharmacotherapy considerations in elderly patients.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Pediatric Pharmacy', 'code' => 'PHARM508', 'description' => 'Pharmacotherapy considerations in pediatric patients.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Oncology Pharmacy', 'code' => 'PHARM509', 'description' => 'Pharmaceutical care for cancer patients.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Infectious Diseases Pharmacotherapy', 'code' => 'PHARM510', 'description' => 'Management of infectious diseases with antimicrobial agents.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Cardiovascular Pharmacotherapy', 'code' => 'PHARM511', 'description' => 'Management of cardiovascular diseases.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Endocrine Pharmacotherapy', 'code' => 'PHARM512', 'description' => 'Management of endocrine disorders like diabetes and thyroid diseases.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Renal Pharmacotherapy', 'code' => 'PHARM513', 'description' => 'Management of kidney diseases and drug dosing in renal impairment.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pulmonary Pharmacotherapy', 'code' => 'PHARM514', 'description' => 'Management of respiratory diseases like asthma and COPD.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Neurology and Psychiatry Pharmacotherapy', 'code' => 'PHARM515', 'description' => 'Management of neurological and psychiatric disorders.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Gastrointestinal Pharmacotherapy', 'code' => 'PHARM516', 'description' => 'Management of gastrointestinal disorders.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Rheumatology and Immunology Pharmacotherapy', 'code' => 'PHARM517', 'description' => 'Management of autoimmune and rheumatic diseases.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Dermatology Pharmacotherapy', 'code' => 'PHARM518', 'description' => 'Management of skin disorders.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Nutrition Support Pharmacy', 'code' => 'PHARM519', 'description' => 'Parenteral and enteral nutrition support.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Pain Management', 'code' => 'PHARM520', 'description' => 'Pharmacological and non-pharmacological management of pain.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacy Administration and Management', 'code' => 'PHARM521', 'description' => 'Principles of management applied to pharmacy practice.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Industrial Pharmacy', 'code' => 'PHARM411', 'description' => 'Pharmaceutical manufacturing processes and quality control.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Herbal Medicine', 'code' => 'PHARM412', 'description' => 'Scientific study of herbal remedies and their use in healthcare.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Forensic Pharmacy', 'code' => 'PHARM522', 'description' => 'Application of pharmaceutical sciences in legal investigations.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Advanced Dispensing Pharmacy', 'code' => 'PHARM306', 'description' => 'Compounding and dispensing of specialized pharmaceutical preparations.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Biostatistics', 'code' => 'STAT202', 'description' => 'Statistical methods for analyzing biological and health data.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'PharmD'],
            [ 'name' => 'Health Informatics', 'code' => 'PHARM413', 'description' => 'Use of information technology in healthcare and pharmacy.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacogenomics', 'code' => 'PHARM523', 'description' => 'Study of how genes affect a person\'s response to drugs.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Critical Care Pharmacy', 'code' => 'PHARM524', 'description' => 'Pharmaceutical care for critically ill patients.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Ambulatory Care Pharmacy', 'code' => 'PHARM525', 'description' => 'Pharmaceutical care in outpatient settings.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Immunizations', 'code' => 'PHARM414', 'description' => 'Principles of vaccination and pharmacist\'s role in immunization.', 'credit_hours' => '1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Sterile Products Preparation', 'code' => 'PHARM415', 'description' => 'Aseptic techniques for preparing sterile pharmaceutical products.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacy Ethics and Professionalism', 'code' => 'PHARM104', 'description' => 'Ethical principles and professional conduct in pharmacy practice.', 'credit_hours' => '2', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Introduction to Psychology', 'code' => 'PSY101', 'description' => 'Basic principles of psychology and human behavior.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Sociology', 'code' => 'SOC101', 'description' => 'Study of social behavior, society, and social institutions.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'English Composition', 'code' => 'ENG102', 'description' => 'Advanced writing skills for academic and professional purposes.', 'credit_hours' => '3', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Introduction to Computers', 'code' => 'CS100', 'description' => 'Basic computer literacy and applications in healthcare.', 'credit_hours' => '2+1', 'category' => 'General', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmacotherapy of Special Populations', 'code' => 'PHARM526', 'description' => 'Drug therapy considerations in pregnant, lactating, and other special populations.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Drug Development Process', 'code' => 'PHARM416', 'description' => 'From drug discovery to market approval.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Toxicology', 'code' => 'PHARM527', 'description' => 'Management of poisoning and drug overdose.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Advanced Pharmacognosy', 'code' => 'PHARM417', 'description' => 'Advanced study of natural products and their therapeutic potential.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Pharmaceutical Marketing', 'code' => 'PHARM528', 'description' => 'Principles of marketing applied to pharmaceutical products.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Quality Control and Quality Assurance', 'code' => 'PHARM418', 'description' => 'Ensuring the quality, safety, and efficacy of pharmaceutical products.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Clinical Research', 'code' => 'PHARM529', 'description' => 'Design and conduct of clinical trials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PharmD'],
            [ 'name' => 'Patient Safety and Medication Error Reduction', 'code' => 'PHARM419', 'description' => 'Strategies to improve patient safety and reduce medication errors.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'PharmD'],
            [ 'name' => 'Introduction to Epidemiology', 'code' => 'EPI101', 'description' => 'Study of the distribution and determinants of health-related states.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'PharmD'],

            // --- MS/MPhil Program Subjects ---
            [ 'name' => 'Advanced Pharmaceutical Chemistry', 'code' => 'PHARM601', 'description' => 'Advanced concepts in medicinal chemistry and drug design.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmacology', 'code' => 'PHARM602', 'description' => 'Molecular mechanisms of drug action and advanced pharmacological concepts.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology in Pharmaceutical Sciences', 'code' => 'PHARM603', 'description' => 'Research design, statistical analysis, and scientific writing in pharmacy.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmaceutics', 'code' => 'PHARM604', 'description' => 'Novel drug delivery systems and advanced formulation techniques.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Clinical Pharmacokinetics', 'code' => 'PHARM605', 'description' => 'Applied pharmacokinetics in clinical practice and therapeutic drug monitoring.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Pharmaceutical Regulatory Affairs', 'code' => 'PHARM606', 'description' => 'Drug registration processes and regulatory requirements.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Pharmaceutical Quality Assurance', 'code' => 'PHARM607', 'description' => 'Quality control and quality assurance in pharmaceutical manufacturing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Clinical Pharmacy Practice', 'code' => 'PHARM608', 'description' => 'Evidence-based pharmacy practice and clinical decision making.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis I', 'code' => 'PHARM698', 'description' => 'Initial phase of MS thesis research in pharmaceutical sciences.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis II', 'code' => 'PHARM699', 'description' => 'Completion and defense of MS thesis.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmacognosy and Phytochemistry', 'code' => 'PHARM609', 'description' => 'Isolation, characterization, and biological evaluation of natural products.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Drug Metabolism and Toxicology', 'code' => 'PHARM610', 'description' => 'Advanced study of drug metabolism pathways and mechanisms of drug-induced toxicity.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Biopharmaceutics', 'code' => 'PHARM611', 'description' => 'Physicochemical and physiological factors affecting drug absorption and bioavailability.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Pharmaceutical Biotechnology and Genomics', 'code' => 'PHARM612', 'description' => 'Application of biotechnology and genomics in drug development and therapy.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmaceutical Analysis', 'code' => 'PHARM613', 'description' => 'Advanced analytical techniques for drug analysis and quality control.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Pharmacoeconomics and Outcomes Research', 'code' => 'PHARM614', 'description' => 'Economic evaluation of drug therapy and health outcomes research.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Hospital Pharmacy Management', 'code' => 'PHARM615', 'description' => 'Management of hospital pharmacy services and resources.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Community Pharmacy Management', 'code' => 'PHARM616', 'description' => 'Management of community pharmacy services and patient care.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Drug Information and Informatics', 'code' => 'PHARM617', 'description' => 'Advanced drug information retrieval, evaluation, and application of health informatics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmacotherapy I', 'code' => 'PHARM618', 'description' => 'In-depth management of cardiovascular and endocrine disorders.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmacotherapy II', 'code' => 'PHARM619', 'description' => 'In-depth management of infectious diseases and neurological disorders.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pharmacotherapy III', 'code' => 'PHARM620', 'description' => 'In-depth management of renal, pulmonary, and gastrointestinal disorders.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Pain Management', 'code' => 'PHARM621', 'description' => 'Comprehensive management of acute and chronic pain.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Toxicology', 'code' => 'PHARM622', 'description' => 'Mechanisms of toxicity and management of poisoning.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Pharmaceutical Sciences', 'code' => 'PHARM690', 'description' => 'Presentation and discussion of current research topics.', 'credit_hours' => '1', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Directed Studies in Pharmacy', 'code' => 'PHARM697', 'description' => 'Independent study on a specialized topic under faculty guidance.', 'credit_hours' => '1-3', 'category' => 'Elective', 'program_level' => 'MS'],

            // --- PhD Program Subjects ---
            [ 'name' => 'Advanced Topics in Pharmaceutical Sciences', 'code' => 'PHARM701', 'description' => 'Current research trends and emerging topics in pharmaceutical sciences.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Research Methodology', 'code' => 'PHARM702', 'description' => 'Advanced research design and methodology for doctoral studies in pharmacy.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Pharmaceutical Research', 'code' => 'PHARM703', 'description' => 'Presentation and discussion of current research in pharmaceutical sciences.', 'credit_hours' => '1-3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Drug Discovery and Development', 'code' => 'PHARM704', 'description' => 'Modern approaches to drug discovery, development, and clinical trials.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Molecular Pharmacology', 'code' => 'PHARM705', 'description' => 'Molecular mechanisms of drug action and receptor pharmacology.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Qualifying Examination Preparation', 'code' => 'PHARM790', 'description' => 'Guided study and preparation for doctoral qualifying examinations.', 'credit_hours' => '3', 'category' => 'Milestone', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research I', 'code' => 'PHARM798', 'description' => 'Initial phase of independent doctoral research leading to dissertation proposal.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research II', 'code' => 'PHARM799', 'description' => 'Continued independent doctoral research, dissertation writing, and defense.', 'credit_hours' => '6-12', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Pharmaceutics', 'code' => 'PHARM706', 'description' => 'Cutting-edge research in drug delivery systems and formulation science.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Pharmacology', 'code' => 'PHARM707', 'description' => 'Emerging areas of pharmacological research.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Medicinal Chemistry', 'code' => 'PHARM708', 'description' => 'Novel strategies in drug design and synthesis.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Pharmacognosy', 'code' => 'PHARM709', 'description' => 'Research on bioactive natural products and their development.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Clinical Pharmacy', 'code' => 'PHARM710', 'description' => 'Research in clinical pharmacy practice and patient outcomes.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching Practicum in Pharmacy', 'code' => 'PHARM795', 'description' => 'Supervised teaching experience in undergraduate pharmacy courses.', 'credit_hours' => '1-3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Research in Pharmacy', 'code' => 'PHARM797', 'description' => 'Independent research on a specialized topic under faculty guidance.', 'credit_hours' => '1-3', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Grant Writing for Pharmaceutical Research', 'code' => 'PHARM711', 'description' => 'Skills for preparing and submitting research grant proposals.', 'credit_hours' => '2', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Ethics in Pharmaceutical Research', 'code' => 'PHARM712', 'description' => 'Ethical considerations in conducting research involving human subjects and animals.', 'credit_hours' => '2', 'category' => 'Core', 'program_level' => 'PhD'],
        ];

        $departmentName = 'Department of Pharmacy';

        $programSemesters = [
            'PharmD' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 10, 'program_ids' => [], 'degree_level_id' => 5],
            'MS' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 2],
            'PhD' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 4],
        ];

        $subSeederHelper = new SubjectSeederHelper();
        
        $info = $subSeederHelper->RunSeeder($departmentName, $subjects, $programSemesters);

        switch ($info['type']) {
            case 'info':
                $this->command->info( '  ' . $info['message'] );
                break;
            case 'warn':
                $this->command->warn( '  ' . $info['message'] );
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
