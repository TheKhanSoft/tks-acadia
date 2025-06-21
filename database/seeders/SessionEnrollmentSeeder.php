<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SessionEnrollment;

class SessionEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming SessionEnrollmentFactory exists and is set up
        // You might need to adjust the count or logic based on your application's needs
        // For example, ensuring that related SessionOffering and Student records exist
        // or are created by the factory.
        if (class_exists(\App\Models\SessionEnrollment::class)) {
            \App\Models\SessionEnrollment::factory()->count(100)->create();
        } else {
            // Optionally, you can output a message if the model or factory doesn't exist
            // $this->command->info('SessionEnrollment model or factory not found, skipping SessionEnrollmentSeeder.');
        }
    }
}
