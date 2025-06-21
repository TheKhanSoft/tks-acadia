<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningOutcome extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'outcomeable_id',
        'outcomeable_type',
        'outcomes',
    ];

    /**
     * Get the parent learnable model (subject, program, course, etc.).
     */
    public function outcomeable()
    {
        return $this->morphTo();
    }
}
