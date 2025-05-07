<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ExportImportService; // Import the new service
use Symfony\Component\HttpFoundation\Response; // For return type hinting

class EmployeeService
{
    protected ExportImportService $exportImportService;

    public function __construct(ExportImportService $exportImportService)
    {
        $this->exportImportService = $exportImportService;
    }

    /**
     * Get paginated and filtered employees.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedEmployees(array $params): LengthAwarePaginator
    {
        $query = Employee::query();

        // Default eager loads
        $defaultWith = ['employeeType', 'employeeWorkStatus', 'offices'];
        // Allow overriding/adding eager loads via params if needed
        $withRelations = Arr::get($params, 'with', $defaultWith);
        $query->with($withRelations);

        // Include soft-deleted records if requested
        $query->when(Arr::get($params, 'with_trashed'), fn ($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn ($q) => $q->onlyTrashed());

        // Apply search filters
        $query = $this->applyFilters($query, $params);

        // Apply sorting
        $query = $this->applySorting($query, $params); // applySorting now handles default

        // Determine items per page
        $perPage = Arr::get($params, 'per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the Employee query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Filtering parameters.
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        // General Search Term Filter
        $searchTerm = Arr::get($params, 'search_term');
        $searchEnabled = Arr::get($params, 'search', false);

        // Rewrite the search 'when' block completely
        if ($searchEnabled && $searchTerm) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('employee_id', 'like', "%{$searchTerm}%")
                  ->orWhere('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%")
                         ->orWhere('email', 'like', "%{$searchTerm}%")
                         ->orWhere('phone', 'like', "%{$searchTerm}%")
                         ->orWhere('nic_no', 'like', "%{$searchTerm}%")
                         ->orWhere('qualification', 'like', "%{$searchTerm}%")
                         ->orWhere('specialization', 'like', "%{$searchTerm}%")
                         // Search related names
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('nic_no', 'like', "%{$searchTerm}%")
                  ->orWhere('qualification', 'like', "%{$searchTerm}%")
                  ->orWhere('specialization', 'like', "%{$searchTerm}%")
                  // Search related names using standard closures for consistency
                  ->orWhereHas('employeeType', function (Builder $qt) use ($searchTerm) {
                      $qt->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('employeeWorkStatus', function (Builder $qs) use ($searchTerm) {
                      $qs->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('offices', function (Builder $qo) use ($searchTerm) {
                      $qo->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('jobNature', function (Builder $qjn) use ($searchTerm) { // Added search by Job Nature name
                      $qjn->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        // End of rewritten block

        // Filter by Employee Type ID
        $query->when(Arr::get($params, 'employee_type_id'), fn (Builder $q, $id) => $q->where('employee_type_id', $id)); // Keep arrow function here

        // Filter by Employee Work Status ID
        $query->when(Arr::get($params, 'employee_work_status_id'), fn ($q, $id) => $q->where('employee_work_status_id', $id));

        // Filter by Job Nature ID (Added)
        $query->when(Arr::get($params, 'job_nature_id'), fn ($q, $id) => $q->where('job_nature_id', $id));

        // Filter by Gender
        $gender = Arr::get($params, 'gender');
        $query->when($gender && in_array($gender, ['Male', 'Female', 'Other']), function (Builder $q) use ($gender) { // Reverted to standard closure
            $q->where('gender', $gender);
        });

        // Removed Status Filter Logic

        // Filter by Appointment Date Range
        $query->when(Arr::get($params, 'appointment_date_start'), fn (Builder $q, $date) => $q->whereDate('appointment_date', '>=', $date));
        $query->when(Arr::get($params, 'appointment_date_end'), fn (Builder $q, $date) => $q->whereDate('appointment_date', '<=', $date));

        // Filter by Termination Date Range
        $query->when(Arr::get($params, 'termination_date_start'), fn (Builder $q, $date) => $q->whereDate('termination_date', '>=', $date));
        $query->when(Arr::get($params, 'termination_date_end'), fn (Builder $q, $date) => $q->whereDate('termination_date', '<=', $date));

        // Filter by Date of Birth Range
        $query->when(Arr::get($params, 'dob_start'), fn (Builder $q, $date) => $q->whereDate('date_of_birth', '>=', $date));
        $query->when(Arr::get($params, 'dob_end'), fn (Builder $q, $date) => $q->whereDate('date_of_birth', '<=', $date));

        // Filter by Office (employee belongs to this office)
        $query->when(Arr::get($params, 'office_id'), fn (Builder $q, $id) => $q->whereHas('offices', fn(Builder $qo) => $qo->where('offices.id', $id)));

        // Filter by Not In Office (employee does NOT belong to this office)
        $query->when(Arr::get($params, 'not_in_office_id'), fn (Builder $q, $id) => $q->whereDoesntHave('offices', fn(Builder $qo) => $qo->where('offices.id', $id)));

        // Filter by Primary Office ID (Added)
        $query->when(Arr::get($params, 'primary_office_id'), fn (Builder $q, $id) => $q->whereHas('offices', fn(Builder $qo) => $qo->where('offices.id', $id)->wherePivot('is_primary_office', true)));

        // Filter by employees without any office assignment (Added)
        $query->when(Arr::get($params, 'no_office_assignment'), fn (Builder $q) => $q->doesntHave('offices'));

        return $query;
    }

    /**
     * Apply sorting to the Employee query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'first_name'); // Default sort field
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc'; // Default sort direction

        // Define allowed sortable fields
        $allowedSortFields = [
            'employee_id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'nic_no',
            'date_of_birth', 'appointment_date', 'termination_date', 'qualification', 'specialization',
            'created_at', 'updated_at',
            // Related fields
            'employee_type_name', 'employee_work_status_name', 'primary_office_name', 'job_nature_name' // Added job_nature_name
        ];

        // Validate sort field
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'first_name'; // Fallback to default if invalid field requested
            $sortDirection = 'asc';
        }

        // Apply sorting based on field type
        if ($sortField === 'employee_type_name') {
            $query->leftJoin('employee_types', 'employees.employee_type_id', '=', 'employee_types.id')
                  ->orderBy('employee_types.name', $sortDirection)
                  ->select('employees.*'); // Ensure we select employee columns
        } elseif ($sortField === 'employee_work_status_name') {
            $query->leftJoin('employee_work_statuses', 'employees.employee_work_status_id', '=', 'employee_work_statuses.id')
                  ->orderBy('employee_work_statuses.name', $sortDirection)
                  ->select('employees.*'); // Ensure we select employee columns
        } elseif ($sortField === 'primary_office_name') { // Added sorting by primary office name
            $query->leftJoin('employee_office as pivot_primary', function ($join) {
                      $join->on('employees.id', '=', 'pivot_primary.employee_id')
                           ->where('pivot_primary.is_primary_office', '=', true);
                  })
                  ->leftJoin('offices as primary_office', 'pivot_primary.office_id', '=', 'primary_office.id')
                  ->orderBy('primary_office.name', $sortDirection)
                  ->select('employees.*'); // Ensure we select employee columns
        } elseif ($sortField === 'job_nature_name') { // Added sorting by job nature name
            $query->leftJoin('job_natures', 'employees.job_nature_id', '=', 'job_natures.id')
                  ->orderBy('job_natures.name', $sortDirection)
                  ->select('employees.*'); // Ensure we select employee columns
        } else {
            // Handle direct model fields (ensure table name for clarity if joins are possible)
            $query->orderBy('employees.' . $sortField, $sortDirection);
        }

        // Add secondary sort for consistency if sorting by name fields
        if (in_array($sortField, ['first_name', 'last_name', 'employee_type_name', 'employee_work_status_name', 'primary_office_name', 'job_nature_name'])) { // Added job_nature_name
            $query->orderBy('employees.first_name', 'asc')->orderBy('employees.last_name', 'asc');
        }

        return $query;
    }

    /**
     * Get a single employee by its ID.
     *
     * @param int $id The ID of the employee.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load.
     * @return Employee|null
     */
    public function getEmployee(int $id, bool $withTrashed = false, array $with = []): ?Employee
    {
        $query = Employee::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple employees by their IDs.
     *
     * @param array $ids Array of employee IDs.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load. (Added)
     * @return Collection<int, Employee>
     */
    public function getEmployeesByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = Employee::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with); // Added eager loading
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new employee using validated data.
     *
     * @param array $validatedData Validated data from EmployeeRequest.
     * @return Employee
     */
    public function createEmployee(array $validatedData): Employee
    {
        // Consider moving data manipulation (like strtoupper) to a mutator in the Employee model if appropriate
        // Example: $validatedData['employee_id'] = strtoupper($validatedData['employee_id'] ?? '');
        return Employee::create($validatedData);
    }

