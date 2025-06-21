<?php

use App\Enums\Gender;
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
        Schema::create('student_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Active", "Graduated", "Withdrawn", "On Leave"
            $table->text('description')->nullable()->comment('Optional description of the status');
            $table->boolean('is_active_status')->default(true)->comment('Indicates if this status means the student is currently active');
            $table->timestamps();
            $table->softDeletes();
        });

        $gender = array_column(Gender::cases(), 'value');

        Schema::create('students', function (Blueprint $table) use ($gender) {
            $table->id();
            $table->string('student_id')->unique()->comment('Unique identifier for the student (e.g., "S2023-001")');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('phone_alternative')->nullable();
            $table->enum('gender', $gender)->default($gender[0]);
            $table->string('nic_no', config('constants.nic_no_length', 15))->nullable()->unique()->comment('National Identity Card number, unique for active records');
            $table->date('date_of_birth')->nullable();
            $table->string('postal_address', 500)->nullable();
            $table->string('permanent_address', 500)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->comment('City of residence, optional for students');
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable()->comment('Brief biography or notes');
            $table->foreignId('student_status_id')->constrained('student_statuses')->comment('Overall status of the student');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['last_name', 'first_name'], 'student_full_name_index'); // Added for performance on name searches
        });

        Schema::create('enrollment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('e.g., Enrolled, Completed, Dropped Out, Cancelled');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false)->comment('Indicates if this status represents an ongoing/active enrollment');
            $table->timestamps();
            $table->softDeletes();
        });

        //What if the table name is changed to program_enrollments?
        Schema::create('student_program_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->comment('Links to program offered by a department/faculty');
            $table->foreignId('academic_session_id')->constrained();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date');
            $table->date('expected_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->decimal('grades', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('enrollment_status_id')->constrained();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'program_id', 'academic_session_id'], 'student_program_session_unique');
            $table->index('enrollment_date');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //session_enrollments
        //session_offerings
        Schema::dropIfExists('student_program_enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('enrollment_statuses'); 
        Schema::dropIfExists('student_statuses');
    }
};