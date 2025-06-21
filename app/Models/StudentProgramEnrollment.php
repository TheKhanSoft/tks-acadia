<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProgramEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'department_program_id', // Links to program offered by a department/faculty
        'academic_session_id',
        'enrollment_date',
        'expected_completion_date',
        'actual_completion_date',
        'grades', // Storing overall grade or GPA for the enrollment
        'remarks',
        'enrollment_status_id',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
        // 'grades' => 'decimal:2', // Keep as string or handle specific type if needed, e.g. JSON for detailed grades
    ];

    /**
     * Get the student that the enrollment belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the department program that the enrollment belongs to.
     */
    public function departmentProgram()
    {
        return $this->belongsTo(DepartmentProgram::class);
    }

    /**
     * Accessor to get the Program model directly.
     * This allows $enrollment->program->name
     */
    public function getProgramAttribute()
    {
        // Ensure departmentProgram and its program relation are loaded or exist
        if ($this->departmentProgram && $this->departmentProgram->program) {
            return $this->departmentProgram->program;
        }
        return null; // Or handle as appropriate if relations might not exist
    }

    /**
     * Get the academic session that the enrollment belongs to.
     */
    public function academicSession() // Renamed from session() to avoid conflict if a 'session' field/method is needed
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    /**
     * Get the enrollment status for the enrollment.
     * Renamed from enrollmentStatus() to status() for brevity, e.g., $enrollment->status->name
     */
    public function status()
    {
        return $this->belongsTo(EnrollmentStatus::class, 'enrollment_status_id');
    }
}