    /**
     * Update an existing employee using validated data.
     *
     * @param Employee $employee The employee model instance to update.
     * @param array $validatedData Validated data from EmployeeRequest.
     * @return bool True on success, false otherwise.
     */
    public function updateEmployee(Employee $employee, array $validatedData): bool
    {
        // Consider moving data manipulation to a mutator in the Employee model
        return $employee->update($validatedData);
    }

    /**
     * Soft delete an employee.
     *
     * @param Employee $employee The employee model instance to delete.
     * @return bool|null
     */
    public function deleteEmployee(Employee $employee): ?bool
    {
        return $employee->delete();
    }

    /**
     * Soft delete an employee by ID.
     *
     * @param int $employeeId The employee ID to delete.
     * @return bool True on success, false if not found.
     */
    public function deleteEmployeeById(int $employeeId): bool
    {
        return Employee::destroy($employeeId) > 0; // destroy returns count
    }

    /**
     * Bulk soft delete employees by their IDs.
     *
     * @param array $employeeIds Array of employee IDs to delete.
     * @return int Number of employees deleted.
     */
    public function bulkDeleteEmployeeByIds(array $employeeIds): int
    {
        return Employee::destroy($employeeIds); // destroy handles arrays
    }

    /**
     * Restore a soft-deleted employee by its ID.
     *
     * @param int $id The ID of the soft-deleted employee.
     * @return bool True on success, false if not found or not deleted.
     */
    public function restoreEmployee(int $id): bool
    {
        $employee = Employee::withTrashed()->find($id);
        return $employee && $employee->restore(); // restore returns bool/null
    }

