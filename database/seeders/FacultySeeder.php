<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    { 
        $faculties = [
            ['id' => 1, 'name' => 'Arts & Humanities', 'code' => 'AH'],
            ['id' => 2, 'name' => 'Business & Economics', 'code' => 'BE'],
            ['id' => 3, 'name' => 'Chemical & Life Sciences', 'code' => 'CLS'],
            ['id' => 4, 'name' => 'Phyical & Numerical Sciences', 'code' => 'PNS'],
            ['id' => 5, 'name' => 'Social Sciences', 'code' => 'SS'],
        ];
       
        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}
