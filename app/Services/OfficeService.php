<?php

namespace App\Services;

use App\Models\Office;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OfficeService
{
    /**
     * Get paginated and filtered offices.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedOffices(array $params)
    {
        $query = Office::query();
            
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
        }
        
        // Determine items per page, default to 15
        $perPage = $params['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }
    
    /**
     * Apply filters to the Office query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params)
    {

        if (isset($params['search']) && isset($params['search_term']) && filter_var($params['search'], FILTER_VALIDATE_BOOLEAN)) {
            $searchTerm = $params['search_term'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('short_name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                   ->orWhere('description', 'like', "%{$searchTerm}%")
                   ->orWhereHas('officeType', fn($q) => $q->where('name', 'like', "%{$searchTerm}%"))
                   ->orWhereHas('campus', fn($q) => $q->where('name', 'like', "%{$searchTerm}%")->orWhere('code', 'like', "%{$searchTerm}%")) 
                   ->orWhereHas('faculty', fn($q) => $q->where('name', 'like', "%{$searchTerm}%")->orWhere('code', 'like', "%{$searchTerm}%")) 
                    ->orWhereHas('head', fn($q) => $q->where('first_name', 'like', "%{$searchTerm}%")->orWhere('last_name', 'like', "%{$searchTerm}%"));
            });
        }

        // Filter by Office Type ID
        if (!empty($params['office_type_id'])) {
            $query->where('office_type_id', $params['office_type_id']);
        }

        // Filter by Campus ID
        if (!empty($params['campus_id'])) {
            $query->where('campus_id', $params['campus_id']);
        }

        // Filter by Faculty ID
        if (!empty($params['faculty_id'])) {
            $query->where('faculty_id', $params['faculty_id']);
        }

        // Filter by Head ID
        if (!empty($params['head_id'])) {
            $query->where('head_id', $params['head_id']);
        }

        // Filter by active status (handles '', 'active', 'inactive')
        if (isset($params['status']) && $params['status'] !== '') {
            $isActive = $params['status'] === 'active'; // true for 'active', false for 'inactive'
            $query->where('is_active', $isActive);
        }
        // No need for an 'else' because if status is '', we don't filter by is_active

        // Filter by Parent Office ID
        if (!empty($params['parent_office_id'])) {
            $query->where('parent_office_id', $params['parent_office_id']);
        }

        // Filter by Established Year Range
        if (!empty($params['established_year_start'])) {
            $query->where('established_year', '>=', $params['established_year_start']);
        }
        if (!empty($params['established_year_end'])) {
            $query->where('established_year', '<=', $params['established_year_end']);
        }

        return $query;
    }
    
    /**
     * Apply sorting to the Office query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params)
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields for Office
        $allowedSortFields = ['name', 'short_name', 'code', 'created_at', 'is_active'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sort if the requested field is not allowed
            $query->orderBy('name', 'asc'); 
        }
        
        return $query;
    }
    
    /**
     * Get a single office by its ID.
     *
     * @param int $id The ID of the office.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load.
     * @return Office|Builder|null
     */
    public function getOffice(int $id, bool $withTrashed = false, array $with = [])
    {
        $query = Office::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Get a single office by its ID.
     *
     * @param int $id The ID of the office.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, Office>|Builder|null
     */
    public function getOffices(int|array $ids, bool $withTrashed = false)
    {
        $query = Office::query();
        
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids);
    }

    //Office::whereIn('id', $this->selectedOffices)->get()
    
    /**
     * Create a new office using validated data.
     *
     * @param array $validatedData Validated data for creating the office.
     * @return Office
     */
    public function createOffice(array $validatedData)
    {
        // Ensure 'code' and 'short_name' are uppercase if needed
        if (isset($validatedData['code'])) {
            $validatedData['code'] = strtoupper($validatedData['code']);
        }
        if (isset($validatedData['short_name'])) {
            $validatedData['short_name'] = strtoupper($validatedData['short_name']); // Assuming short_name should also be uppercase
        }
        return Office::create($validatedData);
    }
    
    /**
     * Update an existing office using validated data.
     *
     * @param Office $office The office model instance to update.
     * @param array $validatedData Validated data for updating the office.
     * @return bool True on success, false otherwise.
     */
    public function updateOffice(Office $office, array $validatedData)
    {
        // Ensure 'code' and 'short_name' are uppercase if needed
        if (isset($validatedData['code'])) {
            $validatedData['code'] = strtoupper($validatedData['code']);
        }
        if (isset($validatedData['short_name'])) {
            $validatedData['short_name'] = strtoupper($validatedData['short_name']); // Assuming short_name should also be uppercase
        }
        // Ensure nullable fields are handled correctly during update
        $validatedData['campus_id'] = $validatedData['campus_id'] ?? null;
        $validatedData['faculty_id'] = $validatedData['faculty_id'] ?? null;
        return $office->update($validatedData);
    }
    
    /**
     * Toggle the active status of an office.
     *
     * @param Office $office The office model instance.
     * @return bool True on success, false otherwise.
     */
    public function toggleActiveStatus(Office $office)
    {
        // $office->toggle('is_active');
        $office->is_active = !$office->is_active;
        return $office->save();
    }

     
    /**
     * Toggle the active status of an office.
     *
     * @param Office $office The office model instance.
     * @return array of count, i.e. totalToggledCount, activatedCount, deactivatedCount
     */
    public function bulkToggleActiveStatus($offices)
    {
        $toggleCount = [
            'totalToggledCount' =>  0, 
            'activatedCount' => 0,
            'deactivatedCount' => 0, 
        ];
        foreach($offices as $office){
            $currenStatus = $office->is_active;
            $toggleCount['totalToggledCount'] += $this->toggleActiveStatus($office);
            $currenStatus ? $toggleCount['deactivatedCount'] += 1 : $toggleCount['activatedCount'] += 1;
        }
        return $toggleCount;
    }
    
    /**
     * Soft delete an office.
     *
     * @param Office $office The office model instance to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteOffice(Office $office )
    {
        return $office->delete(); // Uses SoftDeletes trait
    }

    /**
     * Soft delete an office.
     *
     * @param $officeId The office id to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteOfficeById($officeId)
    {
        $office = Office::findOrFail($officeId);
        return $office->delete(); 
    }

      /**
     * Soft delete an office.
     *
     * @param array The office model instance to delete.
     * @return bool True on success, false otherwise.
     */
    public function bulkDeleteOfficeByIds($officesIds)
    {
        return Office::whereIn('id', $officesIds)->delete();
    }
    
    /**
     * Restore a soft-deleted office by its ID.
     *
     * @param int $id The ID of the soft-deleted office.
     * @return bool True on success, false otherwise.
     */
    public function restoreOffice(int $id)
    {
        $office = Office::withTrashed()->find($id);
        return $office ? $office->restore() : false;
    }

    /**
     * Restore a soft-deleted office by its ID.
     *
     * @param array $officeIdsToRestore The ID of the soft-deleted office.
     * @return bool|mixed|null
     */
    public function bulkRestoreOffices(array $officeIdsToRestore)
    {
        $offices = Office::withTrashed()->whereIn('id', $officeIdsToRestore);
        return $offices->restore();
    }

    
    /**
     * Permanently delete an office by its ID (use with caution).
     *
     * @param int $id The ID of the office to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDelete(int $id)
    {
        $office = Office::withTrashed()->find($id);
        return $office ? $office->forceDelete() : false;
    }

    
    /**
     * Restore a soft-deleted office by its ID.
     *
     * @param array $officeIdsToPermanentDelete The ID of the soft-deleted office.
     * @return bool|mixed|null
     */
    public function bulkPermanentDelete(array $officeIdsToPermanentDelete)
    {
        $offices = Office::withTrashed()->whereIn('id', $officeIdsToPermanentDelete);
        return $offices->forceDelete();
    }

    
    
    /**
     * Count the number of active offices.
     *
     * @return int
     */
    public function countActiveOffices()
    {
        return Office::where('is_active', true)->count();
    }
    
    /**
     * Get a list of offices suitable for dropdowns (ID, name, code).
     *
     * @param bool $activeOnly Include only active offices.
     * @return Collection
     */
    public function getOfficesForDropdown(bool $activeOnly = true)
    {
        $query = Office::query();
        
        if ($activeOnly) {
            $query->where('is_active', true); // Or use the scope: $query->active();
        }
        
        return $query->orderBy('name')->select('id', 'name', 'code')->get();
    }
    
    /**
     * Check if an office with the given code already exists.
     *
     * @param string $code The code to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool True if the code exists, false otherwise.
     */
    public function codeExists(string $code, ?int $exceptId = null)
    {
        $query = Office::where('code', strtoupper($code)); // Ensure case-insensitivity if needed
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }
}
