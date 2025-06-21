<?php

namespace Database\Seeders;

use App\Models\DeliveryMode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeliveryModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    { 

        $modes = [
            ['id' => 1, 'name' => 'On-Campus', 'code' => 'OC', 'description' => ''],
            ['id' => 2, 'name' => 'Online', 'code' => 'ON', 'description' => ''],
            ['id' => 3, 'name' => 'Hybrid', 'code' => 'HY', 'description' => ''],
            ['id' => 4, 'name' => 'Not Offering Anymore', 'code' => 'NO', 'description' => ''], 
        ];
       
        foreach ($modes as $mode) {
            DeliveryMode::create($mode);
        }
    }
}
