<?php

namespace App\Models;

// Removed: use App\Enums\SubjectType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'credit_hours',
        'parent_department_id',
        'subject_type_id', 
        'description',
        'is_active', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|object>
     */
    protected $casts = [
        'description' => 'string',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Default ordering or global scopes can be added here if needed
        // Example: static::addGlobalScope('active', function (Builder $builder) {
        // $builder->where('is_active', true);
        // });

        static::creating(function ($subject) {
            if (!isset($subject->is_active)) { // Check if not set, to allow explicit false
                $subject->is_active = true; // Default to active
            }
        });
    }

    // Relationships

    public function scopeDepartment($query)
    {
        return $query->whereHas('officeType', function($q) {
            $q->where('name', 'Department');
        });
    }

    /**
     * Get the parent department of the subject.
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Office::class, 'parent_department_id')->withDefault();
    }

    /**
     * The programs that offer this subject.
     * (Already present, but shown here for clarity)
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_subjects')
            ->withPivot('semester', 'is_elective', 'is_active')
            ->withTimestamps();
    }

    // public function program()
    // {
    //     return $this->belongsTo(Program::class, 'program_id')->withDefault();
    // }

    /**
     * Get the prerequisite subject for this subject.
     */
    public function prerequisites()
    {
        return $this->hasMany(SubjectPrerequisite::class);
    }

    /**
     * Get the subjects for which this subject is a prerequisite.
     */
    public function dependentSubjects()
    {
        return $this->hasMany(Subject::class, 'prerequisite_subject_id');
    }

    /**
     * Get the learning outcomes associated with this subject.
     * Uses polymorphic relationship to allow for different types of outcomeable models.
     */
    public function learningOutcomes()
    {
        return $this->morphMany(LearningOutcome::class, 'outcomeable');
    }

    /**
     * The departments this subject is associated with via the pivot table.
     * This is if a subject can be offered by multiple departments through department_subjects table.
     * If a subject has one primary department via 'department_id', this relationship might serve a different purpose
     * or represent a many-to-many offering.
     */
    public function departmentsViaPivot(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'department_subjects', 'subject_id', 'department_id')
            ->withTimestamps();
            // ->withPivot('is_active'); // If 'is_active' is on pivot
    }

    // Scopes

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('subjects.is_active', true); // Prefixed with table name for clarity in joins
    }

    /**
     * Scope a query to only include inactive subjects.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('subjects.is_active', false); // Prefixed with table name
    }

    // Relationships (Added subjectType relationship)
    /**
     * Get the type of the subject.
     */
    public function subjectType(): BelongsTo
    {
        return $this->belongsTo(SubjectType::class, 'subject_type_id')->withDefault();
    }

    /**
     * Scope a query to only include subjects belonging to a specific department (direct relationship).
     */
    public function scopeByDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include subjects that have a specific prerequisite.
     */
    public function scopeWithPrerequisite(Builder $query, int $prerequisiteSubjectId): Builder
    {
        return $query->where('prerequisite_subject_id', $prerequisiteSubjectId);
    }

    /**
     * Scope a query to only include subjects that do not have any prerequisite.
     */
    public function scopeWithoutPrerequisite(Builder $query): Builder
    {
        return $query->whereNull('prerequisite_subject_id');
    }

    /**
     * Scope a query to search for subjects by name or code.
     */
    public function scopeSearch(Builder $query, ?string $searchTerm): Builder
    {
        if (!$searchTerm) {
            return $query;
        }
        return $query->where(function (Builder $q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('subject_code', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Accessors & Mutators

    /**
     * Get the subject's full name including code.
     * e.g., "CS101 - Introduction to Programming"
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->subject_code} - {$this->name}";
    }

    // Other Methods

    // Scopes (Updated type-related scopes)
    /**
     * Scope a query to only include subjects of a specific type (by ID).
     */
    public function scopeOfType(Builder $query, int $subjectTypeId): Builder
    {
        return $query->where('subject_type_id', $subjectTypeId);
    }

    // Other Methods (Updated type-related checks)
    /**
     * Check if the subject is of a specific type (by ID or name).
     */
    public function isType(int|string $subjectTypeIdentifier): bool
    {
        if (is_int($subjectTypeIdentifier)) {
            return $this->subject_type_id === $subjectTypeIdentifier;
        }
        return $this->subjectType && $this->subjectType->name === $subjectTypeIdentifier;
    }

    /**
     * Check if the subject has a prerequisite.
     */
    public function hasPrerequisite(): bool
    {
        return !is_null($this->prerequisite_subject_id) && $this->prerequisite_subject_id > 0;
    }

    /**
     * Check if the subject is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
