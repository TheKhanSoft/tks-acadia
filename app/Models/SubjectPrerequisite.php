<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectPrerequisite extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'prerequisite_subject_id',
    ];

    /**
     * Get the subject that has this prerequisite.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the prerequisite subject.
     */
    public function prerequisiteSubject()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_subject_id');
    }
}
