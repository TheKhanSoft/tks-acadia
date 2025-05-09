<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',         // e.g., "Active", "Graduated", "Withdrawn", "On Leave"
        'description',  // Optional description of the status
        'is_active_status', // Boolean to indicate if this status means the student is currently active
    ];

    protected $casts = [
        'is_active_status' => 'boolean',
    ];

    /**
     * Get the students associated with this status.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
