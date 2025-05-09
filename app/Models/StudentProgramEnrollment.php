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
        'session_id',
        'enrollment_date',
        'expected_completion_date',
        'actual_completion_date',
        'grades',
        'remarks',
        'enrollment_status_id',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'grades' => 'decimal:2',
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
     * Get the session that the enrollment belongs to.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the enrollment status for the enrollment.
     */
    public function enrollmentStatus()
    {
        return $this->belongsTo(EnrollmentStatus::class);
    }
}
