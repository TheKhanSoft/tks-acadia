<?php

namespace App\Services;

use App\Models\DepartmentSubject;
use App\Enums\IsActiveFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentSubjectService
{
    protected function getBaseQuery(bool $withTrashed = false): Builder
    {
        $query = DepartmentSubject::query()->with(['department', 'subject']);

        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(DepartmentSubject::class));
    }

    public function paginate(
        string $search = '',
        int $perPage = 10,
        string $sortBy = 'department_id',
        string $sortDirection = 'asc',
        ?IsActiveFilter $isActiveFilter = null,
        ?int $departmentId = null,
        ?int $subjectId = null,
        bool $withTrashed = false
    ): LengthAwarePaginator {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('department', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('subject', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
                // Add search for other pivot fields if any, e.g., semester
                // $q->orWhere('semester', 'like', "%{$search}%");
            });
        }

        if ($isActiveFilter && $isActiveFilter !== IsActiveFilter::ALL) {
            $query->where('is_active', $isActiveFilter->getBoolValue());
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Handle sorting by related model names
        if ($sortBy === 'department.name') {
            $query->join('departments', 'department_subject.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortDirection)
                  ->select('department_subject.*'); // Avoid ambiguity
        } elseif ($sortBy === 'subject.name') {
            $query->join('subjects', 'department_subject.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortDirection)
                  ->select('department_subject.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function all(
        string $search = '',
        string $sortBy = 'department_id',
        string $sortDirection = 'asc',
        ?IsActiveFilter $isActiveFilter = null,
        ?int $departmentId = null,
        ?int $subjectId = null,
        bool $withTrashed = false
    ): \Illuminate\Database\Eloquent\Collection {
        $query = $this->getBaseQuery($withTrashed);

        if ($search) {
             $query->where(function (Builder $q) use ($search) {
                $q->whereHas('department', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('subject', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        if ($isActiveFilter && $isActiveFilter !== IsActiveFilter::ALL) {
            $query->where('is_active', $isActiveFilter->getBoolValue());
        }
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($sortBy === 'department.name') {
            $query->join('departments', 'department_subject.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortDirection)
                  ->select('department_subject.*');
        } elseif ($sortBy === 'subject.name') {
            $query->join('subjects', 'department_subject.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortDirection)
                  ->select('department_subject.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->get();
    }

    public function find(int $id): ?DepartmentSubject
    {
        return $this->getBaseQuery()->find($id);
    }

    public function findWithDetails(int $id, bool $withTrashed = false, array $relations = ['department', 'subject']): ?DepartmentSubject
    {
        $query = $this->getBaseQuery($withTrashed);
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($id);
    }

    public function findOnlyTrashed(int $id): ?DepartmentSubject
    {
        if (!$this->usesSoftDeletes()) {
            return null;
        }
        return $this->getBaseQuery(true)->onlyTrashed()->find($id);
    }

    public function create(array $data): DepartmentSubject
    {
        DB::beginTransaction();
        try {
            // Ensure is_active is set, defaulting to true if not provided
            $data['is_active'] = $data['is_active'] ?? true;
            $departmentSubject = DepartmentSubject::create($data);
            DB::commit();
            return $departmentSubject;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Department Subject assignment: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(DepartmentSubject $departmentSubject, array $data): DepartmentSubject
    {
        DB::beginTransaction();
        try {
            $departmentSubject->update($data);
            DB::commit();
            return $departmentSubject;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating Department Subject assignment {$departmentSubject->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(DepartmentSubject $departmentSubject): bool
    {
        DB::beginTransaction();
        try {
            $result = $departmentSubject->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting Department Subject assignment {$departmentSubject->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function restore(DepartmentSubject $departmentSubject): bool
    {
        if (!$this->usesSoftDeletes() || !$departmentSubject->trashed()) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $departmentSubject->restore();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error restoring Department Subject assignment {$departmentSubject->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function forceDelete(DepartmentSubject $departmentSubject): bool
    {
        if (!$this->usesSoftDeletes()) {
            return $this->delete($departmentSubject);
        }

        DB::beginTransaction();
        try {
            $result = $departmentSubject->forceDelete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error force deleting Department Subject assignment {$departmentSubject->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        try {
            $count = DepartmentSubject::whereIn('id', $ids)->delete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting Department Subject assignments: ' . $e->getMessage());
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
            $count = DepartmentSubject::whereIn('id', $ids)->onlyTrashed()->restore();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk restoring Department Subject assignments: ' . $e->getMessage());
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
            $count = DepartmentSubject::whereIn('id', $ids)->withTrashed()->forceDelete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk force deleting Department Subject assignments: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getExportQuery(
        string $search = '',
        array $sortBy = ['column' => 'department_id', 'direction' => 'asc'],
        ?string $isActiveFilterValue = null, // Changed to string to match view
        ?int $departmentId = null,
        ?int $subjectId = null,
        bool $withTrashed = false
    ): Builder {
        $query = $this->getBaseQuery($withTrashed);
        $isActiveFilter = IsActiveFilter::tryFrom($isActiveFilterValue ?? 'all');


        if ($search) {
             $query->where(function (Builder $q) use ($search) {
                $q->whereHas('department', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('subject', function (Builder $sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        if ($isActiveFilter && $isActiveFilter !== IsActiveFilter::ALL) {
            $query->where('is_active', $isActiveFilter->getBoolValue());
        }
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($sortBy['column'] === 'department.name') {
            $query->join('departments', 'department_subject.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortBy['direction'])
                  ->select('department_subject.*');
        } elseif ($sortBy['column'] === 'subject.name') {
            $query->join('subjects', 'department_subject.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.name', $sortBy['direction'])
                  ->select('department_subject.*');
        } else {
            $query->orderBy($sortBy['column'], $sortBy['direction']);
        }
        
        return $query;
    }
}
