<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class ManagementSciencesSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core BBA Subjects
            [ 'name' => 'Principles of Management', 'code' => 'MGT101', 'description' => 'Fundamental concepts and theories of management, including planning, organizing, leading, and controlling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Principles of Accounting I', 'code' => 'ACC101', 'description' => 'Introduction to financial accounting, recording transactions, and preparing financial statements.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Principles of Accounting II', 'code' => 'ACC102', 'description' => 'Continuation of ACC101, covering partnerships, corporations, and statement of cash flows.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Microeconomics', 'code' => 'ECO101', 'description' => 'Analysis of individual economic behavior, market structures, and resource allocation.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Macroeconomics', 'code' => 'ECO102', 'description' => 'Analysis of aggregate economic activity, including inflation, unemployment, and economic growth.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Business Mathematics I', 'code' => 'MTH111', 'description' => 'Mathematical concepts and techniques relevant to business applications.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Business Statistics I', 'code' => 'STA111', 'description' => 'Descriptive statistics, probability, and basic inferential statistics for business.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Introduction to Business Computing', 'code' => 'CSC111', 'description' => 'Fundamentals of computer hardware, software, and applications in business.', 'credit_hours' => '2+1', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Principles of Marketing', 'code' => 'MKT201', 'description' => 'Core marketing concepts, consumer behavior, market segmentation, and marketing mix.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Business Communication', 'code' => 'ENG201', 'description' => 'Effective written and oral communication skills for business contexts.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Financial Management I', 'code' => 'FIN201', 'description' => 'Introduction to financial decision-making, time value of money, risk and return.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Organizational Behavior', 'code' => 'MGT202', 'description' => 'Individual and group behavior in organizations, motivation, leadership, and organizational culture.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Business Law', 'code' => 'LAW201', 'description' => 'Legal principles and regulations relevant to business operations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Cost Accounting', 'code' => 'ACC202', 'description' => 'Cost concepts, cost behavior, job costing, process costing, and budgeting.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Human Resource Management', 'code' => 'HRM301', 'description' => 'Principles and practices of managing human resources, including recruitment, training, and compensation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Operations Management', 'code' => 'MGT302', 'description' => 'Design, planning, and control of production and service operations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Management Information Systems', 'code' => 'MIS301', 'description' => 'Role of information systems in organizations, data management, and decision support.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Marketing Management', 'code' => 'MKT302', 'description' => 'Strategic marketing planning, product development, pricing, promotion, and distribution strategies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Financial Management II', 'code' => 'FIN302', 'description' => 'Capital budgeting, working capital management, and long-term financing decisions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Business Research Methods', 'code' => 'MGT311', 'description' => 'Research design, data collection, and analysis techniques for business problems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Entrepreneurship', 'code' => 'ENT401', 'description' => 'Process of identifying business opportunities, developing business plans, and launching new ventures.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Strategic Management', 'code' => 'MGT402', 'description' => 'Formulation and implementation of business strategies for competitive advantage.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'International Business', 'code' => 'IB401', 'description' => 'Management of business operations in a global environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Business Ethics and Corporate Social Responsibility', 'code' => 'MGT411', 'description' => 'Ethical issues in business and the role of corporations in society.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'BBA Final Year Project I', 'code' => 'BBA498A', 'description' => 'Proposal development and initial research for the final year business project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'BBA Final Year Project II', 'code' => 'BBA498B', 'description' => 'Completion, analysis, and presentation of the final year business project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Business Mathematics II', 'code' => 'MTH112', 'description' => 'Advanced mathematical tools for business, including calculus and linear algebra applications.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Business Statistics II', 'code' => 'STA112', 'description' => 'Inferential statistics, hypothesis testing, regression, and time series analysis for business.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BBA'],
            [ 'name' => 'Supply Chain Management', 'code' => 'SCM301', 'description' => 'Planning and management of activities involved in sourcing, procurement, conversion, and logistics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Consumer Behavior', 'code' => 'MKT311', 'description' => 'Psychological and sociological factors influencing consumer decision-making.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Investment and Portfolio Management', 'code' => 'FIN401', 'description' => 'Principles of investment analysis, security valuation, and portfolio construction.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Leadership and Team Management', 'code' => 'MGT421', 'description' => 'Theories and practices of effective leadership and team dynamics in organizations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'E-Commerce and Digital Business', 'code' => 'MIS401', 'description' => 'Strategies and technologies for conducting business online.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Project Management', 'code' => 'MGT431', 'description' => 'Planning, execution, and control of projects to achieve specific goals.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Sales Management', 'code' => 'MKT411', 'description' => 'Managing sales force, sales techniques, and customer relationship management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Financial Markets and Institutions', 'code' => 'FIN311', 'description' => 'Structure and functioning of financial markets and institutions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Performance Management', 'code' => 'HRM401', 'description' => 'Designing and implementing performance appraisal systems and managing employee performance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Quality Management', 'code' => 'MGT441', 'description' => 'Principles and tools of total quality management (TQM) and continuous improvement.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Advertising and Promotion', 'code' => 'MKT421', 'description' => 'Integrated marketing communications, advertising strategies, and promotional tools.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Corporate Finance', 'code' => 'FIN411', 'description' => 'Advanced topics in financial decision-making for corporations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BBA'],
            [ 'name' => 'Advanced Managerial Accounting', 'code' => 'ACC601', 'description' => 'Advanced cost management, decision-making, and performance evaluation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Managerial Economics', 'code' => 'ECO601', 'description' => 'Application of economic theory to managerial decision-making.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Advanced Financial Management', 'code' => 'FIN601', 'description' => 'In-depth analysis of investment, financing, and dividend policy decisions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Strategic Marketing Management', 'code' => 'MKT601', 'description' => 'Developing and implementing marketing strategies in a competitive environment.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Advanced Organizational Behavior and Leadership', 'code' => 'MGT601', 'description' => 'Complex organizational dynamics, leadership theories, and change management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Advanced Operations and Supply Chain Management', 'code' => 'MGT602', 'description' => 'Strategic issues in operations, global supply chains, and logistics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Advanced Human Resource Management', 'code' => 'HRM601', 'description' => 'Strategic HRM, talent management, and international HRM.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Corporate Strategy and Policy', 'code' => 'MGT603', 'description' => 'Advanced strategic analysis, competitive dynamics, and corporate governance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Business Research and Quantitative Methods', 'code' => 'MGT611', 'description' => 'Advanced research methodologies and statistical techniques for business analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'International Financial Management', 'code' => 'FIN611', 'description' => 'Managing finance in multinational corporations, foreign exchange risk.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'MBA Thesis/Project I', 'code' => 'MBA698A', 'description' => 'Research proposal and initial work for MBA thesis or capstone project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'MBA Thesis/Project II', 'code' => 'MBA698B', 'description' => 'Completion and defense of MBA thesis or capstone project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Management Control Systems', 'code' => 'ACC611', 'description' => 'Design and use of systems to monitor and control organizational activities.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Negotiation and Conflict Resolution', 'code' => 'MGT621', 'description' => 'Strategies and tactics for effective negotiation and conflict management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Innovation and Technology Management', 'code' => 'MGT631', 'description' => 'Managing innovation processes and leveraging technology for competitive advantage.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Global Marketing Strategy', 'code' => 'MKT611', 'description' => 'Developing marketing strategies for international markets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Mergers and Acquisitions', 'code' => 'FIN621', 'description' => 'Valuation, structuring, and strategic considerations in M&A.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Change Management and Organizational Development', 'code' => 'MGT641', 'description' => 'Leading and managing organizational change and development initiatives.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Business Analytics and Decision Making', 'code' => 'MIS601', 'description' => 'Using data analytics tools and techniques for informed business decisions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Strategic Brand Management', 'code' => 'MKT621', 'description' => 'Building and managing strong brands for long-term value.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Venture Capital and Private Equity', 'code' => 'FIN631', 'description' => 'Financing new ventures and understanding private equity markets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Cross-Cultural Management', 'code' => 'MGT651', 'description' => 'Managing diverse teams and operating effectively in different cultural contexts.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Services Marketing', 'code' => 'MKT631', 'description' => 'Unique aspects of marketing services and managing service quality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Real Estate Finance and Investment', 'code' => 'FIN641', 'description' => 'Analysis of real estate markets, valuation, and investment strategies.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Strategic Human Capital Management', 'code' => 'HRM611', 'description' => 'Aligning human capital strategies with overall business objectives.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Business Process Reengineering', 'code' => 'MGT661', 'description' => 'Redesigning business processes for improved efficiency and effectiveness.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Digital Marketing and Analytics', 'code' => 'MKT641', 'description' => 'Strategies for digital marketing channels and analyzing digital marketing performance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Derivatives and Risk Management', 'code' => 'FIN651', 'description' => 'Understanding and using financial derivatives for hedging and speculation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Organizational Theory and Design', 'code' => 'MGT671', 'description' => 'Theories of organizational structure and design for optimal performance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Customer Relationship Management (CRM)', 'code' => 'MKT651', 'description' => 'Strategies and technologies for managing customer interactions and building loyalty.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Behavioral Finance', 'code' => 'FIN661', 'description' => 'Psychological influences on financial decision-making and market behavior.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Public Sector Management', 'code' => 'MGT681', 'description' => 'Principles and practices of management in government and non-profit organizations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Retail Management', 'code' => 'MKT661', 'description' => 'Strategies for managing retail operations, merchandising, and customer experience.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Fixed Income Securities', 'code' => 'FIN671', 'description' => 'Valuation, analysis, and management of bonds and other fixed-income instruments.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Knowledge Management', 'code' => 'MIS611', 'description' => 'Strategies and systems for capturing, sharing, and leveraging organizational knowledge.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Industrial Relations and Labor Laws', 'code' => 'HRM621', 'description' => 'Managing labor relations, collective bargaining, and compliance with labor laws.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],
            [ 'name' => 'Logistics Management', 'code' => 'SCM601', 'description' => 'Planning, implementing, and controlling the efficient flow and storage of goods and services.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MBA'],

            // Executive MBA (EMBA) Subjects
            [ 'name' => 'Strategic Leadership for Executives', 'code' => 'EMBA601', 'description' => 'Advanced leadership concepts and practices for senior managers.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Global Business Strategy for Executives', 'code' => 'EMBA602', 'description' => 'Formulating and executing strategy in a complex global landscape.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Financial Strategy for Executives', 'code' => 'EMBA603', 'description' => 'Advanced corporate finance and investment strategies for executive decision-making.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Executive Marketing and Brand Management', 'code' => 'EMBA604', 'description' => 'High-level marketing strategy, brand equity, and digital transformation.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Corporate Governance and Ethics for Executives', 'code' => 'EMBA605', 'description' => 'Frameworks for effective corporate governance and ethical leadership.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Executive-Level Negotiation and Deal Making', 'code' => 'EMBA606', 'description' => 'Advanced negotiation, persuasion, and conflict resolution skills for high-stakes deals.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'EMBA'],
            [ 'name' => 'EMBA Capstone Project', 'code' => 'EMBA699', 'description' => 'Applied strategic project addressing a real-world business challenge.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'EMBA'],
            [ 'name' => 'Operations and Supply Chain for Executives', 'code' => 'EMBA607', 'description' => 'Strategic management of operations and global supply chains for competitive advantage.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'EMBA'],
            [ 'name' => 'Data Analytics for Strategic Decision Making', 'code' => 'EMBA608', 'description' => 'Leveraging data analytics and business intelligence for strategic insights.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'EMBA'],
            [ 'name' => 'Leading Digital Transformation', 'code' => 'EMBA609', 'description' => 'Strategies for leading and managing digital transformation initiatives in established firms.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'EMBA'],
            [ 'name' => 'Corporate Entrepreneurship and Innovation', 'code' => 'EMBA610', 'description' => 'Fostering innovation and entrepreneurial activities within large organizations.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'EMBA'],

            // MS Subjects
            [ 'name' => 'Philosophy of Management Science', 'code' => 'MGT701', 'description' => 'Epistemological and ontological foundations of management research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Quantitative Research Methods', 'code' => 'MGT702', 'description' => 'Advanced multivariate statistical techniques and structural equation modeling.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Qualitative Research Methods', 'code' => 'MGT703', 'description' => 'In-depth study of qualitative research designs, data collection, and analysis.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Organizational Theory', 'code' => 'MGT704', 'description' => 'Critical review of classical and contemporary theories of organization.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Strategic Management', 'code' => 'MGT705', 'description' => 'Advanced topics and current research in strategic management.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Marketing Theory', 'code' => 'MKT701', 'description' => 'Foundational and contemporary theories in marketing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Financial Theory', 'code' => 'FIN701', 'description' => 'Advanced theories of corporate finance and asset pricing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Seminar in Human Resource Management', 'code' => 'HRM701', 'description' => 'Current research and theoretical perspectives in HRM.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Econometrics for Management Research', 'code' => 'ECO701', 'description' => 'Econometric techniques for analyzing business and economic data.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Technology and Innovation Management', 'code' => 'MGT711', 'description' => 'Research on the management of technology, innovation, and R&D.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Topics in Supply Chain Management', 'code' => 'SCM701', 'description' => 'Current research and trends in global supply chain management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'MS Thesis', 'code' => 'MGT799', 'description' => 'Independent research thesis for the MS degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Organizational Development', 'code' => 'MGT712', 'description' => 'Research on organizational change, intervention strategies, and consulting.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Consumer Behavior Research', 'code' => 'MKT702', 'description' => 'In-depth study of consumer decision-making models and research methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Advanced Corporate Finance', 'code' => 'FIN702', 'description' => 'Advanced topics in capital structure, dividend policy, and corporate valuation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],
            [ 'name' => 'Research in International Business', 'code' => 'IB701', 'description' => 'Contemporary research issues in international business and global strategy.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS'],

            // PhD Subjects
            [ 'name' => 'Doctoral Seminar in Research Methodology', 'code' => 'MGT801', 'description' => 'Advanced research design and philosophy of science for doctoral students.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Econometrics', 'code' => 'ECO801', 'description' => 'Panel data, time series, and limited dependent variable models for business research.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Theory Building in Management', 'code' => 'MGT802', 'description' => 'The process of developing and testing management theories.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Organizational Behavior', 'code' => 'MGT803', 'description' => 'In-depth analysis of micro and macro organizational behavior theories and research.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Corporate Strategy', 'code' => 'MGT804', 'description' => 'Advanced research in strategy formulation, implementation, and competitive dynamics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Marketing Strategy', 'code' => 'MKT801', 'description' => 'Advanced research on marketing strategy, consumer behavior, and modeling.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Corporate Finance', 'code' => 'FIN801', 'description' => 'Advanced research in corporate financial policy and governance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Asset Pricing', 'code' => 'FIN802', 'description' => 'Theoretical and empirical research in asset pricing and financial markets.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Doctoral Seminar in Strategic HRM', 'code' => 'HRM801', 'description' => 'Advanced research on the strategic management of human capital.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Teaching and Pedagogy in Higher Education', 'code' => 'MGT811', 'description' => 'Training in university-level teaching methods, course design, and assessment.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Entrepreneurship', 'code' => 'ENT801', 'description' => 'Research on new venture creation, entrepreneurial finance, and corporate venturing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'International Business Strategy', 'code' => 'IB801', 'description' => 'Research on multinational corporations, global strategy, and cross-cultural management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'MGT899', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = ['Department of Management Sciences', 'Institute of Business Leadership'];    
         
        $programSemesters = [
            'BBA' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 8, 'program_ids' => [], 'degree_level_id' => 1],
            'MBA' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 3],
            'EMBA' => ['current_semester' => 1, 'subjects_in_semester' => 0, 'max_semesters' => 4, 'program_ids' => [], 'degree_level_id' => 3],
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
