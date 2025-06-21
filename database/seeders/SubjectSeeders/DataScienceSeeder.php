<?php

namespace Database\Seeders\SubjectSeeders;

use App\Helpers\SubjectSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DataScienceSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Data Science
            [ 'name' => 'Introduction to Data Science', 'code' => 'DSC101', 'description' => 'Overview of data science, lifecycle, tools, and applications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Programming for Data Science', 'code' => 'DSC102', 'description' => 'Fundamentals of programming using Python for data analysis and manipulation.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Calculus for Data Science', 'code' => 'DSC111', 'description' => 'Differential and integral calculus with applications in data science.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Linear Algebra for Data Science', 'code' => 'DSC112', 'description' => 'Vectors, matrices, and linear transformations essential for data science algorithms.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Probability and Statistics for Data Science', 'code' => 'DSC201', 'description' => 'Fundamental concepts of probability, distributions, and statistical inference.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Structures and Algorithms for Data Science', 'code' => 'DSC202', 'description' => 'Efficient data organization and algorithmic problem-solving in data science contexts.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Database Systems for Data Science', 'code' => 'DSC203', 'description' => 'Relational databases, SQL, NoSQL databases, and data warehousing concepts.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Wrangling and Preprocessing', 'code' => 'DSC204', 'description' => 'Techniques for cleaning, transforming, and preparing data for analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Machine Learning Fundamentals', 'code' => 'DSC301', 'description' => 'Core concepts of machine learning, supervised and unsupervised learning algorithms.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Modeling and Inference', 'code' => 'DSC302', 'description' => 'Building statistical models, hypothesis testing, and regression analysis.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Visualization Techniques', 'code' => 'DSC303', 'description' => 'Principles and tools for creating effective visualizations of data.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Big Data Technologies', 'code' => 'DSC304', 'description' => 'Introduction to big data ecosystems, Hadoop, Spark, and distributed computing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Time Series Analysis and Forecasting', 'code' => 'DSC305', 'description' => 'Methods for analyzing time-dependent data and making predictions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Deep Learning', 'code' => 'DSC401', 'description' => 'Neural networks, convolutional neural networks (CNNs), and recurrent neural networks (RNNs).', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Natural Language Processing', 'code' => 'DSC402', 'description' => 'Techniques for processing and understanding human language by computers.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Ethics and Privacy', 'code' => 'DSC403', 'description' => 'Ethical considerations, bias, fairness, and privacy issues in data science.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Cloud Computing for Data Science', 'code' => 'DSC404', 'description' => 'Utilizing cloud platforms (AWS, Azure, GCP) for data storage, processing, and model deployment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Science Capstone Project I', 'code' => 'DSC498A', 'description' => 'Problem definition, literature review, and proposal for a data science project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Data Science Capstone Project II', 'code' => 'DSC498B', 'description' => 'Implementation, evaluation, and presentation of the data science project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Optimization Methods for Data Science', 'code' => 'DSC311', 'description' => 'Mathematical optimization techniques used in machine learning and data analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Experimental Design and A/B Testing', 'code' => 'DSC312', 'description' => 'Designing experiments and conducting A/B tests for data-driven decisions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Bayesian Data Analysis', 'code' => 'DSC411', 'description' => 'Principles of Bayesian statistics and their application in data modeling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Reinforcement Learning', 'code' => 'DSC412', 'description' => 'Algorithms and techniques for learning optimal actions in an environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Scientific Computing with Python', 'code' => 'DSC211', 'description' => 'Using Python libraries like NumPy, SciPy, and Pandas for scientific computation.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Information Retrieval', 'code' => 'DSC321', 'description' => 'Techniques for searching, indexing, and retrieving information from large datasets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Machine Learning', 'code' => 'DSC501', 'description' => 'Advanced topics in machine learning, including ensemble methods and graphical models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Computer Vision', 'code' => 'DSC502', 'description' => 'Techniques for enabling computers to interpret and understand visual information.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Big Data Analytics', 'code' => 'DSC503', 'description' => 'Advanced analytical techniques for large-scale datasets.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Social Network Analysis', 'code' => 'DSC504', 'description' => 'Methods for analyzing structure and dynamics of social networks.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Bioinformatics Data Analysis', 'code' => 'DSC505', 'description' => 'Computational techniques for analyzing biological data, genomics, and proteomics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Financial Data Science', 'code' => 'DSC506', 'description' => 'Application of data science techniques in finance, risk management, and trading.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Healthcare Analytics', 'code' => 'DSC507', 'description' => 'Using data science to improve healthcare outcomes, operations, and research.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Geospatial Data Analysis', 'code' => 'DSC508', 'description' => 'Techniques for analyzing and visualizing geographic and spatial data.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Recommender Systems', 'code' => 'DSC509', 'description' => 'Algorithms and techniques for building personalized recommendation engines.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Data Mining and Knowledge Discovery', 'code' => 'DSC510', 'description' => 'Extracting patterns and knowledge from large datasets.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Text Analytics and Sentiment Analysis', 'code' => 'DSC511', 'description' => 'Analyzing textual data to extract insights and understand sentiment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Anomaly Detection', 'code' => 'DSC512', 'description' => 'Techniques for identifying unusual patterns or outliers in data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Causal Inference for Data Scientists', 'code' => 'DSC513', 'description' => 'Methods for determining cause-and-effect relationships from observational data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Graph Data Analytics', 'code' => 'DSC514', 'description' => 'Analyzing data represented as graphs, including network analysis and graph databases.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Data Storytelling and Communication', 'code' => 'DSC515', 'description' => 'Effectively communicating data insights through narratives and visualizations.', 'credit_hours' => '2', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Machine Learning Operations (MLOps)', 'code' => 'DSC516', 'description' => 'Principles and practices for deploying, monitoring, and managing machine learning models in production.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Deep Learning Architectures', 'code' => 'DSC517', 'description' => 'Transformers, GANs, and other advanced neural network architectures.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Unsupervised Learning and Clustering', 'code' => 'DSC518', 'description' => 'Techniques for finding patterns in unlabeled data and grouping similar data points.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Feature Engineering and Selection', 'code' => 'DSC519', 'description' => 'Creating and selecting relevant features to improve machine learning model performance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Scalable Data Processing with Spark', 'code' => 'DSC520', 'description' => 'Using Apache Spark for large-scale data processing and analytics.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Data Warehousing and Business Intelligence', 'code' => 'DSC521', 'description' => 'Designing data warehouses and using BI tools for reporting and analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cybersecurity Analytics', 'code' => 'DSC522', 'description' => 'Applying data science techniques to detect and mitigate cybersecurity threats.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Ethical Hacking and Penetration Testing for Data Scientists', 'code' => 'DSC523', 'description' => 'Understanding security vulnerabilities from an attacker\'s perspective to secure data systems.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Internet of Things (IoT) Analytics', 'code' => 'DSC524', 'description' => 'Analyzing data generated by IoT devices for insights and decision-making.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Robotics Process Automation (RPA) with Data Insights', 'code' => 'DSC525', 'description' => 'Automating business processes using RPA tools and leveraging data analytics.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Computing for Data Science', 'code' => 'DSC526', 'description' => 'Introduction to quantum algorithms and their potential applications in data science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Predictive Maintenance', 'code' => 'DSC527', 'description' => 'Using data science to predict equipment failures and optimize maintenance schedules.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Supply Chain Analytics', 'code' => 'DSC528', 'description' => 'Applying data analytics to optimize supply chain operations and logistics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Customer Analytics and Marketing Science', 'code' => 'DSC529', 'description' => 'Using data to understand customer behavior and optimize marketing strategies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Sports Analytics', 'code' => 'DSC530', 'description' => 'Application of data science techniques to analyze performance in sports.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Urban Analytics and Smart Cities', 'code' => 'DSC531', 'description' => 'Using data to understand and improve urban environments and city services.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Digital Forensics and Data Recovery', 'code' => 'DSC532', 'description' => 'Techniques for investigating digital crimes and recovering data from digital devices.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Statistical Computing with R', 'code' => 'DSC533', 'description' => 'Advanced use of R for complex statistical analysis and visualization.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Data Governance and Management', 'code' => 'DSC534', 'description' => 'Principles and practices for managing data assets, ensuring quality, security, and compliance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // --- MS/MPhil Program Subjects ---
            [ 'name' => 'Advanced Statistical Theory', 'code' => 'DSC601', 'description' => 'In-depth study of statistical theory, including estimation, hypothesis testing, and Bayesian methods.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Machine Learning', 'code' => 'DSC602', 'description' => 'Advanced topics in machine learning, such as graphical models, kernel methods, and ensemble learning.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Big Data Systems and Architecture', 'code' => 'DSC603', 'description' => 'Design and implementation of scalable systems for big data processing and analytics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Research Methodology for Data Science', 'code' => 'DSC604', 'description' => 'Conducting research in data science, including experimental design and scientific writing.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Deep Learning', 'code' => 'DSC610', 'description' => 'Advanced neural network architectures, including GANs, transformers, and autoencoders.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Natural Language Processing', 'code' => 'DSC611', 'description' => 'State-of-the-art techniques in NLP, including language models and machine translation.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Computer Vision', 'code' => 'DSC612', 'description' => 'Advanced topics in computer vision, such as object detection, segmentation, and 3D vision.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Reinforcement Learning Theory and Practice', 'code' => 'DSC613', 'description' => 'In-depth study of reinforcement learning algorithms and their applications.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Data Ethics, Governance, and Security', 'code' => 'DSC614', 'description' => 'Advanced topics in data privacy, fairness, and security.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Cloud-Native Data Engineering', 'code' => 'DSC615', 'description' => 'Building and managing data pipelines and infrastructure on cloud platforms.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Causal Inference and Experimentation', 'code' => 'DSC616', 'description' => 'Advanced methods for causal inference from observational and experimental data.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Time Series Analysis and Forecasting at Scale', 'code' => 'DSC617', 'description' => 'Advanced techniques for modeling and forecasting large-scale time series data.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Optimization for Machine Learning', 'code' => 'DSC618', 'description' => 'Advanced optimization algorithms for training large-scale machine learning models.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'MS'],
            [ 'name' => 'Data Visualization and Storytelling', 'code' => 'DSC619', 'description' => 'Advanced techniques for creating compelling data narratives and interactive visualizations.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis Research I', 'code' => 'DSC698', 'description' => 'Initial phase of MS thesis research.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'MS Thesis Research II', 'code' => 'DSC699', 'description' => 'Completion and defense of MS thesis.', 'credit_hours' => '6', 'category' => 'Research', 'program_level' => 'MS'],
            [ 'name' => 'Domain-Specific Data Science: Finance', 'code' => 'DSC701', 'description' => 'Application of data science techniques in quantitative finance and algorithmic trading.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Domain-Specific Data Science: Healthcare', 'code' => 'DSC702', 'description' => 'Advanced analytics for clinical data, electronic health records, and bioinformatics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Graph Analytics and Network Science', 'code' => 'DSC703', 'description' => 'Advanced methods for analyzing large-scale graph data and complex networks.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Interpretable Machine Learning', 'code' => 'DSC704', 'description' => 'Techniques for building transparent and explainable AI and machine learning models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Federated Learning and Privacy-Preserving AI', 'code' => 'DSC705', 'description' => 'Techniques for training machine learning models on decentralized data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Automated Machine Learning (AutoML)', 'code' => 'DSC706', 'description' => 'Methods for automating the end-to-end process of applying machine learning.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Seminar in Data Science', 'code' => 'DSC790', 'description' => 'Presentation and discussion of current research in data science.', 'credit_hours' => '1', 'category' => 'Core', 'program_level' => 'MS'],
            [ 'name' => 'Independent Study in Data Science', 'code' => 'DSC795', 'description' => 'In-depth study of a specialized topic under faculty supervision.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Practicum in Data Science', 'code' => 'DSC797', 'description' => 'Practical, hands-on project in a real-world data science setting.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // --- PhD Program Subjects ---
            [ 'name' => 'Advanced Topics in Data Science Theory', 'code' => 'DSC801', 'description' => 'Foundational theories and mathematical principles underlying modern data science.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Machine Learning Research', 'code' => 'DSC802', 'description' => 'Exploration of cutting-edge research in machine learning.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Seminar in Data Science', 'code' => 'DSC803', 'description' => 'Presentation and critique of doctoral-level research in data science.', 'credit_hours' => '1-3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Research Methodology for Doctoral Studies', 'code' => 'DSC804', 'description' => 'Advanced research design, grant writing, and academic publication strategies.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD'],
            [ 'name' => 'Special Topics in Deep Learning Theory', 'code' => 'DSC810', 'description' => 'Theoretical underpinnings of deep learning, including optimization and generalization.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Special Topics in Causal Inference', 'code' => 'DSC811', 'description' => 'Advanced research topics in causal discovery and inference.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Special Topics in Responsible AI', 'code' => 'DSC812', 'description' => 'Research on fairness, accountability, transparency, and ethics in AI systems.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Special Topics in Large-Scale Data Systems', 'code' => 'DSC813', 'description' => 'Research on next-generation systems for data storage, processing, and analytics.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
            [ 'name' => 'Directed Research in Data Science', 'code' => 'DSC901', 'description' => 'Independent research on a specialized topic under faculty guidance.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'PhD Qualifying Examination', 'code' => 'DSC990', 'description' => 'Comprehensive examination of the student\'s knowledge in their field of study.', 'credit_hours' => '3', 'category' => 'Milestone', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Proposal', 'code' => 'DSC991', 'description' => 'Development and defense of the doctoral research proposal.', 'credit_hours' => '3', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research I', 'code' => 'DSC998', 'description' => 'Independent doctoral research leading to the dissertation.', 'credit_hours' => '9', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Doctoral Dissertation Research II', 'code' => 'DSC999', 'description' => 'Continuation of doctoral research, dissertation writing, and defense.', 'credit_hours' => '9', 'category' => 'Research', 'program_level' => 'PhD'],
            [ 'name' => 'Teaching Practicum in Data Science', 'code' => 'DSC950', 'description' => 'Supervised teaching experience in an undergraduate data science course.', 'credit_hours' => '1-3', 'category' => 'Elective', 'program_level' => 'PhD'],
            [ 'name' => 'Advanced Topics in Probabilistic Modeling', 'code' => 'DSC814', 'description' => 'Research on advanced probabilistic models and inference techniques.', 'credit_hours' => '3', 'category' => 'Specialization', 'program_level' => 'PhD'],
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
