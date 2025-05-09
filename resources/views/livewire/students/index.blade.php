<?php

use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\StudentStudentStatus;
use App\Models\Office; // Needed for potential filtering/assignment
use App\Services\StudentService;
use App\Http\Requests\StudentRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\On; // Import the On attribute
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse; // For download response type hint

new class extends Component {
    use WithPagination, Toast;

    // Student Model Properties
    public $studentId = null; // To store the ID of the student being edited/viewed
    public $student_id_field; // Renamed from student_id to avoid conflict with model instance
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $phone_alternative;
    public $gender;
    public $nic_no;
    public $date_of_birth;
    public $student_type_id;
    public $city_id;
    public $postal_address;
    public $permanent_address;
    public $photo_path; 
    public $bio;
    public $student_status_id;

    // Options for selects
    public $studentStatus = [];

    // Table & Filtering properties
    #[Url]
    public $perPage = 10;
    #[Url]
    public $search = '';
    #[Url]
    public $sortField = 'first_name'; // Default sort field
    #[Url]
    public $sortDirection = 'asc';
    #[Url]
    public $showDeletedRecords = false;
    public $selectedStudents = []; // Changed from selectedOffices
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $viewStudent = null; // Changed from viewOffice
    public $showHistoryModal = false; // Added for employment history
    public $historyStudent = null; // Added for employment history data
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // --- Export Properties ---
    public $showGenericExportModal = false; // Controls the generic export modal
    public $exportingSelectedOnly = false; // Flag for bulk export context
    public $studentExportColumns = []; // All available columns for student export
    public $defaultExportColumns = []; // Default selected columns for export
    // Note: Actual selection/order will be managed client-side in the modal via AlpineJS
    public $exportFormat = null; // Added: To store the selected export format
    public $exportColumnsData = []; // Added: To store the selected columns data

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterStudentTypeId = null;
    #[Url]
    public $filterStudentStatusId = null;
    #[Url]
    public $filterGender = ''; // '', 'Male', 'Female', 'Other'
    // Removed Status Filter property declaration
    #[Url]
    public $filterOfficeId = null; // Filter by office assignment
    #[Url]
    public $filterEnrolmentDate = null;
    #[Url]
    public $filterPassoutDate = null;
    #[Url]
    public $filterDobStart = null;
    #[Url]
    public $filterDobEnd = null;
  
    // Modal tab control
    public $selectedTab = 'basic'; // Default to the 'basic' tab

    // Define table headers for Students
    public $headers = [
        ['key' => 'full_name', 'label' => 'Student Info'], // Combined Name, ID, Type
        ['key' => 'contact', 'label' => 'Contact', 'sortable' => false], // Combined Email, Phone
        ['key' => 'currentProgram.name', 'label' => 'Current Enrolment', 'sortable' => false, 'class' => 'hidden lg:table-cell'], // Corrected Job Nature key
        ['key' => 'currentEnrolmentStatus.name', 'label' => 'Current Status', 'sortable' => false, 'class' => 'hidden lg:table-cell'],
    ];

    // Reset pagination when search or filters change
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function updatedShowDeletedRecords()
    {
        $this->resetPage();
        $this->selectedStudents = [];
        $this->selectAll = false;
    }
    public function updatedFilterStudentTypeId()
    {
        $this->resetPage();
    }
    public function updatedFilterStudentStatusId()
    {
        $this->resetPage();
    }
    public function updatedFilterGender()
    {
        $this->resetPage();
    }
    // public function updatedFilterStatus() { $this->resetPage(); } // Removed Status Filter
    public function updatedFilterOfficeId()
    {
        $this->resetPage();
    }
    public function updatedFilterAppointmentDateStart()
    {
        $this->resetPage();
    }
    public function updatedFilterAppointmentDateEnd()
    {
        $this->resetPage();
    }
    public function updatedFilterDobStart()
    {
        $this->resetPage();
    }
    public function updatedFilterDobEnd()
    {
        $this->resetPage();
    }
    public function updatedFilterJobNatureId()
    {
        $this->resetPage();
    } // Added reset for Job Nature filter
    public function updatedFilterQualification()
    {
        $this->resetPage();
    } // Added reset for Qualification filter
    public function updatedFilterSpecialization()
    {
        $this->resetPage();
    } // Added reset for Specialization filter
    public function updatedFilterTerminationDateStart()
    {
        $this->resetPage();
    } // Added reset for Termination Date filter
    public function updatedFilterTerminationDateEnd()
    {
        $this->resetPage();
    } // Added reset for Termination Date filter

    public function sortBy($field)
    {
        // Use allowed fields from StudentService applySorting if possible, or define here
        $allowedSortFields = [
            'student_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'nic_no',
            'date_of_birth',
            'created_at',
            'updated_at', // Removed 'is_active'
            // Add related fields if supported by service/query
            'student_status_name',
        ];

        // Handle combined name sorting
        if ($field === 'full_name') {
            if ($this->sortField === 'first_name' || $this->sortField === 'last_name') {
                // If already sorting by name, just toggle direction
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                // Default to sorting by first_name asc when 'full_name' is clicked
                $this->sortField = 'first_name';
                $this->sortDirection = 'asc';
            }
            return;
        }

        if (!in_array($field, $allowedSortFields)) {
            // Maybe default to first_name or ignore invalid field
            $field = 'first_name';
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        $items = $this->getStudents(app(StudentService::class)); // Inject service
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedStudents = array_unique(array_merge($this->selectedStudents, $itemIds));
        } else {
            $this->selectedStudents = array_diff($this->selectedStudents, $itemIds);
        }
    }

    public function updatedSelectedStudents($value)
    {
        $items = $this->getStudents(app(StudentService::class)); // Inject service
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedStudents));
    }

    public function mount()
    {
        // Pre-load options for selects
        $this->studentTypes = StudentType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $this->studentStudentStatuses = StudentStatus::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $this->jobNatures = JobNature::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']); // Load Job Natures
        $this->offices = Office::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']); // For filtering/assignment dropdowns

        // Define available columns for student export
        $this->studentExportColumns = $this->getAvailableStudentExportColumns();
        // Define default selected columns (can be adjusted)
        $this->defaultExportColumns = $this->getDefaultStudentExportColumns();
    }

    // Helper to define available export columns specifically for Students
    private function getAvailableStudentExportColumns(): array
    {
        // Define columns with keys (matching model/relation attributes) and user-friendly labels
        return [
            ['key' => 'student_id', 'label' => 'Student ID'],
            ['key' => 'first_name', 'label' => 'First Name'],
            ['key' => 'last_name', 'label' => 'Last Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'nic_no', 'label' => 'NIC Number'],
            ['key' => 'date_of_birth', 'label' => 'Date of Birth'],
            ['key' => 'studentType.name', 'label' => 'Student Type'],
            ['key' => 'appointment_date', 'label' => 'Appointment Date'],
            ['key' => 'termination_date', 'label' => 'Termination Date'],
            ['key' => 'postal_address', 'label' => 'Postal Address'],
            ['key' => 'permanent_address', 'label' => 'Permanent Address'],
            ['key' => 'qualification', 'label' => 'Qualification'],
            ['key' => 'specialization', 'label' => 'Specialization'],
            // ['key' => 'photo_path', 'label' => 'Photo Path'], // Usually not useful in export
            ['key' => 'bio', 'label' => 'Bio'],
            ['key' => 'studentStudentStatus.name', 'label' => 'Work Status'],
            ['key' => 'jobNature.name', 'label' => 'Job Nature'],
            ['key' => 'primaryOffice.name', 'label' => 'Primary Office'], // Example related field
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
        ];
    }

    // Helper to define the default selected/ordered columns for export
    private function getDefaultStudentExportColumns(): array
    {
        // Return an array of column *keys* in the desired default order
        return ['student_id', 'first_name', 'last_name', 'email', 'phone', 'studentType.name', 'jobNature.name', 'studentStudentStatus.name', 'primaryOffice.name', 'appointment_date'];
    }

    // Temporarily remove StudentService injection from signature to test dependency resolution
    public function openModal($id = null)
    {
        // Manually resolve the service if needed (especially for the edit case)
        $studentService = app(StudentService::class);

        $this->resetValidation();
        // Reset all public properties except persistent ones and pre-loaded options
        $this->resetExcept([
            'search',
            'sortField',
            'sortDirection',
            'perPage',
            'showDeletedRecords',
            'selectedStudents',
            'selectAll',
            'headers',
            'studentTypes',
            'studentStudentStatuses',
            'jobNatures',
            'offices',
            // Filter properties
            'filterStudentTypeId',
            'filterStudentStatusId',
            'filterGender',
            'filterOfficeId',
            'filterEnrolmentDate',
            'filterPassoutDate',
            'filterDobStart',
            'filterDobEnd',
            'filterJobNatureId',
            'filterQualification',
            'filterSpecialization',
            'filterTerminationDateStart',
            'filterTerminationDateEnd', // Added new filters
        ]);

        $this->showModal = true;
        $this->studentId = $id; // Use the separate property for the ID
        $this->selectedTab = 'basic'; // Default tab

        if ($id) {
            // Use the manually resolved service to get the student
            $student = $studentService->getStudent($id, $this->showDeletedRecords, ['studentType', 'studentStudentStatus', 'jobNature']); // Eager load relations including jobNature
            if ($student) {
                $this->student_id_field = $student->student_id;
                $this->first_name = $student->first_name;
                $this->last_name = $student->last_name;
                $this->email = $student->email;
                $this->phone = $student->phone;
                $this->gender = $student->gender;
                $this->nic_no = $student->nic_no;
                $this->date_of_birth = $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : null;
                $this->student_type_id = $student->student_type_id;
                $this->appointment_date = $student->appointment_date ? $student->appointment_date->format('Y-m-d') : null;
                $this->termination_date = $student->termination_date ? $student->termination_date->format('Y-m-d') : null;
                $this->postal_address = $student->postal_address;
                $this->permanent_address = $student->permanent_address;
                $this->qualification = $student->qualification;
                $this->specialization = $student->specialization;
                $this->photo_path = $student->photo_path; // Display only, upload handled separately if needed
                $this->bio = $student->bio;
                $this->student_work_status_id = $student->student_work_status_id;
                $this->job_nature_id = $student->job_nature_id; // Load job_nature_id
                // $this->is_active = $student->is_active; // Removed
            } else {
                $this->error('Student not found.');
                $this->closeModal();
                return;
            }
        } else {
            // Reset all form fields for a new entry
            $this->student_id_field = '';
            $this->first_name = '';
            $this->last_name = '';
            $this->email = '';
            $this->phone = '';
            $this->gender = null;
            $this->nic_no = '';
            $this->date_of_birth = null;
            $this->student_type_id = null;
            $this->appointment_date = null;
            $this->termination_date = null;
            $this->postal_address = '';
            $this->permanent_address = '';
            $this->qualification = '';
            $this->specialization = '';
            $this->photo_path = '';
            $this->bio = '';
            $this->student_work_status_id = null;
            $this->job_nature_id = null; // Reset job_nature_id
            // $this->is_active = true; // Removed
        }
    }

    public function openViewModal($id, StudentService $studentService)
    {
        // Eager load relationships needed for the view modal
        $this->viewStudent = $studentService->getStudent($id, true, ['studentType', 'studentStudentStatus', 'jobNature', 'offices', 'primaryOffice']); // Load necessary relations including jobNature directly
        if (!$this->viewStudent) {
            $this->error('Student not found.');
            return;
        }
        $this->showViewModal = true;
    }

    // Added method to open the employment history modal
    public function openHistoryModal($id, StudentService $studentService)
    {
        // Eager load relationships needed for the history modal
        // Load offices relationship
        $this->historyStudent = $studentService->getStudent($id, true, ['offices']); // Removed jobNature loading here

        if (!$this->historyStudent) {
            $this->error('Student not found.');
            return;
        }

        // Check if the relationship loaded correctly (optional but good practice)
        if (!$this->historyStudent->relationLoaded('offices')) {
            \Log::warning("StudentOffices relationship not loaded for student ID: {$id}");
            // Optionally, force load if needed, though eager loading should handle it
            // $this->historyStudent->load(['studentOffices.office', 'studentOffices.jobNature']);
        }

        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->historyStudent = null;
        $this->showHistoryModal = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showViewModal = false;
        $this->confirmingDeletion = false;
        $this->confirmingBulkDeletion = false;
        $this->confirmingPermanentDeletion = false;
        $this->confirmingBulkPermanentDeletion = false;
        $this->confirmingRestore = false;
        $this->confirmingBulkRestore = false;
        $this->studentId = null; // Reset the ID
        $this->viewStudent = null; // Reset view data
        $this->historyStudent = null; // Reset history data
    }

    public function save(StudentService $studentService)
    {
        $request = new StudentRequest();
        $currentId = $this->studentId; // Use the separate property
        // Pass the current ID to the rules method if it expects it
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'student_id' => $this->student_id_field, // Use the renamed field
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'nic_no' => $this->nic_no,
            'date_of_birth' => $this->date_of_birth,
            'student_type_id' => $this->student_type_id,
            'appointment_date' => $this->appointment_date,
            'termination_date' => $this->termination_date,
            'postal_address' => $this->postal_address,
            'permanent_address' => $this->permanent_address,
            'qualification' => $this->qualification,
            'specialization' => $this->specialization,
            'photo_path' => $this->photo_path, // Validation might need adjustment for uploads
            'bio' => $this->bio,
            'student_work_status_id' => $this->student_work_status_id,
            'job_nature_id' => $this->job_nature_id, // Add job_nature_id to validation data
            // 'is_active' => $this->is_active, // Removed
        ];

        // Run validation
        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        // Prepare data (e.g., handle boolean, nulls) - StudentRequest prepareForValidation might handle some of this
        // $validatedData['is_active'] = filter_var($validatedData['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN); // Removed
        // Ensure empty strings become null for nullable fields if necessary
        foreach (['last_name', 'phone', 'nic_no', 'date_of_birth', 'appointment_date', 'termination_date', 'postal_address', 'permanent_address', 'qualification', 'specialization', 'photo_path', 'bio'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        try {
            if ($this->studentId) {
                $student = $studentService->getStudent($this->studentId);
                if ($student) {
                    $studentService->updateStudent($student, $validatedData);
                    $this->success('Student updated successfully! 🧑‍💼');
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
            // Provide more specific feedback if possible (e.g., unique constraint violation)
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('Failed to save student. A record with the same Student ID, Email, or NIC already exists.');
            } else {
                $this->error('An error occurred while saving the Student.');
            }
        }
    }

    // Removed toggleActive method

    // --- Bulk Actions ---
    public function handleBulkAction($action)
    {
        if (!$action || empty($this->selectedStudents)) {
            if (empty($this->selectedStudents)) {
                $this->warning('Please select students first 🤔');
            }
            // Reset the dropdown visually
            $this->dispatch('reset-bulk-action'); // Dispatch browser event to reset select
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
            // Add placeholders for new bulk actions - these would likely open modals or perform direct actions
            'assignOffice' => null, // Placeholder: Implement assignOffice logic/modal trigger
            'updateStudentStatus' => null, // Placeholder: Implement updateStudentStatus logic/modal trigger
            'exportSelected' => null, // Will trigger the export modal with selected context
        ];

        // Removed bulkToggleActive logic

        // Handle actions that require confirmation
        if (isset($confirmationMap[$action]) && $confirmationMap[$action] !== null) {
            $this->{$confirmationMap[$action]} = true;
        }
        // Handle actions that might execute directly or need different handling
        elseif ($action === 'assignOffice') {
            // TODO: Implement logic, maybe open a modal
            $this->warning('Bulk Assign Office action clicked (not implemented yet).');
        } elseif ($action === 'updateStudentStatus') {
            // TODO: Implement logic, maybe open a modal
            $this->warning('Bulk Update Work Status action clicked (not implemented yet).');
        } elseif ($action === 'exportSelected') {
            // Set flag and open the export modal for format selection
            if (empty($this->selectedStudents)) {
                $this->warning('No students selected for export.');
            } else {
                $this->exportingSelectedOnly = true;
                $this->showGenericExportModal = true; // Open the generic modal
            }
        }

        // Reset the dropdown selection visually
        $this->dispatch('reset-bulk-action'); // Dispatch browser event to reset select
    }

    // Removed bulkToggleActive method

    // --- Deletion ---
    public function confirmDelete($id)
    {
        $this->studentId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(StudentService $studentService)
    {
        try {
            $successful = $studentService->deleteStudentById($this->studentId);
            $this->confirmingDeletion = false;
            if ($successful) {
                $this->warning('Student deleted successfully! 🗑️');
            } else {
                $this->error('Failed to delete Student. It might have already been deleted or does not exist.');
            }
            $this->studentId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Student Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('An error occurred while deleting the Student.');
        }
    }

    public function bulkDelete(StudentService $studentService)
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $deletedCount = $studentService->bulkDeleteStudentByIds($studentIds);
            $this->confirmingBulkDeletion = false;
            if ($deletedCount > 0) {
                $this->warning($deletedCount . ' students deleted successfully! 🗑️');
            } else {
                $this->error('Failed to delete selected students. They might have already been deleted or do not exist.');
            }
            $this->selectedStudents = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false;
            $this->error('An error occurred while deleting selected students.');
        }
    }

    // --- Restoration ---
    public function confirmRestore($id)
    {
        $this->studentId = $id;
        $this->confirmingRestore = true;
    }

    public function restore(StudentService $studentService)
    {
        try {
            $successful = $studentService->restoreStudent($this->studentId);
            $this->confirmingRestore = false;
            if ($successful) {
                $this->success('Student restored successfully! ♻️');
            } else {
                $this->error('Failed to restore Student. It might not be deleted or does not exist.');
            }
            $this->studentId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Student Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false;
            $this->error('An error occurred while restoring the Student.');
        }
    }

    public function bulkRestore(StudentService $studentService)
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $restoredCount = $studentService->bulkRestoreStudents($studentIds);
            $this->confirmingBulkRestore = false;
            if ($restoredCount > 0) {
                $this->success($restoredCount . ' students restored successfully! ♻️');
            } else {
                $this->error('Failed to restore selected students. They might not be deleted or do not exist.');
            }
            $this->selectedStudents = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false;
            $this->error('An error occurred while restoring selected students.');
        }
    }

    // --- Permanent Deletion ---
    public function confirmPermanentDelete($id)
    {
        $this->studentId = $id;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(StudentService $studentService)
    {
        try {
            $successful = $studentService->permanentlyDelete($this->studentId);
            $this->confirmingPermanentDeletion = false;
            if ($successful) {
                $this->warning('Student permanently deleted! 💥');
            } else {
                $this->error('Failed to permanently delete Student. It might not exist.');
            }
            $this->studentId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false;
            // Add specific checks, e.g., if deletion is blocked due to relationships
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete this student. They might be linked to other records (e.g., Head of Office).');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkPermanentDelete(StudentService $studentService)
    {
        try {
            $studentIds = array_map('intval', $this->selectedStudents);
            $deletedCount = $studentService->bulkPermanentDelete($studentIds);
            $this->confirmingBulkPermanentDeletion = false;
            if ($deletedCount > 0) {
                $this->warning($deletedCount . ' students permanently deleted! 💥');
            } else {
                $this->error('Failed to permanently delete selected students. They might not exist.');
            }
            $this->selectedStudents = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete selected students. Some might be linked to other records.');
            } else {
                $this->error('An error occurred during bulk permanent deletion.');
            }
        }
    }

    // --- Filters ---
    public function getActiveFiltersProperty()
    {
        $filters = [];

        if ($this->filterStudentTypeId) {
            $typeName = $this->studentTypes->firstWhere('id', $this->filterStudentTypeId)?->name ?? 'Unknown Type';
            $filters[] = ['key' => 'filterStudentTypeId', 'label' => 'Type', 'value' => $typeName];
        }
        if ($this->filterStudentStatusId) {
            $statusName = $this->studentStudentStatuses->firstWhere('id', $this->filterStudentStatusId)?->name ?? 'Unknown Status';
            $filters[] = ['key' => 'filterStudentStatusId', 'label' => 'Work Status', 'value' => $statusName];
        }
        if ($this->filterGender) {
            $filters[] = ['key' => 'filterGender', 'label' => 'Gender', 'value' => $this->filterGender];
        }
        // Removed Status Filter logic from active filters display
        // if ($this->filterStatus !== '') {
        //     $filters[] = ['key' => 'filterStatus', 'label' => 'Status', 'value' => ucfirst($this->filterStatus)];
        // }
        if ($this->filterOfficeId) {
            $officeName = $this->offices->firstWhere('id', $this->filterOfficeId)?->name ?? 'Unknown Office';
            $filters[] = ['key' => 'filterOfficeId', 'label' => 'Office', 'value' => $officeName];
        }
        if ($this->filterEnrolmentDate) {
            $filters[] = ['key' => 'filterEnrolmentDate', 'label' => 'Appointed From', 'value' => $this->filterEnrolmentDate];
        }
        if ($this->filterPassoutDate) {
            $filters[] = ['key' => 'filterPassoutDate', 'label' => 'Appointed To', 'value' => $this->filterPassoutDate];
        }
        if ($this->filterDobStart) {
            $filters[] = ['key' => 'filterDobStart', 'label' => 'DOB From', 'value' => $this->filterDobStart];
        }
        if ($this->filterDobEnd) {
            $filters[] = ['key' => 'filterDobEnd', 'label' => 'DOB To', 'value' => $this->filterDobEnd];
        }
        if ($this->filterJobNatureId) {
            $jobNatureName = $this->jobNatures->firstWhere('id', $this->filterJobNatureId)?->name ?? 'Unknown Nature';
            $filters[] = ['key' => 'filterJobNatureId', 'label' => 'Job Nature', 'value' => $jobNatureName];
        }
        if ($this->filterQualification) {
            $filters[] = ['key' => 'filterQualification', 'label' => 'Qualification', 'value' => $this->filterQualification];
        }
        if ($this->filterSpecialization) {
            $filters[] = ['key' => 'filterSpecialization', 'label' => 'Specialization', 'value' => $this->filterSpecialization];
        }
        if ($this->filterTerminationDateStart) {
            $filters[] = ['key' => 'filterTerminationDateStart', 'label' => 'Terminated From', 'value' => $this->filterTerminationDateStart];
        }
        if ($this->filterTerminationDateEnd) {
            $filters[] = ['key' => 'filterTerminationDateEnd', 'label' => 'Terminated To', 'value' => $this->filterTerminationDateEnd];
        }

        return $filters;
    }

    public function removeFilter($filterKey)
    {
        // Updated to remove filterStatus check
        if (in_array($filterKey, ['filterGender'])) {
            $this->reset($filterKey); // Reset gender to default ''
        } else {
            $this->$filterKey = null; // Reset other filters to null
        }
        $this->resetPage();
        $this->success('Filter removed.');
    }

    public function resetFilters()
    {
        // Updated to include all filter properties
        $this->reset(
            'filterStudentTypeId',
            'filterStudentStatusId',
            'filterGender',
            'filterOfficeId',
            'filterEnrolmentDate',
            'filterPassoutDate',
            'filterDobStart',
            'filterDobEnd',
            'filterJobNatureId',
            'filterQualification',
            'filterSpecialization',
            'filterTerminationDateStart',
            'filterTerminationDateEnd', // Added new filters
        );
        $this->resetPage();
        $this->success('Filters reset.');
    }

    // Fetch students using the service
    private function getStudents(StudentService $studentService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
            // Add specific student filters
            'student_type_id' => $this->filterStudentTypeId,
            'student_work_status_id' => $this->filterStudentStatusId,
            'gender' => $this->filterGender,
            // Removed 'status' => $this->filterStatus,
            'office_id' => $this->filterOfficeId,
            'appointment_date_start' => $this->filterEnrolmentDate,
            'appointment_date_end' => $this->filterPassoutDate,
            'dob_start' => $this->filterDobStart,
            'dob_end' => $this->filterDobEnd,
            'job_nature_id' => $this->filterJobNatureId,
            'qualification' => $this->filterQualification, // Pass qualification filter
            'specialization' => $this->filterSpecialization, // Pass specialization filter
            'termination_date_start' => $this->filterTerminationDateStart, // Pass termination date filter
            'termination_date_end' => $this->filterTerminationDateEnd, // Pass termination date filter
            // Add other filters supported by StudentService->applyFilters
        ];

        // Clean up null/empty filters before passing to service
        // Removed 'status' from in_array check
        $filterParams = array_filter(
            $filterParams,
            function ($value, $key) {
                return ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir', 'per_page']);
            },
            ARRAY_FILTER_USE_BOTH,
        );

        // Ensure boolean/empty string values are correctly set if they were filtered out
        $filterParams['search'] = !empty($this->search);
        // Removed line setting $filterParams['status']
        $filterParams['gender'] = $this->filterGender ?? '';
        $filterParams['qualification'] = $this->filterQualification ?? ''; // Ensure empty string if null
        $filterParams['specialization'] = $this->filterSpecialization ?? ''; // Ensure empty string if null
        $filterParams['with_trashed'] = $this->showDeletedRecords;

        // Define default eager loads needed for the table/view
        $filterParams['with'] = ['studentType', 'studentStudentStatus', 'jobNature', 'primaryOffice']; // Eager load jobNature directly

        return $studentService->getPaginatedStudents($filterParams);
    }

    // Render the view
    public function render(): mixed
    {
        \Log::info('[Livewire Render] Rendering Student List.'); // <-- Add this line
        $students = $this->getStudents(app(StudentService::class)); // Inject service

        // Update selectAll state based on current page items
        $currentPageIds = $students->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedStudents));

        return view('livewire.students', [
            'students' => $students, // Pass students to the view
            'headers' => $this->headers,
        ]);
    }

    /**
     * Opens the generic export configuration modal.
     */
    public function openExportModal()
    {
        $this->exportingSelectedOnly = false; // Ensure context is reset for 'Export All'
        $this->showGenericExportModal = true;
    }

    /**
     * Handles the export request triggered from the generic export modal.
     * Listens for the 'start-student-export' event dispatched from AlpineJS.
     *
     * @param StudentService $studentService Injected service.
     * @param array $eventData Contains 'format' (string) and 'selectedColumns' (array).
     * @param array $eventData Contains 'format' (string) and 'selectedColumns' (array).
     * @return BinaryFileResponse|void
     */

    #[On('start-student-export')] 
    public function handleStudentExport()
    {
        // Manually resolve StudentService
        $studentService = app(StudentService::class);

        // Read data from public properties
        $format = $this->exportFormat;
        $selectedColumnsData = $this->exportColumnsData;

        // Reset properties after reading (optional but good practice)
        $this->exportFormat = null;
        $this->exportColumnsData = [];

        // Validate extracted data
        if (empty($format) || empty($selectedColumnsData)) {
            \Log::error('Student Export Error: Invalid format or column data read from properties.', ['format' => $format, 'columns' => $selectedColumnsData]);
            $this->error('Export failed due to missing format or column data.');
            $this->showGenericExportModal = false; // Close modal on error
            return;
        }

        // Validate that selectedColumnsData is not empty (it should be an array of {key, label})
        if (empty($selectedColumnsData)) {
            $this->error('No columns selected for export.');
            $this->showGenericExportModal = false; // Close modal on error
            return;
        }

        // 1. Gather current filters and sorting state
        $filters = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            // Add specific student filters from component state
            'student_type_id' => $this->filterStudentTypeId,
            'student_work_status_id' => $this->filterStudentStatusId,
            'gender' => $this->filterGender,
            'office_id' => $this->filterOfficeId,
            'appointment_date_start' => $this->filterEnrolmentDate,
            'appointment_date_end' => $this->filterPassoutDate,
            'dob_start' => $this->filterDobStart,
            'dob_end' => $this->filterDobEnd,
            'job_nature_id' => $this->filterJobNatureId,
            'qualification' => $this->filterQualification,
            'specialization' => $this->filterSpecialization,
            'termination_date_start' => $this->filterTerminationDateStart,
            'termination_date_end' => $this->filterTerminationDateEnd,
        ];

        // Clean up null/empty filters (similar to getStudents method)
        $filters = array_filter(
            $filters,
            function ($value, $key) {
                return ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir']);
            },
            ARRAY_FILTER_USE_BOTH,
        );
        // Ensure boolean/empty string values are correctly set
        $filters['search'] = !empty($this->search);
        $filters['gender'] = $this->filterGender ?? '';
        $filters['qualification'] = $this->filterQualification ?? '';
        $filters['specialization'] = $this->filterSpecialization ?? '';
        $filters['with_trashed'] = $this->showDeletedRecords;

        // 2. Handle exporting only selected students
        if ($this->exportingSelectedOnly && !empty($this->selectedStudents)) {
            $filters['ids'] = array_map('intval', $this->selectedStudents); // Add ID filter
        }

        // 3. Generate filename
        $timestamp = now()->format('Ymd_His');
        $filename = "students_{$timestamp}.{$format}";

        // 4. Trigger download via StudentService
        try {
            $this->success('Export started... Please wait. ⏳');
            $this->showGenericExportModal = false; // Close modal immediately
            $this->exportingSelectedOnly = false; // Reset context flag

            // Call the service method with the correct arguments
            // The service method handles query building, filename generation, and relation loading internally based on filters and columns.
            return $studentService->exportStudents(
                $format,             // 1st arg: format (string)
                $filters,            // 2nd arg: filters (array)
                $selectedColumnsData, // 3rd arg: selectedColumns (array of {key, label})
                'Student Report', // 4th arg: title (string)
                "Selected Students", // 5th arg: subtitle (string)
            );
        } catch (\Exception $e) {
            \Log::error('Student Export Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'selectedColumnsData' => $selectedColumnsData,
                'format' => $format,
                'exception' => $e,
            ]);
            $this->error('An error occurred during the export process. Please check logs.');
            $this->showGenericExportModal = false; // Close modal on error
            $this->exportingSelectedOnly = false; // Reset context flag
        }
    }

   
};

