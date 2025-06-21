<?php

namespace App\Models;

use Carbon\Carbon;
use Faker\Core\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added

class DegreeLevel extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'duration', 'equivalent', 
        'min_semester', 'max_semester', 'total_credit_hours', 
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'min_semester' => 'integer',
        'max_semester' => 'integer',
        'total_credit_hours' => 'integer',
        'prerequisites' => 'array',
    ];

    // protected $attributes = [
    //     'created_at' => new DateTime(),
    //      'updated_at' => \now(),
    //     'is_active' => true,
    // ];
    
    // public function coordinator()
    // {
    //     return $this->belongsTo(Employee::class, 'coordinator_id');
    // }
    
    // public function offices()
    // {
    //     return $this->belongsToMany(Office::class)
    //         ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
    //         ->withTimestamps();
    // }
    
    // public function activeOffices()
    // {
    //     return $this->belongsToMany(Office::class)
    //         ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
    //         ->wherePivot('is_active', true)
    //         ->where('offices.is_active', true)
    //         ->withTimestamps();
    // }
    
    // public function departments()
    // {
    //     return $this->belongsToMany(Office::class)
    //         ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
    //         ->whereHas('officeType', function($query) {
    //             $query->where('name', 'Department');
    //         })
    //         ->withTimestamps();
    // }
    
    // public function campuses()
    // {
    //     return $this->hasManyThrough(
    //         Campus::class,
    //         DepartmentProgram::class,
    //         'program_id',
    //         'id',
    //         'id',
    //         'office_id'
    //     )->distinct();
    // }
    
    // public function scopeActive($query)
    // {
    //     return $query->where('is_active', true);
    // }
    
    // public function scopeUndergraduate($query)
    // {
    //     return $query->where('program_level', 'Undergraduate');
    // }
    
    // public function scopeGraduate($query)
    // {
    //     return $query->where('program_level', 'Graduate');
    // }
}
