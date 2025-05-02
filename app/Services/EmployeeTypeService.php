<?php

namespace App\Services;

use App\Models\EmployeeType; // Use EmployeeType model
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EmployeeTypeService
{
    /**
     * Get paginated and filtered employee types.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedEmployeeTypes(array $params): LengthAwarePaginator
    {
        $query = EmployeeType::query();

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
     * Apply filters to the EmployeeType query.
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
     * Apply sorting to the EmployeeType query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields for EmployeeType
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
     * Get a single employee type by its ID.
     *
     * @param int $id The ID of the employee type.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load.
     * @return EmployeeType|Builder|null
     */
    public function getEmployeeType(int $id, bool $withTrashed = false, array $with = []): EmployeeType|Builder|null
    {
        $query = EmployeeType::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Get multiple employee types by their IDs.
     *
     * @param array $ids Array of employee type IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, EmployeeType>|Builder
     */
    public function getEmployeeTypes(array $ids, bool $withTrashed = false): Collection|Builder
    {
        $query = EmployeeType::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids);
    }

    /**
     * Create a new employee type using validated data.
     *
     * @param array $validatedData Validated data for creating the employee type.
     * @return EmployeeType
     */
    public function createEmployeeType(array $validatedData): EmployeeType
    {
        return EmployeeType::create($validatedData);
    }

    /**
     * Update an existing employee type using validated data.
     *
     * @param EmployeeType $employeeType The employee type model instance to update.
     * @param array $validatedData Validated data for updating the employee type.
     * @return bool True on success, false otherwise.
     */
    public function updateEmployeeType(EmployeeType $employeeType, array $validatedData): bool
    {
        return $employeeType->update($validatedData);
    }

    /**
     * Toggle the active status of an employee type.
     *
     * @param EmployeeType $employeeType The employee type model instance.
     * @return bool True on success, false otherwise.
     */
    public function toggleActiveStatus(EmployeeType $employeeType): bool
    {
        $employeeType->is_active = !$employeeType->is_active;
        return $employeeType->save();
    }

    /**
     * Bulk toggle the active status of multiple employee types.
     *
     * @param Collection<int, EmployeeType> $employeeTypes Collection of employee types to toggle.
     * @return array Counts of toggled statuses: ['totalToggledCount', 'activatedCount', 'deactivatedCount']
     */
    public function bulkToggleActiveStatus(Collection $employeeTypes): array
    {
        $toggleCount = [
            'totalToggledCount' => 0,
            'activatedCount' => 0,
            'deactivatedCount' => 0,
        ];
        foreach ($employeeTypes as $type) {
            $currentStatus = $type->is_active;
            if ($this->toggleActiveStatus($type)) {
                $toggleCount['totalToggledCount']++;
                $currentStatus ? $toggleCount['deactivatedCount']++ : $toggleCount['activatedCount']++;
            }
        }
        return $toggleCount;
    }

    /**
     * Soft delete an employee type.
     *
     * @param EmployeeType $employeeType The employee type model instance to delete.
     * @return bool|null True on success, false on failure, null if model not found or already deleted.
     */
    public function deleteEmployeeType(EmployeeType $employeeType): ?bool
    {
        return $employeeType->delete(); // Assumes SoftDeletes trait is used on EmployeeType model
    }

    /**
     * Soft delete an employee type by its ID.
     *
     * @param int $id The employee type ID to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteEmployeeTypeById(int $id): bool
    {
        $employeeType = EmployeeType::find($id);
        return $employeeType ? $employeeType->delete() : false;
    }

    /**
     * Bulk soft delete employee types by their IDs.
     *
     * @param array $ids Array of employee type IDs to delete.
     * @return int The number of records deleted.
     */
    public function bulkDeleteEmployeeTypeByIds(array $ids): int
    {
        return EmployeeType::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted employee type by its ID.
     *
     * @param int $id The ID of the soft-deleted employee type.
     * @return bool True on success, false otherwise.
     */
    public function restoreEmployeeType(int $id): bool
    {
        $employeeType = EmployeeType::withTrashed()->find($id);
        return $employeeType ? $employeeType->restore() : false;
    }

    /**
     * Bulk restore soft-deleted employee types by their IDs.
     *
     * @param array $ids Array of employee type IDs to restore.
     * @return bool|mixed|null Result of the restore operation.
     */
    public function bulkRestoreEmployeeTypes(array $ids)
    {
        $types = EmployeeType::withTrashed()->whereIn('id', $ids);
        return $types->restore();
    }

    /**
     * Permanently delete an employee type by its ID (use with caution).
     *
     * @param int $id The ID of the employee type to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDelete(int $id): bool
    {
        $employeeType = EmployeeType::withTrashed()->find($id);
        return $employeeType ? $employeeType->forceDelete() : false;
    }

    /**
     * Bulk permanently delete employee types by their IDs.
     *
     * @param array $ids Array of employee type IDs to permanently delete.
     * @return bool|mixed|null Result of the force delete operation.
     */
    public function bulkPermanentDelete(array $ids)
    {
        $types = EmployeeType::withTrashed()->whereIn('id', $ids);
        return $types->forceDelete();
    }

    /**
     * Count the number of active employee types.
     *
     * @return int
     */
    public function countActiveEmployeeTypes(): int
    {
        return EmployeeType::where('is_active', true)->count();
    }

    /**
     * Get a list of employee types suitable for dropdowns (ID, name).
     *
     * @param bool $activeOnly Include only active employee types.
     * @return Collection
     */
    public function getEmployeeTypesForDropdown(bool $activeOnly = true): Collection
    {
        $query = EmployeeType::query();

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->select('id', 'name')->get();
    }
}
