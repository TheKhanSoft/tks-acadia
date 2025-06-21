<?php

namespace App\Services;

use App\Models\SubjectType;
use App\Enums\IsActiveFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectTypeService
{
    public function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectType::class));
    }

    /**
     * Get paginated and filtered subject types.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedSubjectTypes(array $params): LengthAwarePaginator
    {
        $query = SubjectType::query();

        if (!empty($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN) && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        if (!empty($params['only_trashed']) && filter_var($params['only_trashed'], FILTER_VALIDATE_BOOLEAN) && $this->usesSoftDeletes()) {
            $query->onlyTrashed();
        }

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        $perPage = $params['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the SubjectType query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        if (!empty($params['search_term'])) {
            $searchTerm = $params['search_term'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if (isset($params['is_active_filter']) && $params['is_active_filter'] instanceof IsActiveFilter && $params['is_active_filter'] !== IsActiveFilter::ALL) {
            $query->where('is_active', $params['is_active_filter']->getBoolValue());
        } elseif (isset($params['is_active']) && is_bool($params['is_active'])) { // For direct boolean filter
            $query->where('is_active', $params['is_active']);
        }

        return $query;
    }

    /**
     * Apply sorting to the SubjectType query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name';
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

        if (in_array($sortField, $allowedSortFields)) {
            if ($sortField === 'deleted_at' && !$this->usesSoftDeletes()) {
                // Avoid sorting by deleted_at if model doesn't use soft deletes
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('name', 'asc');
        }
        return $query;
    }

    /**
     * Get a single subject type by its ID.
     *
     * @param int $id The ID of the subject type.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $relations Relations to load.
     * @return SubjectType|null
     */
    public function getSubjectType(int $id, bool $withTrashed = false, array $relations = []): ?SubjectType
    {
        $query = SubjectType::query();
        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($id);
    }
    
    /**
     * Get multiple subject types by their IDs.
     *
     * @param array $ids Array of IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Builder
     */
    public function getSubjectTypesQuery(array $ids, bool $withTrashed = false): Builder
    {
        $query = SubjectType::query()->whereIn('id', $ids);
        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }
        return $query;
    }


    /**
     * Create a new subject type.
     *
     * @param array $data
     * @return SubjectType
     * @throws \Exception
     */
    public function createSubjectType(array $data): SubjectType
    {
        DB::beginTransaction();
        try {
            $data['is_active'] = $data['is_active'] ?? true; // Default to true
            $subjectType = SubjectType::create($data);
            DB::commit();
            return $subjectType;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Subject Type: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Update an existing subject type.
     *
     * @param SubjectType $subjectType
     * @param array $data
     * @return SubjectType
     * @throws \Exception
     */
    public function updateSubjectType(SubjectType $subjectType, array $data): SubjectType
    {
        DB::beginTransaction();
        try {
            $subjectType->update($data);
            DB::commit();
            return $subjectType;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating Subject Type {$subjectType->id}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Toggle the active status of a subject type.
     *
     * @param SubjectType $subjectType
     * @return bool
     */
    public function toggleActiveStatus(SubjectType $subjectType): bool
    {
        DB::beginTransaction();
        try {
            $subjectType->is_active = !$subjectType->is_active;
            $result = $subjectType->save();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error toggling active status for Subject Type {$subjectType->id}: " . $e->getMessage(), ['exception' => $e]);
            throw $e; // Or return false
        }
    }
    
    /**
     * Bulk toggle the active status of subject types.
     *
     * @param Collection $subjectTypes Collection of SubjectType models.
     * @return array Counts of total toggled, activated, and deactivated.
     */
    public function bulkToggleActiveStatus(Collection $subjectTypes): array
    {
        $counts = ['totalToggledCount' => 0, 'activatedCount' => 0, 'deactivatedCount' => 0];
        DB::beginTransaction();
        try {
            foreach ($subjectTypes as $subjectType) {
                $currentStatus = $subjectType->is_active;
                $subjectType->is_active = !$currentStatus;
                if ($subjectType->save()) {
                    $counts['totalToggledCount']++;
                    if ($subjectType->is_active) {
                        $counts['activatedCount']++;
                    } else {
                        $counts['deactivatedCount']++;
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk toggling active status for Subject Types: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
        return $counts;
    }


    /**
     * Soft delete a subject type by its ID.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteSubjectTypeById(int $id): bool
    {
        if (!$this->usesSoftDeletes()) return false; // Or throw exception if soft delete is expected

        $subjectType = $this->getSubjectType($id);
        if (!$subjectType) return false; // Or throw ModelNotFoundException

        DB::beginTransaction();
        try {
            $result = $subjectType->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting Subject Type {$id}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Bulk soft delete subject types by their IDs.
     *
     * @param array $ids
     * @return int Number of records deleted.
     * @throws \Exception
     */
    public function bulkDeleteSubjectTypeByIds(array $ids): int
    {
        if (!$this->usesSoftDeletes()) return 0;

        DB::beginTransaction();
        try {
            $count = SubjectType::whereIn('id', $ids)->delete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting Subject Types: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Restore a soft-deleted subject type by its ID.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function restoreSubjectType(int $id): bool
    {
        if (!$this->usesSoftDeletes()) return false;

        $subjectType = $this->getSubjectType($id, true); // Get with trashed
        if (!$subjectType || !$subjectType->trashed()) return false;

        DB::beginTransaction();
        try {
            $result = $subjectType->restore();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error restoring Subject Type {$id}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Bulk restore soft-deleted subject types by their IDs.
     *
     * @param array $ids
     * @return int Number of records restored.
     * @throws \Exception
     */
    public function bulkRestoreSubjectTypes(array $ids): int
    {
        if (!$this->usesSoftDeletes()) return 0;

        DB::beginTransaction();
        try {
            $count = SubjectType::whereIn('id', $ids)->onlyTrashed()->restore();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk restoring Subject Types: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Permanently delete a subject type by its ID.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function permanentlyDelete(int $id): bool
    {
        $subjectType = $this->getSubjectType($id, true); // Get with trashed to ensure it exists
        if (!$subjectType) return false;

        DB::beginTransaction();
        try {
            $result = $subjectType->forceDelete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error permanently deleting Subject Type {$id}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Bulk permanently delete subject types by their IDs.
     *
     * @param array $ids
     * @return int Number of records permanently deleted.
     * @throws \Exception
     */
    public function bulkPermanentDelete(array $ids): int
    {
        DB::beginTransaction();
        try {
            // Ensure we are dealing with models that might be soft-deleted
            $query = $this->usesSoftDeletes() ? SubjectType::withTrashed() : SubjectType::query();
            $count = $query->whereIn('id', $ids)->forceDelete();
            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk permanently deleting Subject Types: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
    
    /**
     * Get all subject types, typically for exports or lists where pagination isn't needed.
     *
     * @param array $params Filtering and sorting parameters.
     * @return Collection
     */
    public function getAllSubjectTypes(array $params): Collection
    {
        $query = SubjectType::query();

        if (!empty($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN) && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }
        
        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        return $query->get();
    }


    /**
     * Get a query builder instance for exporting subject types.
     * This can be used by an export service.
     *
     * @param array $params Filtering and sorting parameters.
     * @return Builder
     */
    public function getExportQuery(array $params): Builder
    {
        $query = SubjectType::query();

        if (!empty($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN) && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }
        
        // Convert IsActiveFilter string from component to Enum if necessary
        if (isset($params['is_active_filter_value'])) {
            $params['is_active_filter'] = IsActiveFilter::tryFrom($params['is_active_filter_value']);
        }

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);
        
        return $query;
    }

    /**
     * Get a list of subject types suitable for dropdowns (ID, name).
     *
     * @param bool $activeOnly Include only active subject types.
     * @return Collection
     */
    public function getSubjectTypesForDropdown(bool $activeOnly = true): Collection
    {
        $query = SubjectType::query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('name')->select('id', 'name')->get();
    }

    /**
     * Get subject types for export based on current filters.
     *
     * @param string|null $searchTerm
     * @param string $sortField
     * @param string $sortDirection
     * @param bool $withTrashed
     * @return Collection
     */
    public function getSubjectTypesForExport(?string $searchTerm, string $sortField, string $sortDirection, bool $withTrashed): Collection
    {
        $query = SubjectType::query();

        if ($withTrashed && $this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        if ($searchTerm) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $allowedSortFields = ['name', 'description', 'is_active', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->get();
    }
}
