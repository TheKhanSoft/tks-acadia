<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectLearningOutcome extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subject_learning_outcomes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'description',
        'outcomes', // JSON field for specific learning points
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|object>
     */
    protected $casts = [
        'outcomes' => 'array', // Cast JSON to array
        'description' => 'string',
    ];

    // Relationships

    /**
     * Get the subject that this learning outcome belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id')->withDefault();
    }

    // Scopes

    /**
     * Scope a query to only include learning outcomes for a specific subject.
     */
    public function scopeForSubject(Builder $query, int $subjectId): Builder
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to search for learning outcomes by description.
     */
    public function scopeSearchByDescription(Builder $query, ?string $searchTerm): Builder
    {
        if (!$searchTerm) {
            return $query;
        }
        return $query->where('description', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope a query to search for learning outcomes containing a specific outcome text within the JSON 'outcomes' field.
     * Note: This requires database support for JSON querying (e.g., MySQL 5.7+, PostgreSQL).
     */
    public function scopeContainsOutcomeText(Builder $query, ?string $outcomeText): Builder
    {
        if (!$outcomeText) {
            return $query;
        }
        // This is a basic example. For more complex JSON searches, you might need raw expressions
        // or specific database functions like JSON_CONTAINS or ->> operator.
        return $query->whereJsonContains('outcomes', $outcomeText);
    }

    // Accessors & Mutators

    // Example: public function getFormattedOutcomesAttribute(): string
    // {
    // if (is_array($this->outcomes)) {
    // return implode('; ', $this->outcomes);
    // }
    // return '';
    // }

    // Other Methods

    /**
     * Add a specific outcome to the outcomes list.
     *
     * @param string $outcome
     * @return bool
     */
    public function addOutcome(string $outcome): bool
    {
        $currentOutcomes = $this->outcomes ?? [];
        if (!in_array($outcome, $currentOutcomes)) {
            $currentOutcomes[] = $outcome;
            $this->outcomes = $currentOutcomes;
            return $this->save();
        }
        return false; 
    }

    /**
     * Remove a specific outcome from the outcomes list.
     *
     * @param string $outcome
     * @return bool
     */
    public function removeOutcome(string $outcome): bool
    {
        $currentOutcomes = $this->outcomes ?? [];
        if (($key = array_search($outcome, $currentOutcomes)) !== false) {
            unset($currentOutcomes[$key]);
            $this->outcomes = array_values($currentOutcomes); 
            return $this->save();
        }
        return false; 
    }
}
