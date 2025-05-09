<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'state_id',
        'latitude',
        'longitude',
        'wikiDataId',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the state that the city belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the students in the city.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
