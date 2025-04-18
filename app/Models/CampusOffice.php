<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusOffice extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'campus_id', 'office_id', 'building_name', 'room_number', 
        'is_primary_location', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_primary_location' => 'boolean'
    ];
}