?>

<!-- Main Container -->
<div wire:id="{{ $this->getId() }}"> {{-- Explicitly add wire:id --}}

    <!-- Header -->
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Student Management" icon="o-users" icon-classes="bg-warning rounded-full p-1 w-8 h-8"
        subtitle="Total Students: {{ $students->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}"
        subtitle-class="mr-2 mt-0.5 ">

        <x-slot:middle class="!justify-end">
            <x-button label="Test Listener" onclick="Livewire.dispatch('test-event')" />

            <x-input placeholder="Search Students..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add Student"
                label="Add Student" />
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner tooltip-left="Filters" />
            {{-- Export Button triggers modal --}}
            <x-button icon="o-arrow-down-tray" wire:click="openExportModal"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner="openExportModal"
                tooltip-left="Export Data..." />
        </x-slot:actions>
    </x-header>

    <!-- Active Filters Display -->
    @if (count($this->activeFilters))
        <div
            class="px-4 py-2 bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Active Filters:</span>
            @foreach ($this->activeFilters as $filter)
                <x-badge class="badge-primary badge-xs font-semibold gap-1"
                    value="{{ $filter['label'] }} : {{ $filter['value'] }}" />
            @endforeach
            <x-button label="Clear All" wire:click="resetFilters" class="btn-ghost btn-xs text-red-500" spinner />
        </div>
    @endif

    <!-- Filters and Bulk Actions Row -->
    <div
        class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <!-- Filters -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error"
                hint="{{ $showDeletedRecords ? 'Showing deleted' : '' }}" />

            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <x-select wire:model.live="perPage" :options="[
                    ['id' => 5, 'name' => 5],
                    ['id' => 10, 'name' => 10],
                    ['id' => 25, 'name' => 25],
                    ['id' => 50, 'name' => 50],
                    ['id' => 100, 'name' => 100],
                ]"
                    class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>

        @if (count($selectedStudents))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Perform a bulk action" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'assignOffice', 'name' => 'Assign Office...'], // Added
                    ['id' => 'updateStudentStatus', 'name' => 'Update Work Status...'], // Added
                    ['id' => 'exportSelected', 'name' => 'Export Selected...'], // Added
                    // Removed 'bulkToggleActive' option
                    ...$showDeletedRecords
                        ? [
                            ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                            ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete'],
                        ]
                        : [],
                ]"
                    class="select select-bordered select-sm py-0" id="bulk-action-select" x-data
                    x-on:change="$wire.handleBulkAction($event.target.value)" x-init="$watch('$wire.selectedStudents', value => { if (value.length === 0) $el.value = ''; })"
                    x-on:reset-bulk-action.window="$el.value = ''" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedStudents) }} selected
                </span>
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
                                @if (
                                    ($header['sortable'] ?? true) &&
                                        ($sortField === $header['key'] ||
                                            ($header['key'] === 'full_name' && in_array($sortField, ['first_name', 'last_name']))))
                                    <x-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}"
                                        class="h-3 w-3" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($students as $student)
                    <tr wire:key="student-{{ $student->id }}"
                        class="{{ $student->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150 ease-in-out">
                        {{-- Added transition --}}
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedStudents" value="{{ (string) $student->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        <!-- Student Info (Combined) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-4"> {{-- Added space-x-4 --}}
                                <div class="flex-shrink-0">
                                    @php
                                        // Check if photo_path is an absolute URL or relative path
                                        $photoUrl = $student->photo_path
                                            ? (Str::startsWith($student->photo_path, ['http://', 'https://'])
                                                ? $student->photo_path // Use directly if absolute URL
                                                : asset('storage/' . $student->photo_path)) // Prepend storage if relative
                                            : 'https://ui-avatars.com/api/?name=' .
                                                urlencode($student->first_name . ' ' . $student->last_name) .
                                                '&color=FFFFFF&background=0D8ABC&bold=true&size=64'; // Fallback
                                    @endphp
                                    <img class="h-12 w-12 rounded-full object-cover shadow-sm"
                                        src="{{ $photoUrl }}"
                                        alt="{{ $student->first_name }} {{ $student->last_name }}">
                                </div>
                                <div class="flex-grow">
                                    {{-- Line 1: Name --}}
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </div>

                                    {{-- Line 2: ID and Type Badge --}}
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center space-x-2 flex-wrap">
                                        <span>ID: {{ $student->student_id ?? 'N/A' }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        {{-- Student Type Badge --}}
                                        @php
                                            $type = $student->studentType?->name;
                                            $typeColorClasses = match ($type) {
                                                'Permanent'
                                                    => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'Contract'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'Intern'
                                                    => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                                default
                                                    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-0.5 inline-flex text-[11px] leading-4 font-medium rounded-full {{ $typeColorClasses }}">
                                            {{ $type ?? 'N/A' }}
                                        </span>
                                    </div>

                                    {{-- Line 3: Primary Office --}}
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                        <x-icon name="o-building-office"
                                            class="w-3 h-3 mr-1 text-gray-400 dark:text-gray-500" />
                                        {{ $student->primaryOffice->first()?->name ?? 'No Office' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!-- Contact (Email, Phone) - Slightly smaller text -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1"> {{-- Added space-y-1 --}}
                                <div class="text-xs text-gray-700 dark:text-gray-300 flex items-center">
                                    {{-- Reduced font size --}}
                                    <x-icon name="o-envelope"
                                        class="w-3.5 h-3.5 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    {{-- Adjusted icon size/margin --}}
                                    {{ $student->email ?? 'No Email' }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                    {{-- Reduced font size --}}
                                    <x-icon name="o-phone"
                                        class="w-3.5 h-3.5 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    {{-- Adjusted icon size/margin --}}
                                    {{ $student->phone ?? 'No Phone' }}
                                </div>
                            </div>
                        </td>
                        <!-- Job Nature -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                {{ $student->jobNature?->name ?? 'N/A' }} {{-- Display job nature directly --}}
                            </span>
                        </td>
                        <!-- Work Status - Adjusted Badge Style -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            {{-- Dynamic Badge Color for Work Status - Lighter Backgrounds --}}
                            @php
                                $status = $student->studentStudentStatus?->name;
                                $statusColorClasses = match ($status) {
                                    'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'On Leave'
                                        => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'Terminated' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'Probation' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                    'Earned Leave'
                                        => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    'Suspended'
                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'Resigned' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'Retired' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'Contract Expired'
                                        => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'Internship'
                                        => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                    'Demoted'
                                        => 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100', // Slightly darker red
                                    'Transferred' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'Removal From Service'
                                        => 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100', // Slightly darker red
                                    'Relieved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',

                                    default
                                        => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200', // Default/Fallback
                                };
                            @endphp
                            <span
                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full {{ $statusColorClasses }}">
                                {{-- Adjusted font --}}
                                {{ $status ?? 'N/A' }}
                            </span>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1.5">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $student->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" />

                                {{-- Added History Button --}}
                                <x-button icon="o-building-library"
                                    wire:click="openHistoryModal({{ $student->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                    spinner tooltip-left="Employment History" />

                                @if (!$student->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $student->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner tooltip-left="Edit Student" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $student->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Delete Student" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $student->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner tooltip-left="Restore Student" />
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $student->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Permanently Delete Student" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}" {{-- +2 for checkbox and actions --}}
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-16 w-16 text-gray-400 mb-4" />
                                <span class="text-lg font-medium">No students found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new student.' }}
                                </p>
                                @if ($search)
                                    <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm"
                                        label="Clear Search" />
                                @else
                                    <x-button wire:click="openModal" class="mt-3 btn btn-primary btn-sm"
                                        label="Add Your First Student" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div
        class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
        {{ $students->links() }}
    </div>

    <!-- Add/Edit Student Modal -->
    <x-modal wire:model="showModal" :title="$studentId ? 'Edit Student' : 'Add New Student'" box-class="max-w-4xl" separator persistent
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab">

                <!-- Basic Info Tab -->
                <x-tab name="basic" label="Basic Info" icon="o-identification" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                        <x-input wire:model="student_id_field" label="Student ID"
                            placeholder="Unique ID (e.g., EMP-001)" icon="o-hashtag" inline required />
                        <x-input wire:model="first_name" label="First Name" placeholder="Enter first name"
                            icon="o-user" inline required />
                        <x-input wire:model="last_name" label="Last Name" placeholder="Enter last name"
                            icon="o-user" inline />
                        <x-input wire:model="email" label="Email" type="email" placeholder="Enter email address"
                            icon="o-envelope" inline required />
                        <x-input wire:model="phone" label="Phone" placeholder="Enter phone number" icon="o-phone"
                            inline />
                        <x-select wire:model="gender" label="Gender" :options="[
                            ['id' => 'Male', 'name' => 'Male'],
                            ['id' => 'Female', 'name' => 'Female'],
                            ['id' => 'Other', 'name' => 'Other'],
                        ]" placeholder="Select gender"
                            icon="o-user-group" inline required />
                        <x-input wire:model="nic_no" label="NIC Number" placeholder="e.g., 12345-1234567-1"
                            icon="o-identification" inline />
                        <x-datepicker wire:model="date_of_birth" label="Date of Birth" icon="o-calendar" inline />
                        {{-- Removed Active Status Toggle --}}
                    </div>
                </x-tab>

                <!-- Employment Details Tab -->
                <x-tab name="employment" label="Employment" icon="o-briefcase" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        <x-select wire:model="student_type_id" label="Student Type" :options="$studentTypes"
                            placeholder="Select type" icon="o-tag" inline required />
                        <x-select wire:model="student_work_status_id" label="Work Status" :options="$studentStudentStatuses"
                            placeholder="Select status" icon="o-building-office-2" inline required />
                        <x-select wire:model="job_nature_id" label="Job Nature" :options="$jobNatures"
                            placeholder="Select job nature" icon="o-briefcase" inline required />
                        {{-- Added Job Nature Select --}}
                        <x-datepicker wire:model="appointment_date" label="Appointment Date" icon="o-calendar-days"
                            inline />
                        <x-datepicker wire:model="termination_date" label="Termination Date" icon="o-calendar-days"
                            inline />
                        <x-input wire:model="qualification" label="Qualification" placeholder="Highest qualification"
                            icon="o-academic-cap" inline />
                        <x-input wire:model="specialization" label="Specialization" placeholder="Area of expertise"
                            icon="o-sparkles" inline />
                    </div>
                </x-tab>

                <!-- Address Tab -->
                <x-tab name="address" label="Address" icon="o-map-pin" x-cloak>
                    <div class="grid grid-cols-1 gap-4 p-4">
                        <x-textarea wire:model="postal_address" label="Postal Address"
                            placeholder="Enter postal address" rows="3" icon="o-envelope-open" inline />
                        <x-textarea wire:model="permanent_address" label="Permanent Address"
                            placeholder="Enter permanent address" rows="3" icon="o-home" inline />
                    </div>
                </x-tab>

                <!-- Bio & Photo Tab -->
                <x-tab name="bio_photo" label="Bio & Photo" icon="o-user-circle" x-cloak>
                    <div class="grid grid-cols-1 gap-4 p-4">
                        <!-- Photo Upload/Display Placeholder -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo</label>
                            <!-- Basic display of path, replace with actual upload component if needed -->
                            @if ($photo_path)
                                <img src="{{ asset('storage/' . $photo_path) }}" alt="Student Photo"
                                    class="h-20 w-20 rounded-full object-cover mb-2 shadow-sm"> {{-- Added shadow-sm --}}
                            @else
                                <div
                                    class="h-20 w-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-2 shadow-sm">
                                    {{-- Added shadow-sm --}}
                                    <x-icon name="o-photo" class="h-10 w-10 text-gray-400" />
                                </div>
                            @endif
                            <x-input wire:model="photo_path" label="Photo Path (Manual)"
                                placeholder="path/to/photo.jpg" icon="o-photo" inline
                                hint="Manual path entry for now. Implement file upload later." />
                            {{-- <x-file wire:model="new_photo" label="Upload New Photo" accept="image/png, image/jpeg" /> --}}
                        </div>
                        <x-textarea wire:model="bio" label="Bio" placeholder="Short biography" rows="4"
                            icon="o-document-text" inline />
                    </div>
                </x-tab>

            </x-tabs>
        </x-form>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button type="submit" label="{{ $studentId ? 'Update Student' : 'Create Student' }}"
                class="btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Student Modal -->
    <x-modal wire:model="showViewModal" title="View Student Details" separator box-class="max-w-4xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($viewStudent)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div class="flex items-center mb-3 sm:mb-0">
                        @php
                            // Check if photo_path is an absolute URL or relative path
                            $viewPhotoUrl = $viewStudent->photo_path
                                ? (Str::startsWith($viewStudent->photo_path, ['http://', 'https://'])
                                    ? $viewStudent->photo_path // Use directly if absolute URL
                                    : asset('storage/' . $viewStudent->photo_path)) // Prepend storage if relative
                                : 'https://ui-avatars.com/api/?name=' .
                                    urlencode($viewStudent->first_name . ' ' . $viewStudent->last_name) .
                                    '&color=7F9CF5&background=EBF4FF'; // Fallback
                        @endphp
                        <img class="h-16 w-16 rounded-full object-cover mr-4 shadow-sm" src="{{ $viewPhotoUrl }}"
                            alt="{{ $viewStudent->first_name }} {{ $viewStudent->last_name }}">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $viewStudent->first_name }} {{ $viewStudent->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $viewStudent->student_id }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center justify-start sm:justify-end w-full sm:w-auto">
                        {{-- Dynamic Badge Color for Student Type (View Modal) - Lighter Backgrounds --}}
                        @php
                            $viewType = $viewStudent->studentType?->name;
                            $viewTypeColorClasses = match ($viewType) {
                                'Permanent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'Contract' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'Intern' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            };
                        @endphp
                        <span title="Student Type"
                            class="inline-flex items-center px-3 py-1 shadow-sm rounded-full text-xs font-medium {{ $viewTypeColorClasses }}">
                            {{-- Adjusted shadow/font --}}
                            <x-icon name="o-tag" class="h-3.5 w-3.5 mr-1" /> {{-- Slightly larger icon --}}
                            {{ $viewType ?? 'N/A' }}
                        </span>
                        {{-- Dynamic Badge Color for Job Nature (View Modal) --}}
                        @php
                            $viewJobNature = $viewStudent->jobNature?->name;
                            // Define color classes based on job nature if needed, otherwise use a default
                            $viewJobNatureColorClasses =
                                'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200'; // Example color
                        @endphp
                        <span title="Job Nature"
                            class="inline-flex items-center px-3 py-1 shadow-sm rounded-full text-xs font-medium {{ $viewJobNatureColorClasses }}">
                            <x-icon name="o-briefcase" class="h-3.5 w-3.5 mr-1" />
                            {{ $viewJobNature ?? 'N/A' }}
                        </span>
                        {{-- Dynamic Badge Color for Work Status (View Modal) - Lighter Backgrounds --}}
                        @php
                            $viewStatus = $viewStudent->studentStudentStatus?->name;
                            $viewStatusColorClasses = match ($viewStatus) {
                                'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'On Leave' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                'Terminated' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'Probation' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                'Earned Leave'
                                    => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                'Suspended' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'Resigned' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                'Retired' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                'Contract Expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                'Internship' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                'Demoted' => 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100',
                                'Transferred' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'Removal From Service' => 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-100',
                                'Relieved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            };
                        @endphp
                        <span title="Work Status"
                            class="inline-flex items-center px-3 py-1 shadow-sm rounded-full text-xs font-medium {{ $viewStatusColorClasses }}">
                            {{-- Adjusted shadow/font --}}
                            <x-icon name="o-building-office-2" class="h-3.5 w-3.5 mr-1" /> {{-- Slightly larger icon --}}
                            {{ $viewStatus ?? 'N/A' }}
                        </span>
                        {{-- Removed Status Badge --}}
                        @if ($viewStudent->deleted_at)
                            <span title="Deleted Status"
                                class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" />
                                Deleted
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                {{-- Changed grid to use fractional units for a 2:2:3 ratio on medium screens --}}
                <div class="grid grid-cols-1 md:grid-cols-[2fr_2fr_3fr] gap-x-6 gap-y-5 pt-4">

                    <!-- Column 1: Contact & Personal -->
                    <div class="space-y-4">
                        <!-- Contact (Email & Phone) -->
                        <div class="flex items-start">
                            <x-icon name="o-identification"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            {{-- Using identification icon for general contact --}}
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Contact
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center">
                                    <x-icon name="o-envelope" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" />
                                    {{ $viewStudent->email ?: 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center mt-1">
                                    <x-icon name="o-phone" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" />
                                    {{ $viewStudent->phone ?: 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        <!-- Gender -->
                        <div class="flex items-start">
                            <x-icon name="o-user-group"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Gender
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->gender ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <!-- NIC Number -->
                        <div class="flex items-start">
                            <x-icon name="o-identification"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">NIC
                                    Number</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->nic_no ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Date of Birth -->
                        <div class="flex items-start">
                            <x-icon name="o-cake"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Date of
                                    Birth</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->date_of_birth ? $viewStudent->date_of_birth->format('M d, Y') : 'Not specified' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Employment -->
                    <div class="space-y-4">
                        <!-- Appointment Date -->
                        <div class="flex items-start">
                            <x-icon name="o-calendar-days"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Appointment Date</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->appointment_date ? $viewStudent->appointment_date->format('M d, Y') : 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        <!-- Termination Date -->
                        <div class="flex items-start">
                            <x-icon name="o-calendar-days"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Termination Date</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->termination_date ? $viewStudent->termination_date->format('M d, Y') : 'Not applicable' }}
                                </p>
                            </div>
                        </div>
                        <!-- Qualification -->
                        <div class="flex items-start">
                            <x-icon name="o-academic-cap"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Qualification</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->qualification ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Specialization -->
                        <div class="flex items-start">
                            <x-icon name="o-sparkles"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Specialization</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->specialization ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Primary Office -->
                        <div class="flex items-start">
                            <x-icon name="o-building-office"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Primary
                                    Office</div>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                    {{ $viewStudent->primaryOffice->first()?->name ?? 'Not Assigned' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Address & Bio -->
                    <div class="space-y-4"> {{-- Removed md:col-span-2 as grid definition handles ratio --}}
                        <!-- Postal Address -->
                        <div class="flex items-start">
                            <x-icon name="o-envelope-open"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Postal
                                    Address</div>
                                <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">
                                    {{ $viewStudent->postal_address ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Permanent Address -->
                        <div class="flex items-start">
                            <x-icon name="o-home"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Permanent
                                    Address</div>
                                <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">
                                    {{ $viewStudent->permanent_address ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Bio -->
                        <div class="flex items-start">
                            <x-icon name="o-document-text"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Bio</div>
                                <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">
                                    {{ $viewStudent->bio ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Timestamps Section -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1">
                            <x-icon name="o-calendar" class="h-4 w-4" />
                            <span>Created:
                                {{ $viewStudent->created_at ? $viewStudent->created_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <x-icon name="o-pencil-square" class="h-4 w-4" />
                            <span>Updated:
                                {{ $viewStudent->updated_at ? $viewStudent->updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        @if ($viewStudent->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                <x-icon name="o-archive-box-x-mark" class="h-4 w-4" />
                                <span>Deleted: {{ $viewStudent->deleted_at->format('M d, Y h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div> {{-- End p-6 space-y-6 --}}
        @else
            <!-- Skeleton Loader -->
            <div class="p-8 flex justify-center items-center">
                <div class="w-full max-w-2xl animate-pulse space-y-6">
                    <!-- Header Skeleton -->
                    <div class="flex items-center pb-4 border-b dark:border-gray-700">
                        <div class="h-16 w-16 bg-gray-300 dark:bg-gray-700 rounded-full mr-4"></div>
                        <div class="flex-grow">
                            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-24"></div>
                            <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-20"></div>
                        </div>
                    </div>
                    <!-- Details Grid Skeleton -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                        @foreach (range(1, 3) as $_)
                            <div class="space-y-5">
                                @foreach (range(1, 4) as $__)
                                    <div class="flex items-start">
                                        <div class="h-5 w-5 bg-gray-300 dark:bg-gray-700 rounded mr-2 mt-0.5"></div>
                                        <div class="flex-grow">
                                            <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-20 mb-1.5"></div>
                                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <!-- Timestamps Skeleton -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-40"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-40"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <div class="flex justify-end gap-3 w-full">
                @if ($viewStudent && !$viewStudent->deleted_at)
                    <x-button label="Edit Student" icon="o-pencil" wire:click="openModal({{ $viewStudent->id }})"
                        class="btn-primary" spinner />
                @endif
                <x-button label="Close" wire:click="closeHistoryModal()" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Employment History Modal -->
    <x-modal wire:model="showHistoryModal" title="Employment History" separator box-class="max-w-3xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($historyStudent)
            <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    History for: {{ $historyStudent->first_name }} {{ $historyStudent->last_name }}
                    ({{ $historyStudent->student_id }})
                </h3>

                @forelse($historyStudent->offices->sortBy('pivot.start_date') as $officeRecord)
                    {{-- Use $officeRecord to avoid confusion with $record --}}
                    <div
                        class="border dark:border-gray-700 rounded-lg p-4 mb-3 shadow-sm bg-gray-50 dark:bg-gray-800/50">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-2">
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Office
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $officeRecord->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                    Designation</div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $officeRecord->pivot->role ?? 'N/A' }}</p>
                            </div>
                            {{-- Removed Job Nature from history modal --}}
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Start
                                    Date</div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $officeRecord->pivot->start_date ? \Carbon\Carbon::parse($officeRecord->pivot->start_date)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">End Date
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $officeRecord->pivot->end_date ? \Carbon\Carbon::parse($officeRecord->pivot->end_date)->format('M d, Y') : 'Present' }}
                                </p>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Is
                                    Primary</div>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">
                                    @if ($officeRecord->pivot->is_primary_office)
                                        <x-icon name="o-check-circle" class="w-5 h-5 text-green-500 inline-block" />
                                        Yes
                                    @else
                                        <x-icon name="o-x-circle" class="w-5 h-5 text-red-500 inline-block" /> No
                                    @endif
                                </p>
                            </div>
                        </div>
                        {{-- Display remarks from the pivot table --}}
                        @if ($officeRecord->pivot->remarks)
                            <div class="mt-3 pt-2 border-t dark:border-gray-700">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Remarks
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-wrap">
                                    {{ $officeRecord->pivot->remarks }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                        <x-icon name="o-inbox" class="h-12 w-12 mx-auto text-gray-400 mb-3" />
                        No employment history found for this student.
                    </div>
                @endforelse
            </div>
        @else
            <!-- Optional: Loading state for history modal -->
            <div class="p-8 flex justify-center items-center">
                <x-loading class="loading-lg text-primary" />
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" wire:click="closeModal" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (Delete, Restore, Permanent Delete - Single & Bulk) -->
    <!-- Delete Confirmation -->
    <x-modal wire:model="confirmingDeletion" title="Delete Student" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this student? This action will soft delete the record.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete" wire:click="delete" class="btn-error" wire:loading.attr="disabled"
                wire:target="delete" />
        </x-slot:actions>
    </x-modal>

    <!-- Bulk Delete Confirmation -->
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Students" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedStudents) }} selected students? This action
                    will soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedStudents) }} Students" wire:click="bulkDelete"
                class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Restore Confirmation -->
    <x-modal wire:model="confirmingRestore" title="Restore Student" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this student?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore" wire:click="restore" class="btn-success" wire:loading.attr="disabled"
                wire:target="restore" />
        </x-slot:actions>
    </x-modal>

    <!-- Bulk Restore Confirmation -->
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Students" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedStudents) }} selected students?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedStudents) }} Students" wire:click="bulkRestore"
                class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    <!-- Permanent Delete Confirmation -->
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Student" separator persistent
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this student? This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="permanentDelete" class="btn-error"
                wire:loading.attr="disabled" wire:target="permanentDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Bulk Permanent Delete Confirmation -->
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Students" separator
        persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedStudents) }} selected students?
                    This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedStudents) }} Students"
                wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled"
                wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Advanced Filter Drawer -->
    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button
        class="w-11/12 lg:w-1/3 bg-gray-50 dark:bg-gray-900"> {{-- Added background color --}}
        <div class="px-4 space-y-4"> {{-- Increased padding and spacing --}}

            {{-- Section 1: Core Attributes --}}
            <div class="space-y-5"> {{-- Increased spacing within section --}}
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Core Attributes</div>
                <x-select wire:model.live="filterStudentTypeId" label="Student Type" :options="$studentTypes"
                    placeholder="All Types" icon="o-tag" clearable inline />
                <x-select wire:model.live="filterStudentStatusId" label="Work Status" :options="$studentStudentStatuses"
                    placeholder="All Statuses" icon="o-building-office-2" clearable inline />
                <x-select wire:model.live="filterJobNatureId" label="Job Nature" :options="$jobNatures"
                    placeholder="All Natures" icon="o-briefcase" clearable inline />
                <x-select wire:model.live="filterGender" label="Gender" :options="[
                    ['id' => '', 'name' => 'All'],
                    ['id' => 'Male', 'name' => 'Male'],
                    ['id' => 'Female', 'name' => 'Female'],
                    ['id' => 'Other', 'name' => 'Other'],
                ]" placeholder="All Genders"
                    icon="o-user-group" clearable inline />
            </div>

            {{-- Divider --}}
            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Section 2: Assignment --}}
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Assignment & Skills</div>
                {{-- Renamed Section --}}
                <x-select wire:model.live="filterOfficeId" label="Assigned Office" :options="$offices"
                    placeholder="Any Office" icon="o-building-office" clearable inline />
                <x-input wire:model.live.debounce="filterQualification" label="Qualification"
                    placeholder="e.g., BSc, PhD" icon="o-academic-cap" clearable inline /> {{-- Added Qualification Input --}}
                <x-input wire:model.live.debounce="filterSpecialization" label="Specialization"
                    placeholder="e.g., Accounting" icon="o-sparkles" clearable inline /> {{-- Added Specialization Input --}}
            </div>

            {{-- Divider --}}
            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Section 3: Dates --}}
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Date Ranges</div>
                {{-- Appointment Date Range --}}
                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800/50">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appointment
                        Date</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-datepicker wire:model.live="filterEnrolmentDate" placeholder="From"
                            icon="o-calendar-days" clearable />
                        <x-datepicker wire:model.live="filterPassoutDate" placeholder="To"
                            icon="o-calendar-days" clearable />
                    </div>
                </div>

                {{-- Date of Birth Range --}}
                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800/50">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of
                        Birth</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-datepicker wire:model.live="filterDobStart" placeholder="From" icon="o-cake" clearable />
                        <x-datepicker wire:model.live="filterDobEnd" placeholder="To" icon="o-cake" clearable />
                    </div>
                </div>

                {{-- Termination Date Range --}}
                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800/50">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Termination
                        Date</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-datepicker wire:model.live="filterTerminationDateStart" placeholder="From"
                            icon="o-calendar-days" clearable />
                        <x-datepicker wire:model.live="filterTerminationDateEnd" placeholder="To"
                            icon="o-calendar-days" clearable />
                    </div>
                </div>
            </div>

        </div> {{-- End p-6 space-y-8 --}}

        {{-- Actions are placed outside the scrolling content area by MaryUI's drawer --}}
        <x-slot:actions>
            <div
                class="flex justify-between w-full px-4 py-3 border-t dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                {{-- Added background/padding to actions --}}
                <x-button label="Reset Filters" icon="o-arrow-path" wire:click="resetFilters"
                    class="btn-ghost text-red-500" spinner />
                <x-button label="Apply Filters" icon="o-check" class="btn-primary"
                    @click="$wire.showFilterDrawer = false" /> {{-- Changed label and action --}}
            </div>
        </x-slot:actions>
    </x-drawer>

    <!-- Export Format Selection Modal -->
    <x-modal wire:model="showGenericExportModal" title="Configure Export" separator persistent box-class="max-w-3xl"
        class="backdrop-blur-sm">
        <div x-data="{
            availableColumns: {{ json_encode($studentExportColumns) }},
            selectedColumns: {{ json_encode($defaultExportColumns) }}, // Array of keys in order
            get orderedSelectedFullColumns() {
                return this.selectedColumns
                    .map(key => this.availableColumns.find(col => col.key === key))
                    .filter(col => col !== undefined);
            },
            isColumnSelected(key) {
                return this.selectedColumns.includes(key);
            },
            toggleColumn(key) {
                if (this.isColumnSelected(key)) {
                    this.selectedColumns = this.selectedColumns.filter(item => item !== key);
                } else {
                    this.selectedColumns.push(key);
                }
            },
            moveUp(index) {
                if (index > 0) {
                    [this.selectedColumns[index - 1], this.selectedColumns[index]] = [this.selectedColumns[index], this.selectedColumns[index - 1]];
                }
            },
            moveDown(index) {
                if (index < this.selectedColumns.length - 1) {
                    [this.selectedColumns[index + 1], this.selectedColumns[index]] = [this.selectedColumns[index], this.selectedColumns[index + 1]];
                }
            },
            selectAllColumns() {
                this.selectedColumns = this.availableColumns.map(col => col.key);
            },
            deselectAllColumns() {
                this.selectedColumns = [];
            },
            resetToDefaults() {
                this.selectedColumns = {{ json_encode($defaultExportColumns) }};
            },
            // Removed triggerExport(format) as dispatch is now inline on buttons
        }" x-init="$watch('$wire.showGenericExportModal', value => {
            if (value) {
                // Reset selection to defaults when modal opens, or retain previous state?
                // For now, let's reset to defaults.
                selectedColumns = {{ json_encode($defaultExportColumns) }};
            }
        })">

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Side: Available Columns -->
                <div>
                    <h4 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Available Columns</h4>
                    <div class="mb-2 flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Select columns to include:</span>
                        <div>
                            <x-button label="Select All" x-on:click="selectAllColumns" class="btn-xs btn-ghost" />
                            <x-button label="Deselect All" x-on:click="deselectAllColumns"
                                class="btn-xs btn-ghost" />
                        </div>
                    </div>
                    <div
                        class="max-h-80 overflow-y-auto border dark:border-gray-700 rounded-md p-3 space-y-2 bg-gray-50 dark:bg-gray-800/50">
                        <template x-for="column in availableColumns" :key="column.key">
                            <label
                                class="flex items-center space-x-2 p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" :value="column.key" x-model="selectedColumns"
                                    class="checkbox checkbox-sm checkbox-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="column.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Right Side: Selected & Ordered Columns -->
                <div>
                    <h4 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Selected Columns (Drag to
                        Reorder - Placeholder)</h4>
                    <div class="mb-2 flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Order of columns in export:</span>
                        <x-button label="Reset Order" x-on:click="resetToDefaults" class="btn-xs btn-ghost" />
                    </div>
                    <div
                        class="max-h-80 overflow-y-auto border dark:border-gray-700 rounded-md p-3 space-y-1 bg-white dark:bg-gray-800">
                        <template x-if="selectedColumns.length === 0">
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                Please select columns from the left.
                            </div>
                        </template>
                        <template x-for="(column, index) in orderedSelectedFullColumns" :key="column.key">
                            <div
                                class="flex items-center justify-between p-1.5 rounded bg-gray-100 dark:bg-gray-700 group">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                    x-text="column.label"></span>
                                <div
                                    class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button x-on:click="moveUp(index)" :disabled="index === 0"
                                        class="btn btn-xs btn-ghost p-1 disabled:opacity-30">
                                        <x-icon name="o-chevron-up" class="w-4 h-4" />
                                    </button>
                                    <button x-on:click="moveDown(index)"
                                        :disabled="index === selectedColumns.length - 1"
                                        class="btn btn-xs btn-ghost p-1 disabled:opacity-30">
                                        <x-icon name="o-chevron-down" class="w-4 h-4" />
                                    </button>
                                    <button x-on:click="toggleColumn(column.key)"
                                        class="btn btn-xs btn-ghost p-1 text-red-500 hover:text-red-700">
                                        <x-icon name="o-x-mark" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Format Selection -->
            <div class="p-6 border-t dark:border-gray-700 mt-4">
                <h4 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white text-center">Select Export Format
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- PDF Export Button (Standard) --}}
                    <div x-on:click="
                            if (selectedColumns.length === 0) {
                                alert('Please select at least one column to export.');
                            } else {
                                @this.set('exportFormat', 'pdf');
                                @this.set('exportColumnsData', orderedSelectedFullColumns);
                                Livewire.dispatch('start-student-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleStudentExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-text"
                            class="w-10 h-10 text-red-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as PDF (Standard)</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generates a formatted PDF document.</p>
                        <div wire:loading wire:target="handleStudentExport" class="mt-2">
                            <x-loading class="loading-sm text-red-500" />
                        </div>
                    </div>

                    {{-- Excel (XLSX) Export Button --}}
                    <div x-on:click="
                            if (selectedColumns.length === 0) {
                                alert('Please select at least one column to export.');
                            } else {
                                @this.set('exportFormat', 'xlsx');
                                @this.set('exportColumnsData', orderedSelectedFullColumns);
                                Livewire.dispatch('start-student-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleStudentExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-table-cells"
                            class="w-10 h-10 text-green-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as Excel</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Creates a standard XLSX spreadsheet file.</p>
                        <div wire:loading wire:target="handleStudentExport" class="mt-2">
                            <x-loading class="loading-sm text-green-500" />
                        </div>
                    </div>

                    {{-- CSV Export Button --}}
                    <div x-on:click="
                            if (selectedColumns.length === 0) {
                                alert('Please select at least one column to export.');
                            } else {
                                @this.set('exportFormat', 'csv');
                                @this.set('exportColumnsData', orderedSelectedFullColumns);
                                Livewire.dispatch('start-student-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleStudentExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-chart-bar"
                            class="w-10 h-10 text-blue-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as CSV</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exports data in comma-separated values format.</p>
                        <div wire:loading wire:target="handleStudentExport" class="mt-2">
                            <x-loading class="loading-sm text-blue-500" />
                        </div>
                    </div>
                </div>
            </div>

        </div> {{-- End x-data --}}

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.showGenericExportModal = false" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>

</div>
