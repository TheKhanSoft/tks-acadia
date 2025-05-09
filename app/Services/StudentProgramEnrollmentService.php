<?php

namespace App\Services;

use App\Models\StudentProgramEnrollment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StudentProgramEnrollmentService
{
    /**
     * Get paginated and filtered student program enrollments.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedStudentProgramEnrollments(array $params): LengthAwarePaginator
    {
        $query = StudentProgramEnrollment::query();

        $defaultWith = ['student', 'departmentProgram', 'session', 'enrollmentStatus'];
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
     * Apply filters to the StudentProgramEnrollment query.
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
                $q->orWhere('remarks', 'like', "%{$searchTerm}%")
                  ->orWhereHas('student', fn (Builder $qs) => $qs->where('first_name', 'like', "%{$searchTerm}%")->orWhere('last_name', 'like', "%{$searchTerm}%")->orWhere('student_id', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('departmentProgram', fn (Builder $qdp) => $qdp->where('name', 'like', "%{$searchTerm}%")) // Assuming DepartmentProgram has a 'name'
                  ->orWhereHas('session', fn (Builder $qs) => $qs->where('name', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('enrollmentStatus', fn (Builder $qes) => $qes->where('name', 'like', "%{$searchTerm}%"));
            });
        }

        $query->when(Arr::get($params, 'student_id'), fn (Builder $q, $id) => $q->where('student_id', $id));
        $query->when(Arr::get($params, 'department_program_id'), fn (Builder $q, $id) => $q->where('department_program_id', $id));
        $query->when(Arr::get($params, 'session_id'), fn (Builder $q, $id) => $q->where('session_id', $id));
        $query->when(Arr::get($params, 'enrollment_status_id'), fn (Builder $q, $id) => $q->where('enrollment_status_id', $id));

        $query->when(Arr::get($params, 'enrollment_date_start'), fn (Builder $q, $date) => $q->whereDate('enrollment_date', '>=', $date));
        $query->when(Arr::get($params, 'enrollment_date_end'), fn (Builder $q, $date) => $q->whereDate('enrollment_date', '<=', $date));
        $query->when(Arr::get($params, 'expected_completion_date_start'), fn (Builder $q, $date) => $q->whereDate('expected_completion_date', '>=', $date));
        $query->when(Arr::get($params, 'expected_completion_date_end'), fn (Builder $q, $date) => $q->whereDate('expected_completion_date', '<=', $date));
        $query->when(Arr::get($params, 'actual_completion_date_start'), fn (Builder $q, $date) => $q->whereDate('actual_completion_date', '>=', $date));
        $query->when(Arr::get($params, 'actual_completion_date_end'), fn (Builder $q, $date) => $q->whereDate('actual_completion_date', '<=', $date));

        $query->when(Arr::get($params, 'grades_min'), fn (Builder $q, $grades) => $q->where('grades', '>=', $grades));
        $query->when(Arr::get($params, 'grades_max'), fn (Builder $q, $grades) => $q->where('grades', '<=', $grades));


        return $query;
    }

    /**
     * Apply sorting to the StudentProgramEnrollment query.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param array $params Sorting parameters.
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'enrollment_date');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = [
            'enrollment_date', 'expected_completion_date', 'actual_completion_date',
            'grades', 'created_at', 'updated_at',
            'student_name', 'department_program_name', 'session_name', 'enrollment_status_name'
        ];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'enrollment_date';
            $sortDirection = 'desc';
        }

        if ($sortField === 'student_name') {
             $query->leftJoin('students', 'student_program_enrollments.student_id', '=', 'students.id')
                  ->orderBy('students.first_name', $sortDirection)
                  ->orderBy('students.last_name', $sortDirection)
                  ->select('student_program_enrollments.*');
        } elseif ($sortField === 'department_program_name') {
             $query->leftJoin('department_program', 'student_program_enrollments.department_program_id', '=', 'department_program.id')
                  ->orderBy('department_program.name', $sortDirection) // Assuming DepartmentProgram has a 'name'
                  ->select('student_program_enrollments.*');
        } elseif ($sortField === 'session_name') {
             $query->leftJoin('sessions', 'student_program_enrollments.session_id', '=', 'sessions.id')
                  ->orderBy('sessions.name', $sortDirection)
                  ->select('student_program_enrollments.*');
        } elseif ($sortField === 'enrollment_status_name') {
             $query->leftJoin('enrollment_statuses', 'student_program_enrollments.enrollment_status_id', '=', 'enrollment_statuses.id')
                  ->orderBy('enrollment_statuses.name', $sortDirection)
                  ->select('student_program_enrollments.*');
        }
        else {
            $query->orderBy('student_program_enrollments.' . $sortField, $sortDirection);
        }

        return $query;
    }

    /**
     * Get a single student program enrollment by its ID.
     */
    public function getStudentProgramEnrollment(int $id, bool $withTrashed = false, array $with = []): ?StudentProgramEnrollment
    {
        $query = StudentProgramEnrollment::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->find($id);
    }

    /**
     * Get multiple student program enrollments by their IDs.
     */
    public function getStudentProgramEnrollmentsByIds(array $ids, bool $withTrashed = false, array $with = []): Collection
    {
        $query = StudentProgramEnrollment::query();
        if ($withTrashed) $query->withTrashed();
        if (!empty($with)) $query->with($with);
        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Create a new student program enrollment using validated data.
     */
    public function createStudentProgramEnrollment(array $validatedData): StudentProgramEnrollment
    {
        return StudentProgramEnrollment::create($validatedData);
    }

    /**
     * Update an existing student program enrollment using validated data.
     */
    public function updateStudentProgramEnrollment(StudentProgramEnrollment $studentProgramEnrollment, array $validatedData): bool
    {
        return $studentProgramEnrollment->update($validatedData);
    }

    /**
     * Soft delete a student program enrollment.
     */
    public function deleteStudentProgramEnrollment(StudentProgramEnrollment $studentProgramEnrollment): ?bool
    {
        return $studentProgramEnrollment->delete();
    }

    /**
     * Soft delete a student program enrollment by ID.
     */
    public function deleteStudentProgramEnrollmentById(int $id): bool
    {
        $enrollment = StudentProgramEnrollment::find($id);
        return $enrollment ? $enrollment->delete() : false;
    }

    /**
     * Bulk soft delete student program enrollments by their IDs.
     */
    public function bulkDeleteStudentProgramEnrollments(array $ids): int
    {
        return StudentProgramEnrollment::whereIn('id', $ids)->delete();
    }

    /**
     * Restore a soft-deleted student program enrollment by its ID.
     */
    public function restoreStudentProgramEnrollment(int $id): bool
    {
        $enrollment = StudentProgramEnrollment::withTrashed()->find($id);
        return $enrollment ? $enrollment->restore() : false;
    }

    /**
     * Bulk restore soft-deleted student program enrollments by their IDs.
     */
    public function bulkRestoreStudentProgramEnrollments(array $ids): int
    {
        return StudentProgramEnrollment::withTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Permanently delete a student program enrollment by its ID.
     */
    public function permanentlyDelete(int $id): bool
    {
        $enrollment = StudentProgramEnrollment::withTrashed()->find($id);
        return $enrollment ? $enrollment->forceDelete() : false;
    }

    /**
     * Bulk permanently delete student program enrollments by their IDs.
     */
    public function bulkPermanentDeleteStudentProgramEnrollments(array $ids): int
    {
        return StudentProgramEnrollment::withTrashed()->whereIn('id', $ids)->forceDelete();
    }

    /**
     * Get a list of student program enrollments suitable for dropdowns (e.g., for linking).
     */
    public function getStudentProgramEnrollmentsForDropdown(array $filters = [], array $with = []): Collection
    {
        $query = StudentProgramEnrollment::query();

        if (!empty($with)) $query->with($with);

        $query->when(Arr::get($filters, 'student_id'), fn (Builder $q, $id) => $q->where('student_id', $id));
        $query->when(Arr::get($filters, 'department_program_id'), fn (Builder $q, $id) => $q->where('department_program_id', $id));
        $query->when(Arr::get($filters, 'session_id'), fn (Builder $q, $id) => $q->where('session_id', $id));
        $query->when(Arr::get($filters, 'enrollment_status_id'), fn (Builder $q, $id) => $q->where('enrollment_status_id', $id));


        // Select relevant fields for dropdown, potentially including related names
        return $query->select('id', 'student_id', 'department_program_id', 'session_id', 'enrollment_date')->get();
    }
}
