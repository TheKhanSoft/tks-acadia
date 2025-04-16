<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Generator as FakerGenerator; // Import Faker

class TestFakerProviders extends Command
{
    protected $signature = 'test:faker {count=5 : Number of samples to generate}'; // Added count option
    protected $description = 'Tests the custom pk_PK Faker providers by generating sample data';

    // Inject Faker Generator through the constructor for cleaner dependency management
    protected $faker;

    public function __construct(FakerGenerator $faker)
    {
        parent::__construct();
        $this->faker = $faker;
    }

    public function handle(): int // Or void for older Laravel versions
    {
        $count = (int) $this->argument('count');
        $this->info("--- Testing Custom Faker Providers (Sample Count: {$count}) ---");

        for ($i = 0; $i < $count; $i++) {
            $this->line("\n--- Sample " . ($i + 1) . " ---");

            $this->comment('Address:');
            $this->line('  Street Address: ' . $this->faker->streetAddress());
            $this->line('  Mohallah: ' . ($this->faker->optional(0.6)->mohallah() ?? 'N/A')); // Use optional here too
            $this->line('  Village: ' . ($this->faker->optional(0.3)->village() ?? 'N/A'));
            $this->line('  City: ' . $this->faker->city());
            $this->line('  Tehsil: ' . ($this->faker->optional(0.7)->tehsil() ?? 'N/A'));
            $this->line('  District: ' . ($this->faker->optional(0.8)->district() ?? 'N/A'));
            $this->line('  Province: ' . $this->faker->province());
            $this->line('  Postal Code: ' . $this->faker->postcode());

            $this->comment('Person:');
            $this->line('  Name: ' . $this->faker->name());
            $this->line('  First Name (M): ' . $this->faker->firstNameMale());
            $this->line('  First Name (F): ' . $this->faker->firstNameFemale());
            $this->line('  Last Name: ' . $this->faker->lastName());
            
            $this->comment('Contact Details:');
            $this->line('  Mobile: ' . $this->faker->mobileNumber());
            $this->line('  Landline: ' . $this->faker->landlineNumber());
            $this->line('  Username: ' . $this->faker->username());
            $this->line('  Email: ' . $this->faker->customEmail());
            $this->line('  Domain Name: ' . $this->faker->domainName());

            $this->comment('Company:');
            $this->line('  Company Name: ' . $this->faker->company());
            $this->line('  Industry: ' . $this->faker->industry());
        }

        $this->info("\n--- Testing Complete ---");
        return Command::SUCCESS; // Or return 0;
    }
}