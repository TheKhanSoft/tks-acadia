<?php

namespace App\Services;

use App\Models\OfficeType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OfficeTypeService
{
    /**
     * Get paginated and filtered office types.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedOfficeTypes(array $params)
    {
        $query = OfficeType::query();
            
        // Include soft-deleted records if requested
        if (isset($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }
        
        // Show only soft-deleted records if requested
        if (isset($params['only_trashed']) && filter_var($params['only_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->onlyTrashed();
        }

        // Apply search filters if search is enabled and a term is provided
        if (isset($params['search']) && isset($params['search_term']) && filter_var($params['search'], FILTER_VALIDATE_BOOLEAN)) {
            $query = $this->applyFilters($query, $params);
        }

        // Apply sorting if requested
        if (isset($params['sort_by'])) {
            $query = $this->applySorting($query, $params);
        }
        
        // Determine items per page, default to 15
        $perPage = $params['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }
    
    /**
     * Apply filters to the OfficeType query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params)
    {
        // Filter by search term across name and code
        if (!empty($params['search_term'])) {
            $searchTerm = $params['search_term'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by active status
        if (isset($params['is_active']) && $params['is_active'] !== '') {
            $isActive = filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }
        
        return $query;
    }
    
    /**
     * Apply sorting to the OfficeType query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params)
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction
        
        // Define allowed sortable fields for OfficeType
        $allowedSortFields = ['name', 'code', 'created_at', 'is_active']; 
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sort if the requested field is not allowed
            $query->orderBy('name', 'asc'); 
        }
        
        return $query;
    }
    
    /**
     * Get a single office type by its ID.
     *
     * @param int $id The ID of the office type.
     * @param bool $withTrashed Include soft-deleted records.
     * @return OfficeType|Builder|null
     */
    public function getOfficeType(int $id, bool $withTrashed = false)
    {
        $query = OfficeType::query();
        
        if ($withTrashed) {
            $query->withTrashed();
        }
        
        return $query->find($id);
    }

    /**
     * Get a single office type by its ID.
     *
     * @param int $id The ID of the office type.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, OfficeType>|Builder|null
     */
    public function getOfficeTypes(int|array $ids, bool $withTrashed = false)
    {
        $query = OfficeType::query();
        
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids);
    }

    //OfficeType::whereIn('id', $this->selectedOfficeTypes)->get()
    
    /**
     * Create a new office type using validated data.
     *
     * @param array $validatedData Validated data for creating the office type.
     * @return OfficeType
     */
    public function createOfficeType(array $validatedData)
    {
        if (isset($validatedData['code'])) {
            $validatedData['code'] = strtoupper($validatedData['code']);
        }
        return OfficeType::create($validatedData);
    }
    
    /**
     * Update an existing office type using validated data.
     *
     * @param OfficeType $officeType The office type model instance to update.
     * @param array $validatedData Validated data for updating the office type.
     * @return bool True on success, false otherwise.
     */
    public function updateOfficeType(OfficeType $officeType, array $validatedData)
    {
        // Ensure 'code' is uppercase if that's a requirement
        if (isset($validatedData['code'])) {
            $validatedData['code'] = strtoupper($validatedData['code']);
        }
        return $officeType->update($validatedData);
    }
    
    /**
     * Toggle the active status of an office type.
     *
     * @param OfficeType $officeType The office type model instance.
     * @return bool True on success, false otherwise.
     */
    public function toggleActiveStatus(OfficeType $officeType)
    {
        // $officeType->toggle('is_active');
        $officeType->is_active = !$officeType->is_active;
        return $officeType->save();
    }

     
    /**
     * Toggle the active status of an office type.
     *
     * @param OfficeType $officeType The office type model instance.
     * @return array of count, i.e. totalToggledCount, activatedCount, deactivatedCount
     */
    public function bulkToggleActiveStatus($officeTypes)
    {
        $toggleCount = [
            'totalToggledCount' =>  0, 
            'activatedCount' => 0,
            'deactivatedCount' => 0, 
        ];
        foreach($officeTypes as $officeType){
            $currenStatus = $officeType->is_active;
            $toggleCount['totalToggledCount'] += $this->toggleActiveStatus($officeType);
            $currenStatus ? $toggleCount['deactivatedCount'] += 1 : $toggleCount['activatedCount'] += 1;
        }
        return $toggleCount;
    }
    
    /**
     * Soft delete an office type.
     *
     * @param OfficeType $officeType The office type model instance to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteOfficeType(OfficeType $officeType )
    {
        return $officeType->delete(); // Uses SoftDeletes trait
    }

    /**
     * Soft delete an office type.
     *
     * @param $officeTypeId The office type id to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteOfficeTypeById($officeTypeId)
    {
        $officeType = OfficeType::findOrFail($officeTypeId);
        return $officeType->delete(); 
    }

      /**
     * Soft delete an office type.
     *
     * @param array The office type model instance to delete.
     * @return bool True on success, false otherwise.
     */
    public function bulkDeleteOfficeTypeByIds($officeTypesIds)
    {
        return OfficeType::whereIn('id', $officeTypesIds)->delete();
    }
    
    /**
     * Restore a soft-deleted office type by its ID.
     *
     * @param int $id The ID of the soft-deleted office type.
     * @return bool True on success, false otherwise.
     */
    public function restoreOfficeType(int $id)
    {
        $officeType = OfficeType::withTrashed()->find($id);
        return $officeType ? $officeType->restore() : false;
    }

    /**
     * Restore a soft-deleted office type by its ID.
     *
     * @param array $officeTypeIdsToRestore The ID of the soft-deleted office type.
     * @return bool|mixed|null
     */
    public function bulkRestoreOfficeTypes(array $officeTypeIdsToRestore)
    {
        $officeTypes = OfficeType::withTrashed()->whereIn('id', $officeTypeIdsToRestore);
        return $officeTypes->restore();
    }

    
    /**
     * Permanently delete an office type by its ID (use with caution).
     *
     * @param int $id The ID of the office type to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDelete(int $id)
    {
        $officeType = OfficeType::withTrashed()->find($id);
        return $officeType ? $officeType->forceDelete() : false;
    }

    
    /**
     * Restore a soft-deleted office type by its ID.
     *
     * @param array $officeTypeIdsToPermanentDelete The ID of the soft-deleted office type.
     * @return bool|mixed|null
     */
    public function bulkPermanentDelete(array $officeTypeIdsToPermanentDelete)
    {
        $officeTypes = OfficeType::withTrashed()->whereIn('id', $officeTypeIdsToPermanentDelete);
        return $officeTypes->forceDelete();
    }

    
    
    /**
     * Count the number of active office types.
     *
     * @return int
     */
    public function countActiveOfficeTypes()
    {
        return OfficeType::where('is_active', true)->count();
    }
    
    /**
     * Get a list of office types suitable for dropdowns (ID, name, code).
     *
     * @param bool $activeOnly Include only active office types.
     * @return Collection
     */
    public function getOfficeTypesForDropdown(bool $activeOnly = true)
    {
        $query = OfficeType::query();
        
        if ($activeOnly) {
            $query->where('is_active', true); // Or use the scope: $query->active();
        }
        
        return $query->orderBy('name')->select('id', 'name', 'code')->get();
    }
    
    /**
     * Check if an office type with the given code already exists.
     *
     * @param string $code The code to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool True if the code exists, false otherwise.
     */
    public function codeExists(string $code, ?int $exceptId = null)
    {
        $query = OfficeType::where('code', strtoupper($code)); // Ensure case-insensitivity if needed
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }
}
