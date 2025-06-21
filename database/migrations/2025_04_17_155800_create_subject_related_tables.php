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
        Schema::create('subject_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'core', 'elective'
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('credit_hours', 10);
            $table->foreignId('parent_department_id')->constrained('offices')->onDelete('cascade');
            // $table->foreignId('prerequisite_subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('subject_type_id')->nullable()->constrained('subject_types')->onDelete('set null'); // Changed from enum
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Corrected unique constraint to use 'code' instead of 'subject_code'
            $table->unique(['parent_department_id', 'code', 'name'], 'unique_department_subject_name');
        });

        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('prerequisite_subject_id')->constrained('subjects')->onDelete('cascade'); 
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->morphs('outcomeable'); // Changed to polymorphic relationship
            $table->text('outcomes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('department_subjects', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('department_id')->constrained('offices')->onDelete('cascade'); 
        //     $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); 
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->unique(['department_id', 'subject_id']);
        //     // $table->index(columns: 'is_active'); // Added
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('department_subjects');
        Schema::dropIfExists('learning_outcomes'); // Corrected table name
        Schema::dropIfExists('subject_prerequisite'); // Added missing table drop
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('subject_types');
    }
};
