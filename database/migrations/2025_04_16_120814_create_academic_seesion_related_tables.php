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
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('e.g., "2023-2027", "Fall 2023 Semester", "Spring 2024"');
            $table->unsignedInteger('year')->comment("e.g., 2023, 2024");
            $table->string('type', 15)->comment('e.g., "Fall", "Spring", "Summer"');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('date_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('e.g., "Start Date (Old Student)", "Start Date (New Student)", "End Date", "Midterm"');
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

         /**
         * s-1 = Fall 2024
         * 
         * c-1 = Garden
         * 
         * d-1 = Date Type ('Start Date') - OLD
         * d-2 = Date Type ('Start Date') - NEW
         * d-3 = Date Type ('End Date') - ALL
         * d-4 = Date Type ('Midterm Date')
         * d-5 = Date Type ('Final term Date')
         * d-6 = Date Type ('Thesis Submission')
         * 
         * SESSION | CAMPUS   | DATE TYPE|   DATE 
         *  s-1    |   c-1->  |  d-1     |  01-10-2024
         *  s-1    |   c-1->  |  d-2     |  11-11-2024
         *  s-1    |   c-1->  |  d-4     |  11-11-2024
         * 
         */

        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->foreignId('date_type_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        
            $table->unique(['session_id', 'campus_id', 'date_type_id'], 'unique_session_campus_date_type');
        });

        Schema::create('session_campus_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable(); // e.g., "Adjusted for Hilly Area calendar", "Exam period starts X"
            $table->timestamps();
            $table->softDeletes();
        
            $table->unique(['session_id', 'campus_id'], 'unique_session_campus_dates');
            // $table->primary(['session_id', 'campus_id'], 'unique_session_campus_dates');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'session_campus_dates'); 
        Schema::dropIfExists(table: 'academic_calendars'); 
        Schema::dropIfExists(table: 'academic_sessions'); 
        Schema::dropIfExists(table: 'date_types'); 
    }
};
