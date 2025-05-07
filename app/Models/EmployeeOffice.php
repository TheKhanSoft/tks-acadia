<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOffice extends Pivot
{
    use SoftDeletes, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_office'; 

    protected $fillable = [
        'office_id', 'employee_id', 'role', 'start_date', 
        'end_date', 'is_primary_office', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_primary_office' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get the employee associated with the office assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the office associated with the assignment.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
