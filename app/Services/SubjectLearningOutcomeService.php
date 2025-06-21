<?php

namespace App\Services;

use App\Models\SubjectLearningOutcome;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectLearningOutcomeService
{
    protected function getBaseQuery(bool $withTrashed = false): Builder
    {
        $query = SubjectLearningOutcome::query()->with('subject'); // Eager load subject

        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
    }

    public function paginate(
        string $search = '',
        int $perPage = 10,
        string $sortBy = 'subject_id',
        string $sortDirection = 'asc',
        ?int $subjectId = null,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('subject', function (Builder $sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Handle sorting by related subject name
        if ($sortBy === 'subject.name') {
            $query->join('subjects', 'subject_learning_outcomes.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortDirection)
                  ->select('subject_learning_outcomes.*'); // Avoid ambiguity
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function all(
        string $search = '',
        string $sortBy = 'subject_id',
        string $sortDirection = 'asc',
        ?int $subjectId = null,
        bool $withTrashed = false
    ): \Illuminate\Database\Eloquent\Collection {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('subject', function (Builder $sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($sortBy === 'subject.name') {
            $query->join('subjects', 'subject_learning_outcomes.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortDirection)
                  ->select('subject_learning_outcomes.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->get();
    }

    public function find(int $id): ?SubjectLearningOutcome
    {
        return $this->getBaseQuery()->find($id);
    }

    public function findWithDetails(int $id, bool $withTrashed = false, array $relations = ['subject']): ?SubjectLearningOutcome
    {
        $query = $this->getBaseQuery($withTrashed);
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($id);
    }

    public function findOnlyTrashed(int $id): ?SubjectLearningOutcome
    {
        if (!$this->usesSoftDeletes()) {
            return null;
        }
        return $this->getBaseQuery(true)->onlyTrashed()->find($id);
    }

    public function create(array $data): SubjectLearningOutcome
    {
        DB::beginTransaction();
        try {
            $slo = SubjectLearningOutcome::create($data);
            DB::commit();
            return $slo;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Subject Learning Outcome: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(SubjectLearningOutcome $slo, array $data): SubjectLearningOutcome
    {
        DB::beginTransaction();
        try {
            $slo->update($data);
            DB::commit();
            return $slo;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating Subject Learning Outcome {$slo->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(SubjectLearningOutcome $slo): bool
    {
        DB::beginTransaction();
        try {
            $result = $slo->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting Subject Learning Outcome {$slo->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function restore(SubjectLearningOutcome $slo): bool
    {
        if (!$this->usesSoftDeletes() || !$slo->trashed()) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $slo->restore();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error restoring Subject Learning Outcome {$slo->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function forceDelete(SubjectLearningOutcome $slo): bool
    {
        if (!$this->usesSoftDeletes()) {
            // If not using soft deletes, delete() is already a force delete.
            // Or, you might want to prevent this if not explicitly intended.
            // For consistency, we'll assume forceDelete is only for soft-deletable models.
            return $this->delete($slo);
        }

        DB::beginTransaction();
        try {
            $result = $slo->forceDelete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error force deleting Subject Learning Outcome {$slo->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        try {
            $count = SubjectLearningOutcome::whereIn('id', $ids)->delete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting Subject Learning Outcomes: ' . $e->getMessage());
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
            $count = SubjectLearningOutcome::whereIn('id', $ids)->onlyTrashed()->restore();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk restoring Subject Learning Outcomes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkForceDelete(array $ids): int
    {
        if (!$this->usesSoftDeletes()) {
            return $this->bulkDelete($ids); // Fallback to normal delete if not using soft deletes
        }
        DB::beginTransaction();
        try {
            $count = SubjectLearningOutcome::whereIn('id', $ids)->withTrashed()->forceDelete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk force deleting Subject Learning Outcomes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getExportQuery(
        string $search = '',
        array $sortBy = ['column' => 'subject_id', 'direction' => 'asc'],
        ?int $subjectId = null,
        bool $withTrashed = false
    ): Builder {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('subject', function (Builder $sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($sortBy['column'] === 'subject.name') {
            $query->join('subjects', 'subject_learning_outcomes.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortBy['direction'])
                  ->select('subject_learning_outcomes.*');
        } else {
            $query->orderBy($sortBy['column'], $sortBy['direction']);
        }
        
        return $query;
    }
}
