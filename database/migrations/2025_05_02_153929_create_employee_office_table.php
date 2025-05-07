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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_office');
    }
};
