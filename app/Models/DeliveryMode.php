<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DeliveryMode extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    // public function programs()
    // {
    //     return $this->belongsToMany(Program::class)
    //         ->withPivot('offered_since', 'annual_intake', 'is_flagship_program', 'is_active')
    //         ->withTimestamps();
    // }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
}
