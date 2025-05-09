<?php

namespace App\Services;

use App\Models\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class SessionService
{
    /**
     * Get paginated and filtered sessions.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedSessions(array $params): LengthAwarePaginator
    {
        $query = Session::query();

        $query->when(Arr::get($params, 'with_trashed'), fn ($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn ($q) => $q->onlyTrashed());

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        $perPage = Arr::get($params, 'per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the Session query.
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
                  ->orWhere('type', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $query->when(Arr::get($params, 'type'), fn (Builder $q, $type) => $q->where('type', $type));
        $query->when(Arr::get($params, 'start_date_start'), fn (Builder $q, $date) => $q->whereDate('start_date', '>=', $date));
        $query->when(Arr::get($params, 'start_date_end'), fn (Builder $q, $date) => $q->whereDate('start_date', '<=', $date));
        $query->when(Arr::get($params, 'end_date_start'), fn (Builder $q, $date) => $q->whereDate('end_date', '>=', $date));
        $query->when(Arr::get($params, 'end_date_end'), fn (Builder $q, $date) => $q->whereDate('end_date', '<=', $date));


        return $query;
    }

    /**
     * Apply sorting to the Session query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'name');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = [
            'name', 'start_date', 'end_date', 'type', 'created_at', 'updated_at'
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc'); // Default sort
        }

        return $query;
    }

    /**
     * Get a single session by its ID.
     */
    public function getSession(int $id, bool $withTrashed = false, array $with = []): ?Session
    {
        $query = Session::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple sessions by their IDs.
     */
    public function getSessionsByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = Session::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new session using validated data.
     */
    public function createSession(array $validatedData): Session
    {
        return Session::create($validatedData);
    }

    /**
     * Update an existing session using validated data.
     */
    public function updateSession(Session $session, array $validatedData): bool
    {
        return $session->update($validatedData);
    }

    /**
     * Soft delete a session.
     */
    public function deleteSession(Session $session): ?bool
    {
        return $session->delete();
    }

    /**
     * Soft delete a session by ID.
     */
    public function deleteSessionById(int $id): bool
    {
        $session = Session::find($id);
        return $session ? $session->delete() : false;
    }

    /**
     * Bulk soft delete sessions by their IDs.
     */
    public function bulkDeleteSessions(array $ids): int
    {
        return Session::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted session by its ID.
     */
    public function restoreSession(int $id): bool
    {
        $session = Session::withTrashed()->find($id);
        return $session ? $session->restore() : false;
    }

    /**
     * Bulk restore soft-deleted sessions by their IDs.
     */
    public function bulkRestoreSessions(array $ids): int
    {
        return Session::withTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a session by its ID.
     */
    public function permanentlyDelete(int $id): bool
    {
        $session = Session::withTrashed()->find($id);
        return $session ? $session->forceDelete() : false;
    }

    /**
     * Bulk permanently delete sessions by their IDs.
     */
    public function bulkPermanentDeleteSessions(array $ids): int
    {
        return Session::withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * Get a list of sessions suitable for dropdowns.
     */
    public function getSessionsForDropdown(array $filters = []): Collection
    {
        $query = Session::query();

        $query->when(Arr::get($filters, 'type'), fn (Builder $q, $type) => $q->where('type', $type));
        $query->when(Arr::get($filters, 'start_date_after'), fn (Builder $q, $date) => $q->whereDate('start_date', '>', $date));
        $query->when(Arr::get($filters, 'end_date_before'), fn (Builder $q, $date) => $q->whereDate('end_date', '<', $date));


        return $query->orderBy('name')->select('id', 'name')->get();
    }
}
