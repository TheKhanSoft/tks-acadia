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

        //SESSION_OFFERINGS: session + program_subject + teacher + semester
        Schema::create('session_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained();
            $table->foreignId('program_subject_id')->constrained(); // Replace with actual table name for program subjects
            // $table->bigInteger('program_subject_id');
            $table->foreignId('employee_id')->constrained();
            $table->tinyInteger('semester')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_session_id', 'program_subject_id', 'employee_id'], 'session_program_subject');
        });

         //SESSION_ENROLLMENTS: session_offering + student
         Schema::create('session_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_offering_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->date('enrollment_date');
            $table->decimal('grades', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status');
            //$table->foreignId('enrollment_status_id')->constrained('enrollment_statuses');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['session_offering_id', 'student_id'], 'student_program_session_unique');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'session_enrollments'); 
        Schema::dropIfExists(table: 'session_offerings'); 
    }
};
