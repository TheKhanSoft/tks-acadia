<?php

namespace Database\Seeders;

use App\Models\EmployeeWorkStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade if needed, though updateOrCreate is on model
use Carbon\Carbon; // Import Carbon for timestamps

class EmployeeWorkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Define the specific employment statuses with descriptions and active status
        $statuses = [
            ['name' => "Working", 'code' => 'WORK', 'description' => 'Employee is actively performing their duties.', 'is_active' => true],
            ['name' => "Transferred", 'code' => 'TRANS', 'description' => 'Employee has been transferred to another position or location.', 'is_active' => true], // Still employed
            ['name' => "Promoted", 'code' => 'PROMO', 'description' => 'Employee has been promoted to a higher position.', 'is_active' => true], // Still employed
            ['name' => "Demoted", 'code' => 'DEMOT', 'description' => 'Employee has been demoted to a lower position.', 'is_active' => true], // Still employed
            ['name' => "Sick Leave", 'code' => 'SICK', 'description' => 'Employee is on approved leave due to illness.', 'is_active' => true], // Still employed
            ['name' => "Earned Leave", 'code' => 'EARNED', 'description' => 'Employee is on approved paid leave.', 'is_active' => true], // Still employed
            ['name' => "Study Leave", 'code' => 'STUDY', 'description' => 'Employee is on approved leave for educational purposes.', 'is_active' => true], // Still employed
            ['name' => "Maternity Leave", 'code' => 'MATERN', 'description' => 'Employee is on approved leave related to childbirth.', 'is_active' => true], // Still employed
            ['name' => "Paternity Leave", 'code' => 'PATERN', 'description' => 'Employee is on approved leave related to childbirth (paternity).', 'is_active' => true], // Still employed
            ['name' => "On Deputation", 'code' => 'DEPUT', 'description' => 'Employee is temporarily assigned to another organization.', 'is_active' => true], // Still employed, but currently work in another organization
            ['name' => "Relieved", 'code' => 'RELIEV', 'description' => 'Employee has been formally released from their duties (e.g., end of contract, transfer out).', 'is_active' => true], // No longer employed in this context
            ['name' => "Contract Expired", 'code' => 'CONEXP', 'description' => 'Employee\'s fixed-term contract has ended.', 'is_active' => true], // No longer employed
            ['name' => "Retired", 'code' => 'RETIRE', 'description' => 'Employee has officially ended their career.', 'is_active' => true], // No longer employed
            ['name' => "Suspended", 'code' => 'SUSPEN', 'description' => 'Employee is temporarily barred from performing duties, pending investigation or disciplinary action.', 'is_active' => true], // Still employed, but restricted
            ['name' => "Removal From Service", 'code' => 'REMOVE', 'description' => 'Employee has been dismissed due to disciplinary reasons.', 'is_active' => true], // No longer employed
            ['name' => "Terminated", 'code' => 'TERMIN', 'description' => 'Employee\'s employment has been ended by the employer (non-disciplinary or disciplinary).', 'is_active' => true], // No longer employed
            ['name' => "Died", 'code' => 'DIED', 'description' => 'Employee passed away while in service.', 'is_active' => true] // No longer employed
        ];

        // Use updateOrCreate to seed the data, ensuring descriptions and active status are set
        foreach ($statuses as $statusData) {
            EmployeeWorkStatus::updateOrCreate(
                ['name' => $statusData['name']], // Unique key to find existing record
                [
                    'code' => $statusData['code'],
                    'description' => $statusData['description'],
                    'is_active' => $statusData['is_active'],
                    'created_at' => $now, // Set timestamps only if creating
                    'updated_at' => $now,
                ]
            );
        }
    }
}
