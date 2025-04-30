<?php

namespace Database\Seeders;

use App\Models\EmploymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['type' => "Working", 'code' => "WORK"],
            ['type' => "Transferred", 'code' => "TRNSD"],
            ['type' => "Promoted", 'code' => "PROMO"],
            ['type' => "Demoted", 'code' => "DEMO"],
            ['type' => "Sick Leave", 'code' => "SL"],
            ['type' => "Earned Leave", 'code' => "EL"],
            ['type' => "Study Leave", 'code' => "STD-LEV"],
            ['type' => "Maternity Leave", 'code' => "MET-LEV"],
            ['type' => "Fraternity Leave", 'code' => "FR-LEV"],
            ['type' => "On Deputation", 'code' => "DEPUTATION"],
            ['type' => "Contract Expired", 'code' => "CON-EXP"],
            ['type' => "Relieved", 'code' => "RELD"],
            ['type' => "Retired", 'code' => "RTD"],
            ['type' => "Suspended", 'code' => "SUSPND"],
            ['type' => "Removal From Service", 'code' => "RFS"],
            ['type' => "Terminated", 'code' => "TERM"],
            ['type' => "Died", 'code' => "DIED"]
        ];

        EmploymentStatus::insert($statuses);
    }
}