    /**
     * Bulk restore soft-deleted employees by their IDs.
     *
     * @param array $employeeIdsToRestore Array of employee IDs to restore.
     * @return int Number of employees restored.
     */
    public function bulkRestoreEmployees(array $employeeIdsToRestore): int
    {
        return Employee::withTrashed()->whereIn('id', $employeeIdsToRestore)->restore();
    }

    /**
     * Permanently delete an employee by its ID (use with caution).
     *
     * @param int $id The ID of the employee to delete permanently.
     * @return bool True on success, false if not found.
     */
    public function permanentlyDelete(int $id): bool
    {
        $employee = Employee::withTrashed()->find($id);
        // Add safety checks here if needed (e.g., check headOfOffices relationship)
        // if ($employee && $employee->headOfOffices()->exists()) {
        //     throw new \Exception("Cannot permanently delete employee who is head of an office.");
        // }
        return $employee && $employee->forceDelete(); // forceDelete returns bool/null
    }

    /**
     * Bulk permanently delete employees by their IDs (use with extreme caution).
     *
     * @param array $employeeIdsToPermanentDelete Array of employee IDs to permanently delete.
     * @return int Number of employees permanently deleted.
     */
    public function bulkPermanentDelete(array $employeeIdsToPermanentDelete): int
    {
        // Add safety checks if necessary before force deleting.
        // Example: Check if any are heads of offices
        // $heads = Employee::whereIn('id', $employeeIdsToPermanentDelete)->has('headOfOffices')->exists();
        // if ($heads) {
        //     throw new \Exception("Cannot bulk delete employees including heads of offices.");
        // }
        return Employee::withTrashed()->whereIn('id', $employeeIdsToPermanentDelete)->forceDelete();
    }

