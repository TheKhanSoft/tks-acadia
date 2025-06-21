<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use App\Helpers\SubjectSeederHelper;
use Illuminate\Support\Facades\Log;

class EconomicsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            [ 'name' => 'Principles of Economics', 'code' => 'ECON101', 'description' => 'Introduction to basic economic concepts, supply and demand, and market mechanisms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Microeconomics', 'code' => 'ECON201', 'description' => 'Study of individual economic units, consumer behavior, and firm theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Macroeconomics', 'code' => 'ECON202', 'description' => 'Analysis of aggregate economic variables, national income, and economic policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Statistics for Economics', 'code' => 'ECON203', 'description' => 'Statistical methods and techniques applied to economic data analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Mathematical Economics', 'code' => 'ECON301', 'description' => 'Application of mathematical tools and techniques to economic analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Econometrics', 'code' => 'ECON302', 'description' => 'Statistical methods for testing economic theories and forecasting.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Development Economics', 'code' => 'ECON303', 'description' => 'Economic development theories, poverty, and growth in developing countries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'International Economics', 'code' => 'ECON304', 'description' => 'International trade theory, balance of payments, and exchange rates.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Public Economics', 'code' => 'ECON305', 'description' => 'Government role in economy, public goods, taxation, and public expenditure.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Monetary Economics', 'code' => 'ECON306', 'description' => 'Money, banking system, monetary policy, and financial markets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Financial Economics', 'code' => 'ECON307', 'description' => 'Financial markets, investment theory, and corporate finance principles.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'History of Economic Thought', 'code' => 'ECON308', 'description' => 'Evolution of economic ideas and schools of thought.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Managerial Economics', 'code' => 'ECON310', 'description' => 'Application of economic theory to business decision-making.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Labor Economics', 'code' => 'ECON401', 'description' => 'Labor market analysis, employment, wages, and human resource economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Environmental Economics', 'code' => 'ECON403', 'description' => 'Economic analysis of environmental issues and natural resource management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Pakistan Economy', 'code' => 'ECON404', 'description' => 'Structure and performance of Pakistani economy, economic policies and challenges.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Behavioral Economics', 'code' => 'ECON409', 'description' => 'Psychological factors in economic decision-making and market behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Agricultural Economics', 'code' => 'ECON410', 'description' => 'Economic analysis of agricultural production, food security, and rural development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Energy Economics', 'code' => 'ECON411', 'description' => 'Economics of energy markets, renewable energy, and environmental policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Urban Economics', 'code' => 'ECON412', 'description' => 'Economic analysis of urbanization, city structure, and urban policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Islamic Economics and Finance', 'code' => 'ECON413', 'description' => 'Principles of Islamic economic system and Sharia-compliant finance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Industrial Economics', 'code' => 'ECON414', 'description' => 'Market structures, firm behavior, and industrial policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Health Economics', 'code' => 'ECON415', 'description' => 'Economic aspects of health care, health policy, and health systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Welfare Economics', 'code' => 'ECON416', 'description' => 'Social welfare theory, income distribution, and poverty measurement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economic Planning', 'code' => 'ECON417', 'description' => 'National economic planning, development strategies, and policy implementation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Regional Economics', 'code' => 'ECON418', 'description' => 'Regional development, spatial economics, and policy interventions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economic Forecasting', 'code' => 'ECON419', 'description' => 'Time series analysis, forecasting methods, and economic prediction models.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Population Economics', 'code' => 'ECON420', 'description' => 'Demographic trends, population growth, and economic implications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Gender and Development Economics', 'code' => 'ECON421', 'description' => 'Gender issues in economic development and policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Transport Economics', 'code' => 'ECON422', 'description' => 'Economics of transportation systems and infrastructure.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Resource Economics', 'code' => 'ECON423', 'description' => 'Management and allocation of natural resources.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economic Data Analysis', 'code' => 'ECON424', 'description' => 'Statistical software applications, data visualization, and empirical analysis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economic Policy Analysis', 'code' => 'ECON425', 'description' => 'Policy evaluation methods, cost-benefit analysis, and impact assessment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Game Theory for Economists', 'code' => 'ECON426', 'description' => 'Strategic decision-making in interactive situations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'International Finance', 'code' => 'ECON427', 'description' => 'Global financial markets, foreign exchange, and international investment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Political Economy', 'code' => 'ECON428', 'description' => 'Interaction between political processes and economic outcomes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Experimental Economics', 'code' => 'ECON429', 'description' => 'Using laboratory experiments to test economic theories.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economics of Innovation', 'code' => 'ECON430', 'description' => 'Economic analysis of technological change and innovation processes.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Public Choice Theory', 'code' => 'ECON431', 'description' => 'Economic analysis of political decision-making and institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Comparative Economic Systems', 'code' => 'ECON434', 'description' => 'Analysis of different economic systems and their performance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Advanced Microeconomics', 'code' => 'ECON435', 'description' => 'In-depth study of microeconomic theory and applications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Advanced Macroeconomics', 'code' => 'ECON436', 'description' => 'Advanced topics in macroeconomic theory and policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Time Series Econometrics', 'code' => 'ECON437', 'description' => 'Econometric modeling of time series data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Panel Data Econometrics', 'code' => 'ECON438', 'description' => 'Econometric techniques for analyzing panel or longitudinal data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economics of Poverty and Inequality', 'code' => 'ECON439', 'description' => 'Analysis of causes and consequences of poverty and income inequality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'International Trade Policy', 'code' => 'ECON440', 'description' => 'Analysis of trade policies, agreements, and their economic impact.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Financial Econometrics', 'code' => 'ECON441', 'description' => 'Econometric methods applied to financial market data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economics of Climate Change', 'code' => 'ECON442', 'description' => 'Economic analysis of climate change impacts and mitigation policies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Real Estate Economics', 'code' => 'ECON443', 'description' => 'Economic principles applied to real estate markets and investment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economics of Regulation', 'code' => 'ECON444', 'description' => 'Economic analysis of government regulation of industries.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Research Methods in Economics', 'code' => 'ECON497', 'description' => 'Research methodology, proposal writing, and data collection techniques.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS', ],
            [ 'name' => 'Economics Research Project', 'code' => 'ECON499', 'description' => 'Independent research project in economics under faculty supervision.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'BS', ],
           
            // MS/MPhil Subjects
            [ 'name' => 'Advanced Microeconomic Theory', 'code' => 'ECON501', 'description' => 'Rigorous study of consumer and producer theory, general equilibrium, and welfare economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Macroeconomic Theory', 'code' => 'ECON502', 'description' => 'In-depth analysis of macroeconomic models, economic growth, and business cycles.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Econometrics', 'code' => 'ECON503', 'description' => 'Advanced econometric techniques, including time series, panel data, and limited dependent variable models.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Mathematical Methods for Economics', 'code' => 'ECON504', 'description' => 'Advanced mathematical tools for economic analysis, including optimization and dynamic analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Research Methodology', 'code' => 'ECON505', 'description' => 'Advanced research design, methodology, and academic writing in economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Development Policy', 'code' => 'ECON510', 'description' => 'Analysis of development policies, foreign aid, and institutional reforms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'International Trade and Finance', 'code' => 'ECON511', 'description' => 'Advanced topics in international trade theory, policy, and global financial markets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Public Finance and Fiscal Policy', 'code' => 'ECON512', 'description' => 'Advanced study of public expenditure, taxation, and fiscal policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Monetary Theory and Policy', 'code' => 'ECON513', 'description' => 'Advanced analysis of monetary theory, central banking, and monetary policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Applied Econometrics', 'code' => 'ECON514', 'description' => 'Practical application of econometric methods to real-world economic problems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Labor Economics and Industrial Relations', 'code' => 'ECON515', 'description' => 'Advanced topics in labor markets, wage determination, and industrial relations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Environmental and Resource Economics', 'code' => 'ECON516', 'description' => 'Economic analysis of environmental policy and natural resource management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Financial Markets and Institutions', 'code' => 'ECON517', 'description' => 'Advanced study of financial systems, risk management, and financial regulation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Health Economics and Policy', 'code' => 'ECON518', 'description' => 'Economic analysis of health care systems, insurance, and public health policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Economics of Growth and Development', 'code' => 'ECON519', 'description' => 'Theories and empirical analysis of economic growth and development.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Project Appraisal and Investment Analysis', 'code' => 'ECON520', 'description' => 'Techniques for project evaluation, cost-benefit analysis, and investment decisions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Microeconometrics', 'code' => 'ECON521', 'description' => 'Econometric methods for microeconomic data analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Macroeconometrics', 'code' => 'ECON522', 'description' => 'Econometric modeling of macroeconomic time series data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Game Theory and Economic Applications', 'code' => 'ECON523', 'description' => 'Advanced game theory with applications to economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Economics of Information', 'code' => 'ECON524', 'description' => 'Analysis of markets with asymmetric information and uncertainty.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Industrial Organization and Policy', 'code' => 'ECON525', 'description' => 'Advanced study of market structure, firm strategy, and regulation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Behavioral and Experimental Economics', 'code' => 'ECON526', 'description' => 'Advanced topics in behavioral economics and experimental methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Topics in Economic Theory', 'code' => 'ECON527', 'description' => 'Selected advanced topics in economic theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'MS Thesis', 'code' => 'ECON599', 'description' => 'Independent research thesis for MS in Economics.', 'credit_hours' => '6', 'category' => 'Major', 'program_level' => 'MS', ],
            
            // PhD Subjects
            [ 'name' => 'Advanced Microeconomic Analysis I', 'code' => 'ECON601', 'description' => 'PhD-level treatment of consumer and producer theory, and choice under uncertainty.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Macroeconomic Analysis I', 'code' => 'ECON602', 'description' => 'PhD-level analysis of dynamic macroeconomic models and economic growth.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Econometric Methods for PhD', 'code' => 'ECON603', 'description' => 'Advanced econometric theory and methods for doctoral research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Mathematical Economics', 'code' => 'ECON604', 'description' => 'Advanced mathematical techniques for economic theory and research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Microeconomic Analysis II', 'code' => 'ECON611', 'description' => 'PhD-level topics in game theory, information economics, and contract theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Macroeconomic Analysis II', 'code' => 'ECON612', 'description' => 'PhD-level topics in monetary economics, fiscal policy, and open-economy macroeconomics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Time Series Econometrics', 'code' => 'ECON613', 'description' => 'Advanced time series analysis for macroeconomic and financial data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Panel Data Econometrics', 'code' => 'ECON614', 'description' => 'Advanced econometric methods for panel and longitudinal data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Development Economics', 'code' => 'ECON710', 'description' => 'Doctoral seminar on current research in development economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in International Economics', 'code' => 'ECON711', 'description' => 'Doctoral seminar on current research in international trade and finance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Public Economics', 'code' => 'ECON712', 'description' => 'Doctoral seminar on current research in public finance and fiscal policy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Labor Economics', 'code' => 'ECON715', 'description' => 'Doctoral seminar on current research in labor economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Financial Economics', 'code' => 'ECON717', 'description' => 'Doctoral seminar on current research in financial economics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Econometrics', 'code' => 'ECON720', 'description' => 'Cutting-edge topics and research frontiers in econometrics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'ECON799', 'description' => 'Original research dissertation for PhD in Economics.', 'credit_hours' => '12', 'category' => 'Major', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Economics';

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
