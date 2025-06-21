<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Faculty; // Assuming Program belongs to a Faculty
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Program::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $programLevels = ['Undergraduate', 'Graduate', 'Postgraduate', 'Diploma', 'Certificate'];
        $deliveryModes = ['On-Campus', 'Online', 'Hybrid'];
        $accreditations = ['HEC Recognized', 'PEC Accredited', 'PMDC Accredited', 'NBEAC Accredited', 'NCEAC Accredited', 'Not Applicable'];

        $programDetails = [
            [ 'title' => 'AD', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Associate Degree', 'equivalent' => '14'],
            [ 'title' => 'BS', 'minSemesters' => 8, 'maxSemesters' => 12, 'degreeTitle' => 'Bachelor of Science', 'equivalent' => '16'],
            [ 'title' => 'MS', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science', 'equivalent' => '18'],
            [ 'title' => 'MPhil', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Philosophy', 'equivalent' => '18'],
            [ 'title' => 'PhD', 'minSemesters' => 4, 'maxSemesters' => 12, 'degreeTitle' => 'Doctor of Philosophy', 'equivalent' => '20'],
            [ 'title' => 'MA', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Art (16 years)', 'equivalent' => '16'],
            [ 'title' => 'MSc', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science (16 years)', 'equivalent' => '16'],
            [ 'title' => 'MSc (Hons)', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Science', 'equivalent' => '18'],
        ];

        $programNames = [
            'Accounting & Finance',
            'Artificial Intelligence',
            'Chemistry',
            'Computer Science',
            'Data Science',
            'Economics',
            'English Literature',
            'Information Technology',
            'International Relations',
            'Islamic Studies',
            'Mathematics',
            'Media Studies',
            'Physics',
            'Psychology',
            'Sociology',
            'Software Engineering',
            'Urdu',
        ];

        $programWithDiscipline = [
            [ 'title' => 'BBA (Hons)', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Bachelor of Business Administration', 'equivalent' => '16'],
            [ 'title' => 'MBA', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Business Administration', 'equivalent' => '18'],
            [ 'title' => 'Executive MBA', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Executive Master of Business Administration', 'equivalent' => '18'],
            [ 'title' => 'Pharm-D', 'minSemesters' => 5, 'maxSemesters' => 14, 'degreeTitle' => 'Doctor of Pharmacy', 'equivalent' => '16'],
            [ 'title' => 'LLB', 'minSemesters' => 5, 'maxSemesters' => 14, 'degreeTitle' => 'Bachelor of Law', 'equivalent' => '16'],
            [ 'title' => 'DPT', 'minSemesters' => 4, 'maxSemesters' => 6, 'degreeTitle' => 'Doctor of Physical Therapy', 'equivalent' => '14'],
            [ 'title' => 'LLM', 'minSemesters' => 3, 'maxSemesters' => 6, 'degreeTitle' => 'Master of Law', 'equivalent' => '18'],
            [ 'title' => 'Diploma in Web', 'minSemesters' => 2, 'maxSemesters' => 2, 'degreeTitle' => 'Diploma in Web Development', 'equivalent' => '14'],
            [ 'title' => 'Certificate in GD', 'minSemesters' => 1, 'maxSemesters' => 1, 'degreeTitle' => 'Certificate in Graphic Design', 'equivalent' => '14'],
        ];

        $allPrograms = [];
        foreach ($programDetails as $programDetail) {
            foreach ($programNames as $program) {
                $allPrograms .= ['title' => $programDetail['title'] . " " . $program, 'minSemesters' => $programDetail['minSemesters'], 'maxSemesters' => $programDetail['maxSemesters'], 'degreeTitle' => $programDetail['degreeTitle'], 'equivalent' => $programDetail['equivalent']];
            }
        }
        $degreeTitles = [
            'Bachelor of Science', 'Bachelor of Engineering', 'Bachelor of Business Administration', 'Master of Business Administration',
            'Bachelor of Medicine, Bachelor of Surgery', 'Doctor of Pharmacy', 'Master of Arts', 'Master of Science', 
            'Doctor of Philosophy', 'Diploma', 'Certificate'
        ];

        $pakistaniProgramNames = [
            'BS Computer Science', 'BS Software Engineering', 'BS Information Technology', 'BS Data Science', 'BS Artificial Intelligence',
            'BBA (Hons)', 'MBA', 'Executive MBA', 'BS Accounting & Finance', 'BS Economics',
            'MBBS', 'BDS', 'Pharm-D', 'BS Nursing', 'DPT (Doctor of Physical Therapy)',
            'BE Electrical Engineering', 'BE Mechanical Engineering', 'BE Civil Engineering', 'BE Chemical Engineering',
            'MA English Literature', 'MA Urdu', 'MA Islamic Studies', 'BS International Relations', 'BS Media Studies',
            'M.Phil Physics', 'PhD Chemistry', 'M.Sc Mathematics',
            'Diploma in Web Development', 'Certificate in Graphic Design'
        ];
        
        $minSem = $this->faker->numberBetween(2, 8);
        $maxSem = $minSem + $this->faker->numberBetween(0, 4);

        return [
            'name' => $this->faker->unique()->randomElement($pakistaniProgramNames),
            'code' => $this->faker->unique()->bothify(strtoupper(substr(str_replace(' ', '', $this->faker->randomElement($pakistaniProgramNames)), 0, 3)) . '-###'), // e.g., BCS-101
            'description' => $this->faker->optional()->paragraph(2),
            'degree_title' => $this->faker->randomElement($degreeTitles),
            'duration' => $this->faker->randomElement(['2 Years', '3 Years', '4 Years', '5 Years', '1 Year', '6 Months']), // General duration string
            'equivalent' => $this->faker->optional()->word,
            'min_semester' => $minSem,
            'max_semester' => $maxSem,
            'total_credit_hours' => $this->faker->numberBetween(60, 150),
            'program_level' => $this->faker->randomElement($programLevels),
            'delivery_mode' => $this->faker->randomElement($deliveryModes),
            'accreditation' => $this->faker->optional()->randomElement($accreditations),
            'start_date' => $this->faker->optional()->date('Y-m-d', '-10 years'),
            'prerequisites' => $this->faker->optional()->words(3, true), // Example: "Intermediate, FSc Pre-Engineering"
            'learning_outcomes' => $this->faker->optional()->sentences(2, true),
            // 'coordinator_id' => Employee::inRandomOrder()->first()?->id, // Assuming Employee model and seeder exist
            'coordinator_id' => null, // Set to null for now
            'is_active' => $this->faker->boolean(90),
            // 'faculty_id' is not a direct field in Program model based on your fillable.
            // It's usually associated via DepartmentProgram or similar pivot/relationship.
            // The DepartmentProgramFactory handles linking Program to a Faculty (as office_id).
        ];
    }
}
