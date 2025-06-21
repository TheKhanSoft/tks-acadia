<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AcademicSession; // Added import

class SessionOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_session_id', 
        'program_subject_id',
        'employee_id',
        'semester',
        'remarks',
        'status',
    ];

    /**
     * Get the session associated with the offering.
     */
    public function academicSession() // Renamed method and changed model
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id'); // Changed foreign key
    }

    /**
     * Get the program subject associated with the offering.
     */
    public function programSubject()
    {
        // Assuming ProgramSubject model exists
        return $this->belongsTo(ProgramSubject::class);
    }

    /**
     * Get the employee (teacher) associated with the offering.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the session enrollments for this offering.
     */
    public function sessionEnrollments()
    {
        // Assuming SessionEnrollment model exists
        return $this->hasMany(SessionEnrollment::class);
    }
}
