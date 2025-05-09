<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // e.g., Enrolled, Completed, Dropped Out, Cancelled
        'description',
        'is_active', // Indicates if this status represents an ongoing/active enrollment
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the student program enrollments with this status.
     */
    public function studentProgramEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }
}
