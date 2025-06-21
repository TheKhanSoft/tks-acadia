<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added

class Program extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name', 'code', 'description', 'degree_title', 
        'department_id', 'degree_level_id', 'delivery_mode_id ', 'duration',
        'min_semester', 'max_semester', 'total_credit_hours', 'equivalent', 
        'accreditation_status', 'start_date', 'prerequisites', 'learning_outcomes', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'min_semester' => 'integer',
        'max_semester' => 'integer',
        'start_date' => 'date',
        'prerequisites' => 'array',
        'learning_outcomes' => 'array'
    ];
    
    public function coordinator()
    {
        return $this->belongsTo(Employee::class, 'coordinator_id');
    }
    
    public function offices()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
            ->withTimestamps();
    }
    
    public function activeOffices()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
            ->wherePivot('is_active', true)
            ->where('offices.is_active', true)
            ->withTimestamps();
    }
    
    public function departments()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
            ->whereHas('officeType', function($query) {
                $query->where('name', 'Department');
            })
            ->withTimestamps();
    }
    
    public function campuses()
    {
        return $this->hasManyThrough(
            Campus::class,
            DepartmentProgram::class,
            'program_id',
            'id',
            'id',
            'office_id'
        )->distinct();
    }

    /**
     * Get the learning outcomes associated with this subject.
     * Uses polymorphic relationship to allow for different types of outcomeable models.
     */
    public function learningOutcomes()
    {
        return $this->morphMany(LearningOutcome::class, 'outcomeable');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeUndergraduate($query)
    {
        return $query->where('program_level', 'Undergraduate');
    }
    
    public function scopeGraduate($query)
    {
        return $query->where('program_level', 'Graduate');
    }
}
