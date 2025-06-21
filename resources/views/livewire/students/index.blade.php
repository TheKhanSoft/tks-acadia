<?php

use App\Enums\Gender;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\Program;
use App\Models\EnrollmentStatus; 
use App\Services\StudentService;
use App\Http\Requests\StudentRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

new class extends Component {
    use WithPagination, Toast;

    // Student Model Properties
    public $studentId = null;
    public $student_id_field; // For the form input
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $phone_alternative;
    public $gender;
    public $nic_no;
    public $date_of_birth;
    public $postal_address;
    public $permanent_address;
    public $photo_path;
    public $bio;
    public $student_status_id;
    
    // Options for selects
    public $studentStatusOptions = [];
    // Add other options if new filters/fields require them (e.g., programs for filtering)

    // Table & Filtering properties
    #[Url]
    public $perPage = 10;
    #[Url]
    public $search = '';
    #[Url]
    public $sortField = 'first_name';
    #[Url]
    public $sortDirection = 'asc';
    #[Url]
    public $showDeletedRecords = false;
    public $selectedStudents = [];
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $viewStudent = null;
    // public $showEnrollmentHistoryModal = false; // If student enrollment history is added
    // public $historyStudent = null;             // If student enrollment history is added
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // --- Export Properties ---
    public $showGenericExportModal = false;
    public $exportingSelectedOnly = false;
    public $studentExportColumns = []; // Type hinted
    public $defaultExportColumns = []; // Type hinted
    public $exportFormat = null;
    public $exportColumnsData = []; // Type hinted

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterStudentStatusId = null;
    #[Url]
    public $filterGender = '';
    #[Url]
    public $filterDobStart = null;
    #[Url]
    public $filterDobEnd = null;
    // Add more student-specific filters if needed, e.g., program, enrollment date
    #[Url]
    public $filterProgramId = null; // Example: Filter by program
    #[Url]
    public $filterEnrollmentDateStart = null; // Example
    #[Url]
    public $filterEnrollmentDateEnd = null; // Example

    public $programOptions = []; // For program filter dropdown

    // Modal tab control
    public $selectedTab = 'basic';

    // Define table headers for Students
    public array $headers = [ // Type hinted
        ['key' => 'full_name', 'label' => 'Student Info'],
        ['key' => 'contact', 'label' => 'Contact', 'sortable' => false],
        ['key' => 'currentProgram.name', 'label' => 'Current Program', 'sortable' => false, 'class' => 'hidden lg:table-cell'],
        ['key' => 'currentEnrolmentStatus.name', 'label' => 'Current Status', 'sortable' => false, 'class' => 'hidden lg:table-cell'],
    ];

    // Reset pagination when search or filters change
    public function updatedSearch(): void {$this->resetPage();} // Type hinted return
    public function updatedPerPage(): void {$this->resetPage();}
    public function updatedShowDeletedRecords(): void {
        $this->resetPage();
        $this->selectedStudents = [];
        $this->selectAll = false;
    }
    public function updatedFilterStudentStatusId(): void {$this->resetPage();}
    public function updatedFilterGender(): void {$this->resetPage();}
    public function updatedFilterDobStart(): void {$this->resetPage();}
    public function updatedFilterDobEnd(): void {$this->resetPage();}
    public function updatedFilterProgramId(): void {$this->resetPage();} // Example
    public function updatedFilterEnrollmentDateStart(): void {$this->resetPage();} // Example
    public function updatedFilterEnrollmentDateEnd(): void {$this->resetPage();} // Example

    public function sortBy(string $field): void // Type hinted
    {
        $allowedSortFields = [
            'student_id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'nic_no', 'date_of_birth',
            'created_at', 'updated_at',
            // Related fields (ensure StudentService handles these or adjust query)
            'student_status_name', // Assumes relation 'studentStatus.name'
            'current_program_name', // Assumes 'currentEnrolment.program.name'
            'current_enrollment_status_name', // Assumes 'currentEnrolment.status.name'
        ];

        if ($field === 'full_name') {
            $this->sortField = ($this->sortField === 'first_name' || $this->sortField === 'last_name')
                ? ($this->sortDirection === 'asc' ? 'first_name' : 'first_name') // Keep first_name, toggle dir
                : 'first_name';
            $this->sortDirection = ($this->sortField === 'first_name' && $this->sortDirection === 'asc' && $field === 'full_name') ? 'desc' : 'asc';
            return;
        }
        
        if (!in_array($field, $allowedSortFields)) {
            $field = 'first_name'; // Default if invalid
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll(bool $value): void // Type hinted
    {
        $items = $this->getStudents(app(StudentService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedStudents = array_unique(array_merge($this->selectedStudents, $itemIds));
        } else {
            $this->selectedStudents = array_diff($this->selectedStudents, $itemIds);
        }
    }

    public function updatedSelectedStudents(): void // Type hinted
    {
        $items = $this->getStudents(app(StudentService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedStudents));
    }

    public function mount(): void // Type hinted
    {
        $this->studentStatusOptions = StudentStatus::orderBy('name')->get(['id', 'name']);
        $this->programOptions = Program::where('is_active', true)->orderBy('name')->get(['id', 'name']); // Example for program filter

        $this->studentExportColumns = $this->getAvailableStudentExportColumns();
        $this->defaultExportColumns = $this->getDefaultStudentExportColumns();
    }

    private function getAvailableStudentExportColumns(): array
    {
        return [
            ['key' => 'student_id', 'label' => 'Student ID'],
            ['key' => 'first_name', 'label' => 'First Name'],
            ['key' => 'last_name', 'label' => 'Last Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'phone_alternative', 'label' => 'Alt. Phone'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'nic_no', 'label' => 'NIC Number'],
            ['key' => 'date_of_birth', 'label' => 'Date of Birth'],
            ['key' => 'studentStatus.name', 'label' => 'Student Status'],
            ['key' => 'currentEnrolment.program.name', 'label' => 'Current Program'], // Example
            ['key' => 'currentEnrolment.status.name', 'label' => 'Current Enrollment Status'], // Example
            ['key' => 'postal_address', 'label' => 'Postal Address'],
            ['key' => 'permanent_address', 'label' => 'Permanent Address'],
            ['key' => 'bio', 'label' => 'Bio'],
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
        ];
    }

    private function getDefaultStudentExportColumns(): array
    {
        return ['student_id', 'first_name', 'last_name', 'email', 'phone', 'studentStatus.name', 'currentEnrolment.program.name', 'currentEnrolment.status.name'];
    }

    public function openModal($id = null): void // Type hinted
    {
        $studentService = app(StudentService::class);
        $this->resetValidation();
        $this->resetExcept([
            'search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords',
            'selectedStudents', 'selectAll', 'headers', 'studentStatusOptions', 'programOptions', // Keep programOptions
            // Filter properties
            'filterStudentStatusId', 'filterGender', 'filterDobStart', 'filterDobEnd',
            'filterProgramId', 'filterEnrollmentDateStart', 'filterEnrollmentDateEnd', // Keep new filters
        ]);

        $this->showModal = true;
        $this->studentId = $id;
        $this->selectedTab = 'basic';

        if ($id) {
            $student = $studentService->getStudent($id, $this->showDeletedRecords, ['studentStatus']); // Eager load
            if ($student) {
                $this->student_id_field = $student->student_id;
                $this->first_name = $student->first_name;
                $this->last_name = $student->last_name;
                $this->email = $student->email;
                $this->phone = $student->phone;
                $this->phone_alternative = $student->phone_alternative;
                $this->gender = $student->gender;
                $this->nic_no = $student->nic_no;
                $this->date_of_birth = $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : null;
                $this->postal_address = $student->postal_address;
                $this->permanent_address = $student->permanent_address;
                $this->photo_path = $student->photo_path;
                $this->bio = $student->bio;
                $this->student_status_id = $student->student_status_id;
            } else {
                $this->error('Student not found.');
                $this->closeModal();
                return;
            }
        } else {
            // Reset all form fields for a new entry
            $this->student_id_field = ''; $this->first_name = ''; $this->last_name = ''; $this->email = '';
            $this->phone = ''; $this->phone_alternative = ''; $this->gender = null; $this->nic_no = '';
            $this->date_of_birth = null; $this->postal_address = ''; $this->permanent_address = '';
            $this->photo_path = ''; $this->bio = ''; $this->student_status_id = null;
        }
    }

    public function openViewModal($id, StudentService $studentService): void // Type hinted
    {
        $this->viewStudent = $studentService->getStudent($id, true, ['studentStatus', 'currentEnrolment.program', 'currentEnrolment.status']);
        if (!$this->viewStudent) {
            $this->error('Student not found.');
            return;
        }
        $this->showViewModal = true;
    }

    // public function openEnrollmentHistoryModal($id, StudentService $studentService) // Example
    // {
    //     $this->historyStudent = $studentService->getStudent($id, true, ['enrollments.program', 'enrollments.status', 'enrollments.session']); // Adjust relations
    //     if (!$this->historyStudent) { $this->error('Student not found.'); return; }
    //     $this->showEnrollmentHistoryModal = true;
    // }

    // public function closeEnrollmentHistoryModal() // Example
    // {
    //     $this->historyStudent = null;
    //     $this->showEnrollmentHistoryModal = false;
    // }

    public function closeModal(): void // Type hinted
    {
        $this->showModal = false;
        $this->showViewModal = false;
        // $this->showEnrollmentHistoryModal = false; // If added
        $this->confirmingDeletion = false;
        $this->confirmingBulkDeletion = false;
        $this->confirmingPermanentDeletion = false;
        $this->confirmingBulkPermanentDeletion = false;
        $this->confirmingRestore = false;
        $this->confirmingBulkRestore = false;
        $this->studentId = null;
        $this->viewStudent = null;
        // $this->historyStudent = null; // If added
    }

    public function save(StudentService $studentService): void // Type hinted
    {
        $request = new StudentRequest();
        $rules = $request->rules($this->studentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'student_id' => $this->student_id_field,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_alternative' => $this->phone_alternative,
            'gender' => $this->gender,
            'nic_no' => $this->nic_no,
            'date_of_birth' => $this->date_of_birth,
            'postal_address' => $this->postal_address,
            'permanent_address' => $this->permanent_address,
            'photo_path' => $this->photo_path,
            'bio' => $this->bio,
            'student_status_id' => $this->student_status_id,
            // 'city_id' => $this->city_id, // If added back
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        foreach (['last_name', 'phone', 'phone_alternative', 'nic_no', 'date_of_birth', 'postal_address', 'permanent_address', 'photo_path', 'bio'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        try {
            if ($this->studentId) {
                $student = $studentService->getStudent($this->studentId);
                if ($student) {
                    $studentService->updateStudent($student, $validatedData);
                    $this->success('Student updated successfully! 🎓');
                } else {
                    $this->error('Student not found for update.');
                    $this->closeModal();
                    return;
                }
            } else {
                $studentService->createStudent($validatedData);
                $this->success('New Student added successfully! ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Student Save Error: ' . $e->getMessage(), ['exception' => $e]);
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('Failed to save student. A record with the same Student ID, Email, or NIC already exists.');
            } else {
                $this->error('An error occurred while saving the Student.');
            }
        }
    }

    public function handleBulkAction(string $action): void // Type hinted
    {
        if (!$action || empty($this->selectedStudents)) {
            if (empty($this->selectedStudents)) $this->warning('Please select students first 🤔');
            $this->dispatch('reset-bulk-action');
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
            'updateStudentStatus' => null, // Placeholder for a potential modal
            'exportSelected' => null,
        ];

        if (isset($confirmationMap[$action]) && $confirmationMap[$action] !== null) {
            $this->{$confirmationMap[$action]} = true;
        } elseif ($action === 'updateStudentStatus') {
            // TODO: Implement logic, maybe open a modal to select new status
            $this->warning('Bulk Update Student Status action clicked (not implemented yet).');
        } elseif ($action === 'exportSelected') {
            if (empty($this->selectedStudents)) {
                $this->warning('No students selected for export.');
            } else {
                $this->exportingSelectedOnly = true;
                $this->showGenericExportModal = true;
            }
        }
        $this->dispatch('reset-bulk-action');
    }

    public function confirmDelete($id): void {$this->studentId = $id; $this->confirmingDeletion = true;} // Type hinted

    public function delete(StudentService $studentService): void // Type hinted
    {
        try {
            $successful = $studentService->deleteStudentById($this->studentId);
            $this->confirmingDeletion = false;
            if ($successful) $this->warning('Student deleted successfully! 🗑️');
            else $this->error('Failed to delete Student.');
            $this->studentId = null;
        } catch (\Exception $e) {
            \Log::error('Student Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('An error occurred while deleting the Student.');
        }
    }

    public function bulkDelete(StudentService $studentService): void // Type hinted
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $deletedCount = $studentService->bulkDeleteStudentByIds($studentIds);
            $this->confirmingBulkDeletion = false;
            if ($deletedCount > 0) $this->warning($deletedCount . ' students deleted successfully! 🗑️');
            else $this->error('Failed to delete selected students.');
            $this->selectedStudents = []; $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false;
            $this->error('An error occurred while deleting selected students.');
        }
    }

    public function confirmRestore($id): void {$this->studentId = $id; $this->confirmingRestore = true;} // Type hinted

    public function restore(StudentService $studentService): void // Type hinted
    {
        try {
            $successful = $studentService->restoreStudent($this->studentId);
            $this->confirmingRestore = false;
            if ($successful) $this->success('Student restored successfully! ♻️');
            else $this->error('Failed to restore Student.');
            $this->studentId = null;
        } catch (\Exception $e) {
            \Log::error('Student Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false;
            $this->error('An error occurred while restoring the Student.');
        }
    }

    public function bulkRestore(StudentService $studentService): void // Type hinted
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $restoredCount = $studentService->bulkRestoreStudents($studentIds);
            $this->confirmingBulkRestore = false;
            if ($restoredCount > 0) $this->success($restoredCount . ' students restored successfully! ♻️');
            else $this->error('Failed to restore selected students.');
            $this->selectedStudents = []; $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false;
            $this->error('An error occurred while restoring selected students.');
        }
    }

    public function confirmPermanentDelete($id): void {$this->studentId = $id; $this->confirmingPermanentDeletion = true;} // Type hinted

    public function permanentDelete(StudentService $studentService): void // Type hinted
    {
        try {
            $successful = $studentService->permanentlyDelete($this->studentId);
            $this->confirmingPermanentDeletion = false;
            if ($successful) $this->warning('Student permanently deleted! 💥');
            else $this->error('Failed to permanently delete Student.');
            $this->studentId = null;
        } catch (\Exception $e) {
            \Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete. Student linked to other records.');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkPermanentDelete(StudentService $studentService): void // Type hinted
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $deletedCount = $studentService->bulkPermanentDelete($studentIds);
            $this->confirmingBulkPermanentDeletion = false;
            if ($deletedCount > 0) $this->warning($deletedCount . ' students permanently deleted! 💥');
            else $this->error('Failed to permanently delete selected students.');
            $this->selectedStudents = []; $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete. Some students linked to other records.');
            } else {
                $this->error('An error occurred during bulk permanent deletion.');
            }
        }
    }

    public function getActiveFiltersProperty(): array // Type hinted
    {
        $filters = [];
        if ($this->filterStudentStatusId) {
            $statusName = $this->studentStatusOptions->firstWhere('id', $this->filterStudentStatusId)?->name ?? 'Unknown';
            $filters[] = ['key' => 'filterStudentStatusId', 'label' => 'Status', 'value' => $statusName];
        }
        if ($this->filterGender) {
            $filters[] = ['key' => 'filterGender', 'label' => 'Gender', 'value' => $this->filterGender];
        }
        if ($this->filterDobStart) {
            $filters[] = ['key' => 'filterDobStart', 'label' => 'DOB From', 'value' => $this->filterDobStart];
        }
        if ($this->filterDobEnd) {
            $filters[] = ['key' => 'filterDobEnd', 'label' => 'DOB To', 'value' => $this->filterDobEnd];
        }
        if ($this->filterProgramId) { // Example
            $programName = $this->programOptions->firstWhere('id', $this->filterProgramId)?->name ?? 'Unknown Program';
            $filters[] = ['key' => 'filterProgramId', 'label' => 'Program', 'value' => $programName];
        }
        if ($this->filterEnrollmentDateStart) { // Example
            $filters[] = ['key' => 'filterEnrollmentDateStart', 'label' => 'Enrolled From', 'value' => $this->filterEnrollmentDateStart];
        }
        if ($this->filterEnrollmentDateEnd) { // Example
            $filters[] = ['key' => 'filterEnrollmentDateEnd', 'label' => 'Enrolled To', 'value' => $this->filterEnrollmentDateEnd];
        }
        return $filters;
    }

    public function removeFilter(string $filterKey): void // Type hinted
    {
        if (in_array($filterKey, ['filterGender'])) $this->reset($filterKey);
        else $this->$filterKey = null;
        $this->resetPage();
        $this->success('Filter removed.');
    }

    public function resetFilters(): void // Type hinted
    {
        $this->reset(
            'filterStudentStatusId', 'filterGender', 'filterDobStart', 'filterDobEnd',
            'filterProgramId', 'filterEnrollmentDateStart', 'filterEnrollmentDateEnd' // Reset new filters
        );
        $this->resetPage();
        $this->success('Filters reset.');
    }

    private function getStudents(StudentService $studentService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
            'student_status_id' => $this->filterStudentStatusId,
            'gender' => $this->filterGender,
            'dob_start' => $this->filterDobStart,
            'dob_end' => $this->filterDobEnd,
            'program_id' => $this->filterProgramId, // Example
            'enrollment_date_start' => $this->filterEnrollmentDateStart, // Example
            'enrollment_date_end' => $this->filterEnrollmentDateEnd, // Example
        ];

        $filterParams = array_filter($filterParams, fn($value, $key) => ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir', 'per_page']), ARRAY_FILTER_USE_BOTH);
        $filterParams['search'] = !empty($this->search);
        $filterParams['gender'] = $this->filterGender ?? '';
        $filterParams['with_trashed'] = $this->showDeletedRecords;
        $filterParams['with'] = ['studentStatus', 'currentEnrolment.program', 'currentEnrolment.status']; // Ensure relations for display

        return $studentService->getPaginatedStudents($filterParams);
    }

    public function render(): mixed
    {
        $students = $this->getStudents(app(StudentService::class));
        $currentPageIds = $students->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedStudents));

        return view('livewire.students.index', [ // Changed view path
            'students' => $students,
            'headers' => $this->headers,
        ]);
    }

    public function openExportModal(): void // Type hinted
    {
        $this->exportingSelectedOnly = false;
        $this->showGenericExportModal = true;
    }

    #[On('start-student-export')]
    public function handleStudentExport(): ?BinaryFileResponse // Type hinted return
    {
        $studentService = app(StudentService::class);
        $format = $this->exportFormat;
        $selectedColumnsData = $this->exportColumnsData;

        $this->exportFormat = null; $this->exportColumnsData = [];

        if (empty($format) || empty($selectedColumnsData)) {
            \Log::error('Student Export Error: Invalid format or column data.', ['format' => $format, 'columns' => $selectedColumnsData]);
            $this->error('Export failed: missing format or columns.');
            $this->showGenericExportModal = false; return null;
        }

        $filters = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search), 'search_term' => $this->search,
            'sort_by' => $this->sortField, 'sort_dir' => $this->sortDirection,
            'student_status_id' => $this->filterStudentStatusId, 'gender' => $this->filterGender,
            'dob_start' => $this->filterDobStart, 'dob_end' => $this->filterDobEnd,
            'program_id' => $this->filterProgramId, // Example
            'enrollment_date_start' => $this->filterEnrollmentDateStart, // Example
            'enrollment_date_end' => $this->filterEnrollmentDateEnd, // Example
        ];
        $filters = array_filter($filters, fn($value, $key) => ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir']), ARRAY_FILTER_USE_BOTH);
        $filters['search'] = !empty($this->search); $filters['gender'] = $this->filterGender ?? '';
        $filters['with_trashed'] = $this->showDeletedRecords;

        if ($this->exportingSelectedOnly && !empty($this->selectedStudents)) {
            $filters['ids'] = array_map('intval', $this->selectedStudents);
        }

        try {
            $this->success('Export started... ⏳');
            $this->showGenericExportModal = false; $this->exportingSelectedOnly = false;
            return $studentService->exportStudents($format, $filters, $selectedColumnsData, 'Student Report', "Selected Students");
        } catch (\Exception $e) {
            \Log::error('Student Export Error: ' . $e->getMessage(), ['filters' => $filters, 'selectedColumnsData' => $selectedColumnsData, 'format' => $format, 'exception' => $e]);
            $this->error('An error occurred during export. Check logs.');
            $this->showGenericExportModal = false; $this->exportingSelectedOnly = false;
            return null;
        }
    }
};
?>

<!-- Main Container -->
<div wire:id="{{ $this->getId() }}">

    <!-- Header -->
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Student Management" icon="o-academic-cap" icon-classes="bg-primary rounded-full p-1 w-8 h-8"
        subtitle="Total Students: {{ $students->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}"
        subtitle-class="mr-2 mt-0.5 ">
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Students..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add Student"
                label="Add Student" />
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner tooltip-left="Filters" />
            <x-button icon="o-arrow-down-tray" wire:click="openExportModal"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner="openExportModal"
                tooltip-left="Export Data..." />
        </x-slot:actions>
    </x-header>

    <!-- Active Filters Display -->
    @if (count($this->activeFilters))
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Active Filters:</span>
            @foreach ($this->activeFilters as $filter)
                <x-badge class="badge-primary badge-xs font-semibold gap-1"
                    value="{{ $filter['label'] }} : {{ $filter['value'] }}" />
            @endforeach
            <x-button label="Clear All" wire:click="resetFilters" class="btn-ghost btn-xs text-red-500" spinner />
        </div>
    @endif

    <!-- Filters and Bulk Actions Row -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error"
                hint="{{ $showDeletedRecords ? 'Showing deleted' : '' }}" />
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <x-select wire:model.live="perPage" :options="[['id' => 5, 'name' => 5], ['id' => 10, 'name' => 10], ['id' => 25, 'name' => 25], ['id' => 50, 'name' => 50], ['id' => 100, 'name' => 100]]"
                    class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>
        @if (count($selectedStudents))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Perform a bulk action" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'updateStudentStatus', 'name' => 'Update Student Status...'], // Student specific
                    ['id' => 'exportSelected', 'name' => 'Export Selected...'],
                    ...($showDeletedRecords ? [
                        ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                        ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete'],
                    ] : []),
                ]"
                    class="select select-bordered select-sm py-0" id="bulk-action-select" x-data
                    x-on:change="$wire.handleBulkAction($event.target.value)" x-init="$watch('$wire.selectedStudents', value => { if (value.length === 0) $el.value = ''; })"
                    x-on:reset-bulk-action.window="$el.value = ''" />
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($selectedStudents) }} selected</span>
            </div>
        @endif
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-b-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="p-4 w-8">
                        <x-checkbox wire:model.live="selectAll" class="checkbox-sm checkbox-primary" />
                    </th>
                    @foreach ($headers as $header)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider {{ $header['sortable'] ?? true ? 'cursor-pointer' : '' }} {{ $header['class'] ?? '' }}"
                            @if ($header['sortable'] ?? true) wire:click="sortBy('{{ $header['key'] }}')" @endif>
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if (($header['sortable'] ?? true) && ($sortField === $header['key'] || ($header['key'] === 'full_name' && in_array($sortField, ['first_name', 'last_name']))))
                                    <x-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}" class="h-3 w-3" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($students as $student)
                    <tr wire:key="student-{{ $student->id }}" class="{{ $student->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150 ease-in-out">
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedStudents" value="{{ (string) $student->id }}" class="checkbox-sm checkbox-primary" />
                        </td>
                        <!-- Student Info (Combined) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @php
                                        $photoUrl = $student->photo_path ? (Str::startsWith($student->photo_path, ['http://', 'https://']) ? $student->photo_path : asset('storage/' . $student->photo_path)) : 'https://ui-avatars.com/api/?name=' . urlencode($student->first_name . ' ' . $student->last_name) . '&color=FFFFFF&background=0D8ABC&bold=true&size=64';
                                    @endphp
                                    <img class="h-12 w-12 rounded-full object-cover shadow-sm" src="{{ $photoUrl }}" alt="{{ $student->first_name }} {{ $student->last_name }}">
                                </div>
                                <div class="flex-grow">
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center space-x-2 flex-wrap">
                                        <span>ID: {{ $student->student_id ?? 'N/A' }}</span>
                                        {{-- Student Status Badge (Optional here, or rely on dedicated column) --}}
                                        @php
                                            $statusName = $student->studentStatus?->name;
                                            $statusColor = match (strtolower($statusName ?? '')) {
                                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'inactive', 'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'graduated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'dropped_out', 'withdrawn' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            };
                                        @endphp
                                        @if($statusName)
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            <span class="px-2 py-0.5 inline-flex text-[11px] leading-4 font-medium rounded-full {{ $statusColor }}">
                                                {{ $statusName }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!-- Contact (Email, Phone) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <div class="text-xs text-gray-700 dark:text-gray-300 flex items-center">
                                    <x-icon name="o-envelope" class="w-3.5 h-3.5 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    {{ $student->email ?? 'No Email' }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                    <x-icon name="o-phone" class="w-3.5 h-3.5 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    {{ $student->phone ?? 'No Phone' }}
                                </div>
                            </div>
                        </td>
                        <!-- Current Program -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                {{ $student->currentEnrolment->program->name ?? 'N/A' }}
                            </span>
                        </td>
                        <!-- Current Enrollment Status -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            @php
                                $enrollStatus = $student->currentEnrolment->status->name ?? 'N/A';
                                $enrollStatusColor = match (strtolower($enrollStatus)) {
                                    'enrolled', 'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'pending', 'incomplete' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'completed', 'graduated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'cancelled', 'withdrawn' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full {{ $enrollStatusColor }}">
                                {{ $enrollStatus }}
                            </span>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1.5">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" spinner tooltip-left="View Details" />
                                {{-- <x-button icon="o-academic-cap" wire:click="openEnrollmentHistoryModal({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300" spinner tooltip-left="Enrollment History" /> --}}
                                @if (!$student->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" spinner tooltip-left="Edit Student" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" spinner tooltip-left="Delete Student" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" spinner tooltip-left="Restore Student" />
                                    <x-button icon="o-no-symbol" wire:click="confirmPermanentDelete({{ $student->id }})" class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" spinner tooltip-left="Permanently Delete Student" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-16 w-16 text-gray-400 mb-4" />
                                <span class="text-lg font-medium">No students found</span>
                                <p class="text-sm mt-1">{{ $search ? 'Try adjusting your search term.' : 'Start by adding a new student.' }}</p>
                                @if ($search)
                                    <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm" label="Clear Search" />
                                @else
                                    <x-button wire:click="openModal" class="mt-3 btn btn-primary btn-sm" label="Add Your First Student" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
        {{ $students->links() }}
    </div>

    <!-- Add/Edit Student Modal -->
    <x-modal wire:model="showModal" :title="$studentId ? 'Edit Student' : 'Add New Student'" box-class="max-w-4xl" separator class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab">
                <x-tab name="basic" label="Basic Info" icon="o-identification" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                        <x-input wire:model="student_id_field" label="Student ID" placeholder="Unique ID (e.g., S2023-001)" icon="o-hashtag" inline required />
                        <x-input wire:model="first_name" label="First Name" placeholder="Enter first name" icon="o-user" inline required />
                        <x-input wire:model="last_name" label="Last Name" placeholder="Enter last name" icon="o-user" inline />
                        <x-input wire:model="email" label="Email" type="email" placeholder="Enter email address" icon="o-envelope" inline required />
                        <x-input wire:model="phone" label="Phone" placeholder="Enter phone number" icon="o-phone" inline />
                        <x-input wire:model="phone_alternative" label="Alternative Phone" placeholder="Alternative phone number" icon="o-phone-arrow-up-right" inline />
                        <x-select wire:model="gender" label="Gender" :options="Gender::toSelectArray()" placeholder="Select gender" icon="o-user-group" inline required />
                        <x-input wire:model="nic_no" label="NIC Number" placeholder="e.g., 12345-1234567-1" icon="o-identification" inline />
                        <x-datepicker wire:model="date_of_birth" label="Date of Birth" icon="o-calendar" inline />
                    </div>
                </x-tab>
                <x-tab name="status_info" label="Status" icon="o-tag" x-cloak> {{-- Renamed from categorization_status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        <x-select wire:model="student_status_id" label="Student Status" :options="$studentStatusOptions" placeholder="Select status" icon="o-identification" inline required />
                        {{-- Add other student-specific status or categorization fields here if any --}}
                        {{-- e.g., <x-select wire:model="enrollment_type_id" label="Enrollment Type" :options="$enrollmentTypes" placeholder="Select type" icon="o-clipboard-document-list" inline /> --}}
                    </div>
                </x-tab>
                <x-tab name="address" label="Address" icon="o-map-pin" x-cloak>
                    <div class="grid grid-cols-1 gap-4 p-4">
                        <x-textarea wire:model="postal_address" label="Postal Address" placeholder="Enter postal address" rows="3" icon="o-envelope-open" inline />
                        <x-textarea wire:model="permanent_address" label="Permanent Address" placeholder="Enter permanent address" rows="3" icon="o-home" inline />
                        {{-- <x-select wire:model="city_id" label="City" :options="$cityOptions" placeholder="Select city" icon="o-map" inline /> --}}
                    </div>
                </x-tab>
                <x-tab name="bio_photo" label="Bio & Photo" icon="o-user-circle" x-cloak>
                    <div class="grid grid-cols-1 gap-4 p-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo</label>
                            @if ($photo_path && Storage::disk('public')->exists($photo_path))
                                <img src="{{ asset('storage/' . $photo_path) }}" alt="Student Photo" class="h-20 w-20 rounded-full object-cover mb-2 shadow-sm">
                            @else
                                <div class="h-20 w-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-2 shadow-sm">
                                    <x-icon name="o-photo" class="h-10 w-10 text-gray-400" />
                                </div>
                            @endif
                            <x-input wire:model="photo_path" label="Photo Path (Manual)" placeholder="path/to/photo.jpg" icon="o-photo" inline hint="Manual path entry. Implement file upload later." />
                            {{-- <x-file wire:model="new_photo" label="Upload New Photo" accept="image/png, image/jpeg" /> --}}
                        </div>
                        <x-textarea wire:model="bio" label="Bio" placeholder="Short biography" rows="4" icon="o-document-text" inline />
                    </div>
                </x-tab>
            </x-tabs>
        </x-form>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button type="submit" label="{{ $studentId ? 'Update Student' : 'Create Student' }}" class="btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Student Modal -->
    <x-modal wire:model="showViewModal" title="View Student Details" separator box-class="max-w-4xl">
        @if ($viewStudent)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">
                <div class="flex flex-col sm:flex-row justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div class="flex items-center mb-3 sm:mb-0">
                        @php
                            $viewPhotoUrl = $viewStudent->photo_path ? (Str::startsWith($viewStudent->photo_path, ['http://', 'https://']) ? $viewStudent->photo_path : asset('storage/' . $viewStudent->photo_path)) : 'https://ui-avatars.com/api/?name=' . urlencode($viewStudent->first_name . ' ' . $viewStudent->last_name) . '&color=7F9CF5&background=EBF4FF';
                        @endphp
                        <img class="h-16 w-16 rounded-full object-cover mr-4 shadow-sm" src="{{ $viewPhotoUrl }}" alt="{{ $viewStudent->first_name }} {{ $viewStudent->last_name }}">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $viewStudent->first_name }} {{ $viewStudent->last_name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $viewStudent->student_id }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center justify-start sm:justify-end w-full sm:w-auto">
                        @php
                            $viewStatusName = $viewStudent->studentStatus?->name;
                            $viewStatusColor = match (strtolower($viewStatusName ?? '')) {
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'inactive', 'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'graduated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'dropped_out', 'withdrawn' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            };
                        @endphp
                        <span title="Student Status" class="inline-flex items-center px-3 py-1 shadow-sm rounded-full text-xs font-medium {{ $viewStatusColor }}">
                            <x-icon name="o-identification" class="h-3.5 w-3.5 mr-1" /> {{ $viewStatusName ?? 'N/A' }}
                        </span>
                        @if ($viewStudent->deleted_at)
                            <span title="Deleted Status" class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" /> Deleted
                            </span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-[2fr_2fr_3fr] gap-x-6 gap-y-5 pt-4">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <x-icon name="o-identification" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Contact</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center"><x-icon name="o-envelope" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" /> {{ $viewStudent->email ?: 'N/A' }}</p>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center mt-1"><x-icon name="o-phone" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" /> {{ $viewStudent->phone ?: 'Not specified' }}</p>
                                @if($viewStudent->phone_alternative)
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center mt-1"><x-icon name="o-phone-arrow-up-right" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" /> {{ $viewStudent->phone_alternative }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-user-group" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Gender
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->gender?->label() ?: 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-identification" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow"><div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">NIC Number</div><p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $viewStudent->nic_no ?: 'Not specified' }}</p></div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-cake" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow"><div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Date of Birth</div><p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $viewStudent->date_of_birth ? $viewStudent->date_of_birth->format('M d, Y') : 'Not specified' }}</p></div>
                        </div>
                    </div>
                    <div class="space-y-4">
                         <!-- Current Program -->
                        <div class="flex items-start">
                            <x-icon name="o-academic-cap" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Current Program</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $viewStudent->currentEnrolment->program->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <!-- Current Enrollment Status -->
                        <div class="flex items-start">
                            <x-icon name="o-check-badge" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Current Enrollment Status</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $viewStudent->currentEnrolment->status->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-envelope-open" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow"><div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Postal Address</div><p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">{{ $viewStudent->postal_address ?: 'Not specified' }}</p></div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-home" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow"><div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Permanent Address</div><p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">{{ $viewStudent->permanent_address ?: 'Not specified' }}</p></div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <x-icon name="o-document-text" class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow"><div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Bio</div><p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">{{ $viewStudent->bio ?: 'Not specified' }}</p></div>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1"><x-icon name="o-calendar" class="h-4 w-4" /><span>Created: {{ $viewStudent->created_at ? $viewStudent->created_at->format('M d, Y h:i A') : '-' }}</span></div>
                        <div class="flex items-center gap-1"><x-icon name="o-pencil-square" class="h-4 w-4" /><span>Updated: {{ $viewStudent->updated_at ? $viewStudent->updated_at->format('M d, Y h:i A') : '-' }}</span></div>
                        @if ($viewStudent->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400"><x-icon name="o-archive-box-x-mark" class="h-4 w-4" /><span>Deleted: {{ $viewStudent->deleted_at->format('M d, Y h:i A') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="p-8 flex justify-center items-center"><x-loading class="loading-lg text-primary" /></div>
        @endif
        <x-slot:actions>
            <div class="flex justify-end gap-3 w-full">
                @if ($viewStudent && !$viewStudent->deleted_at)
                    <x-button label="Edit Student" icon="o-pencil" wire:click="openModal({{ $viewStudent->id }})" class="btn-primary" spinner />
                @endif
                <x-button label="Close" wire:click="closeModal" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    {{-- Confirmation Modals (Delete, Restore, Permanent Delete - Single & Bulk) --}}
    <x-modal wire:model="confirmingDeletion" title="Delete Student" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to delete this student? This action will soft delete the record.</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete" wire:click="delete" class="btn-error" wire:loading.attr="disabled" wire:target="delete" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Students" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to delete {{ count($selectedStudents) }} selected students? This action will soft delete these records.</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete {{ count($selectedStudents) }} Students" wire:click="bulkDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingRestore" title="Restore Student" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to restore this student?</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Restore" wire:click="restore" class="btn-success" wire:loading.attr="disabled" wire:target="restore" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Students" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to restore {{ count($selectedStudents) }} selected students?</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Restore {{ count($selectedStudents) }} Students" wire:click="bulkRestore" class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Student" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to permanently delete this student? This action cannot be undone.</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Permanently Delete" wire:click="permanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="permanentDelete" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Students" separator>
        <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" /></div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left"><p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to permanently delete {{ count($selectedStudents) }} selected students? This action cannot be undone.</p></div>
        </div>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Permanently Delete {{ count($selectedStudents) }} Students" wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkPermanentDelete" /></x-slot:actions>
    </x-modal>

    <!-- Advanced Filter Drawer -->
    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button class="w-11/12 lg:w-1/3 bg-gray-50 dark:bg-gray-900">
        <div class="px-4 space-y-4">
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Core Attributes</div>
                <x-select wire:model.live="filterStudentStatusId" label="Student Status" :options="$studentStatusOptions" placeholder="All Statuses" icon="o-identification" clearable inline />
                <x-select wire:model.live="filterGender" label="Gender" :options="[['id' => '', 'name' => 'All'], ['id' => 'Male', 'name' => 'Male'], ['id' => 'Female', 'name' => 'Female'], ['id' => 'Other', 'name' => 'Other']]" placeholder="All Genders" icon="o-user-group" clearable inline />
            </div>
            <hr class="border-gray-200 dark:border-gray-700">
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Academic Filters</div>
                <x-select wire:model.live="filterProgramId" label="Program" :options="$programOptions" placeholder="Any Program" icon="o-academic-cap" clearable inline /> {{-- Example --}}
                 <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800/50">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enrollment Date</label> {{-- Example --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-datepicker wire:model.live="filterEnrollmentDateStart" placeholder="From" icon="o-calendar-days" clearable />
                        <x-datepicker wire:model.live="filterEnrollmentDateEnd" placeholder="To" icon="o-calendar-days" clearable />
                    </div>
                </div>
            </div>
            <hr class="border-gray-200 dark:border-gray-700">
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Date Ranges</div>
                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800/50">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-datepicker wire:model.live="filterDobStart" placeholder="From" icon="o-cake" clearable />
                        <x-datepicker wire:model.live="filterDobEnd" placeholder="To" icon="o-cake" clearable />
                    </div>
                </div>
            </div>
        </div>
        <x-slot:actions>
            <div class="flex justify-between w-full px-4 py-3 border-t dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                <x-button label="Reset Filters" icon="o-arrow-path" wire:click="resetFilters" class="btn-ghost text-red-500" spinner />
                <x-button label="Apply Filters" icon="o-check" class="btn-primary" @click="$wire.showFilterDrawer = false" />
            </div>
        </x-slot:actions>
    </x-drawer>

    <!-- Export Format Selection Modal -->
    <x-modal wire:model="showGenericExportModal" title="Configure Export" separator box-class="max-w-3xl" class="backdrop-blur-sm">
        <div x-data="{
            availableColumns: {{ json_encode($studentExportColumns) }},
            selectedColumns: {{ json_encode($defaultExportColumns) }},
            get orderedSelectedFullColumns() { return this.selectedColumns.map(key => this.availableColumns.find(col => col.key === key)).filter(col => col !== undefined); },
            isColumnSelected(key) { return this.selectedColumns.includes(key); },
            toggleColumn(key) { if (this.isColumnSelected(key)) { this.selectedColumns = this.selectedColumns.filter(item => item !== key); } else { this.selectedColumns.push(key); } },
            moveUp(index) { if (index > 0) { [this.selectedColumns[index - 1], this.selectedColumns[index]] = [this.selectedColumns[index], this.selectedColumns[index - 1]]; } },
            moveDown(index) { if (index < this.selectedColumns.length - 1) { [this.selectedColumns[index + 1], this.selectedColumns[index]] = [this.selectedColumns[index], this.selectedColumns[index + 1]]; } },
            selectAllColumns() { this.selectedColumns = this.availableColumns.map(col => col.key); },
            deselectAllColumns() { this.selectedColumns = []; },
            resetToDefaults() { this.selectedColumns = {{ json_encode($defaultExportColumns) }}; },
        }" x-init="$watch('$wire.showGenericExportModal', value => { if (value) { selectedColumns = {{ json_encode($defaultExportColumns) }}; } })">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Available Columns</h4>
                    <div class="mb-2 flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Select columns to include:</span>
                        <div><x-button label="Select All" x-on:click="selectAllColumns" class="btn-xs btn-ghost" /><x-button label="Deselect All" x-on:click="deselectAllColumns" class="btn-xs btn-ghost" /></div>
                    </div>
                    <div class="max-h-80 overflow-y-auto border dark:border-gray-700 rounded-md p-3 space-y-2 bg-gray-50 dark:bg-gray-800/50">
                        <template x-for="column in availableColumns" :key="column.key">
                            <label class="flex items-center space-x-2 p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" :value="column.key" x-model="selectedColumns" class="checkbox checkbox-sm checkbox-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="column.label"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Selected Columns (Order)</h4>
                    <div class="mb-2 flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Order of columns in export:</span>
                        <x-button label="Reset Order" x-on:click="resetToDefaults" class="btn-xs btn-ghost" />
                    </div>
                    <div class="max-h-80 overflow-y-auto border dark:border-gray-700 rounded-md p-3 space-y-1 bg-white dark:bg-gray-800">
                        <template x-if="selectedColumns.length === 0"><div class="text-center text-gray-500 dark:text-gray-400 py-4">Please select columns.</div></template>
                        <template x-for="(column, index) in orderedSelectedFullColumns" :key="column.key">
                            <div class="flex items-center justify-between p-1.5 rounded bg-gray-100 dark:bg-gray-700 group">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="column.label"></span>
                                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button x-on:click="moveUp(index)" :disabled="index === 0" class="btn btn-xs btn-ghost p-1 disabled:opacity-30"><x-icon name="o-chevron-up" class="w-4 h-4" /></button>
                                    <button x-on:click="moveDown(index)" :disabled="index === selectedColumns.length - 1" class="btn btn-xs btn-ghost p-1 disabled:opacity-30"><x-icon name="o-chevron-down" class="w-4 h-4" /></button>
                                    <button x-on:click="toggleColumn(column.key)" class="btn btn-xs btn-ghost p-1 text-red-500 hover:text-red-700"><x-icon name="o-x-mark" class="w-4 h-4" /></button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t dark:border-gray-700 mt-4">
                <h4 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white text-center">Select Export Format</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div x-on:click="if (selectedColumns.length === 0) { alert('Please select columns.'); } else { @this.set('exportFormat', 'pdf'); @this.set('exportColumnsData', orderedSelectedFullColumns); Livewire.dispatch('start-student-export'); }" wire:loading.attr="disabled" wire:target="handleStudentExport" :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }" class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-text" class="w-10 h-10 text-red-500 mb-2 group-hover:scale-110 transition-transform" /><span class="font-semibold text-gray-700 dark:text-gray-300">Export as PDF</span><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formatted PDF document.</p><div wire:loading wire:target="handleStudentExport" class="mt-2"><x-loading class="loading-sm text-red-500" /></div>
                    </div>
                    <div x-on:click="if (selectedColumns.length === 0) { alert('Please select columns.'); } else { @this.set('exportFormat', 'xlsx'); @this.set('exportColumnsData', orderedSelectedFullColumns); Livewire.dispatch('start-student-export'); }" wire:loading.attr="disabled" wire:target="handleStudentExport" :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }" class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-table-cells" class="w-10 h-10 text-green-500 mb-2 group-hover:scale-110 transition-transform" /><span class="font-semibold text-gray-700 dark:text-gray-300">Export as Excel</span><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">XLSX spreadsheet file.</p><div wire:loading wire:target="handleStudentExport" class="mt-2"><x-loading class="loading-sm text-green-500" /></div>
                    </div>
                    <div x-on:click="if (selectedColumns.length === 0) { alert('Please select columns.'); } else { @this.set('exportFormat', 'csv'); @this.set('exportColumnsData', orderedSelectedFullColumns); Livewire.dispatch('start-student-export'); }" wire:loading.attr="disabled" wire:target="handleStudentExport" :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }" class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-chart-bar" class="w-10 h-10 text-blue-500 mb-2 group-hover:scale-110 transition-transform" /><span class="font-semibold text-gray-700 dark:text-gray-300">Export as CSV</span><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated values.</p><div wire:loading wire:target="handleStudentExport" class="mt-2"><x-loading class="loading-sm text-blue-500" /></div>
                    </div>
                </div>
            </div>
        </div>
        <x-slot:actions><x-button label="Cancel" @click="$wire.showGenericExportModal = false" class="btn-ghost" /></x-slot:actions>
    </x-modal>
</div>
