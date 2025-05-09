<?php

namespace App\Services;

use App\Models\StudentStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class StudentStatusService
{
    /**
     * Get paginated and filtered student statuses.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedStudentStatuses(array $params): LengthAwarePaginator
    {
        $query = StudentStatus::query();

        $query->when(Arr::get($params, 'with_trashed'), fn ($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn ($q) => $q->onlyTrashed());

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        $perPage = Arr::get($params, 'per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the StudentStatus query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        $searchTerm = Arr::get($params, 'search_term');
        $searchEnabled = Arr::get($params, 'search', false);

        if ($searchEnabled && $searchTerm) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by active status (is_active_status field)
        // Handles 'all', 'active', 'inactive'
        $statusFilter = Arr::get($params, 'status_filter'); // e.g. 'active', 'inactive', 'all'
        if ($statusFilter && $statusFilter !== 'all') {
            $isActive = $statusFilter === 'active';
            $query->where('is_active_status', $isActive);
        }

        return $query;
    }

    /**
     * Apply sorting to the StudentStatus query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'name');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = ['name', 'created_at', 'is_active_status'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc'); // Default sort
        }

        return $query;
    }

    /**
     * Get a single student status by its ID.
     */
    public function getStudentStatus(int $id, bool $withTrashed = false, array $with = []): ?StudentStatus
    {
        $query = StudentStatus::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple student statuses by their IDs.
     */
    public function getStudentStatusesByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = StudentStatus::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new student status using validated data.
     */
    public function createStudentStatus(array $validatedData): StudentStatus
    {
        // is_active_status is handled by prepareForValidation in StudentStatusRequest
        return StudentStatus::create($validatedData);
    }

    /**
     * Update an existing student status using validated data.
     */
    public function updateStudentStatus(StudentStatus $studentStatus, array $validatedData): bool
    {
        // is_active_status is handled by prepareForValidation in StudentStatusRequest
        return $studentStatus->update($validatedData);
    }

    /**
     * Toggle the active status of a student status.
     */
    public function toggleActiveStatus(StudentStatus $studentStatus): bool
    {
        $studentStatus->is_active_status = !$studentStatus->is_active_status;
        return $studentStatus->save();
    }

    /**
     * Bulk toggle the active status of multiple student statuses.
     */
    public function bulkToggleActiveStatus(Collection $studentStatuses): array
    {
        $toggleCount = [
            'totalToggledCount' => 0,
            'activatedCount' => 0,
            'deactivatedCount' => 0,
        ];
        foreach ($studentStatuses as $status) {
            $currentStatus = $status->is_active_status;
            if ($this->toggleActiveStatus($status)) {
                $toggleCount['totalToggledCount']++;
                $currentStatus ? $toggleCount['deactivatedCount']++ : $toggleCount['activatedCount']++;
            }
        }
        return $toggleCount;
    }

    /**
     * Soft delete a student status.
     */
    public function deleteStudentStatus(StudentStatus $studentStatus): ?bool
    {
        return $studentStatus->delete();
    }

    /**
     * Soft delete a student status by ID.
     */
    public function deleteStudentStatusById(int $id): bool
    {
        $studentStatus = StudentStatus::find($id);
        return $studentStatus ? $studentStatus->delete() : false;
    }

    /**
     * Bulk soft delete student statuses by their IDs.
     */
    public function bulkDeleteStudentStatuses(array $ids): int
    {
        return StudentStatus::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted student status by its ID.
     */
    public function restoreStudentStatus(int $id): bool
    {
        $studentStatus = StudentStatus::withTrashed()->find($id);
        return $studentStatus ? $studentStatus->restore() : false;
    }

    /**
     * Bulk restore soft-deleted student statuses by their IDs.
     */
    public function bulkRestoreStudentStatuses(array $ids): int
    {
        return StudentStatus::withTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a student status by its ID.
     */
    public function permanentlyDelete(int $id): bool
    {
        $studentStatus = StudentStatus::withTrashed()->find($id);
        return $studentStatus ? $studentStatus->forceDelete() : false;
    }

    /**
     * Bulk permanently delete student statuses by their IDs.
     */
    public function bulkPermanentDeleteStudentStatuses(array $ids): int
    {
        return StudentStatus::withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * Count the number of active student statuses.
     */
    public function countActiveStudentStatuses(): int
    {
        return StudentStatus::where('is_active_status', true)->count();
    }

    /**
     * Get a list of student statuses suitable for dropdowns.
     */
    public function getStudentStatusesForDropdown(bool $activeOnly = true): Collection
    {
        $query = StudentStatus::query();

        if ($activeOnly) {
            $query->where('is_active_status', true);
        }

        return $query->orderBy('name')->select('id', 'name')->get();
    }
}
