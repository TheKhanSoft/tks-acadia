<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CityService
{
    /**
     * Get a paginated list of cities.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedCities(array $filters = []): LengthAwarePaginator
    {
        $query = City::query();

        // Apply filters
        if (!empty($filters['search_term'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_term'] . '%');
            });
        }

        if (!empty($filters['state_id'])) {
            $query->where('state_id', $filters['state_id']);
        } elseif (!empty($filters['country_id'])) {
            // Filter by country via the state relationship
            $query->whereHas('state', function ($q) use ($filters) {
                $q->where('country_id', $filters['country_id']);
            });
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_dir'] ?? 'asc';

        // Handle sorting by related state or country name if needed
        if ($sortField === 'state_id') {
             $query->join('states', 'cities.state_id', '=', 'states.id')
                   ->orderBy('states.name', $sortDirection)
                   ->select('cities.*'); // Select cities columns to avoid ambiguity
        }
        // Add sorting by country name if needed (requires joining states and countries)
        // elseif ($sortField === 'country_id') {
        //      $query->join('states', 'cities.state_id', '=', 'states.id')
        //            ->join('countries', 'states.country_id', '=', 'countries.id')
        //            ->orderBy('countries.name', $sortDirection)
        //            ->select('cities.*');
        // }
        else {
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
     * Get a single city by ID.
     *
     * @param int $id
     * @param bool $withTrashed
     * @param array $with
     * @return City|null
     */
    public function getCity(int $id, bool $withTrashed = false, array $with = []): ?City
    {
        $query = City::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Create a new city.
     *
     * @param array $data
     * @return City
     */
    public function createCity(array $data): City
    {
        return City::create($data);
    }

    /**
     * Update an existing city.
     *
     * @param City $city
     * @param array $data
     * @return bool
     */
    public function updateCity(City $city, array $data): bool
    {
        return $city->update($data);
    }

    /**
     * Soft delete a city by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCityById(int $id): bool
    {
        $city = $this->getCity($id);
        if ($city) {
            return $city->delete();
        }
        return false;
    }

    /**
     * Soft delete multiple cities by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkDeleteCityByIds(array $ids): int
    {
        return City::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted city.
     *
     * @param int $id
     * @return bool
     */
    public function restoreCity(int $id): bool
    {
        $city = $this->getCity($id, true); // Include trashed
        if ($city && $city->trashed()) {
            return $city->restore();
        }
        return false;
    }

    /**
     * Restore multiple soft-deleted cities.
     *
     * @param array $ids
     * @return int
     */
    public function bulkRestoreCities(array $ids): int
    {
        return City::onlyTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a city by ID.
     *
     * @param int $id
     * @return bool
     */
    public function permanentlyDelete(int $id): bool
    {
        $city = $this->getCity($id, true); // Include trashed
        if ($city) {
            return $city->forceDelete();
        }
        return false;
    }

    /**
     * Permanently delete multiple cities by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkPermanentDelete(array $ids): int
    {
        return City::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }
}
