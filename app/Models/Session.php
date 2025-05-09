<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // e.g., "2023-2027", "Fall 2023 Semester", "Spring 2024"
        'start_date',
        'end_date',
        'type', // e.g., Academic Year, Semester, Trimester
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the student program enrollments for the session.
     */
    public function studentProgramEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }
}
