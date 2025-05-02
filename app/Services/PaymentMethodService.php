<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Http\Requests\PaymentMethodRequest; // Assuming you have this request
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PaymentMethodService
{
    /**
     * Get paginated and filtered payment methods.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedPaymentMethods(array $params): LengthAwarePaginator
    {
        $query = PaymentMethod::query();

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
     * Apply filters to the PaymentMethod query.
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

        // Add other filters specific to PaymentMethod if needed in the future

        return $query;
    }

    /**
     * Apply sorting to the PaymentMethod query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = $params['sort_by'] ?? 'name'; // Default sort field
        $sortDirection = isset($params['sort_dir']) && strtolower($params['sort_dir']) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields for PaymentMethod
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
     * Get a single payment method by its ID.
     *
     * @param int $id The ID of the payment method.
     * @param bool $withTrashed Include soft-deleted records.
     * @return PaymentMethod|null
     */
    public function getPaymentMethod(int $id, bool $withTrashed = false): ?PaymentMethod
    {
        $query = PaymentMethod::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

     /**
     * Get multiple payment methods by their IDs.
     *
     * @param array $ids Array of payment method IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @return Collection<int, PaymentMethod>
     */
    public function getPaymentMethodsByIds(array $ids, bool $withTrashed = false): Collection
    {
        $query = PaymentMethod::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new payment method using validated data.
     *
     * @param array $validatedData Validated data from PaymentMethodRequest.
     * @return PaymentMethod
     */
    public function createPaymentMethod(array $validatedData): PaymentMethod
    {
        return PaymentMethod::create($validatedData);
    }

    /**
     * Update an existing payment method using validated data.
     *
     * @param PaymentMethod $paymentMethod The payment method model instance to update.
     * @param array $validatedData Validated data from PaymentMethodRequest.
     * @return bool True on success, false otherwise.
     */
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $validatedData): bool
    {
        return $paymentMethod->update($validatedData);
    }

    /**
     * Soft delete a payment method.
     *
     * @param PaymentMethod $paymentMethod The payment method model instance to delete.
     * @return bool|null True on success, false on failure, null if model not found.
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod): ?bool
    {
        return $paymentMethod->delete(); // Uses SoftDeletes trait
    }

    /**
     * Soft delete multiple payment methods by their IDs.
     *
     * @param array $ids Array of payment method IDs to delete.
     * @return int The number of records deleted.
     */
    public function bulkDeletePaymentMethodsByIds(array $ids): int
    {
        return PaymentMethod::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted payment method by its ID.
     *
     * @param int $id The ID of the soft-deleted payment method.
     * @return bool True on success, false otherwise.
     */
    public function restorePaymentMethod(int $id): bool
    {
        $paymentMethod = PaymentMethod::withTrashed()->find($id);
        return $paymentMethod ? $paymentMethod->restore() : false;
    }

     /**
     * Restore multiple soft-deleted payment methods by their IDs.
     *
     * @param array $ids Array of payment method IDs to restore.
     * @return int The number of records restored.
     */
    public function bulkRestorePaymentMethodsByIds(array $ids): int
    {
        return PaymentMethod::withTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a payment method by its ID (use with caution).
     *
     * @param int $id The ID of the payment method to delete permanently.
     * @return bool True on success, false otherwise.
     */
    public function permanentlyDeletePaymentMethod(int $id): bool
    {
        $paymentMethod = PaymentMethod::withTrashed()->find($id);
        return $paymentMethod ? $paymentMethod->forceDelete() : false;
    }

    /**
     * Permanently delete multiple payment methods by their IDs.
     *
     * @param array $ids Array of payment method IDs to permanently delete.
     * @return int The number of records permanently deleted.
     */
    public function bulkPermanentDeletePaymentMethodsByIds(array $ids): int
    {
        return PaymentMethod::withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * Get a list of payment methods suitable for dropdowns (ID, name).
     *
     * @return Collection<int, object{id: int, name: string}>
     */
    public function getPaymentMethodsForDropdown(): Collection
    {
        return PaymentMethod::orderBy('name')->select('id', 'name')->get();
    }

    /**
     * Check if a payment method with the given name already exists.
     *
     * @param string $name The name to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool True if the name exists, false otherwise.
     */
    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $query = PaymentMethod::where('name', $name);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}
