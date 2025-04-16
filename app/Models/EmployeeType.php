<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'code', 'description', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
