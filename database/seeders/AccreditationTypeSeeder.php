<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccreditationType; // Simplified alias
// use App\Enums\AccreditationType as AccreditationTypeEnum; // Enum might not be directly used here anymore

class AccreditationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Updated to a simple array of names
        $accreditationTypeNames = [
            'Not Applicable',
            'Applied',
            'Pending',
            'Processed',
            'In Review',
            'Conditionally Accredited',
            'Accredited',
            'Rejected',
            'Suspended',
            'Revoked',
            'Expired',
            'Withdrawn',
            'Deferred',
            'Appealed',
            'Probationary',
        ];

        foreach ($accreditationTypeNames as $typeName) {
            AccreditationType::firstOrCreate(
                ['name' => $typeName]
            );
        }

        $this->command->info('Default accreditation types seeded successfully!');
    }
}
