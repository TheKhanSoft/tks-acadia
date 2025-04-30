<?php

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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('degree_title'); // BSc, MSc, PhD, etc.
            $table->string('duration'); // e.g., "4 years", "2 years"
            $table->string('equivalent')->nullable(); // Equivalent degree information
            $table->integer('min_semester')->unsigned();
            $table->integer('max_semester')->unsigned();
            $table->integer('total_credit_hours')->unsigned()->nullable();
            $table->string('program_level'); // Undergraduate, Graduate, etc.
            $table->string('delivery_mode')->nullable(); // On-campus, Online, Hybrid
            $table->string('accreditation')->nullable(); // Accreditation information
            $table->date('start_date')->nullable(); // When the program was first offered
            $table->json('prerequisites')->nullable(); // JSON of prerequisites
            $table->json('learning_outcomes')->nullable(); // JSON of learning outcomes
            $table->foreignId('coordinator_id')->nullable()->constrained('employees');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
