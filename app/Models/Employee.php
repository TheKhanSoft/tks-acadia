<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeOffice;

class Employee extends Model
{
    use SoftDeletes, HasFactory;
    
    protected $fillable = [
        'employee_id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'nic_no',
        'date_of_birth', 'employee_type_id', 'appointment_date', 'termination_date',
        'postal_address', 'permanent_address', 'qualification', 'specialization', 'photo_path', 'bio',
        'employee_work_status_id',
        'job_nature_id' // Added job_nature_id
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'appointment_date' => 'date',
        'termination_date' => 'date',
    ];
    
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function employeeWorkStatus()
    {
        return $this->belongsTo(EmployeeWorkStatus::class);
    }

    public function jobNature()
    {
        return $this->belongsTo(JobNature::class);
    }
    
    public function offices()
    {
        return $this->belongsToMany(Office::class)
            ->using(EmployeeOffice::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_primary_office', 'is_active')
            ->withTimestamps();
    }
    
    public function primaryOffice()
    {
        return $this->belongsToMany(Office::class)
        ->using(EmployeeOffice::class) // Use class name
        ->withPivot('role', 'start_date', 'end_date', 'is_primary_office', 'is_active')
        ->wherePivot('is_primary_office', true); // Remove ->first()
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
    
    // Removed scopeFaculty
    
    // Removed scopeAdministrative
}
