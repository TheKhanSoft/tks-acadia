<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\City; // Import City model
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ExportImportService;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Gender; // Import Gender enum

class StudentService
{
    protected ExportImportService $exportImportService;

    public function __construct(ExportImportService $exportImportService)
    {
        $this->exportImportService = $exportImportService;
    }

    /**
     * Get paginated and filtered students.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedStudents(array $params): LengthAwarePaginator
    {
        $query = Student::query();

        // Include studentStatus and city relations by default
        $defaultWith = ['studentStatus', 'city'];
        $withRelations = Arr::get($params, 'with', $defaultWith);
        $query->with($withRelations);

        $query->when(Arr::get($params, 'with_trashed'), fn ($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn ($q) => $q->onlyTrashed());

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);

        $perPage = Arr::get($params, 'per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the Student query.
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
                $q->where('student_id', 'like', "%{$searchTerm}%")
                  ->orWhere('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_alternative', 'like', "%{$searchTerm}%") // Added phone_alternative search
                  ->orWhere('nic_no', 'like', "%{$searchTerm}%")
                  ->orWhere('postal_address', 'like', "%{$searchTerm}%") // Added address search
                  ->orWhere('permanent_address', 'like', "%{$searchTerm}%") // Added address search
                  ->orWhere('bio', 'like', "%{$searchTerm}%") // Added bio search
                  ->orWhereHas('studentStatus', fn (Builder $qs) => $qs->where('name', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('city', fn (Builder $qc) => $qc->where('name', 'like', "%{$searchTerm}%")); // Added city search
            });
        }

        $query->when(Arr::get($params, 'student_status_id'), fn (Builder $q, $id) => $q->where('student_status_id', $id));
        $query->when(Arr::get($params, 'city_id'), fn (Builder $q, $id) => $q->where('city_id', $id)); // Added city_id filter
        $query->when(Arr::get($params, 'gender'), fn (Builder $q, $gender) => $q->where('gender', $gender));

        $query->when(Arr::get($params, 'dob_start'), fn (Builder $q, $date) => $q->whereDate('date_of_birth', '>=', $date));
        $query->when(Arr::get($params, 'dob_end'), fn (Builder $q, $date) => $q->whereDate('date_of_birth', '<=', $date));

        // Removed enrollment_date and graduation_date filters as they are in student_program_enrollments

        return $query;
    }

    /**
     * Apply sorting to the Student query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'first_name');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = [
            'student_id', 'first_name', 'last_name', 'email', 'phone', 'phone_alternative', 'gender', 'nic_no',
            'date_of_birth', 'created_at', 'updated_at',
            'student_status_name', 'city_name' // Added city_name sorting
        ];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'first_name';
            $sortDirection = 'asc';
        }

        if ($sortField === 'student_status_name') {
            $query->leftJoin('student_statuses', 'students.student_status_id', '=', 'student_statuses.id')
                  ->orderBy('student_statuses.name', $sortDirection)
                  ->select('students.*');
        } elseif ($sortField === 'city_name') { // Added city_name sorting logic
            $query->leftJoin('cities', 'students.city_id', '=', 'cities.id')
                  ->orderBy('cities.name', $sortDirection)
                  ->select('students.*');
        } else {
            $query->orderBy('students.' . $sortField, $sortDirection);
        }

        if (in_array($sortField, ['first_name', 'last_name', 'student_status_name', 'city_name'])) { // Updated for city_name
            $query->orderBy('students.first_name', 'asc')->orderBy('students.last_name', 'asc');
        }

        return $query;
    }

    /**
     * Get a single student by its ID.
     */
    public function getStudent(int $id, bool $withTrashed = false, array $with = []): ?Student
    {
        $query = Student::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple students by their IDs.
     */
    public function getStudentsByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = Student::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new student using validated data.
     */
    public function createStudent(array $validatedData): Student
    {
        if (isset($validatedData['student_id'])) {
            $validatedData['student_id'] = strtoupper($validatedData['student_id']);
        }
        return Student::create($validatedData);
    }

    /**
     * Update an existing student using validated data.
     */
    public function updateStudent(Student $student, array $validatedData): bool
    {
        if (isset($validatedData['student_id'])) {
            $validatedData['student_id'] = strtoupper($validatedData['student_id']);
        }
        return $student->update($validatedData);
    }

    /**
     * Soft delete a student.
     */
    public function deleteStudent(Student $student): ?bool
    {
        return $student->delete();
    }

    /**
     * Soft delete a student by ID.
     */
    public function deleteStudentById(int $studentId): bool
    {
        return Student::destroy($studentId) > 0;
    }

    /**
     * Bulk soft delete students by their IDs.
     */
    public function bulkDeleteStudentByIds(array $studentIds): int
    {
        return Student::destroy($studentIds);
    }

    /**
     * Restore a soft-deleted student by its ID.
     */
    public function restoreStudent(int $id): bool
    {
        $student = Student::withTrashed()->find($id);
        return $student && $student->restore();
    }

    /**
     * Bulk restore soft-deleted students by their IDs.
     */
    public function bulkRestoreStudents(array $studentIdsToRestore): int
    {
        return Student::withTrashed()->whereIn('id', $studentIdsToRestore)->restore();
    }

    /**
     * Permanently delete a student by its ID.
     */
    public function permanentlyDelete(int $id): bool
    {
        $student = Student::withTrashed()->find($id);
        return $student && $student->forceDelete();
    }

    /**
     * Bulk permanently delete students by their IDs.
     */
    public function bulkPermanentDelete(array $studentIdsToPermanentDelete): int
    {
        return Student::withTrashed()->whereIn('id', $studentIdsToPermanentDelete)->forceDelete();
    }

    /**
     * Get a list of students suitable for dropdowns.
     */
    public function getStudentsForDropdown(array $filters = [], array $with = []): Collection
    {
        $query = Student::query();

        // Removed program_id and campus_id filters
        if (Arr::get($filters, 'student_status_id')) {
            $query->where('student_status_id', $filters['student_status_id']);
        }
         if (Arr::get($filters, 'city_id')) { // Added city_id filter
            $query->where('city_id', $filters['city_id']);
        }


        if (!empty($with)) $query->with($with);

        $query->orderBy('first_name')->orderBy('last_name');
        return $query->select('id', 'first_name', 'last_name', 'student_id')->get();
    }

    /**
     * Check if a student with the given Student ID already exists.
     */
    public function studentIdExists(string $studentId, ?int $exceptId = null): bool
    {
        return Student::where('student_id', strtoupper($studentId))
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Check if a student with the given email already exists.
     */
    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        return Student::where('email', $email)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Check if a student with the given NIC number already exists.
     */
    public function nicExists(string $nic, ?int $exceptId = null): bool
    {
        return Student::where('nic_no', $nic)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->whereNull('deleted_at') // Check only among active/non-soft-deleted
                       ->exists();
    }

    /**
     * Find a student by their NIC number.
     */
    public function findStudentByNic(string $nic, bool $withTrashed = false, array $with = []): ?Student
    {
        $query = Student::where('nic_no', $nic);
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->first();
    }

    /**
     * Get a query builder instance with filters and sorting applied, suitable for export.
     */
    public function getFilteredQueryForExport(array $filters): Builder
    {
        $query = Student::query();

        if (!empty($filters['with']) && is_array($filters['with'])) {
            $query->with($filters['with']);
        }

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query;
    }

    /**
     * Export student data based on filters and format.
     */
    public function exportStudents(
        string $format,
        array $filters = [],
        array $selectedColumns = [],
        ?string $title = 'Student Report',
        ?string $subtitle = null
    ): Response {
        if (empty($selectedColumns)) {
            throw new \InvalidArgumentException("No columns selected for export.");
        }

        $headings = Arr::pluck($selectedColumns, 'label');
        $mapKeys = Arr::pluck($selectedColumns, 'key');

        $requiredRelations = [];
        foreach ($mapKeys as $key) {
            if (str_contains($key, '.')) {
                $relationName = explode('.', $key)[0];
                $requiredRelations[] = $relationName;
            }
        }
        $filters['with'] = array_unique(array_merge($filters['with'] ?? [], $requiredRelations));

        $students = $this->getFilteredQueryForExport($filters)->get();
        $filename = 'students-' . date('YmdHis') . '.' . $format;

        switch (strtolower($format)) {
            case 'pdf':
                return $this->exportImportService->exportGenericPdf(
                    $students,
                    $headings,
                    $mapKeys,
                    $filename,
                    $title,
                    $subtitle,
                    'landscape'
                );
            case 'xlsx':
                return $this->exportImportService->exportExcel(
                    $students,
                    $headings,
                    $mapKeys,
                    $filename,
                    $title,
                    $subtitle
                );
            case 'csv':
                return $this->exportImportService->exportCsv(
                    $students,
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
