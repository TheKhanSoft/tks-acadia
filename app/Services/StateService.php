<?php

namespace App\Services;

use App\Models\State;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class StateService
{
    /**
     * Get a paginated list of states.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedStates(array $filters = []): LengthAwarePaginator
    {
        $query = State::query();

        // Apply filters
        if (!empty($filters['search_term'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_term'] . '%');
            });
        }

        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_dir'] ?? 'asc';

        // Handle sorting by related country name if needed (requires join or subquery)
        if ($sortField === 'country_id') {
             $query->join('countries', 'states.country_id', '=', 'countries.id')
                   ->orderBy('countries.name', $sortDirection)
                   ->select('states.*'); // Select states columns to avoid ambiguity
        } else {
            $query->orderBy($sortField, $sortDirection);
        }


        $perPage = $filters['per_page'] ?? 10;

        // Eager load relationships
        if (!empty($filters['with'])) {
            $query->with($filters['with']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a single state by ID.
     *
     * @param int $id
     * @param bool $withTrashed
     * @param array $with
     * @return State|null
     */
    public function getState(int $id, bool $withTrashed = false, array $with = []): ?State
    {
        $query = State::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Create a new state.
     *
     * @param array $data
     * @return State
     */
    public function createState(array $data): State
    {
        return State::create($data);
    }

    /**
     * Update an existing state.
     *
     * @param State $state
     * @param array $data
     * @return bool
     */
    public function updateState(State $state, array $data): bool
    {
        return $state->update($data);
    }

    /**
     * Soft delete a state by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteStateById(int $id): bool
    {
        $state = $this->getState($id);
        if ($state) {
            return $state->delete();
        }
        return false;
    }

    /**
     * Soft delete multiple states by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkDeleteStateByIds(array $ids): int
    {
        return State::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted state.
     *
     * @param int $id
     * @return bool
     */
    public function restoreState(int $id): bool
    {
        $state = $this->getState($id, true); // Include trashed
        if ($state && $state->trashed()) {
            return $state->restore();
        }
        return false;
    }

    /**
     * Restore multiple soft-deleted states.
     *
     * @param array $ids
     * @return int
     */
    public function bulkRestoreStates(array $ids): int
    {
        return State::onlyTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a state by ID.
     *
     * @param int $id
     * @return bool
     */
    public function permanentlyDelete(int $id): bool
    {
        $state = $this->getState($id, true); // Include trashed
        if ($state) {
            return $state->forceDelete();
        }
        return false;
    }

    /**
     * Permanently delete multiple states by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkPermanentDelete(array $ids): int
    {
        return State::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }
}
