<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added

class DepartmentProgram extends Model
{
    use HasFactory, SoftDeletes;

    // protected $table = 'department_program'; // Explicitly define the table name
    
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

    /**
     * Get the program associated with this department offering.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the office (department/faculty) that offers this program.
     */
    public function office()
    {
        return $this->belongsTo(Office::class); // Assuming Office model exists
    }
}
