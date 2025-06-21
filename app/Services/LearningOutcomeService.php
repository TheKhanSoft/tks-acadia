<?php

namespace App\Services;

use App\Models\LearningOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LearningOutcomeService
{
    protected function getBaseQuery(bool $withTrashed = false): Builder
    {
        // Eager load the polymorphic relation 'outcomeable'
        $query = LearningOutcome::query()->with('outcomeable');

        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(LearningOutcome::class));
    }

    public function paginate(
        string $search = '',
        int $perPage = 10,
        string $sortBy = 'id', // Default sort by LearningOutcome's id
        string $sortDirection = 'asc',
        ?string $outcomeableType = null,
        ?int $outcomeableId = null,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            // Search within the 'outcomes' field of LearningOutcome
            // Searching related polymorphic fields directly in one query is complex.
            // This can be expanded if specific searchable fields on parents are known and consistent.
            $query->where('outcomes', 'like', "%{$search}%");
        }

        if ($outcomeableType) {
            // Ensure outcomeableType is a valid model short name (e.g., 'Subject', 'Program')
            // Or convert from a table name if that's what's passed
            $fullModelType = $this->getFullModelType($outcomeableType);
            if ($fullModelType) {
                $query->where('outcomeable_type', $fullModelType);
            }
        }

        if ($outcomeableId) {
            $query->where('outcomeable_id', $outcomeableId);
        }

        // Sorting by direct LearningOutcome fields
        // Sorting by outcomeable parent fields would require more complex logic
        // or specific handling for each outcomeable_type.
        if (in_array($sortBy, (new LearningOutcome)->getFillable()) || $sortBy === 'id' || $sortBy === 'created_at' || $sortBy === 'updated_at') {
             $query->orderBy($sortBy, $sortDirection);
        }
        // Add more sophisticated sorting for related outcomeable fields if necessary

        return $query->paginate($perPage);
    }

    public function all(
        string $search = '',
        string $sortBy = 'id',
        string $sortDirection = 'asc',
        ?string $outcomeableType = null,
        ?int $outcomeableId = null,
        bool $withTrashed = false
    ): \Illuminate\Database\Eloquent\Collection {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            $query->where('outcomes', 'like', "%{$search}%");
        }

        if ($outcomeableType) {
            $fullModelType = $this->getFullModelType($outcomeableType);
            if ($fullModelType) {
                $query->where('outcomeable_type', $fullModelType);
            }
        }

        if ($outcomeableId) {
            $query->where('outcomeable_id', $outcomeableId);
        }
        
        if (in_array($sortBy, (new LearningOutcome)->getFillable()) || $sortBy === 'id' || $sortBy === 'created_at' || $sortBy === 'updated_at') {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->get();
    }

    /**
     * Helper to get full model class name from short name or table name.
     * This is a basic implementation and might need to be more robust
     * depending on how $outcomeableType is provided.
     */
    protected function getFullModelType(string $type): ?string
    {
        // Assuming $type could be 'Subject', 'Program', etc.
        $potentialModelName = 'App\\Models\\' . Str::studly(Str::singular($type));
        if (class_exists($potentialModelName)) {
            return $potentialModelName;
        }
        // Add more mappings if type is passed differently (e.g., table names)
        return null;
    }


    public function find(int $id): ?LearningOutcome
    {
        return $this->getBaseQuery()->find($id);
    }

    public function findWithDetails(int $id, bool $withTrashed = false, array $relations = ['outcomeable']): ?LearningOutcome
    {
        $query = $this->getBaseQuery($withTrashed);
        if (!empty($relations)) {
            $query->with($relations); // Default eager loads 'outcomeable'
        }
        return $query->find($id);
    }

    public function findOnlyTrashed(int $id): ?LearningOutcome
    {
        if (!$this->usesSoftDeletes()) {
            return null;
        }
        return $this->getBaseQuery(true)->onlyTrashed()->find($id);
    }

    /**
     * Create a learning outcome for a given outcomeable model.
     */
    public function createForOutcomeable(Model $outcomeable, array $data): LearningOutcome
    {
        DB::beginTransaction();
        try {
            // 'outcomeable_id' and 'outcomeable_type' are set automatically by morphMany->create()
            $learningOutcome = $outcomeable->learningOutcomes()->create($data);
            DB::commit();
            return $learningOutcome;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Learning Outcome: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a learning outcome with explicit outcomeable_id and outcomeable_type.
     * Ensure outcomeable_type is the full class name (e.g., App\Models\Subject).
     */
    public function create(array $data): LearningOutcome
    {
        DB::beginTransaction();
        try {
            // Ensure 'outcomeable_type' is the full model class name if passed directly
            if (isset($data['outcomeable_type']) && !Str::startsWith($data['outcomeable_type'], 'App\\Models\\')) {
                 $fullModelType = $this->getFullModelType($data['outcomeable_type']);
                 if ($fullModelType) {
                     $data['outcomeable_type'] = $fullModelType;
                 } else {
                     // Handle error: invalid type
                     throw new \InvalidArgumentException("Invalid outcomeable_type provided: " . $data['outcomeable_type']);
                 }
            }

            $learningOutcome = LearningOutcome::create($data);
            DB::commit();
            return $learningOutcome;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Learning Outcome: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(LearningOutcome $learningOutcome, array $data): LearningOutcome
    {
        DB::beginTransaction();
        try {
            $learningOutcome->update($data);
            DB::commit();
            return $learningOutcome;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating Learning Outcome {$learningOutcome->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(LearningOutcome $learningOutcome): bool
    {
        DB::beginTransaction();
        try {
            $result = $learningOutcome->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting Learning Outcome {$learningOutcome->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function restore(LearningOutcome $learningOutcome): bool
    {
        if (!$this->usesSoftDeletes() || !$learningOutcome->trashed()) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $learningOutcome->restore();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error restoring Learning Outcome {$learningOutcome->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function forceDelete(LearningOutcome $learningOutcome): bool
    {
        if (!$this->usesSoftDeletes()) {
            return $this->delete($learningOutcome);
        }

        DB::beginTransaction();
        try {
            $result = $learningOutcome->forceDelete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error force deleting Learning Outcome {$learningOutcome->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        try {
            $count = LearningOutcome::whereIn('id', $ids)->delete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting Learning Outcomes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkRestore(array $ids): int
    {
        if (!$this->usesSoftDeletes()) {
            return 0;
        }
        DB::beginTransaction();
        try {
            $count = LearningOutcome::whereIn('id', $ids)->onlyTrashed()->restore();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk restoring Learning Outcomes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkForceDelete(array $ids): int
    {
        if (!$this->usesSoftDeletes()) {
            return $this->bulkDelete($ids);
        }
        DB::beginTransaction();
        try {
            $count = LearningOutcome::whereIn('id', $ids)->withTrashed()->forceDelete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk force deleting Learning Outcomes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getExportQuery(
        string $search = '',
        array $sortBy = ['column' => 'id', 'direction' => 'asc'], // Default sort by LearningOutcome's id
        ?string $outcomeableType = null,
        ?int $outcomeableId = null,
        bool $withTrashed = false
    ): Builder {
        $query = $this->getBaseQuery($withTrashed);
        $query->with('outcomeable'); // Ensure outcomeable is loaded for export

        if ($search) {
            $query->where('outcomes', 'like', "%{$search}%");
        }

        if ($outcomeableType) {
            $fullModelType = $this->getFullModelType($outcomeableType);
            if ($fullModelType) {
                $query->where('outcomeable_type', $fullModelType);
            }
        }

        if ($outcomeableId) {
            $query->where('outcomeable_id', $outcomeableId);
        }
        
        $sortColumn = $sortBy['column'] ?? 'id';
        $sortDirection = $sortBy['direction'] ?? 'asc';

        if (in_array($sortColumn, (new LearningOutcome)->getFillable()) || $sortColumn === 'id' || $sortColumn === 'created_at' || $sortColumn === 'updated_at') {
            $query->orderBy($sortColumn, $sortDirection);
        }
        // Note: Sorting by related outcomeable fields for export would require more complex logic here,
        // potentially joining tables based on outcomeable_type or processing data after retrieval.

        return $query;
    }
}
