<?php

namespace App\Services;

use App\Models\EmployeeWorkStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EmployeeWorkStatusService
{
    /**
     * Get paginated and filtered employee work statuses.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedEmployeeWorkStatuses(array $params): LengthAwarePaginator
    {
        $query = EmployeeWorkStatus::query();

        // Include soft-deleted records if requested
        if (isset($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }

        // Show only soft-deleted records if requested
        if (isset($params['only_trashed']) && filter_var($params['only_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->onlyTrashed();
        }

        // Apply search filters if search is enabled and a term is provided
        $query = $this->applyFilters($query, $params);

        // Apply sorting if requested
        if (isset($params['sort_by'])) {
            $query = $this->applySorting($query, $params);
        } else {
            // Default sort if none requested
             $query->orderBy('name', 'asc');
        }

        // Determine items per page, default to 15
        $perPage = $params['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the EmployeeWorkStatus query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        if (isset($params['search']) && isset($params['search_term']) && filter_var($params['search'], FILTER_VALIDATE_BOOLEAN)) {
            $searchTerm = $params['search_term'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by active status (handles '', 'active', 'inactive')
        if (isset($params['status']) && $params['status'] !== '') {
            $isActive = $params['status'] === 'active'; // true for 'active', false for 'inactive'
            $query->where('is_active', $isActive);
        }

        return $query;
    }

    /**
     * Apply sorting to the EmployeeWorkStatus query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields for EmployeeWorkStatus
        $allowedSortFields = ['name', 'created_at', 'is_active'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sort if the requested field is not allowed
            $query->orderBy('name', 'asc');
        }

        return $query;
    }

    /**
     * Get a single employee work status by its ID.
     *
     * @param int $id The ID of the employee work status.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load.
     * @return EmployeeWorkStatus|Builder|null
     */
    public function getEmployeeWorkStatus(int $id, bool $withTrashed = false, array $with = []): EmployeeWorkStatus|Builder|null
    {
        $query = EmployeeWorkStatus::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Get multiple employee work statuses by their IDs.
     *
     * @param array $ids Array of employee work status IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, EmployeeWorkStatus>|Builder
     */
    public function getEmployeeWorkStatuses(array $ids, bool $withTrashed = false): Collection|Builder
    {
        $query = EmployeeWorkStatus::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids);
    }

    /**
     * Create a new employee work status using validated data.
     *
     * @param array $validatedData Validated data for creating the employee work status.
     * @return EmployeeWorkStatus
     */
    public function createEmployeeWorkStatus(array $validatedData): EmployeeWorkStatus
    {
        return EmployeeWorkStatus::create($validatedData);
    }

    /**
     * Update an existing employee work status using validated data.
     *
     * @param EmployeeWorkStatus $employeeWorkStatus The employee work status model instance to update.
     * @param array $validatedData Validated data for updating the employee work status.
     * @return bool True on success, false otherwise.
     */
    public function updateEmployeeWorkStatus(EmployeeWorkStatus $employeeWorkStatus, array $validatedData): bool
    {
        return $employeeWorkStatus->update($validatedData);
    }

    /**
     * Toggle the active status of an employee work status.
     *
     * @param EmployeeWorkStatus $employeeWorkStatus The employee work status model instance.
     * @return bool True on success, false otherwise.
     */
    public function toggleActiveStatus(EmployeeWorkStatus $employeeWorkStatus): bool
    {
        $employeeWorkStatus->is_active = !$employeeWorkStatus->is_active;
        return $employeeWorkStatus->save();
    }

    /**
     * Bulk toggle the active status of multiple employee work statuses.
     *
     * @param Collection<int, EmployeeWorkStatus> $employeeWorkStatuses Collection of employee work statuses to toggle.
     * @return array Counts of toggled statuses: ['totalToggledCount', 'activatedCount', 'deactivatedCount']
     */
    public function bulkToggleActiveStatus(Collection $employeeWorkStatuses): array
    {
        $toggleCount = [
            'totalToggledCount' => 0,
            'activatedCount' => 0,
            'deactivatedCount' => 0,
        ];
        foreach ($employeeWorkStatuses as $status) {
            $currentStatus = $status->is_active;
            if ($this->toggleActiveStatus($status)) {
                $toggleCount['totalToggledCount']++;
                $currentStatus ? $toggleCount['deactivatedCount']++ : $toggleCount['activatedCount']++;
            }
        }
        return $toggleCount;
    }

    /**
     * Soft delete an employee work status.
     *
     * @param EmployeeWorkStatus $employeeWorkStatus The employee work status model instance to delete.
     * @return bool|null True on success, false on failure, null if model not found or already deleted.
     */
    public function deleteEmployeeWorkStatus(EmployeeWorkStatus $employeeWorkStatus): ?bool
    {
        return $employeeWorkStatus->delete(); // Uses SoftDeletes trait
    }

    /**
     * Soft delete an employee work status by its ID.
     *
     * @param int $id The employee work status ID to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteEmployeeWorkStatusById(int $id): bool
    {
        $employeeWorkStatus = EmployeeWorkStatus::find($id);
        return $employeeWorkStatus ? $employeeWorkStatus->delete() : false;
    }

    /**
     * Bulk soft delete employee work statuses by their IDs.
     *
     * @param array $ids Array of employee work status IDs to delete.
     * @return int The number of records deleted.
     */
    public function bulkDeleteEmployeeWorkStatuses(array $ids): int
    {
        return EmployeeWorkStatus::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted employee work status by its ID.
     *
     * @param int $id The ID of the soft-deleted employee work status.
     * @return bool True on success, false otherwise.
     */
    public function restoreEmployeeWorkStatus(int $id): bool
    {
        $employeeWorkStatus = EmployeeWorkStatus::withTrashed()->find($id);
        return $employeeWorkStatus ? $employeeWorkStatus->restore() : false;
    }

    /**
     * Bulk restore soft-deleted employee work statuses by their IDs.
     *
     * @param array $ids Array of employee work status IDs to restore.
     * @return bool|mixed|null Result of the restore operation.
     */
    public function bulkRestoreEmployeeWorkStatuses(array $ids)
    {
        $statuses = EmployeeWorkStatus::withTrashed()->whereIn('id', $ids);
        return $statuses->restore();
    }

    /**
     * Permanently delete an employee work status by its ID (use with caution).
     *
     * @param int $id The ID of the employee work status to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDelete(int $id): bool
    {
        $employeeWorkStatus = EmployeeWorkStatus::withTrashed()->find($id);
        return $employeeWorkStatus ? $employeeWorkStatus->forceDelete() : false;
    }

    /**
     * Bulk permanently delete employee work statuses by their IDs.
     *
     * @param array $ids Array of employee work status IDs to permanently delete.
     * @return bool|mixed|null Result of the force delete operation.
     */
    public function bulkPermanentDeleteEmployeeWorkStatuses(array $ids)
    {
        $statuses = EmployeeWorkStatus::withTrashed()->whereIn('id', $ids);
        return $statuses->forceDelete();
    }

    /**
     * Count the number of active employment statuses.
     *
     * @return int
     */
    public function countActiveEmployeeWorkStatuses(): int
    {
        return EmployeeWorkStatus::where('is_active', true)->count();
    }

    /**
     * Get a list of employment statuses suitable for dropdowns (ID, name).
     *
     * @param bool $activeOnly Include only active employment statuses.
     * @return Collection
     */
    public function getEmployeeWorkStatusesForDropdown(bool $activeOnly = true): Collection
    {
        $query = EmployeeWorkStatus::query();

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->select('id', 'name')->get();
    }
}
