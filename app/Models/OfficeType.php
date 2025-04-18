<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeType extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'code', 'description', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function offices()
    {
        return $this->hasMany(Office::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
