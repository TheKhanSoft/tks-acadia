<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'employee_id', 'first_name', 'last_name', 'email', 'phone',
        'designation', 'employee_type_id', 'hire_date', 'termination_date',
        'qualification', 'specialization', 'photo_path', 'bio', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'hire_date' => 'date',
        'termination_date' => 'date',
    ];
    
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
    
    public function offices()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->withTimestamps();
    }
    
    public function primaryOffice()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->wherePivot('is_primary_office', true)
            ->first();
    }
    
    public function headOfOffices()
    {
        return $this->hasMany(Office::class, 'head_id');
    }
    
    public function coordinatedPrograms()
    {
        return $this->hasMany(Program::class, 'coordinator_id');
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeFaculty($query)
    {
        return $query->whereHas('employeeType', function($q) {
            $q->where('name', 'Faculty Member');
        });
    }
    
    public function scopeAdministrative($query)
    {
        return $query->whereHas('employeeType', function($q) {
            $q->where('name', 'Administrative Staff');
        });
    }
}
