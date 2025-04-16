<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeEmployee extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'office_id', 'employee_id', 'role', 'assignment_date', 
        'end_date', 'is_primary_office', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_primary_office' => 'boolean',
        'assignment_date' => 'date',
        'end_date' => 'date'
    ];
}
