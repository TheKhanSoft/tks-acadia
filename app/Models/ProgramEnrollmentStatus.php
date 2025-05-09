<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramEnrollmentStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',        // e.g., "Enrolled", "Completed", "Dropped Out", "Cancelled", "Semester Drop"
        'description', // Optional description
    ];

    /**
     * Get the student program enrollments associated with this status.
     */
    public function studentProgramEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }
}
