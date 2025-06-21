<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class MathematicsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Mathematics
            [ 'name' => 'Calculus I', 'code' => 'MTH101', 'description' => 'Limits, continuity, differentiation, and applications of derivatives.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Calculus II', 'code' => 'MTH102', 'description' => 'Integration, techniques of integration, applications of integrals, and sequences.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Linear Algebra', 'code' => 'MTH103', 'description' => 'Systems of linear equations, matrices, determinants, vector spaces, eigenvalues, and eigenvectors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Discrete Mathematics', 'code' => 'MTH111', 'description' => 'Logic, sets, functions, relations, combinatorics, and graph theory.', 'credit_hours' => '3', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'Calculus III (Multivariable Calculus)', 'code' => 'MTH201', 'description' => 'Vector calculus, partial derivatives, multiple integrals, and theorems of Green, Stokes, and Gauss.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Differential Equations I', 'code' => 'MTH202', 'description' => 'First-order and second-order ordinary differential equations, series solutions, and Laplace transforms.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Probability and Statistics', 'code' => 'MTH203', 'description' => 'Probability theory, random variables, distributions, estimation, and hypothesis testing.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Real Analysis I', 'code' => 'MTH301', 'description' => 'Real number system, sequences, series, limits, continuity, and differentiation of functions of one variable.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Abstract Algebra I (Group Theory)', 'code' => 'MTH302', 'description' => 'Groups, subgroups, cyclic groups, permutation groups, homomorphisms, and isomorphism theorems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Complex Analysis I', 'code' => 'MTH303', 'description' => 'Complex numbers, analytic functions, Cauchy-Riemann equations, elementary functions, and complex integration.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Numerical Analysis I', 'code' => 'MTH304', 'description' => 'Error analysis, solutions of equations in one variable, interpolation, numerical differentiation, and integration.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Topology', 'code' => 'MTH305', 'description' => 'Topological spaces, continuous functions, connectedness, compactness, and separation axioms.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Number Theory', 'code' => 'MTH306', 'description' => 'Divisibility, congruences, prime numbers, Diophantine equations, and cryptographic applications.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Methods', 'code' => 'MTH401', 'description' => 'Fourier series, Fourier transforms, special functions, and partial differential equations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Real Analysis II', 'code' => 'MTH402', 'description' => 'Riemann integration, sequences and series of functions, and introduction to measure theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Abstract Algebra II (Rings and Fields)', 'code' => 'MTH403', 'description' => 'Rings, ideals, integral domains, fields, extension fields, and Galois theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Differential Geometry', 'code' => 'MTH404', 'description' => 'Curves and surfaces in Euclidean space, curvature, geodesics, and an introduction to manifolds.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Functional Analysis', 'code' => 'MTH405', 'description' => 'Normed spaces, Banach spaces, Hilbert spaces, linear operators, and spectral theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Statistics', 'code' => 'MTH406', 'description' => 'Sampling distributions, estimation theory, hypothesis testing, regression, and analysis of variance.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematics Research Project I', 'code' => 'MTH498A', 'description' => 'Literature review, problem formulation, and research proposal for a mathematics project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematics Research Project II', 'code' => 'MTH498B', 'description' => 'Execution, analysis, and presentation of the mathematics research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Vector and Tensor Analysis', 'code' => 'MTH211', 'description' => 'Vector algebra, vector calculus, curvilinear coordinates, and introduction to tensors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Set Theory and Logic', 'code' => 'MTH212', 'description' => 'Axiomatic set theory, ordinal and cardinal numbers, propositional and predicate logic.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Mathematical Software (e.g., MATLAB, Mathematica)', 'code' => 'MTH213', 'description' => 'Using software for symbolic computation, numerical analysis, and visualization.', 'credit_hours' => '2+1', 'category' => 'Supporting', 'program_level' => 'BS'],
            [ 'name' => 'History of Mathematics', 'code' => 'MTH311', 'description' => 'Development of mathematical ideas and concepts from ancient times to the modern era.', 'credit_hours' => '2', 'category' => 'Major', 'program_level' => 'BS'],

            // Elective Subjects for Mathematics
            [ 'name' => 'Partial Differential Equations', 'code' => 'MTH501', 'description' => 'Classification, solution techniques (separation of variables, characteristics), and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Measure Theory and Integration', 'code' => 'MTH502', 'description' => 'Lebesgue measure, Lebesgue integration, Lp spaces, and abstract measure spaces.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Linear Algebra', 'code' => 'MTH503', 'description' => 'Canonical forms, inner product spaces, spectral theory, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Graph Theory', 'code' => 'MTH504', 'description' => 'Connectivity, trees, matchings, colorings, planar graphs, and network flows.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Combinatorics', 'code' => 'MTH505', 'description' => 'Enumeration techniques, generating functions, recurrence relations, and combinatorial designs.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Fluid Dynamics I', 'code' => 'MTH506', 'description' => 'Kinematics of fluids, conservation laws, ideal fluid flow, and potential theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Operations Research I', 'code' => 'MTH507', 'description' => 'Linear programming, simplex method, duality, and sensitivity analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Modeling', 'code' => 'MTH508', 'description' => 'Principles of mathematical modeling, model construction, and applications in various fields.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Stochastic Processes', 'code' => 'MTH509', 'description' => 'Markov chains, Poisson processes, renewal theory, and Brownian motion.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Numerical Analysis II', 'code' => 'MTH510', 'description' => 'Numerical solutions of ODEs, PDEs, and systems of linear equations.', 'credit_hours' => '3+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Complex Analysis II', 'code' => 'MTH511', 'description' => 'Residue theory, conformal mapping, analytic continuation, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Algebraic Topology', 'code' => 'MTH512', 'description' => 'Homotopy, fundamental group, covering spaces, and homology theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Galois Theory', 'code' => 'MTH513', 'description' => 'Field extensions, automorphisms, Galois groups, and solvability of polynomials by radicals.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Theory of Modules', 'code' => 'MTH514', 'description' => 'Modules, submodules, homomorphisms, direct sums, and tensor products.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Homological Algebra', 'code' => 'MTH515', 'description' => 'Chain complexes, homology, cohomology, Ext and Tor functors.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Riemannian Geometry', 'code' => 'MTH516', 'description' => 'Riemannian manifolds, connections, curvature, and geodesics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Dynamical Systems', 'code' => 'MTH517', 'description' => 'Continuous and discrete dynamical systems, stability, bifurcations, and chaos theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Optimization Theory', 'code' => 'MTH518', 'description' => 'Unconstrained and constrained optimization, convex optimization, and duality.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Financial Mathematics I', 'code' => 'MTH519', 'description' => 'Interest rates, annuities, bonds, options, and basic derivative pricing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Actuarial Mathematics I', 'code' => 'MTH520', 'description' => 'Life contingencies, survival models, life insurance, and annuities.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Cryptography', 'code' => 'MTH521', 'description' => 'Classical and modern cryptographic systems, public-key cryptography, and security protocols.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Coding Theory', 'code' => 'MTH522', 'description' => 'Error-correcting codes, linear codes, cyclic codes, and BCH codes.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Logic', 'code' => 'MTH523', 'description' => 'Model theory, computability theory, and Gödel\'s incompleteness theorems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Fuzzy Logic and Systems', 'code' => 'MTH524', 'description' => 'Fuzzy sets, fuzzy logic, fuzzy relations, and applications in control and decision making.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Special Relativity', 'code' => 'MTH525', 'description' => 'Principles of special relativity, Lorentz transformations, relativistic mechanics, and electromagnetism.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'General Relativity', 'code' => 'MTH526', 'description' => 'Tensor calculus, Einstein\'s field equations, Schwarzschild solution, and cosmology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Mechanics I', 'code' => 'MTH527', 'description' => 'Foundations of quantum mechanics, Schrödinger equation, and simple quantum systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Continuum Mechanics', 'code' => 'MTH528', 'description' => 'Kinematics, stress, strain, conservation laws, and constitutive equations for solids and fluids.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Elasticity Theory', 'code' => 'MTH529', 'description' => 'Stress, strain, Hooke\'s law, and boundary value problems in linear elasticity.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Fluid Dynamics II', 'code' => 'MTH530', 'description' => 'Viscous fluid flow, Navier-Stokes equations, boundary layer theory, and turbulence.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Operations Research II', 'code' => 'MTH531', 'description' => 'Integer programming, network models, queuing theory, and inventory models.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Game Theory', 'code' => 'MTH532', 'description' => 'Strategic games, extensive games, Nash equilibrium, and applications in economics and biology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Regression Analysis', 'code' => 'MTH533', 'description' => 'Simple and multiple linear regression, model diagnostics, and variable selection.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Time Series Analysis', 'code' => 'MTH534', 'description' => 'Stationary processes, ARMA models, forecasting, and spectral analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Multivariate Analysis', 'code' => 'MTH535', 'description' => 'Multivariate normal distribution, principal component analysis, factor analysis, and discriminant analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nonparametric Statistics', 'code' => 'MTH536', 'description' => 'Distribution-free tests, rank-based methods, and bootstrap techniques.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Bayesian Statistics', 'code' => 'MTH537', 'description' => 'Bayes\' theorem, prior and posterior distributions, Bayesian inference, and MCMC methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Computational Mathematics', 'code' => 'MTH538', 'description' => 'Advanced numerical methods, scientific computing, and algorithm design.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Biology', 'code' => 'MTH539', 'description' => 'Mathematical models in population dynamics, epidemiology, and ecology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Financial Mathematics II', 'code' => 'MTH540', 'description' => 'Stochastic calculus, Black-Scholes model, risk management, and exotic options.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Actuarial Mathematics II', 'code' => 'MTH541', 'description' => 'Loss models, credibility theory, simulation, and advanced topics in actuarial science.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MPhil Subjects
            [ 'name' => 'Advanced Real Analysis', 'code' => 'MTH601', 'description' => 'Measure theory, Lebesgue integration, and functional analysis.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Abstract Algebra', 'code' => 'MTH602', 'description' => 'Advanced group theory, ring theory, and module theory.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Differential Equations', 'code' => 'MTH603', 'description' => 'Qualitative theory of ODEs, boundary value problems, and introduction to PDEs.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Topology and Geometry', 'code' => 'MTH604', 'description' => 'Advanced topics in point-set and algebraic topology, and differential geometry.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Methods of Applied Mathematics', 'code' => 'MTH605', 'description' => 'Integral equations, calculus of variations, and tensor analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Numerical Analysis', 'code' => 'MTH606', 'description' => 'Numerical solutions of PDEs, finite element method, and spectral methods.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Complex Analysis', 'code' => 'MTH607', 'description' => 'Conformal mappings, Riemann surfaces, and special functions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Functional Analysis and Operator Theory', 'code' => 'MTH608', 'description' => 'Spectral theory of operators, Banach algebras, and C*-algebras.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Algebraic Geometry', 'code' => 'MTH609', 'description' => 'Affine and projective varieties, schemes, and cohomology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Commutative Algebra', 'code' => 'MTH610', 'description' => 'Noetherian rings, dimension theory, and homological methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Lie Groups and Lie Algebras', 'code' => 'MTH611', 'description' => 'Matrix Lie groups, Lie algebras, representations, and classification.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Probability Theory', 'code' => 'MTH612', 'description' => 'Measure-theoretic probability, limit theorems, and martingales.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Mathematical Statistics', 'code' => 'MTH613', 'description' => 'Estimation theory, hypothesis testing, and asymptotic theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Fluid Dynamics', 'code' => 'MTH614', 'description' => 'Compressible flow, turbulence, and magnetohydrodynamics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Mathematical Physics', 'code' => 'MTH615', 'description' => 'Methods of mathematical physics, including group theory and differential geometry in physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Control Theory', 'code' => 'MTH616', 'description' => 'Linear and nonlinear control systems, stability, and optimal control.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Optimization Techniques', 'code' => 'MTH617', 'description' => 'Convex optimization, integer programming, and stochastic optimization.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Financial Mathematics', 'code' => 'MTH618', 'description' => 'Stochastic models in finance, option pricing theory, and risk management.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Theory of Finite Element Method', 'code' => 'MTH619', 'description' => 'Variational formulation, error analysis, and implementation of FEM.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Spectral Theory', 'code' => 'MTH620', 'description' => 'Spectral analysis of operators in Hilbert spaces.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Harmonic Analysis', 'code' => 'MTH621', 'description' => 'Fourier analysis on groups, wavelets, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Analytic Number Theory', 'code' => 'MTH622', 'description' => 'Prime number theorem, Dirichlet series, and L-functions.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Algebraic Number Theory', 'code' => 'MTH623', 'description' => 'Number fields, rings of integers, and class field theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Research Methodology', 'code' => 'MTH698', 'description' => 'Techniques for mathematical research and writing.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'MTH699', 'description' => 'Independent research thesis for the MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Topics in Algebra', 'code' => 'MTH701', 'description' => 'Advanced seminar on current research topics in algebra.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Topics in Analysis', 'code' => 'MTH702', 'description' => 'Advanced seminar on current research topics in analysis.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Topics in Geometry and Topology', 'code' => 'MTH703', 'description' => 'Advanced seminar on current research topics in geometry and topology.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Topics in Applied Mathematics', 'code' => 'MTH704', 'description' => 'Advanced seminar on current research topics in applied mathematics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Homological Algebra', 'code' => 'MTH705', 'description' => 'Derived categories, spectral sequences, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Non-commutative Geometry', 'code' => 'MTH706', 'description' => 'C*-algebras, K-theory, and cyclic cohomology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Theory of Partial Differential Equations', 'code' => 'MTH707', 'description' => 'Sobolev spaces, elliptic regularity, and evolution equations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Numerical Methods', 'code' => 'MTH708', 'description' => 'Multigrid methods, domain decomposition, and adaptive methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Stochastic Differential Equations', 'code' => 'MTH709', 'description' => 'Ito calculus, SDEs, and applications in finance and physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Fluid Mechanics', 'code' => 'MTH710', 'description' => 'Hydrodynamic stability, computational fluid dynamics, and non-Newtonian fluids.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'General Relativity and Cosmology', 'code' => 'MTH711', 'description' => 'Einstein\'s equations, black holes, gravitational waves, and the standard model of cosmology.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Quantum Field Theory', 'code' => 'MTH712', 'description' => 'Canonical quantization, path integrals, and renormalization.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Seminar in Mathematical Education', 'code' => 'MTH713', 'description' => 'Research and trends in mathematics education.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Research Seminar', 'code' => 'MTH798', 'description' => 'Presentation and discussion of ongoing research by students and faculty.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'MTH799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Mathematics';    
         
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
