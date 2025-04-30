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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string( 'short_name')->unique();
            $table->foreignId('office_type_id')->constrained();
            $table->foreignId('campus_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('faculty_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->foreignId('head_id')->nullable()->constrained('employees');
            $table->date('head_appointment_date')->nullable();
            $table->string('office_location')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('established_year')->nullable();
            $table->foreignId('parent_office_id')->nullable()->constrained('offices');
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
        Schema::dropIfExists('offices');
    }
};
