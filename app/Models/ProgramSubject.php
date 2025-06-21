<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramSubject extends Model
{
    use HasFactory, SoftDeletes;

    // Assuming a 'program_subjects' table with at least 'name' and 'program_id'
    protected $fillable = [
        'name',
        'program_id',
        'subject_id',
        'semester',
        'is_elective',
        'is_active',
    ];

    protected $casts = [
        'is_elective' => 'boolean',
        'is_active' => 'boolean',
    ];
    

    /**
     * Get the program that this subject belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the session offerings for this program subject.
     */
    public function sessionOfferings()
    {
        return $this->hasMany(SessionOffering::class);
    }
}
