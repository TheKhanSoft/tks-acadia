<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Changed from Relations\Pivot to Model for more flexibility if needed
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // If soft deletes are needed on the pivot

class DepartmentSubject extends Model // Can extend Pivot if strictly a pivot with no extra logic
{
    use HasFactory, SoftDeletes; // Added SoftDeletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'department_subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'subject_id',
        'is_active', // Assuming an 'is_active' status for the association
        // Add other pivot attributes here if any, e.g., 'offered_since'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|object>
     */
    protected $casts = [
        'is_active' => 'boolean',
        // 'offered_since' => 'date',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true; // Pivot tables often have timestamps

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($departmentSubject) {
            if (!isset($departmentSubject->is_active)) {
                $departmentSubject->is_active = true; // Default to active association
            }
        });
    }

    // Relationships

    /**
     * Get the department associated with this entry.
     * An Office model is used for departments.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'department_id')->withDefault();
    }

    /**
     * Get the subject associated with this entry.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id')->withDefault();
    }

    // Scopes

    /**
     * Scope a query to only include active department-subject associations.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive department-subject associations.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to find associations for a specific department.
     */
    public function scopeForDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to find associations for a specific subject.
     */
    public function scopeForSubject(Builder $query, int $subjectId): Builder
    {
        return $query->where('subject_id', $subjectId);
    }

    // Other Methods

    /**
     * Check if this department-subject association is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate this department-subject association.
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate this department-subject association.
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }
}
