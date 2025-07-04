<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Faculty extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'head_id', 'head_appointment_date',
         'contact_phone', 'contact_email', 'established_year', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'established_year' => 'integer',
        'head_appointment_date' => 'date',
    ];
    
    public function officeType()
    {
        return $this->belongsTo(OfficeType::class);
    }
    
    public function head()
    {
        return $this->belongsTo(Employee::class, 'head_id');
    }
    
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // public function faculty() // Removed incorrect self-referencing relationship
    // {
    //     return $this->belongsTo(Faculty::class);
    // }

    
    public function employees()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->withTimestamps();
    }
    
    public function activeEmployees()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->wherePivot('is_active', true)
            // ->where('employees.is_active', true) // This column does not exist on employees table
            ->withTimestamps();
    }
    
    public function facultyMembers()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->whereHas('employeeType', function($query) {
                $query->where('name', 'Faculty Member');
            });
    }

    public function staffMembers()
    {
        return $this->belongsToMany(Employee::class)
            ->withPivot('role', 'assignment_date', 'end_date', 'is_primary_office', 'is_active')
            ->whereHas('employeeType', function($query) {
                $query->where('name', 'Staff Member');
            });
    }
    
    public function programs()
    {
        return $this->belongsToMany(Program::class)
            ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
            ->withTimestamps();
    }
    
    public function activePrograms()
    {
        return $this->belongsToMany(Program::class)
            ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
            ->wherePivot('is_active', true)
            ->where('programs.is_active', true)
            ->withTimestamps();
    }
    
    public function parentOffice()
    {
        return $this->belongsTo(Office::class, 'parent_office_id');
    }
    
    public function childOffices()
    {
        return $this->hasMany(Office::class, 'parent_office_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeDepartments($query)
    {
        return $query->whereHas('officeType', function($q) {
            $q->where('name', 'Department');
        });
    }
    
    public function scopeHostels($query)
    {
        return $query->whereHas('officeType', function($q) {
            $q->where('name', 'Hostel');
        });
    }
    
    public function scopeColleges($query)
    {
        return $query->whereHas('officeType', function($q) {
            $q->where('name', 'Constituent College');
        });
    }
}
