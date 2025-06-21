<?php

namespace Database\Seeders\SubjectSeeders;

use App\Models\Office;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Helpers\SubjectSeederHelper;

class PhysicsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Core Major Subjects for Physics
            [ 'name' => 'Mechanics I (Newtonian Mechanics)', 'code' => 'PHY101', 'description' => 'Kinematics, dynamics, work, energy, momentum, rotational motion, and oscillations.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Waves and Oscillations', 'code' => 'PHY102', 'description' => 'Simple harmonic motion, damped and forced oscillations, wave motion, superposition, interference, and diffraction.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Electricity and Magnetism I', 'code' => 'PHY201', 'description' => 'Electrostatics, electric fields, Gauss\'s law, electric potential, capacitance, DC circuits.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Thermodynamics and Statistical Mechanics I', 'code' => 'PHY202', 'description' => 'Laws of thermodynamics, kinetic theory of gases, entropy, and basic statistical mechanics.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Modern Physics I (Relativity and Quantum Concepts)', 'code' => 'PHY203', 'description' => 'Special relativity, photoelectric effect, Compton scattering, Bohr model, wave-particle duality.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Optics', 'code' => 'PHY204', 'description' => 'Geometrical optics, wave optics, interference, diffraction, polarization, and lasers.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Methods of Physics I', 'code' => 'PHY301', 'description' => 'Vector calculus, ordinary differential equations, Fourier series, and complex variables relevant to physics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Classical Mechanics', 'code' => 'PHY302', 'description' => 'Lagrangian and Hamiltonian dynamics, central force motion, rigid body dynamics, and small oscillations.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Electricity and Magnetism II', 'code' => 'PHY303', 'description' => 'Magnetostatics, Ampere\'s law, Faraday\'s law, Maxwell\'s equations, electromagnetic waves.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Mechanics I', 'code' => 'PHY304', 'description' => 'Wave functions, Schrödinger equation, operators, angular momentum, and hydrogen atom.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Electronics I', 'code' => 'PHY305', 'description' => 'Semiconductor devices, diodes, transistors, amplifiers, and basic analog circuits.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Solid State Physics I', 'code' => 'PHY401', 'description' => 'Crystal structure, diffraction, lattice vibrations, thermal properties, and free electron theory.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Nuclear Physics I', 'code' => 'PHY402', 'description' => 'Nuclear properties, radioactivity, nuclear models, nuclear reactions, and particle detectors.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Mechanics II', 'code' => 'PHY403', 'description' => 'Perturbation theory, scattering theory, identical particles, and introduction to relativistic quantum mechanics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Computational Physics', 'code' => 'PHY404', 'description' => 'Numerical methods for solving physics problems, simulation techniques, and data analysis using programming.', 'credit_hours' => '2+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Experimental Physics Laboratory I', 'code' => 'PHY251L', 'description' => 'Experiments in mechanics, waves, and thermodynamics.', 'credit_hours' => '0+2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Experimental Physics Laboratory II', 'code' => 'PHY351L', 'description' => 'Experiments in electricity, magnetism, and optics.', 'credit_hours' => '0+2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Experimental Physics Laboratory', 'code' => 'PHY451L', 'description' => 'Advanced experiments in modern physics, solid state physics, and nuclear physics.', 'credit_hours' => '0+2', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physics Research Project I', 'code' => 'PHY498A', 'description' => 'Literature survey, research methodology, and proposal development for a physics research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Physics Research Project II', 'code' => 'PHY498B', 'description' => 'Execution, data analysis, and presentation of the physics research project.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Mathematical Methods of Physics II', 'code' => 'PHY311', 'description' => 'Partial differential equations, special functions, Green\'s functions, and group theory in physics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Statistical Mechanics II', 'code' => 'PHY411', 'description' => 'Ensemble theory, quantum statistics, phase transitions, and critical phenomena.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Atomic and Molecular Physics', 'code' => 'PHY412', 'description' => 'Atomic structure, spectroscopy, molecular bonding, and molecular spectra.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to Astrophysics', 'code' => 'PHY321', 'description' => 'Stars, galaxies, cosmology, and observational techniques in astronomy.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Electronics II (Digital Electronics)', 'code' => 'PHY315', 'description' => 'Logic gates, Boolean algebra, combinational and sequential circuits, microprocessors.', 'credit_hours' => '3+1', 'category' => 'Major', 'program_level' => 'BS'],
            [ 'name' => 'Solid State Physics II', 'code' => 'PHY501', 'description' => 'Semiconductors, magnetism, superconductivity, and dielectric properties of solids.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Particle Physics', 'code' => 'PHY502', 'description' => 'Elementary particles, fundamental forces, quarks, leptons, and the Standard Model.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'General Relativity and Cosmology', 'code' => 'PHY503', 'description' => 'Principles of general relativity, Einstein\'s field equations, black holes, and the Big Bang model.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Plasma Physics', 'code' => 'PHY504', 'description' => 'Properties of plasmas, plasma waves, confinement, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Laser Physics and Applications', 'code' => 'PHY505', 'description' => 'Principles of lasers, types of lasers, laser-matter interaction, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Medical Physics', 'code' => 'PHY506', 'description' => 'Physics principles in medical imaging, radiation therapy, and diagnostics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Environmental Physics', 'code' => 'PHY507', 'description' => 'Physics of the atmosphere, climate change, renewable energy sources, and pollution.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Materials Science', 'code' => 'PHY508', 'description' => 'Structure, properties, processing, and performance of materials (metals, ceramics, polymers).', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nanophysics and Nanotechnology', 'code' => 'PHY509', 'description' => 'Physics at the nanoscale, synthesis and characterization of nanomaterials, and nanodevices.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Fluid Dynamics', 'code' => 'PHY510', 'description' => 'Ideal and viscous fluid flow, Navier-Stokes equations, boundary layers, and turbulence.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Quantum Mechanics', 'code' => 'PHY511', 'description' => 'Path integrals, quantum field theory concepts, and advanced scattering theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Computational Fluid Dynamics', 'code' => 'PHY512', 'description' => 'Numerical methods for solving fluid flow problems, grid generation, and turbulence modeling.', 'credit_hours' => '2+1', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Optics', 'code' => 'PHY513', 'description' => 'Quantization of the electromagnetic field, coherent states, and quantum phenomena in light-matter interaction.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Condensed Matter Physics', 'code' => 'PHY514', 'description' => 'Advanced topics in solid state physics, including many-body theory and topological insulators.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'High Energy Physics', 'code' => 'PHY515', 'description' => 'Experimental techniques in particle physics, accelerators, detectors, and data analysis.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Stellar Astrophysics', 'code' => 'PHY516', 'description' => 'Stellar structure, evolution, nucleosynthesis, and compact objects (white dwarfs, neutron stars, black holes).', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Galactic and Extragalactic Astronomy', 'code' => 'PHY517', 'description' => 'Structure and dynamics of the Milky Way, galaxies, active galactic nuclei, and large-scale structure of the universe.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Biophysics', 'code' => 'PHY518', 'description' => 'Physics principles applied to biological systems, molecular biophysics, and biomechanics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Renewable Energy Technologies', 'code' => 'PHY519', 'description' => 'Physics of solar, wind, geothermal, and other renewable energy sources.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Physics of Semiconductors and Devices', 'code' => 'PHY520', 'description' => 'Semiconductor physics, p-n junctions, transistors, optoelectronic devices, and integrated circuits.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nonlinear Dynamics and Chaos', 'code' => 'PHY521', 'description' => 'Dynamical systems, bifurcations, strange attractors, fractals, and chaotic behavior in physical systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Information and Computation', 'code' => 'PHY522', 'description' => 'Qubits, quantum gates, quantum algorithms, entanglement, and quantum cryptography.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Atmospheric Physics', 'code' => 'PHY523', 'description' => 'Structure and dynamics of the Earth\'s atmosphere, weather phenomena, and climate modeling.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Geophysics', 'code' => 'PHY524', 'description' => 'Physics of the Earth\'s interior, seismology, geomagnetism, and plate tectonics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Acoustics', 'code' => 'PHY525', 'description' => 'Sound waves, architectural acoustics, ultrasonics, and noise control.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Nuclear Reactor Physics', 'code' => 'PHY526', 'description' => 'Neutron physics, reactor theory, reactor design, and nuclear safety.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Radiation Physics and Dosimetry', 'code' => 'PHY527', 'description' => 'Interaction of radiation with matter, radiation detection, and radiation dose measurement.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Space Physics', 'code' => 'PHY528', 'description' => 'Solar wind, magnetosphere, ionosphere, and space weather.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Advanced Mathematical Physics', 'code' => 'PHY529', 'description' => 'Advanced topics in group theory, differential geometry, and topology relevant to physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Physics Education Research', 'code' => 'PHY530', 'description' => 'Research on teaching and learning of physics, curriculum development, and assessment methods.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Introduction to String Theory', 'code' => 'PHY531', 'description' => 'Basic concepts of string theory, extra dimensions, and dualities.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Physics of Climate Change', 'code' => 'PHY532', 'description' => 'Physical basis of climate change, climate models, and mitigation strategies.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Optical Fiber Communication', 'code' => 'PHY533', 'description' => 'Principles of optical fibers, light propagation, and applications in communication systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Quantum Field Theory I', 'code' => 'PHY534', 'description' => 'Canonical quantization, Klein-Gordon field, Dirac field, and interacting fields.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],
            [ 'name' => 'Physics of Medical Imaging', 'code' => 'PHY535', 'description' => 'Physical principles behind X-ray, CT, MRI, ultrasound, and nuclear medicine imaging.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'BS'],

            // MPhil Subjects
            [ 'name' => 'Advanced Classical Mechanics', 'code' => 'PHY601', 'description' => 'Hamilton-Jacobi theory, canonical transformations, and continuum mechanics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Electrodynamics', 'code' => 'PHY602', 'description' => 'Covariant formulation of electrodynamics, radiation from moving charges, and wave guides.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Quantum Mechanics', 'code' => 'PHY603', 'description' => 'Relativistic quantum mechanics, Dirac equation, and introduction to quantum field theory.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Statistical Mechanics', 'code' => 'PHY604', 'description' => 'Ising model, renormalization group, and non-equilibrium statistical mechanics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Solid State Physics', 'code' => 'PHY605', 'description' => 'Many-body theory, superconductivity, and magnetism in condensed matter systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Nuclear Physics', 'code' => 'PHY606', 'description' => 'Nuclear models, nuclear reactions, and elementary particle physics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Particle Physics', 'code' => 'PHY607', 'description' => 'Standard Model, quantum chromodynamics, and electroweak unification.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Computational Physics', 'code' => 'PHY608', 'description' => 'Monte Carlo methods, molecular dynamics, and simulations in physics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Quantum Field Theory I', 'code' => 'PHY609', 'description' => 'Canonical quantization, path integrals, and Feynman diagrams.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'General Relativity', 'code' => 'PHY610', 'description' => 'Tensor calculus, Einstein\'s field equations, and applications in cosmology and astrophysics.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Plasma Physics', 'code' => 'PHY611', 'description' => 'Plasma waves, instabilities, and fusion physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Laser Physics', 'code' => 'PHY612', 'description' => 'Nonlinear optics, quantum optics, and advanced laser systems.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Condensed Matter Theory', 'code' => 'PHY613', 'description' => 'Green\'s functions, diagrammatic techniques, and topological phases of matter.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Astrophysics and Cosmology', 'code' => 'PHY614', 'description' => 'Stellar evolution, galaxy formation, and the large-scale structure of the universe.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Physics of Nanomaterials', 'code' => 'PHY615', 'description' => 'Synthesis, characterization, and properties of nanomaterials.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Advanced Medical Physics', 'code' => 'PHY616', 'description' => 'Advanced radiation therapy techniques, medical imaging, and health physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Quantum Information Theory', 'code' => 'PHY617', 'description' => 'Quantum computation, quantum cryptography, and quantum communication.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Group Theory for Physicists', 'code' => 'PHY618', 'description' => 'Lie groups, Lie algebras, and their applications in physics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Experimental Techniques in Physics', 'code' => 'PHY619', 'description' => 'Advanced laboratory techniques and instrumentation.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Fluid and Plasma Dynamics', 'code' => 'PHY620', 'description' => 'Magnetohydrodynamics, turbulence, and complex fluids.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Semiconductor Physics and Devices', 'code' => 'PHY621', 'description' => 'Advanced semiconductor physics, heterostructures, and quantum devices.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Surface Science', 'code' => 'PHY622', 'description' => 'Physics and chemistry of surfaces and interfaces.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Nonlinear Optics', 'code' => 'PHY623', 'description' => 'Nonlinear optical phenomena and their applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'MS', ],
            [ 'name' => 'Research Methodology', 'code' => 'PHY698', 'description' => 'Techniques for scientific research, literature review, and proposal writing.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'MS', ],
            [ 'name' => 'MPhil Thesis', 'code' => 'PHY699', 'description' => 'Independent research thesis for the MPhil degree.', 'credit_hours' => '6', 'category' => 'Core', 'program_level' => 'MS', ],

            // PhD Subjects
            [ 'name' => 'Quantum Field Theory II', 'code' => 'PHY701', 'description' => 'Renormalization, quantum electrodynamics, and non-Abelian gauge theories.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Standard Model of Particle Physics', 'code' => 'PHY702', 'description' => 'Gauge theories of electroweak and strong interactions.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Condensed Matter Physics', 'code' => 'PHY703', 'description' => 'Topological insulators, quantum Hall effect, and strongly correlated systems.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'String Theory', 'code' => 'PHY704', 'description' => 'Bosonic and superstring theory, D-branes, and string dualities.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Cosmology and Early Universe', 'code' => 'PHY705', 'description' => 'Inflation, cosmic microwave background, and structure formation.', 'credit_hours' => '3', 'category' => 'Major', 'program_level' => 'PhD', ],
            [ 'name' => 'Quantum Gravity', 'code' => 'PHY706', 'description' => 'Approaches to quantum gravity, including loop quantum gravity and string theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Supersymmetry', 'code' => 'PHY707', 'description' => 'Supersymmetric field theories and their phenomenological implications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Theoretical Physics', 'code' => 'PHY708', 'description' => 'Seminar on current research topics in theoretical physics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Topics in Experimental Physics', 'code' => 'PHY709', 'description' => 'Seminar on current research topics in experimental physics.', 'credit_hours' => '3', 'category' => 'Core', 'program_level' => 'PhD', ],
            [ 'name' => 'Conformal Field Theory', 'code' => 'PHY710', 'description' => 'Conformal symmetry, operator product expansion, and applications.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Quantum Chromodynamics', 'code' => 'PHY711', 'description' => 'Perturbative and non-perturbative QCD, and lattice gauge theory.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Neutrino Physics', 'code' => 'PHY712', 'description' => 'Neutrino oscillations, masses, and their role in astrophysics.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Physics of the Early Universe', 'code' => 'PHY713', 'description' => 'Baryogenesis, nucleosynthesis, and dark matter.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Advanced Materials Characterization', 'code' => 'PHY714', 'description' => 'Advanced techniques for characterizing materials at the nanoscale.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'Spintronics', 'code' => 'PHY715', 'description' => 'Spin-based electronics and quantum computing.', 'credit_hours' => '3', 'category' => 'Elective', 'program_level' => 'PhD', ],
            [ 'name' => 'PhD Dissertation', 'code' => 'PHY799', 'description' => 'Original research culminating in a PhD dissertation.', 'credit_hours' => '12', 'category' => 'Core', 'program_level' => 'PhD', ],
        ];

        $department = 'Department of Physics';    
         
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
