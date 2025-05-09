<?php

namespace App\Services;

use App\Models\EnrollmentStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class EnrollmentStatusService
{
    /**
     * Get paginated and filtered enrollment statuses.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedEnrollmentStatuses(array $params): LengthAwarePaginator
    {
        $query = EnrollmentStatus::query();

        $query->when(Arr::get($params, 'with_trashed'), fn ($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn ($q) => $q->onlyTrashed());

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        $perPage = Arr::get($params, 'per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the EnrollmentStatus query.
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

        // Filter by active status (is_active field)
        // Handles 'all', 'active', 'inactive'
        $statusFilter = Arr::get($params, 'status_filter'); // e.g. 'active', 'inactive', 'all'
        if ($statusFilter && $statusFilter !== 'all') {
            $isActive = $statusFilter === 'active';
            $query->where('is_active', $isActive);
        }

        return $query;
    }

    /**
     * Apply sorting to the EnrollmentStatus query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'name');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = ['name', 'created_at', 'is_active'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc'); // Default sort
        }

        return $query;
    }

    /**
     * Get a single enrollment status by its ID.
     */
    public function getEnrollmentStatus(int $id, bool $withTrashed = false, array $with = []): ?EnrollmentStatus
    {
        $query = EnrollmentStatus::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple enrollment statuses by their IDs.
     */
    public function getEnrollmentStatusesByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = EnrollmentStatus::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new enrollment status using validated data.
     */
    public function createEnrollmentStatus(array $validatedData): EnrollmentStatus
    {
        // is_active is handled by prepareForValidation in EnrollmentStatusRequest
        return EnrollmentStatus::create($validatedData);
    }

    /**
     * Update an existing enrollment status using validated data.
     */
    public function updateEnrollmentStatus(EnrollmentStatus $enrollmentStatus, array $validatedData): bool
    {
        // is_active is handled by prepareForValidation in EnrollmentStatusRequest
        return $enrollmentStatus->update($validatedData);
    }

    /**
     * Toggle the active status of an enrollment status.
     */
    public function toggleActiveStatus(EnrollmentStatus $enrollmentStatus): bool
    {
        $enrollmentStatus->is_active = !$enrollmentStatus->is_active;
        return $enrollmentStatus->save();
    }

    /**
     * Bulk toggle the active status of multiple enrollment statuses.
     */
    public function bulkToggleActiveStatus(Collection $enrollmentStatuses): array
    {
        $toggleCount = [
            'totalToggledCount' => 0,
            'activatedCount' => 0,
            'deactivatedCount' => 0,
        ];
        foreach ($enrollmentStatuses as $status) {
            $currentStatus = $status->is_active;
            if ($this->toggleActiveStatus($status)) {
                $toggleCount['totalToggledCount']++;
                $currentStatus ? $toggleCount['deactivatedCount']++ : $toggleCount['activatedCount']++;
            }
        }
        return $toggleCount;
    }

    /**
     * Soft delete an enrollment status.
     */
    public function deleteEnrollmentStatus(EnrollmentStatus $enrollmentStatus): ?bool
    {
        return $enrollmentStatus->delete();
    }

    /**
     * Soft delete an enrollment status by ID.
     */
    public function deleteEnrollmentStatusById(int $id): bool
    {
        $enrollmentStatus = EnrollmentStatus::find($id);
        return $enrollmentStatus ? $enrollmentStatus->delete() : false;
    }

    /**
     * Bulk soft delete enrollment statuses by their IDs.
     */
    public function bulkDeleteEnrollmentStatuses(array $ids): int
    {
        return EnrollmentStatus::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted enrollment status by its ID.
     */
    public function restoreEnrollmentStatus(int $id): bool
    {
        $enrollmentStatus = EnrollmentStatus::withTrashed()->find($id);
        return $enrollmentStatus ? $enrollmentStatus->restore() : false;
    }

    /**
     * Bulk restore soft-deleted enrollment statuses by their IDs.
     */
    public function bulkRestoreEnrollmentStatuses(array $ids): int
    {
        return EnrollmentStatus::withTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete an enrollment status by its ID.
     */
    public function permanentlyDelete(int $id): bool
    {
        $enrollmentStatus = EnrollmentStatus::withTrashed()->find($id);
        return $enrollmentStatus ? $enrollmentStatus->forceDelete() : false;
    }

    /**
     * Bulk permanently delete enrollment statuses by their IDs.
     */
    public function bulkPermanentDeleteEnrollmentStatuses(array $ids): int
    {
        return EnrollmentStatus::withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * Count the number of active enrollment statuses.
     */
    public function countActiveEnrollmentStatuses(): int
    {
        return EnrollmentStatus::where('is_active', true)->count();
    }

    /**
     * Get a list of enrollment statuses suitable for dropdowns.
     */
    public function getEnrollmentStatusesForDropdown(bool $activeOnly = true): Collection
    {
        $query = EnrollmentStatus::query();

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->select('id', 'name')->get();
    }
}
