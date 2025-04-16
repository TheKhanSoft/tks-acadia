<?php

namespace App\Services;

use App\Models\Campus;
use App\Http\Requests\CampusRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CampusService
{
    /**
     * Get paginated and filtered campuses.
     *
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getPaginatedCampuses(array $params): LengthAwarePaginator
    {
        $query = Campus::query();
        
        // Apply filters
        $query = $this->applyFilters($query, $params);
        
        // Apply sorting
        $query = $this->applySorting($query, $params);
        
        // Show or hide deleted records
        if (isset($params['with_trashed']) && filter_var($params['with_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->withTrashed();
        }
        
        if (isset($params['only_trashed']) && filter_var($params['only_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->onlyTrashed();
        }
        
        $perPage = $params['per_page'] ?? 15;
        
        return $query->paginate($perPage)->appends($params);
    }
    
    /**
     * Apply filters to the query
     *
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        // Filter by search term
        if (!empty($params['search'])) {
            $searchTerm = $params['search'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by location
        if (!empty($params['location'])) {
            $query->where('location', 'like', "%{$params['location']}%");
        }
        
        // Filter by founding year
        if (!empty($params['founded_year'])) {
            $query->where('founded_year', $params['founded_year']);
        }
        
        // Filter by founding year range
        if (!empty($params['min_year'])) {
            $query->where('founded_year', '>=', $params['min_year']);
        }
        
        if (!empty($params['max_year'])) {
            $query->where('founded_year', '<=', $params['max_year']);
        }
        
        // Filter by active status
        if (isset($params['is_active']) && $params['is_active'] !== '') {
            $isActive = filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }
        
        // Filter by departments if needed
        if (!empty($params['department_id'])) {
            $query->whereHas('offices', function (Builder $q) use ($params) {
                $q->where('office_type_id', function ($sq) {
                    $sq->select('id')
                      ->from('office_types')
                      ->where('name', 'Department');
                })->where('offices.id', $params['department_id']);
            });
        }
        
        return $query;
    }
    
    /**
     * Apply sorting to the query
     *
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name';
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc';
        
        $allowedSortFields = ['name', 'code', 'location', 'founded_year', 'created_at', 'is_active'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query;
    }
    
    /**
     * Get a single campus by ID
     *
     * @param int $id
     * @param bool $withTrashed
     * @return Campus|null
     */
    public function getCampus(int $id, bool $withTrashed = false): ?Campus
    {
        $query = Campus::query();
        
        if ($withTrashed) {
            $query->withTrashed();
        }
        
        return $query->find($id);
    }
    
    /**
     * Create a new campus
     *
     * @param CampusRequest $request
     * @return Campus
     */
    public function createCampus(CampusRequest $request): Campus
    {
        return Campus::create($request->validated());
    }
    
    /**
     * Update an existing campus
     *
     * @param Campus $campus
     * @param CampusRequest $request
     * @return bool
     */
    public function updateCampus(Campus $campus, CampusRequest $request): bool
    {
        return $campus->update($request->validated());
    }
    
    /**
     * Toggle the active status of a campus
     *
     * @param Campus $campus
     * @return bool
     */
    public function toggleActiveStatus(Campus $campus): bool
    {
        $campus->is_active = !$campus->is_active;
        return $campus->save();
    }
    
    /**
     * Soft delete a campus
     *
     * @param Campus $campus
     * @return bool
     */
    public function deleteCampus(Campus $campus): bool
    {
        return $campus->delete();
    }
    
    /**
     * Restore a soft-deleted campus
     *
     * @param int $id
     * @return bool
     */
    public function restoreCampus(int $id): bool
    {
        return Campus::withTrashed()->findOrFail($id)->restore();
    }
    
    /**
     * Permanently delete a campus
     *
     * @param int $id
     * @return bool
     */
    public function permanentlyDeleteCampus(int $id): bool
    {
        return Campus::withTrashed()->findOrFail($id)->forceDelete();
    }
    
    /**
     * Count active campuses
     *
     * @return int
     */
    public function countActiveCampuses(): int
    {
        return Campus::where('is_active', true)->count();
    }
    
    /**
     * Get list of campuses for dropdown
     *
     * @param bool $activeOnly
     * @return Collection
     */
    public function getCampusesForDropdown(bool $activeOnly = true): Collection
    {
        $query = Campus::query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('name')->select('id', 'name', 'code')->get();
    }
    
    /**
     * Check if a campus with the given code exists
     *
     * @param string $code
     * @param int|null $exceptId
     * @return bool
     */
    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $query = Campus::where('code', $code);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }
}