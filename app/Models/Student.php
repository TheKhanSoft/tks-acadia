<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Gender; // Import the Gender enum
use App\Models\City; // Import the City model
use App\Models\StudentStatus; // Import StudentStatus
use App\Models\StudentProgramEnrollment; // Import StudentProgramEnrollment

class Student extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'student_id',       // Unique identifier for the student (e.g., "S2023-001")
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_alternative',
        'gender',          
        'nic_no',          
        'date_of_birth',
        'postal_address',
        'permanent_address',
        'city_id',          
        'photo_path',
        'bio',
        'student_status_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => Gender::class, // Cast gender to the Enum
    ];

    /**
     * Get the student's current overall academic status.
     */
    public function studentStatus()
    {
        return $this->belongsTo(StudentStatus::class);
    }

    /**
     * Get the city the student resides in.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get all program enrollments for the student.
     */
    public function programEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }

    /**
     * Get the student's current program enrollment.
     * This could be the latest one by date, or one marked as 'active'.
     * For simplicity, using latest by enrollment_date.
     * Adjust logic if 'current' is defined differently (e.g., by an 'is_current' flag or specific status).
     */
    public function currentEnrolment()
    {
        // Assuming 'enrollment_date' exists on StudentProgramEnrollment table
        // and the latest enrollment by this date is considered "current".
        // If there's a specific status or flag for "current", filter by that instead/additionally.
        // For example: ->where('enrollment_status_id', EnrollmentStatus::ACTIVE_ID)->latestOfMany('enrollment_date')
        return $this->hasOne(StudentProgramEnrollment::class)->latestOfMany('enrollment_date');
    }

    /**
     * Get the student's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
