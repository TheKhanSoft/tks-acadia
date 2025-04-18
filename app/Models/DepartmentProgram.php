<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentProgram extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'office_id', 'program_id', 'offered_since', 
        'annual_intake', 'is_flagship_program', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_flagship_program' => 'boolean',
        'annual_intake' => 'integer',
        'offered_since' => 'date'
    ];
}
