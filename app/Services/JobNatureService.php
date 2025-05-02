<?php

namespace App\Services;

use App\Models\JobNature;
use App\Http\Requests\JobNatureRequest; // Assuming you have this request
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Jfcherng\Diff\Renderer\Html\Json;

class JobNatureService
{
    /**
     * Get paginated and filtered job natures.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedJobNatures(array $params): LengthAwarePaginator
    {
        $query = JobNature::query();

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
        $query = $this->applySorting($query, $params);

        // Determine items per page, default to 15
        $perPage = $params['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the JobNature query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        // Apply search term filter
        if (isset($params['search']) && isset($params['search_term']) && filter_var($params['search'], FILTER_VALIDATE_BOOLEAN)) {
            $searchTerm = $params['search_term'];
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        // Add other filters specific to JobNature if needed in the future

        return $query;
    }

    /**
     * Apply sorting to the JobNature query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields for JobNature
        $allowedSortFields = ['name', 'created_at']; // Adjust as needed

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sort if the requested field is not allowed
            $query->orderBy('name', 'asc');
        }

        return $query;
    }

    /**
     * Get a single job nature by its ID.
     *
     * @param int $id The ID of the job nature.
     * @param bool $withTrashed Include soft-deleted records.
     * @return JobNature|Builder|null
     */
    public function getJobNature(int $id, bool $withTrashed = false)
    {
        $query = JobNature::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

     /**
     * Get a single jobNature type by its ID.
     *
     * @param int $id The ID of the jobNature type.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, JobNature>|Builder|null
     */
    public function getJobNatures(int|array $ids, bool $withTrashed = false)
    {
        $query = JobNature::query();
        
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids);
    }

     /**
     * Get multiple job natures by their IDs.
     *
     * @param array $ids Array of job nature IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, JobNature>
     */
    public function getJobNaturesByIds(array $ids, bool $withTrashed = false): Collection
    {
        $query = JobNature::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids)->get();
    }


    /**
     * Create a new job nature using validated data.
     *
     * @param array $validatedData Validated data from JobNatureRequest.
     * @return JobNature
     */
    public function createJobNature(array $validatedData): JobNature
    {
        return JobNature::create($validatedData);
    }

    /**
     * Update an existing job nature using validated data.
     *
     * @param JobNature $jobNature The job nature model instance to update.
     * @param array $validatedData Validated data from JobNatureRequest.
     * @return bool True on success, false otherwise.
     */
    public function updateJobNature(JobNature $jobNature, array $validatedData): bool
    {
        return $jobNature->update($validatedData);
    }

    /**
     * Toggle the active status of an jobNature type.
     *
     * @param JobNature $jobNature The jobNature type model instance.
     * @return bool True on success, false otherwise.
     */
    public function toggleActiveStatus(JobNature $jobNature)
    {
        $jobNature->is_active = !$jobNature->is_active;
        return $jobNature->save();
    }

     /**
     * Toggle the active status of an jobNature.
     *
     * @param JobNature $jobNature The jobNature model instance.
     * @return array of count, i.e. totalToggledCount, activatedCount, deactivatedCount
     */
    public function bulkToggleActiveStatus($jobNatures)
    {
        $toggleCount = [
            'totalToggledCount' =>  0, 
            'activatedCount' => 0,
            'deactivatedCount' => 0, 
        ];
        foreach($jobNatures as $jobNature){
            $currenStatus = $jobNature->is_active;
            $toggleCount['totalToggledCount'] += $this->toggleActiveStatus($jobNature);
            $currenStatus ? $toggleCount['deactivatedCount'] += 1 : $toggleCount['activatedCount'] += 1;
        }
        return $toggleCount;
    }

    /**
     * Soft delete a job nature.
     *
     * @param JobNature $jobNature The job nature model instance to delete.
     * @return bool|null True on success, false on failure, null if model not found.
     */
    public function deleteJobNature(JobNature $jobNature): ?bool
    {
        return $jobNature->delete(); // Uses SoftDeletes trait
    }


    /**
     * Soft delete multiple job natures by their IDs.
     *
     * @param array $ids Array of job nature IDs to delete.
     * @return int The number of records deleted.
     */
    public function bulkDeleteJobNatures(array $ids): int
    {
        return JobNature::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted job nature by its ID.
     *
     * @param int $id The ID of the soft-deleted job nature.
     * @return bool True on success, false otherwise.
     */
    public function restoreJobNature(int $id): bool
    {
        $jobNature = JobNature::withTrashed()->find($id);
        return $jobNature ? $jobNature->restore() : false;
    }

     /**
     * Restore multiple soft-deleted job natures by their IDs.
     *
     * @param array $ids Array of job nature IDs to restore.
     * @return int The number of records restored.
     */
    public function bulkRestoreJobNatures(array $ids): int
    {
        return JobNature::withTrashed()->whereIn('id', $ids)->restore();
    }


    /**
     * Permanently delete a job nature by its ID (use with caution).
     *
     * @param int $id The ID of the job nature to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDelete(int $id): bool
    {
        $jobNature = JobNature::withTrashed()->find($id);
        return $jobNature ? $jobNature->forceDelete() : false;
    }
    

    /**
     * Permanently delete multiple job natures by their IDs.
     *
     * @param array $ids Array of job nature IDs to permanently delete.
     * @return int The number of records permanently deleted.
     */
    public function bulkPermanentDeleteJobNatures(array $ids): int
    {
        return JobNature::withTrashed()->whereIn('id', $ids)->forceDelete();
    }


    /**
     * Get a list of job natures suitable for dropdowns (ID, name).
     *
     * @return Collection<int, object{id: int, name: string}>
     */
    public function getJobNaturesForDropdown(): Collection
    {
        return JobNature::orderBy('name')->select('id', 'name')->get();
    }

    /**
     * Check if a job nature with the given name already exists.
     *
     * @param string $name The name to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool True if the name exists, false otherwise.
     */
    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $query = JobNature::where('name', $name);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}
