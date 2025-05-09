<?php

use App\PromotionEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string( 'name', 150)->unique();
            $table->string('iso3', 3);
            $table->string('iso2', 2);
            $table->unsignedSmallInteger('numeric_code');
            $table->unsignedSmallInteger('phonecode');
            $table->string('capital', 50);
            $table->string('currency', 4);
            $table->string('currency_name', 50);
            $table->string('currency_symbol', 50);
            $table->string('tld', 3);
            $table->string('native', 150);
            $table->string('region', 50);
            $table->unsignedTinyInteger('region_id');
            $table->string('subregion', 20);
            $table->unsignedTinyInteger('subregion_id');
            $table->string('nationality', 150);
            $table->text('timezones');
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',11,8);
            $table->string('emoji', 200);
            $table->string('emojiU', 200);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('state_code', 5);
            $table->string('type', 191)->nullable();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('country_code', 5);
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',11,8);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'country_id']);

        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',11,8);
            $table->string('wikiDataId', 255)->nullable()->comment('Rapid API GeoDB Cities');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'state_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('counters');
    }
};
