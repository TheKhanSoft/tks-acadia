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
        Schema::create('employee_work_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable()->comment('remarks, comments, description etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Faculty Member, Administrative Staff, Ministerial Staff, etc.
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique(); // Employee ID number
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('gender',['Male', 'Female', 'Other'])->default('Male')->comment('Male, Female, Other => Shemale');
            $table->string('nic_no', 15)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('job_nature_id')->constrained('job_natures')->comment('Permanent, Contract, Fixed Pay etc.');
            $table->foreignId('employee_type_id')->constrained()->comment('Staff, Faculty, Technical Staff etc.');
            $table->date('appointment_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('qualification')->nullable()->comment('Highest degree, qualification.');
            $table->string('specialization')->nullable()->comment('Latest Specialization field.');
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->foreignId('employee_work_status_id')->constrained('employee_work_statuses')->comment('Earned Leave, Terminated.');;
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_office', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->constrained()->onDelete('cascade'); // Constraint should work now as offices table exists
            $table->string('role')->nullable(); // e.g., Head, Member, Coordinator
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_primary_office')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Ensure an employee can only be assigned to an office once at any given time (unless soft deleted)
            $table->unique(['employee_id', 'office_id', 'role', 'start_date']);

            // $table->foreignId('head_id')->constrained('employees')->nullable();
        });

      

        // Schema::create('head_office', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('office_id'); // Department, Section, Administrative Office, Constituent College, Hostel, etc.
        //     $table->foreignId('head_id')->nullable()->constrained('employees');
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_office');
        Schema::dropIfExists('employee_office');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('employee_types');
        Schema::dropIfExists('employee_work_statuses');
    }
};
