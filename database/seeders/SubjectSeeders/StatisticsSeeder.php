<?php

namespace Database\Seeders\SubjectSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class StatisticsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // BS Statistics Subjects
            [ 'name' => 'Introduction to Statistics I', 'code' => 'STA101', 'description' => 'Descriptive statistics, data presentation, basic probability concepts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Statistics II', 'code' => 'STA102', 'description' => 'Probability distributions, sampling distributions, and introduction to inferential statistics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Calculus for Statistics I', 'code' => 'STA111', 'description' => 'Differential calculus with applications in statistics.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Calculus for Statistics II', 'code' => 'STA112', 'description' => 'Integral calculus and multivariable calculus with applications in statistics.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Probability Theory I', 'code' => 'STA201', 'description' => 'Axiomatic probability, random variables, discrete and continuous distributions, expectation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Probability Theory II', 'code' => 'STA202', 'description' => 'Joint distributions, conditional probability, limit theorems, and generating functions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Inference I', 'code' => 'STA301', 'description' => 'Point estimation, interval estimation, properties of estimators.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Inference II', 'code' => 'STA302', 'description' => 'Hypothesis testing, likelihood ratio tests, and introduction to Bayesian inference.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Linear Models and Regression Analysis', 'code' => 'STA303', 'description' => 'Simple and multiple linear regression, model diagnostics, and variable selection.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Design and Analysis of Experiments I', 'code' => 'STA304', 'description' => 'Completely randomized design, randomized complete block design, Latin square design.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Sampling Techniques I', 'code' => 'STA305', 'description' => 'Simple random sampling, stratified sampling, systematic sampling, and cluster sampling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Computing with R', 'code' => 'STA211', 'description' => 'Introduction to R programming for data manipulation, visualization, and statistical analysis.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Multivariate Analysis I', 'code' => 'STA401', 'description' => 'Multivariate normal distribution, principal component analysis, factor analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Time Series Analysis I', 'code' => 'STA402', 'description' => 'Stationary processes, ARMA models, model identification, and forecasting.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Nonparametric Statistics', 'code' => 'STA403', 'description' => 'Distribution-free tests, rank-based methods, and resampling techniques.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Categorical Data Analysis', 'code' => 'STA404', 'description' => 'Analysis of contingency tables, log-linear models, and logistic regression.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistics Research Project I', 'code' => 'STA498A', 'description' => 'Literature review, research design, and proposal for a statistics project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistics Research Project II', 'code' => 'STA498B', 'description' => 'Data collection, analysis, and presentation of the statistics research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Statistics', 'code' => 'STA311', 'description' => 'Theoretical foundations of statistical methods, sufficiency, completeness, and optimal tests.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Linear Algebra for Statistics', 'code' => 'STA212', 'description' => 'Matrices, vector spaces, eigenvalues, and their applications in statistics.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Quality Control', 'code' => 'STA312', 'description' => 'Control charts, process capability analysis, and acceptance sampling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Stochastic Processes', 'code' => 'STA411', 'description' => 'Markov chains, Poisson processes, and basic queuing theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Official Statistics', 'code' => 'STA213', 'description' => 'Sources, collection, and interpretation of official statistics (e.g., census, national surveys).', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Mining and Machine Learning for Statisticians', 'code' => 'STA412', 'description' => 'Introduction to data mining techniques and machine learning algorithms from a statistical perspective.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Actuarial Statistics I', 'code' => 'STA313', 'description' => 'Life contingencies, survival models, and basic actuarial mathematics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective Subjects for Statistics
            [ 'name' => 'Bayesian Statistics', 'code' => 'STA501', 'description' => 'Bayes\' theorem, prior and posterior distributions, Bayesian inference, and MCMC methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Survival Analysis', 'code' => 'STA502', 'description' => 'Analysis of time-to-event data, Kaplan-Meier estimation, and Cox proportional hazards models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Generalized Linear Models', 'code' => 'STA503', 'description' => 'Extension of linear models to non-normal data, including logistic and Poisson regression.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Design and Analysis of Experiments II', 'code' => 'STA504', 'description' => 'Factorial designs, fractional factorial designs, and response surface methodology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sampling Techniques II', 'code' => 'STA505', 'description' => 'Advanced sampling designs, ratio and regression estimation, and non-sampling errors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Multivariate Analysis II', 'code' => 'STA506', 'description' => 'Discriminant analysis, cluster analysis, canonical correlation, and MANOVA.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Time Series Analysis II', 'code' => 'STA507', 'description' => 'Non-stationary time series, ARIMA models, spectral analysis, and state-space models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Econometrics', 'code' => 'STA508', 'description' => 'Statistical methods for analyzing economic data, including time series and panel data models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biostatistics', 'code' => 'STA509', 'description' => 'Statistical methods applied to biological and health sciences, including clinical trials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Operations Research for Statisticians', 'code' => 'STA510', 'description' => 'Linear programming, queuing theory, and simulation with statistical applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Consulting', 'code' => 'STA511', 'description' => 'Practical aspects of statistical consulting, communication, and problem-solving.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Statistical Computing (e.g., Python, SAS)', 'code' => 'STA512', 'description' => 'Advanced programming for statistical analysis using software like Python or SAS.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Spatial Statistics', 'code' => 'STA513', 'description' => 'Analysis of geographically referenced data, geostatistics, and point processes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Robust Statistics', 'code' => 'STA514', 'description' => 'Statistical methods that are resistant to outliers and deviations from assumptions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Longitudinal Data Analysis', 'code' => 'STA515', 'description' => 'Methods for analyzing data collected repeatedly over time on the same subjects.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Financial Statistics and Risk Management', 'code' => 'STA516', 'description' => 'Statistical models for financial data, portfolio theory, and risk assessment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Statistics', 'code' => 'STA517', 'description' => 'Statistical methods for analyzing environmental data and assessing environmental impact.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Demographic Techniques', 'code' => 'STA518', 'description' => 'Methods for analyzing population data, life tables, and population projections.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Genetics', 'code' => 'STA519', 'description' => 'Statistical methods for analyzing genetic data, linkage analysis, and association studies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Actuarial Statistics II', 'code' => 'STA520', 'description' => 'Loss models, credibility theory, and advanced topics in actuarial science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Computational Statistics and Simulation', 'code' => 'STA521', 'description' => 'Monte Carlo methods, bootstrap, and other computationally intensive statistical techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Reliability Theory', 'code' => 'STA522', 'description' => 'Statistical models for system reliability, lifetime distributions, and maintenance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Process Control', 'code' => 'STA523', 'description' => 'Advanced control charts, multivariate process control, and Six Sigma methodologies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Survey Research Methods', 'code' => 'STA524', 'description' => 'Design, implementation, and analysis of sample surveys, including questionnaire design.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Learning Theory', 'code' => 'STA525', 'description' => 'Theoretical foundations of machine learning algorithms from a statistical perspective.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Big Data Analytics with Statistics', 'code' => 'STA526', 'description' => 'Statistical methods for handling and analyzing large-scale datasets.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Causal Inference in Statistics', 'code' => 'STA527', 'description' => 'Statistical methods for estimating causal effects from observational and experimental data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Functional Data Analysis', 'code' => 'STA528', 'description' => 'Statistical methods for analyzing data where observations are functions or curves.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Methods in Epidemiology', 'code' => 'STA529', 'description' => 'Design and analysis of epidemiological studies, measures of disease frequency and association.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Topics in Advanced Statistics', 'code' => 'STA530', 'description' => 'Selected advanced topics in statistical theory and methods based on current research.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MS/MPhil Statistics Subjects
            [ 'name' => 'Advanced Probability Theory', 'code' => 'STA601', 'description' => 'Measure-theoretic probability, characteristic functions, and limit theorems.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Statistical Inference', 'code' => 'STA602', 'description' => 'Advanced topics in estimation theory, hypothesis testing, and decision theory.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Linear Models', 'code' => 'STA603', 'description' => 'Theory of linear models, generalized inverses, and mixed models.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Design of Experiments', 'code' => 'STA604', 'description' => 'Advanced experimental designs, including response surface methodology and optimal design.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Multivariate Analysis', 'code' => 'STA611', 'description' => 'Advanced topics in multivariate analysis, including structural equation modeling and correspondence analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Time Series Analysis', 'code' => 'STA612', 'description' => 'Multivariate time series, state-space models, and financial time series.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Sampling Techniques', 'code' => 'STA613', 'description' => 'Complex survey designs, variance estimation, and analysis of survey data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Bayesian Statistics', 'code' => 'STA614', 'description' => 'Hierarchical models, MCMC methods, and model checking in Bayesian analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Survival Analysis', 'code' => 'STA615', 'description' => 'Frailty models, competing risks, and analysis of recurrent events.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Nonparametric Statistics', 'code' => 'STA616', 'description' => 'Kernel density estimation, smoothing techniques, and nonparametric regression.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Statistical Computing', 'code' => 'STA617', 'description' => 'Optimization methods, parallel computing, and advanced simulation techniques.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS'],
            [ 'name' => 'Stochastic Processes', 'code' => 'STA621', 'description' => 'Theory of Markov processes, renewal theory, and Brownian motion.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Econometrics', 'code' => 'STA622', 'description' => 'Advanced econometric models, including panel data and limited dependent variable models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Biostatistics', 'code' => 'STA623', 'description' => 'Design and analysis of clinical trials, longitudinal data analysis in health sciences.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Statistical Machine Learning', 'code' => 'STA624', 'description' => 'Advanced topics in machine learning, including support vector machines, neural networks, and ensemble methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'High-Dimensional Data Analysis', 'code' => 'STA625', 'description' => 'Statistical methods for data with a large number of variables, including regularization techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Spatial and Spatio-Temporal Statistics', 'code' => 'STA626', 'description' => 'Advanced models for spatial and spatio-temporal data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Functional Data Analysis', 'code' => 'STA627', 'description' => 'Advanced methods for analyzing data where observations are functions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Robust and Modern Regression', 'code' => 'STA628', 'description' => 'Advanced robust statistical methods and modern regression techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Categorical and Latent Variable Models', 'code' => 'STA629', 'description' => 'Advanced models for categorical data and latent variable analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Statistical Consulting and Collaboration', 'code' => 'STA630', 'description' => 'Advanced training in statistical consulting, communication, and collaboration.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Order Statistics', 'code' => 'STA631', 'description' => 'Theory and applications of order statistics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Reliability and Life Testing', 'code' => 'STA632', 'description' => 'Advanced models for reliability, accelerated life testing, and system reliability.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Official and Survey Statistics', 'code' => 'STA633', 'description' => 'Advanced topics in the production and analysis of official statistics and complex surveys.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'MPhil Thesis', 'code' => 'STA699', 'description' => 'Independent research thesis for the MPhil degree in Statistics.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS'],

            // PhD Statistics Subjects
            [ 'name' => 'Doctoral Seminar in Probability Theory', 'code' => 'STA801', 'description' => 'Current research topics in advanced probability theory.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar in Statistical Inference', 'code' => 'STA802', 'description' => 'Current research topics in advanced statistical inference.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Asymptotic Theory', 'code' => 'STA803', 'description' => 'Advanced topics in large sample theory, including empirical processes and U-statistics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Advanced Statistical Modeling', 'code' => 'STA811', 'description' => 'Research seminar on cutting-edge statistical models and methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Computational Statistics', 'code' => 'STA812', 'description' => 'Research topics in computationally intensive statistical methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Bayesian Theory and Methods', 'code' => 'STA813', 'description' => 'Advanced research in Bayesian methodology and theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Biostatistics and Bioinformatics', 'code' => 'STA814', 'description' => 'Current research in statistical methods for biological and genomic data.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Seminar in Machine Learning and Data Science', 'code' => 'STA815', 'description' => 'Advanced research at the interface of statistics, machine learning, and data science.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD'],
            [ 'name' => 'Topics in Stochastic Calculus', 'code' => 'STA821', 'description' => 'Advanced topics in stochastic calculus with applications in finance and other fields.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Causal Inference', 'code' => 'STA822', 'description' => 'Advanced theory and methods for causal inference from observational and experimental data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Network Analysis', 'code' => 'STA823', 'description' => 'Statistical models and methods for the analysis of network data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Statistical Consulting Practicum', 'code' => 'STA830', 'description' => 'Supervised experience in statistical consulting with clients from various disciplines.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Pedagogy in Statistics', 'code' => 'STA831', 'description' => 'Training in teaching statistics at the university level.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Directed Readings', 'code' => 'STA890', 'description' => 'In-depth study of a specialized research area under faculty supervision.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Dissertation Research', 'code' => 'STA899', 'description' => 'Original research culminating in a PhD dissertation in Statistics.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD'],
        ];

        $department = 'Department of Statistics';    
         
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
