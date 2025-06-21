<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SessionOffering; // Assuming the model name is SessionOffering

class SessionOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming SessionOfferingFactory exists and is set up
        // You might need to adjust the count or logic based on your application's needs
        // For example, ensuring that related Session, ProgramSubject, and Employee records exist
        // or are created by the factory.
        // If SessionOffering model does not exist, this will need to be created first.
        // For now, I'll assume it exists and has a factory.
        if (class_exists(\App\Models\SessionOffering::class)) {
            \App\Models\SessionOffering::factory()->count(50)->create();
        } else {
            // Optionally, you can output a message if the model or factory doesn't exist
            // $this->command->info('SessionOffering model or factory not found, skipping SessionOfferingSeeder.');
        }
    }
}
