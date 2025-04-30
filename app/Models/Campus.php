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
        'name', 'short_name', 'code', 'description', 'address','location',  
        'phone', 'email', 'website', 'founded_year', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'founded_year' => 'integer',
        'deleted_at' => 'datetime',
    ];
    
    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }
    
    public function activeOffices()
    {
        return $this->belongsToMany(Office::class)
            ->where('offices.is_active', true);
    }
    
    public function departments()
    {
        return $this->belongsToMany(Office::class)
            ->whereHas('officeType', function($query) {
                $query->where('name', 'Department');
            }
        );
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
