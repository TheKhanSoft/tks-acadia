<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Gender; // Import the Gender enum
use App\Models\City; // Import the City model

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
        'gender',           // Uses App\Enums\Gender
        'nic_no',           // National Identity Card number
        'date_of_birth',
        'postal_address',
        'permanent_address',
        'city_id',          // Foreign key to cities table
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
    // You might want to add helper methods to get current/active program enrollments,
    // or specific program details through the enrollments.
    // For example:
    // public function currentProgramEnrollment()
    // {
    //     return $this->hasOne(StudentProgramEnrollment::class)->latestOfMany('enrollment_date'); // Or based on status
    // }

    /**
     * Get the student's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
