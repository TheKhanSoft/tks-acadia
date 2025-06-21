<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'session_offering_id',
        'student_id',
        'enrollment_date',
        'grades',
        'remarks',
        'status',
        // 'enrollment_status_id', // This was commented out in the migration, so omitting for now
    ];

    /**
     * Get the session offering for this enrollment.
     */
    public function sessionOffering()
    {
        return $this->belongsTo(SessionOffering::class);
    }

    /**
     * Get the student for this enrollment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // If enrollment_status_id is to be used:
    // public function enrollmentStatus()
    // {
    //     return $this->belongsTo(EnrollmentStatus::class);
    // }
}
