<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    { 

        $date_types = [
            ['id' => 1, 'name' => 'Start Date (Old Student)', 'description' => ''],
            ['id' => 2, 'name' => 'Start Date (New Student)', 'description' => ''],
            ['id' => 3, 'name' => 'End Date', 'description' => ''],
            ['id' => 4, 'name' => 'Midterm', 'description' => ''],
            ['id' => 5, 'name' => 'Final term', 'description' => ''],
            ['id' => 6, 'name' => 'Theses Submission Date', 'description' => ''],
            ['id' => 7, 'name' => '', 'description' => ''],
            ['id' => 8, 'name' => '', 'description' => ''],
        ];
       
        foreach ($date_types as $date_type) {
            Faculty::create($date_types);
        }
    }
}
