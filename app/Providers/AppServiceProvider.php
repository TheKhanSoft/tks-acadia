<?php

namespace App\Providers;

use Faker\Generator as FakerGenerator;
use Illuminate\Contracts\Foundation\Application; // For Laravel 11+ singleton binding
// Import your custom providers
use App\Faker\Provider\pk_PK\Person;
use App\Faker\Provider\pk_PK\Address;
use App\Faker\Provider\pk_PK\PhoneNumber;
use App\Faker\Provider\pk_PK\Company;
use App\Faker\Provider\pk_PK\Internet; 
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(FakerGenerator::class, function (Application $app) {
        //     $faker = new FakerGenerator();
        //     $faker->addProvider(new Person($faker));
        //     $faker->addProvider(new Address($faker));
        //     $faker->addProvider(new PhoneNumber($faker));
        //     return $faker;
        // });

        $this->app->singleton(FakerGenerator::class, function (Application $app) {
            $faker = \Faker\Factory::create(); // Create the base Faker instance
            $faker->addProvider(new Person($faker));
            $faker->addProvider(new Address($faker));
            $faker->addProvider(new PhoneNumber($faker));
            $faker->addProvider(new Company($faker));
            $faker->addProvider(new Internet($faker)); 
            // Add other PK providers
            return $faker;
       });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
