<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubjectType;

class SubjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjectTypes = [
            ['name' => 'Core', 'description' => 'Unlock essential knowledge with Core subjects, the foundational pillars of your academic journey, mandatory for a comprehensive understanding.'],
            ['name' => 'Elective', 'description' => 'Tailor your learning path with Elective subjects, offering a curated selection to deepen your expertise in chosen areas.'],
            ['name' => 'Specialization', 'description' => 'Dive deep into your passion with Specialization subjects, designed to provide mastery in a focused field of study.'],
            ['name' => 'Optional', 'description' => 'Explore your intellectual curiosity with Optional subjects, giving you the freedom to select topics that intrigue and inspire you.'],
            ['name' => 'Foundation', 'description' => 'Build a strong academic base with Foundation subjects, equipping you with the essential concepts for higher-level learning.'],
            ['name' => 'Practical', 'description' => 'Transform theory into action with Practical subjects, emphasizing hands-on application and real-world problem-solving.'],
            ['name' => 'Project', 'description' => 'Showcase your capabilities with Project subjects, culminating in a significant piece of work that highlights your acquired skills and knowledge.'],
            ['name' => 'Thesis', 'description' => 'Embark on a scholarly investigation with Thesis subjects, involving in-depth research and critical analysis on a chosen topic.'],
            ['name' => 'Internship', 'description' => 'Gain invaluable professional experience with Internship subjects, bridging the gap between academic learning and real-world industry demands.'],
            ['name' => 'Seminar', 'description' => 'Engage in dynamic academic discourse with Seminar subjects, fostering critical thinking through discussions and expert presentations.'],
            ['name' => 'Workshop', 'description' => 'Sharpen your skills in intensive Workshop subjects, offering focused, hands-on training in specific techniques or areas.'],
            ['name' => 'Laboratory', 'description' => 'Discover and experiment in Laboratory subjects, providing a controlled environment for practical exploration and scientific inquiry.'],
            ['name' => 'Research', 'description' => 'Cultivate your investigative prowess with Research subjects, designed to develop your analytical skills and contribute to knowledge.'],
            ['name' => 'Capstone', 'description' => 'Integrate and apply your learning in Capstone subjects, a culminating experience that synthesizes your entire program of study.'],
            ['name' => 'Audit', 'description' => 'Expand your horizons by Auditing subjects, allowing you to explore diverse topics for personal enrichment without academic credit.'],
            ['name' => 'Independent Study', 'description' => 'Chart your own academic course with Independent Study, pursuing a unique topic of interest under expert faculty guidance.'],
            ['name' => 'Field Work', 'description' => 'Experience learning beyond the classroom with Field Work subjects, engaging in practical data collection and real-world application.'],
        ];

        foreach ($subjectTypes as $type) {
            SubjectType::Create($type);
        }
    }
}
