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
        Schema::create('delivery_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('degree_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Undergraduate, Graduate, Postgraduate, Diploma, Certificate
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('equivalent')->nullable(); 
            $table->unsignedInteger('min_semester')->nullable();
            $table->unsignedInteger('max_semester')->nullable();
            $table->unsignedInteger('total_credit_hours')->nullable();
            $table->json('prerequisites')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('degree_title'); 
            $table->text('description')->nullable();
            $table->foreignId('department_id')->constrained("offices");
            $table->foreignId('degree_level_id')->constrained(); 
            $table->foreignId('delivery_mode_id')->nullable()->constrained('delivery_modes');            
            $table->string('duration')->nullable();
            $table->unsignedInteger('min_semester')->nullable();
            $table->unsignedInteger('max_semester')->nullable();
            $table->string('total_credit_hours', 10)->nullable();
            $table->string('equivalent')->nullable(); 
            $table->string('accreditation_status')->nullable()->default('N/A'); 
            $table->date('start_date')->nullable();
            $table->json('prerequisites')->nullable(); 
            $table->json('learning_outcomes')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('department_programs', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('department_id')->constrained("offices");
        //     $table->foreignId('program_id')->constrained();
        //     $table->date('offered_since')->nullable();
        //     $table->integer('annual_intake')->unsigned()->nullable();
        //     $table->boolean('is_flagship_program')->default(false);
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        //     $table->softDeletes();
            
        //     // Ensure unique office-program combinations
        //     $table->unique(['department_id', 'program_id']);
        // });

        Schema::create('program_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_campus_date_id')->constrained()->onDelete('cascade'); // The specific session/campus for this offering
            $table->foreignId('delivery_mode_id')->constrained('delivery_modes')->onDelete('cascade'); // The primary delivery mode for this offering       
            $table->string('offering_name')->nullable(); // e.g., "BSCS Fall 2023 Online Main Campus"
            $table->unsignedInteger('max_capacity')->nullable();
            $table->boolean('is_open_for_admission')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['program_id', 'session_campus_date_id', 'delivery_mode_id'], 'unique_program_offering');
        });
        

        Schema::create('program_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade'); 
            $table->unsignedTinyInteger('semester')->nullable(); 
            $table->boolean('is_elective')->default(false)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['program_id', 'subject_id', 'semester'], 'unique_subject_semester');
        });

        Schema::create('program_coordinators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained();
            $table->foreignId('coordinator_id')->constrained('employees');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('accreditation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');      // e.g., 'Not Applicable', 'Applied'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('accreditations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained();
            $table->string('accreditation_status')->nullable()->default('N/A'); 
            $table->foreignId('accreditation_type_id')->constrained('accreditation_types')->nullable();
            $table->string('accredited_by')->nullable(); 
            $table->string('accredited_at')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accreditations');
        Schema::dropIfExists('accreditation_types');
        Schema::dropIfExists('program_coordinators');
        Schema::dropIfExists('program_subjects');
        Schema::dropIfExists('program_offerings');
        Schema::dropIfExists('department_programs');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('degree_levels');
        Schema::dropIfExists('delivery_modes');
    }
};
