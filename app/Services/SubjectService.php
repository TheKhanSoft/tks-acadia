<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\SubjectType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Services\ExportImportService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SubjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class SubjectService
{
    protected ExportImportService $exportImportService;

    public function __construct(ExportImportService $exportImportService)
    {
        $this->exportImportService = $exportImportService;
    }

    /**
     * Get paginated and filtered subjects.
     *
     * @param array $params Filtering and pagination parameters.
     * @return LengthAwarePaginator
     */
    public function getPaginatedSubjects(array $params): LengthAwarePaginator
    {
        $query = Subject::query()
            ->with(['subjectType', 'parentDepartment'])
            ->withCount([
                'programs as programs_in_same_department_count' => function (Builder $query) {
                    $query->whereColumn('programs.department_id', 'subjects.parent_department_id');
                },
                'programs as programs_in_other_departments_count' => function (Builder $query) {
                    $query->whereColumn('programs.department_id', '!=', 'subjects.parent_department_id');
                },
            ]);

        $query->when(Arr::get($params, 'with_trashed'), fn($q) => $q->withTrashed());
        $query->when(Arr::get($params, 'only_trashed'), fn($q) => $q->onlyTrashed());

        $query = $this->applyFilters($query, $params);
        $query = $this->applySorting($query, $params);
        
        $perPage = Arr::get($params, 'per_page', 15);
        // if ($perPage !== 'all') {
            $query = $query->paginate($perPage);
        // }
        return $query;
    }

    /**
     * Apply filters to the Subject query.
     *
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        $searchTerm = Arr::get($params, 'search_term');
        $searchEnabled = Arr::get($params, 'search', false);

        if ($searchEnabled && $searchTerm) {
            // Ensure search term is a string and trim it safely
            if (is_string($searchTerm)) {
                $trimmedSearchTerm = trim($searchTerm);
                if ($trimmedSearchTerm !== '') {
                    $query->where(function (Builder $q) use ($trimmedSearchTerm) {
                        $q->where('name', 'like', "%{$trimmedSearchTerm}%")
                          ->orWhere('code', 'like', "%{$trimmedSearchTerm}%")
                          ->orWhere('description', 'like', "%{$trimmedSearchTerm}%")
                          ->orWhereHas('subjectType', function (Builder $qt) use ($trimmedSearchTerm) {
                              $qt->where('name', 'like', "%{$trimmedSearchTerm}%");
                          })
                          ->orWhereHas('parentDepartment', function (Builder $qd) use ($trimmedSearchTerm) {
                              $qd->where('name', 'like', "%{$trimmedSearchTerm}%")
                                 ->orWhere('short_name', 'like', "%{$trimmedSearchTerm}%");
                          });
                    });
                }
            }
        }

        $query->when(Arr::get($params, 'subject_type_id'), fn (Builder $q, $id) => $q->where('subject_type_id', $id));
        $query->when(Arr::get($params, 'parent_department_id'), fn (Builder $q, $id) => $q->whereIn('parent_department_id', $id));

        if (Arr::has($params, 'is_active') && !is_null($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        $query->when(Arr::get($params, 'credit_hours'), function (Builder $q, $credits) {
            if (is_string($credits) && trim($credits) !== '') {
                $q->where('credit_hours', 'like', '%' . trim($credits) . '%');
            }
        });

        return $query;
    }

    /**
     * Apply sorting to the Subject query.
     *
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortField = Arr::get($params, 'sort_by', 'name');
        $sortDirection = strtolower(Arr::get($params, 'sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSortFields = [
            'id', 'name', 'code', 'is_active', 'subject_type.name', 
            'parentDepartment.name', 'created_at', 'updated_at'
        ];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }

        if ($sortField === 'subject_type.name') {
            $query->leftJoin('subject_types', 'subjects.subject_type_id', '=', 'subject_types.id')
                  ->orderBy('subject_types.name', $sortDirection)
                  ->select('subjects.*');
        } else if ($sortField === 'parentDepartment.name') {
            $query->leftJoin('offices', 'subjects.parent_department_id', '=', 'offices.id')
                  ->orderBy('offices.name', $sortDirection)
                  ->select('subjects.*');
        }
        else {
            $query->orderBy('subjects.' . $sortField, $sortDirection);
        }

        return $query->withAggregate('parentDepartment','name');
    }

    /**
     * Get a single subject by its ID.
     */
    public function getSubject(int $id, bool $withTrashed = false): ?Subject
    {
        $query = Subject::query();
        if ($withTrashed) $query->withTrashed();
        return $query->find($id);
    }

    /**
     * Get multiple subjects by their IDs.
     */
    public function getSubjectsByIds(array $ids, bool $withTrashed = false): Collection
    {
        $query = Subject::query();
        if ($withTrashed) $query->withTrashed();
        return $query->whereIn('subjects.id', $ids)->get();
    }

    /**
     * Create a new subject.
     */
    public function createSubject(array $validatedData): Subject
    {
        return Subject::create($validatedData);
    }

    /**
     * Update an existing subject.
     */
    public function updateSubject(Subject $subject, array $validatedData): bool
    {
        return $subject->update($validatedData);
    }

    /**
     * Save a subject (create or update) after validation.
     *
     * @param array $data The data for the subject.
     * @param int|null $subjectId The ID of the subject to update, or null to create.
     * @return Subject
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function saveSubject(array $data, ?int $subjectId = null): Subject
    {
        // Instantiate the form request to get rules and check authorization
        $formRequest = new SubjectRequest();
        $formRequest->subjectId = $subjectId; // Set custom property for rules

        // Manually check authorization
        if (method_exists($formRequest, 'authorize') && !$formRequest->authorize()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        // Get validation rules
        $rules = $formRequest->rules();
        $messages = $formRequest->messages();
        $attributes = $formRequest->attributes();

        // Validate the data
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();


        if (array_key_exists('description', $validatedData) && $validatedData['description'] === '') {
            $validatedData['description'] = null;
        }

        if ($subjectId) {
            $subject = $this->getSubject($subjectId);
            if (!$subject) {
                throw new \Exception('Subject not found for update.');
            }
            $this->updateSubject($subject, $validatedData);
            return $subject->fresh();
        } else {
            return $this->createSubject($validatedData);
        }
    }

    /**
     * Soft delete a subject.
     */
    public function deleteSubject(Subject $subject): ?bool
    {
        return $subject->delete();
    }

    /**
     * Soft delete a subject by ID.
     */
    public function deleteSubjectById(int $subjectId): bool
    {
        $subject = $this->getSubject($subjectId);
        if ($subject) {
            return $this->deleteSubject($subject) !== null;
        }
        return false;
    }

    /**
     * Bulk soft delete subjects by IDs.
     */
    public function bulkDeleteSubjectByIds(array $subjectIds): int
    {
        return Subject::destroy($subjectIds);
    }

    /**
     * Restore a soft-deleted subject.
     */
    public function restoreSubject(int $id): bool
    {
        $subject = Subject::withTrashed()->find($id);
        return $subject && $subject->restore();
    }

    /**
     * Bulk restore soft-deleted subjects.
     */
    public function bulkRestoreSubjects(array $subjectIdsToRestore): int
    {
        return Subject::withTrashed()->whereIn('id', $subjectIdsToRestore)->restore();
    }

    /**
     * Permanently delete a subject.
     */
    public function permanentlyDeleteSubject(int $id): bool
    {
        $subject = Subject::withTrashed()->find($id);
        return $subject && $subject->forceDelete();
    }

    /**
     * Bulk permanently delete subjects.
     */
    public function bulkPermanentDeleteSubjects(array $subjectIdsToPermanentDelete): int
    {
        return Subject::withTrashed()->whereIn('id', $subjectIdsToPermanentDelete)->forceDelete();
    }

    /**
     * Bulk activate subjects by IDs.
     */
    public function bulkActivateSubjects(array $subjectIds): int
    {
        return Subject::whereIn('id', $subjectIds)->update(['is_active' => true]);
    }

    /**
     * Bulk deactivate subjects by IDs.
     */
    public function bulkDeactivateSubjects(array $subjectIds): int
    {
        return Subject::whereIn('id', $subjectIds)->update(['is_active' => false]);
    }

    /**
     * Bulk toggle the status of subjects by IDs.
     */
    public function bulkToggleStatusSubjects(array $subjectIds): int
    {
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        $count = 0;
        foreach ($subjects as $subject) {
            $subject->is_active = !$subject->is_active;
            if ($subject->save()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get subjects for dropdowns.
     */
    public function getSubjectsForDropdown(?int $parentDepartmentId = null, ?int $subjectTypeId = null): Collection
    {
        $query = Subject::query()->where('subjects.is_active', true);
        if ($parentDepartmentId) $query->where('subjects.parent_department_id', $parentDepartmentId);
        if ($subjectTypeId) $query->where('subjects.subject_type_id', $subjectTypeId);

        $query->orderBy('subjects.name')->orderBy('subjects.code');
        
        return $query->select('subjects.id', 'subjects.name', 'subjects.code')->get()->map(function ($subject) {
            $subject->display_name = $subject->code . ' - ' . $subject->name;
            return $subject;
        });
    }

    /**
     * Check if a subject with the given code and name already exists for a department.
     */
    public function subjectExists(string $name, string $subjectCode, int $parentDepartmentId, ?int $exceptId = null): bool
    {
        return Subject::where('name', $name)
                       ->where('code', $subjectCode)
                       ->where('parent_department_id', $parentDepartmentId)
                       ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                       ->exists();
    }

    /**
     * Get a query builder instance for export.
     */
    public function getFilteredQueryForExport(array $filters): Builder
    {
        $query = Subject::query();

        if (!empty($filters['with']) && is_array($filters['with'])) {
            $query->with($filters['with']);
        }

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query;
    }

    /**
     * Export subject data.
     */
    public function exportSubjects(
        string $format,
        array $filters = [],
        array $selectedColumns = [],
        ?string $title = 'Subject Report',
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
                $requiredRelations[] = explode('.', $key)[0];
            }
        }
        $filters['with'] = array_unique(array_merge($filters['with'] ?? [], $requiredRelations));

        $subjects = $this->getFilteredQueryForExport($filters)->get();
        $filename = 'subjects-' . date('YmdHis') . '.' . $format;

        switch (strtolower($format)) {
            case 'pdf':
                return $this->exportImportService->exportGenericPdf($subjects, $headings, $mapKeys, $filename, $title, $subtitle, 'landscape');
            case 'xlsx':
                return $this->exportImportService->exportExcel($subjects, $headings, $mapKeys, $filename, $title, $subtitle);
            case 'csv':
                return $this->exportImportService->exportCsv($subjects, $headings, $mapKeys, $filename, $title, $subtitle);
            default:
                throw new \InvalidArgumentException("Invalid export format requested: {$format}. Valid formats are pdf, xlsx, csv.");
        }
    }
}