    /**
     * Get a list of employees suitable for dropdowns (ID, full name, employee_id).
     *
     * @param int|null $employeeTypeId Filter by a specific employee type.
     * @param array $with Relationships to eager load. (Added)
     * @return Collection
     */
    public function getEmployeesForDropdown(?int $employeeTypeId = null, array $with = []): Collection
    {
        $query = Employee::query();

        if ($employeeTypeId) $query->where('employee_type_id', $employeeTypeId);
        if (!empty($with)) $query->with($with); // Added eager loading

        // Order by name for better usability
        $query->orderBy('first_name')->orderBy('last_name');

        // Select necessary fields
        return $query->select('id', 'first_name', 'last_name', 'employee_id')->get();
    }

    /**
     * Check if an employee with the given Employee ID already exists.
     *
     * @param string $employeeId The Employee ID to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool
     */
    public function employeeIdExists(string $employeeId, ?int $exceptId = null): bool
    {
        return Employee::where('employee_id', $employeeId)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Check if an employee with the given email already exists.
     *
     * @param string $email The email to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool
     */
    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        return Employee::where('email', $email)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Check if an employee with the given NIC number already exists.
     *
     * @param string $nic The NIC number to check.
     * @param int|null $exceptId Exclude an ID from the check (useful for updates).
     * @return bool
     */
    public function nicExists(string $nic, ?int $exceptId = null): bool
    {
        return Employee::where('nic_no', $nic)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Find an employee by their NIC number.
     *
     * @param string $nic The NIC number.
     * @param bool $withTrashed Include soft-deleted records.
     * @param array $with Relationships to eager load. (Added)
     * @return Employee|null
     */
    public function findEmployeeByNic(string $nic, bool $withTrashed = false, array $with = []): ?Employee
    {
        $query = Employee::where('nic_no', $nic);
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with); // Added eager loading
        return $query->first();
    }

    /**
     * Get employees belonging to a specific office.
     *
     * @param int $officeId
     * @param array $with Relationships to eager load for the employees. (Added)
     * @return Collection
     */
    public function getEmployeesByOffice(int $officeId, array $with = []): Collection
    {
        $query = Employee::whereHas('offices', fn ($q) => $q->where('offices.id', $officeId));

        if (!empty($with)) $query->with($with); // Added eager loading

        return $query->orderBy('first_name')->orderBy('last_name')->get();
    }

    /**
     * Get employees who are not assigned to any office. (Added)
     *
     * @param array $with Relationships to eager load.
     * @return Collection
     */
    public function getEmployeesWithoutOfficeAssignment(array $with = []): Collection
    {
        $query = Employee::doesntHave('offices');
        if (!empty($with)) $query->with($with);

        return $query->orderBy('first_name')->orderBy('last_name')->get();
    }

    // --- Office Assignment Methods ---

    /**
     * Assign an employee to an office with specific pivot data.
     * If setting as primary, ensures other assignments are not primary.
     *
     * @param Employee $employee The employee instance.
     * @param int $officeId The ID of the office to assign.
     * @param array $pivotData Associative array of pivot data (role, assignment_date, end_date, is_primary_office, is_active).
     * @return void
     */
    public function assignOffice(Employee $employee, int $officeId, array $pivotData): void
    {
        $defaults = [
            'role' => null,
            'assignment_date' => now(),
            'end_date' => null,
            'is_primary_office' => false,
            'is_active' => true, // Pivot active status
        ];
        $pivotData = array_merge($defaults, Arr::only($pivotData, array_keys($defaults))); // Ensure only valid keys

        $isPrimary = filter_var($pivotData['is_primary_office'], FILTER_VALIDATE_BOOLEAN);
        $pivotData['is_primary_office'] = $isPrimary;

        DB::transaction(function () use ($employee, $officeId, $pivotData, $isPrimary) {
            if ($isPrimary) {
                // Set all existing assignments to non-primary first
                $employee->offices()->updateExistingPivot([], ['is_primary_office' => false]);
            }
            // Sync without detaching to add/update the assignment
            $employee->offices()->syncWithoutDetaching([$officeId => $pivotData]);
        });
    }

    /**
     * Update the pivot data for an existing employee-office assignment.
     *
     * @param Employee $employee The employee instance.
     * @param int $officeId The ID of the office assignment to update.
     * @param array $pivotData Associative array of pivot data to update.
     * @return int Number of affected rows (usually 1 if successful).
     */
    public function updateOfficeAssignment(Employee $employee, int $officeId, array $pivotData): int
    {
        // Ensure is_primary_office is boolean if provided
        if (isset($pivotData['is_primary_office'])) {
            $pivotData['is_primary_office'] = filter_var($pivotData['is_primary_office'], FILTER_VALIDATE_BOOLEAN);
        }

        // If setting this as primary, unset others first within a transaction
        if (Arr::get($pivotData, 'is_primary_office') === true) {
            return DB::transaction(function () use ($employee, $officeId, $pivotData) {
                $employee->offices()->where('office_id', '!=', $officeId)->updateExistingPivot([], ['is_primary_office' => false]);
                return $employee->offices()->updateExistingPivot($officeId, $pivotData);
            });
        } else {
            // If setting to non-primary, just update this one
            return $employee->offices()->updateExistingPivot($officeId, $pivotData);
        }
    }

    /**
     * Remove an employee's assignment from a specific office.
     *
     * @param Employee $employee The employee instance.
     * @param int $officeId The ID of the office to detach from.
     * @return int Number of detached records (usually 1).
     */
    public function removeOfficeAssignment(Employee $employee, int $officeId): int
    {
        // Optional: Add logic here to handle primary office reassignment if needed
        return $employee->offices()->detach($officeId);
    }

     /**
     * Set a specific office assignment as the primary one for the employee.
     * Ensures only one office assignment is marked as primary.
     *
     * @param Employee $employee The employee instance.
     * @param int $officeId The ID of the office to set as primary.
     * @return bool True on success, false if the assignment doesn't exist.
     */
    public function setPrimaryOffice(Employee $employee, int $officeId): bool
    {
        // Check if the employee is actually assigned to this office
        if (!$employee->offices()->where('office_id', $officeId)->exists()) {
            return false;
        }

        DB::transaction(function () use ($employee, $officeId) {
            // Set all other assignments to non-primary
            $employee->offices()
                     ->where('office_id', '!=', $officeId)
                     ->updateExistingPivot([], ['is_primary_office' => false]);

            // Set the specified assignment to primary
            $employee->offices()->updateExistingPivot($officeId, ['is_primary_office' => true]);
        });

        // Refresh the relationship if needed immediately after
        // $employee->load('offices');

        return true;
    }

    /**
     * Get the primary office for a given employee. (Helper Method - Added)
     * Assumes 'offices' relationship might already be loaded.
     *
     * @param Employee $employee
     * @return Office|null
     */
    public function getEmployeePrimaryOffice(Employee $employee): ?Office
    {
        // Check if the relationship is loaded and find the primary one
        if ($employee->relationLoaded('offices')) {
            return $employee->offices->firstWhere('pivot.is_primary_office', true);
        }
        // Otherwise, query it (less efficient if called repeatedly without pre-loading)
        return $employee->offices()->wherePivot('is_primary_office', true)->first();
    }

    /**
     * Get the pivot data for a specific employee-office assignment. (Helper Method - Added)
     *
     * @param Employee $employee
     * @param int $officeId
     * @return object|null Pivot data object or null if not assigned.
     */
    public function getEmployeeOfficeAssignment(Employee $employee, int $officeId): ?object
    {
        $office = null;
        // Check if the relationship is loaded
        if ($employee->relationLoaded('offices')) {
            $office = $employee->offices->find($officeId);
        } else {
            // Otherwise, query it
            $office = $employee->offices()->find($officeId);
        }

        return $office ? $office->pivot : null;
    }

    // --- Query Building for Export ---

    /**
     * Get a query builder instance with filters and sorting applied, suitable for export.
     * This method acts as a public interface to the protected filtering/sorting logic.
     *
     * @param array $filters Filters to apply (includes sorting, search, specific criteria).
     * @return Builder
     */
    public function getFilteredQueryForExport(array $filters): Builder
    {
        $query = Employee::query();

        // Apply necessary relations if specified in filters (e.g., for mapping in export)
        if (!empty($filters['with']) && is_array($filters['with'])) {
            $query->with($filters['with']);
        }

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $this->applySorting($query, $filters);

        return $query;
    }

    /**
     * Export employee data based on filters and format.
     *
     * @param string $format The desired export format ('pdf', 'xlsx', 'csv').
     * @param array $filters Filters to apply to the query (including sorting, search).
     * @param array $selectedColumns Array of column definitions [{key: '...', label: '...'}, ...] in the desired order.
     * @param string|null $title Optional title for the export document/sheet.
     * @param string|null $subtitle Optional subtitle for the export document/sheet.
     * @return Response Returns a download response for the requested format.
     * @throws \InvalidArgumentException If the format is invalid or no columns selected.
     */
    public function exportEmployees(
        string $format,
        array $filters = [],
        array $selectedColumns = [], // Added parameter for selected columns
        ?string $title = 'Employee Report',
        ?string $subtitle = null
    ): Response {
        if (empty($selectedColumns)) {
            throw new \InvalidArgumentException("No columns selected for export.");
        }

        // Extract headings and map keys from the selected columns
        $headings = Arr::pluck($selectedColumns, 'label');
        $mapKeys = Arr::pluck($selectedColumns, 'key');

        // Determine required relations based on map keys (e.g., 'employeeType.name' requires 'employeeType')
        $requiredRelations = [];
        foreach ($mapKeys as $key) {
            if (str_contains($key, '.')) {
                $relationName = explode('.', $key)[0];
                // Handle potential nested relations if needed in the future, for now just first level
                $requiredRelations[] = $relationName;
            }
        }
        // Ensure primaryOffice relation is loaded correctly if needed
        // The key 'primaryOffice.name' might need adjustment based on how primaryOffice is loaded/accessed
        // Let's assume 'primaryOffice' is the correct relation name if 'primaryOffice.name' is requested.
        if (in_array('primaryOffice.name', $mapKeys) && !in_array('primaryOffice', $requiredRelations)) {
             $requiredRelations[] = 'primaryOffice';
        }

        // Merge required relations with any existing 'with' in filters
        $filters['with'] = array_unique(array_merge($filters['with'] ?? [], $requiredRelations));

        // Get the filtered data as a collection
        // Ensure the query selects the base model columns explicitly if joins are used in sorting/filtering
        // to avoid ambiguity, although getFilteredQueryForExport already does select('employees.*') in sorting.
        $employees = $this->getFilteredQueryForExport($filters)->get();

        // Generate filename
        $filename = 'employees-' . date('YmdHis') . '.' . $format;

        // Call the appropriate export method
        switch (strtolower($format)) {
            case 'pdf':
                // Consider landscape for potentially wide employee data
                // return $this->exportImportService->exportPdf('exports.pdf-layout',(array) $employees, $filename, 'landscape');
              
                return $this->exportImportService->exportGenericPdf(
                    $employees,
                    $headings,
                    $mapKeys,
                    $filename,
                    $title,
                    $subtitle,
                    'landscape' // Set orientation to landscape
                );
            case 'xlsx':
                return $this->exportImportService->exportExcel(
                    $employees,
                    $headings,
                    $mapKeys,
                    $filename,
                    $title,
                    $subtitle
                );
            case 'csv':
                return $this->exportImportService->exportCsv(
                    $employees,
                    $headings,
                    $mapKeys,
                    $filename,
                    $title,
                    $subtitle
                );
            default:
                throw new \InvalidArgumentException("Invalid export format requested: {$format}. Valid formats are pdf, xlsx, csv.");
        }
    }
}
