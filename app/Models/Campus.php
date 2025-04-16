<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'location', 'code', 'description', 'address', 
        'phone', 'email', 'website', 'founded_year', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'founded_year' => 'integer',
    ];
    
    public function offices()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('building_name', 'room_number', 'is_primary_location', 'is_active')
            ->withTimestamps();
    }
    
    public function activeOffices()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('building_name', 'room_number', 'is_primary_location', 'is_active')
            ->wherePivot('is_active', true)
            ->where('offices.is_active', true)
            ->withTimestamps();
    }
    
    public function departments()
    {
        return $this->belongsToMany(Office::class)
            ->withPivot('building_name', 'room_number', 'is_primary_location', 'is_active')
            ->whereHas('officeType', function($query) {
                $query->where('name', 'Department');
            });
    }
    
    public function programs()
    {
        return $this->hasManyThrough(
            Program::class,
            OfficeProgram::class,
            'office_id',
            'id',
            'id',
            'program_id'
        )->distinct();
    }
    
    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            OfficeEmployee::class,
            'office_id',
            'id',
            'id',
            'employee_id'
        )->distinct();
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
