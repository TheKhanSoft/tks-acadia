<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campus extends Model
{
    use SoftDeletes, HasFactory;
    
    protected $fillable = [
        'name', 'location', 'code', 'description', 'address', 
        'phone', 'email', 'website', 'founded_year', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'founded_year' => 'integer',
        'deleted_at' => 'datetime',
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
            })
            ->withTimestamps();
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
