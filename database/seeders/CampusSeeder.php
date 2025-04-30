<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Campus::factory(200)->create();
        $campuses = [
            ['id' => 1,  'name' => 'Garden Campus', 'short_name' => 'GARDEN', 'code' => 'GARDEN', 'address' => 'Palatoo Road Mardan', 'location' => 'Mardan', 'phone' => '+929379230545', 'email' => 'registrar@awkum.edu.pk' ],
            ['id' => 2,  'name' => 'Main Campus', 'short_name' => 'MAIN', 'code' => 'MAIM', 'address' => 'College Chowk Mardan', 'location' => 'Mardan', 'phone' => '+929379230545', 'email' => 'coordinator-main@awkum.edu.pk' ],
            ['id' => 3,  'name' => 'Timergara Campus', 'short_name' => 'TIMERGARA', 'code' => 'TIMERGARA', 'address' => 'Address of Timergara Campus', 'location' => 'Timergara', 'phone' => '+929379230545', 'email' => 'coordinator-tmg@awkum.edu.pk' ],
            ['id' => 4,  'name' => 'Pabbi Campus', 'short_name' => 'PABBI', 'code' => 'PABBI', 'address' => 'Peshawar Road, Pabbi, Nowshera', 'location' => 'Nowshera', 'phone' => '+929379230545', 'email' => 'coordinator-pabbi@awkum.edu.pk' ],
        ];

        Campus::insert($campuses);

    }
}
