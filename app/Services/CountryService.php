<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CountryService
{
    /**
     * Get a paginated list of countries.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedCountries(array $filters = [])
    {
        $query = Country::query();

        // Apply filters
        if (!empty($filters['search_term'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_term'] . '%')
                  ->orWhere('native', 'like', '%' . $filters['search_term'] . '%');
            });
        }

        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_dir'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    /**
     * Get a single country by ID.
     *
     * @param int $id
     * @param bool $withTrashed
     * @return Country|null|object
     */
    public function getCountry(int $id, bool $withTrashed = false)
    {
        $query = Country::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    /**
     * Create a new country.
     *
     * @param array $data
     * @return Country
     */
    public function createCountry(array $data)
    {
        dd($data);
        return Country::createOrFirst($data);
    }

    /**
     * Update an existing country.
     *
     * @param Country $country
     * @param array $data
     * @return bool
     */
    public function updateCountry(Country $country, array $data): bool
    {
        return $country->update($data);
    }

    /**
     * Soft delete a country by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCountryById(int $id): bool
    {
        $country = $this->getCountry($id);
        if ($country) {
            return $country->delete();
        }
        return false;
    }

    /**
     * Soft delete multiple countries by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkDeleteCountryByIds(array $ids): int
    {
        return Country::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted country.
     *
     * @param int $id
     * @return bool
     */
    public function restoreCountry(int $id): bool
    {
        $country = $this->getCountry($id, true); // Include trashed
        if ($country && $country->trashed()) {
            return $country->restore();
        }
        return false;
    }

    /**
     * Restore multiple soft-deleted countries.
     *
     * @param array $ids
     * @return int
     */
    public function bulkRestoreCountries(array $ids): int
    {
        return Country::onlyTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a country by ID.
     *
     * @param int $id
     * @return bool
     */
    public function permanentlyDelete(int $id): bool
    {
        $country = $this->getCountry($id, true); // Include trashed
        if ($country) {
            return $country->forceDelete();
        }
        return false;
    }

    /**
     * Permanently delete multiple countries by IDs.
     *
     * @param array $ids
     * @return int
     */
    public function bulkPermanentDelete(array $ids): int
    {
        return Country::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }
}
