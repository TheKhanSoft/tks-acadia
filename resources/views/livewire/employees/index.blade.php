<?php

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\EmployeeWorkStatus;
use App\Models\JobNature; // Added JobNature
use App\Models\Office; // Needed for potential filtering/assignment
use App\Services\EmployeeService;
use App\Http\Requests\EmployeeRequest;
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

    // Employee Model Properties
    public $employeeId = null; // To store the ID of the employee being edited/viewed
    public $employee_id_field; // Renamed from employee_id to avoid conflict with model instance
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $gender;
    public $nic_no;
    public $date_of_birth;
    public $employee_type_id;
    public $appointment_date;
    public $termination_date;
    public $postal_address;
    public $permanent_address;
    public $qualification;
    public $specialization;
    public $photo_path; // Consider how to handle file uploads if needed
    public $bio;
    public $employee_work_status_id;
    public $job_nature_id; // Added job_nature_id property

    // Options for selects
    public $employeeTypes = [];
    public $employeeWorkStatuses = [];
    public $jobNatures = []; // Added jobNatures property
    public $offices = []; // For potential assignment or filtering

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
    public $selectedEmployees = []; // Changed from selectedOffices
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $viewEmployee = null; // Changed from viewOffice
    public $showHistoryModal = false; // Added for employment history
    public $historyEmployee = null; // Added for employment history data
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // --- Export Properties ---
    public $showGenericExportModal = false; // Controls the generic export modal
    public $exportingSelectedOnly = false; // Flag for bulk export context
    public $employeeExportColumns = []; // All available columns for employee export
    public $defaultExportColumns = []; // Default selected columns for export
    // Note: Actual selection/order will be managed client-side in the modal via AlpineJS
    public $exportFormat = null; // Added: To store the selected export format
    public $exportColumnsData = []; // Added: To store the selected columns data

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterEmployeeTypeId = null;
    #[Url]
    public $filterWorkStatusId = null;
    #[Url]
    public $filterGender = ''; // '', 'Male', 'Female', 'Other'
    // Removed Status Filter property declaration
    #[Url]
    public $filterOfficeId = null; // Filter by office assignment
    #[Url]
    public $filterAppointmentDateStart = null;
    #[Url]
    public $filterAppointmentDateEnd = null;
    #[Url]
    public $filterDobStart = null;
    #[Url]
    public $filterDobEnd = null;
    #[Url]
    public $filterJobNatureId = null; // Added Job Nature filter property
    #[Url]
    public $filterQualification = ''; // Added Qualification filter
    #[Url]
    public $filterSpecialization = ''; // Added Specialization filter
    #[Url]
    public $filterTerminationDateStart = null; // Added Termination Date filter
    #[Url]
    public $filterTerminationDateEnd = null; // Added Termination Date filter

    // Modal tab control
    public $selectedTab = 'basic'; // Default to the 'basic' tab

    // Define table headers for Employees
    public $headers = [
        ['key' => 'full_name', 'label' => 'Employee Info'], // Combined Name, ID, Type
        ['key' => 'contact', 'label' => 'Contact', 'sortable' => false], // Combined Email, Phone
        ['key' => 'jobNature.name', 'label' => 'Job Nature', 'sortable' => false, 'class' => 'hidden lg:table-cell'], // Corrected Job Nature key
        ['key' => 'employeeWorkStatus.name', 'label' => 'Work Status', 'sortable' => false, 'class' => 'hidden lg:table-cell'],
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
        $this->selectedEmployees = [];
        $this->selectAll = false;
    }
    public function updatedFilterEmployeeTypeId()
    {
        $this->resetPage();
    }
    public function updatedFilterWorkStatusId()
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
        // Use allowed fields from EmployeeService applySorting if possible, or define here
        $allowedSortFields = [
            'employee_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'nic_no',
            'date_of_birth',
            'appointment_date',
            'termination_date',
            'qualification',
            'specialization',
            'created_at',
            'updated_at', // Removed 'is_active'
            // Add related fields if supported by service/query
            'employee_type_name',
            'employee_work_status_name',
            'primary_office_name',
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
        $items = $this->getEmployees(app(EmployeeService::class)); // Inject service
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedEmployees = array_unique(array_merge($this->selectedEmployees, $itemIds));
        } else {
            $this->selectedEmployees = array_diff($this->selectedEmployees, $itemIds);
        }
    }

    public function updatedSelectedEmployees($value)
    {
        $items = $this->getEmployees(app(EmployeeService::class)); // Inject service
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedEmployees));
    }

    public function mount()
    {
        // Pre-load options for selects
        $this->employeeTypes = EmployeeType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $this->employeeWorkStatuses = EmployeeWorkStatus::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $this->jobNatures = JobNature::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']); // Load Job Natures
        $this->offices = Office::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']); // For filtering/assignment dropdowns

        // Define available columns for employee export
        $this->employeeExportColumns = $this->getAvailableEmployeeExportColumns();
        // Define default selected columns (can be adjusted)
        $this->defaultExportColumns = $this->getDefaultEmployeeExportColumns();
    }

    // Helper to define available export columns specifically for Employees
    private function getAvailableEmployeeExportColumns(): array
    {
        // Define columns with keys (matching model/relation attributes) and user-friendly labels
        return [
            ['key' => 'employee_id', 'label' => 'Employee ID'],
            ['key' => 'first_name', 'label' => 'First Name'],
            ['key' => 'last_name', 'label' => 'Last Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'nic_no', 'label' => 'NIC Number'],
            ['key' => 'date_of_birth', 'label' => 'Date of Birth'],
            ['key' => 'employeeType.name', 'label' => 'Employee Type'],
            ['key' => 'appointment_date', 'label' => 'Appointment Date'],
            ['key' => 'termination_date', 'label' => 'Termination Date'],
            ['key' => 'postal_address', 'label' => 'Postal Address'],
            ['key' => 'permanent_address', 'label' => 'Permanent Address'],
            ['key' => 'qualification', 'label' => 'Qualification'],
            ['key' => 'specialization', 'label' => 'Specialization'],
            // ['key' => 'photo_path', 'label' => 'Photo Path'], // Usually not useful in export
            ['key' => 'bio', 'label' => 'Bio'],
            ['key' => 'employeeWorkStatus.name', 'label' => 'Work Status'],
            ['key' => 'jobNature.name', 'label' => 'Job Nature'],
            ['key' => 'primaryOffice.name', 'label' => 'Primary Office'], // Example related field
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
        ];
    }

    // Helper to define the default selected/ordered columns for export
    private function getDefaultEmployeeExportColumns(): array
    {
        // Return an array of column *keys* in the desired default order
        return ['employee_id', 'first_name', 'last_name', 'email', 'phone', 'employeeType.name', 'jobNature.name', 'employeeWorkStatus.name', 'primaryOffice.name', 'appointment_date'];
    }

    // Temporarily remove EmployeeService injection from signature to test dependency resolution
    public function openModal($id = null)
    {
        // Manually resolve the service if needed (especially for the edit case)
        $employeeService = app(EmployeeService::class);

        $this->resetValidation();
        // Reset all public properties except ones and pre-loaded options
        $this->resetExcept([
            'search',
            'sortField',
            'sortDirection',
            'perPage',
            'showDeletedRecords',
            'selectedEmployees',
            'selectAll',
            'headers',
            'employeeTypes',
            'employeeWorkStatuses',
            'jobNatures',
            'offices',
            // Filter properties
            'filterEmployeeTypeId',
            'filterWorkStatusId',
            'filterGender',
            'filterOfficeId',
            'filterAppointmentDateStart',
            'filterAppointmentDateEnd',
            'filterDobStart',
            'filterDobEnd',
            'filterJobNatureId',
            'filterQualification',
            'filterSpecialization',
            'filterTerminationDateStart',
            'filterTerminationDateEnd', // Added new filters
        ]);

        $this->showModal = true;
        $this->employeeId = $id; // Use the separate property for the ID
        $this->selectedTab = 'basic'; // Default tab

        if ($id) {
            // Use the manually resolved service to get the employee
            $employee = $employeeService->getEmployee($id, $this->showDeletedRecords, ['employeeType', 'employeeWorkStatus', 'jobNature']); // Eager load relations including jobNature
            if ($employee) {
                $this->employee_id_field = $employee->employee_id;
                $this->first_name = $employee->first_name;
                $this->last_name = $employee->last_name;
                $this->email = $employee->email;
                $this->phone = $employee->phone;
                $this->gender = $employee->gender;
                $this->nic_no = $employee->nic_no;
                $this->date_of_birth = $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : null;
                $this->employee_type_id = $employee->employee_type_id;
                $this->appointment_date = $employee->appointment_date ? $employee->appointment_date->format('Y-m-d') : null;
                $this->termination_date = $employee->termination_date ? $employee->termination_date->format('Y-m-d') : null;
                $this->postal_address = $employee->postal_address;
                $this->permanent_address = $employee->permanent_address;
                $this->qualification = $employee->qualification;
                $this->specialization = $employee->specialization;
                $this->photo_path = $employee->photo_path; // Display only, upload handled separately if needed
                $this->bio = $employee->bio;
                $this->employee_work_status_id = $employee->employee_work_status_id;
                $this->job_nature_id = $employee->job_nature_id; // Load job_nature_id
                // $this->is_active = $employee->is_active; // Removed
            } else {
                $this->error('Employee not found.');
                $this->closeModal();
                return;
            }
        } else {
            // Reset all form fields for a new entry
            $this->employee_id_field = '';
            $this->first_name = '';
            $this->last_name = '';
            $this->email = '';
            $this->phone = '';
            $this->gender = null;
            $this->nic_no = '';
            $this->date_of_birth = null;
            $this->employee_type_id = null;
            $this->appointment_date = null;
            $this->termination_date = null;
            $this->postal_address = '';
            $this->permanent_address = '';
            $this->qualification = '';
            $this->specialization = '';
            $this->photo_path = '';
            $this->bio = '';
            $this->employee_work_status_id = null;
            $this->job_nature_id = null; // Reset job_nature_id
            // $this->is_active = true; // Removed
        }
    }

    public function openViewModal($id, EmployeeService $employeeService)
    {
        // Eager load relationships needed for the view modal
        $this->viewEmployee = $employeeService->getEmployee($id, true, ['employeeType', 'employeeWorkStatus', 'jobNature', 'offices', 'primaryOffice']); // Load necessary relations including jobNature directly
        if (!$this->viewEmployee) {
            $this->error('Employee not found.');
            return;
        }
        $this->showViewModal = true;
    }

    // Added method to open the employment history modal
    public function openHistoryModal($id, EmployeeService $employeeService)
    {
        // Eager load relationships needed for the history modal
        // Load offices relationship
        $this->historyEmployee = $employeeService->getEmployee($id, true, ['offices']); // Removed jobNature loading here

        if (!$this->historyEmployee) {
            $this->error('Employee not found.');
            return;
        }

        // Check if the relationship loaded correctly (optional but good practice)
        if (!$this->historyEmployee->relationLoaded('offices')) {
            \Log::warning("EmployeeOffices relationship not loaded for employee ID: {$id}");
            // Optionally, force load if needed, though eager loading should handle it
            // $this->historyEmployee->load(['employeeOffices.office', 'employeeOffices.jobNature']);
        }

        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->historyEmployee = null;
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
        $this->employeeId = null; // Reset the ID
        $this->viewEmployee = null; // Reset view data
        $this->historyEmployee = null; // Reset history data
    }

    public function save(EmployeeService $employeeService)
    {
        $request = new EmployeeRequest();
        $currentId = $this->employeeId; // Use the separate property
        // Pass the current ID to the rules method if it expects it
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'employee_id' => $this->employee_id_field, // Use the renamed field
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'nic_no' => $this->nic_no,
            'date_of_birth' => $this->date_of_birth,
            'employee_type_id' => $this->employee_type_id,
            'appointment_date' => $this->appointment_date,
            'termination_date' => $this->termination_date,
            'postal_address' => $this->postal_address,
            'permanent_address' => $this->permanent_address,
            'qualification' => $this->qualification,
            'specialization' => $this->specialization,
            'photo_path' => $this->photo_path, // Validation might need adjustment for uploads
            'bio' => $this->bio,
            'employee_work_status_id' => $this->employee_work_status_id,
            'job_nature_id' => $this->job_nature_id, // Add job_nature_id to validation data
            // 'is_active' => $this->is_active, // Removed
        ];

        // Run validation
        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        // Prepare data (e.g., handle boolean, nulls) - EmployeeRequest prepareForValidation might handle some of this
        // $validatedData['is_active'] = filter_var($validatedData['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN); // Removed
        // Ensure empty strings become null for nullable fields if necessary
        foreach (['last_name', 'phone', 'nic_no', 'date_of_birth', 'appointment_date', 'termination_date', 'postal_address', 'permanent_address', 'qualification', 'specialization', 'photo_path', 'bio'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        try {
            if ($this->employeeId) {
                $employee = $employeeService->getEmployee($this->employeeId);
                if ($employee) {
                    $employeeService->updateEmployee($employee, $validatedData);
                    $this->success('Employee updated successfully! 🧑‍💼');
                } else {
                    $this->error('Employee not found for update.');
                    $this->closeModal();
                    return;
                }
            } else {
                $employeeService->createEmployee($validatedData);
                $this->success('New Employee added successfully! ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Employee Save Error: ' . $e->getMessage(), ['exception' => $e]);
            // Provide more specific feedback if possible (e.g., unique constraint violation)
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('Failed to save employee. A record with the same Employee ID, Email, or NIC already exists.');
            } else {
                $this->error('An error occurred while saving the Employee.');
            }
        }
    }

    // Removed toggleActive method

    // --- Bulk Actions ---
    public function handleBulkAction($action)
    {
        if (!$action || empty($this->selectedEmployees)) {
            if (empty($this->selectedEmployees)) {
                $this->warning('Please select employees first 🤔');
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
            'updateWorkStatus' => null, // Placeholder: Implement updateWorkStatus logic/modal trigger
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
        } elseif ($action === 'updateWorkStatus') {
            // TODO: Implement logic, maybe open a modal
            $this->warning('Bulk Update Work Status action clicked (not implemented yet).');
        } elseif ($action === 'exportSelected') {
            // Set flag and open the export modal for format selection
            if (empty($this->selectedEmployees)) {
                $this->warning('No employees selected for export.');
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
        $this->employeeId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(EmployeeService $employeeService)
    {
        try {
            $successful = $employeeService->deleteEmployeeById($this->employeeId);
            $this->confirmingDeletion = false;
            if ($successful) {
                $this->warning('Employee deleted successfully! 🗑️');
            } else {
                $this->error('Failed to delete Employee. It might have already been deleted or does not exist.');
            }
            $this->employeeId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Employee Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('An error occurred while deleting the Employee.');
        }
    }

    public function bulkDelete(EmployeeService $employeeService)
    {
        try {
            $employeeIds = array_map('intval', $this->selectedEmployees);
            $deletedCount = $employeeService->bulkDeleteEmployeeByIds($employeeIds);
            $this->confirmingBulkDeletion = false;
            if ($deletedCount > 0) {
                $this->warning($deletedCount . ' employees deleted successfully! 🗑️');
            } else {
                $this->error('Failed to delete selected employees. They might have already been deleted or do not exist.');
            }
            $this->selectedEmployees = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false;
            $this->error('An error occurred while deleting selected employees.');
        }
    }

    // --- Restoration ---
    public function confirmRestore($id)
    {
        $this->employeeId = $id;
        $this->confirmingRestore = true;
    }

    public function restore(EmployeeService $employeeService)
    {
        try {
            $successful = $employeeService->restoreEmployee($this->employeeId);
            $this->confirmingRestore = false;
            if ($successful) {
                $this->success('Employee restored successfully! ♻️');
            } else {
                $this->error('Failed to restore Employee. It might not be deleted or does not exist.');
            }
            $this->employeeId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Employee Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false;
            $this->error('An error occurred while restoring the Employee.');
        }
    }

    public function bulkRestore(EmployeeService $employeeService)
    {
        try {
            $employeeIds = array_map('intval', $this->selectedEmployees);
            $restoredCount = $employeeService->bulkRestoreEmployees($employeeIds);
            $this->confirmingBulkRestore = false;
            if ($restoredCount > 0) {
                $this->success($restoredCount . ' employees restored successfully! ♻️');
            } else {
                $this->error('Failed to restore selected employees. They might not be deleted or do not exist.');
            }
            $this->selectedEmployees = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false;
            $this->error('An error occurred while restoring selected employees.');
        }
    }

    // --- Permanent Deletion ---
    public function confirmPermanentDelete($id)
    {
        $this->employeeId = $id;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(EmployeeService $employeeService)
    {
        try {
            $successful = $employeeService->permanentlyDelete($this->employeeId);
            $this->confirmingPermanentDeletion = false;
            if ($successful) {
                $this->warning('Employee permanently deleted! 💥');
            } else {
                $this->error('Failed to permanently delete Employee. It might not exist.');
            }
            $this->employeeId = null; // Reset ID
        } catch (\Exception $e) {
            \Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false;
            // Add specific checks, e.g., if deletion is blocked due to relationships
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete this employee. They might be linked to other records (e.g., Head of Office).');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkPermanentDelete(EmployeeService $employeeService)
    {
        try {
            $employeeIds = array_map('intval', $this->selectedEmployees);
            $deletedCount = $employeeService->bulkPermanentDelete($employeeIds);
            $this->confirmingBulkPermanentDeletion = false;
            if ($deletedCount > 0) {
                $this->warning($deletedCount . ' employees permanently deleted! 💥');
            } else {
                $this->error('Failed to permanently delete selected employees. They might not exist.');
            }
            $this->selectedEmployees = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete selected employees. Some might be linked to other records.');
            } else {
                $this->error('An error occurred during bulk permanent deletion.');
            }
        }
    }

    // --- Filters ---
    public function getActiveFiltersProperty()
    {
        $filters = [];

        if ($this->filterEmployeeTypeId) {
            $typeName = $this->employeeTypes->firstWhere('id', $this->filterEmployeeTypeId)?->name ?? 'Unknown Type';
            $filters[] = ['key' => 'filterEmployeeTypeId', 'label' => 'Type', 'value' => $typeName];
        }
        if ($this->filterWorkStatusId) {
            $statusName = $this->employeeWorkStatuses->firstWhere('id', $this->filterWorkStatusId)?->name ?? 'Unknown Status';
            $filters[] = ['key' => 'filterWorkStatusId', 'label' => 'Work Status', 'value' => $statusName];
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
        if ($this->filterAppointmentDateStart) {
            $filters[] = ['key' => 'filterAppointmentDateStart', 'label' => 'Appointed From', 'value' => $this->filterAppointmentDateStart];
        }
        if ($this->filterAppointmentDateEnd) {
            $filters[] = ['key' => 'filterAppointmentDateEnd', 'label' => 'Appointed To', 'value' => $this->filterAppointmentDateEnd];
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
            'filterEmployeeTypeId',
            'filterWorkStatusId',
            'filterGender',
            'filterOfficeId',
            'filterAppointmentDateStart',
            'filterAppointmentDateEnd',
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

    // Fetch employees using the service
    private function getEmployees(EmployeeService $employeeService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
            // Add specific employee filters
            'employee_type_id' => $this->filterEmployeeTypeId,
            'employee_work_status_id' => $this->filterWorkStatusId,
            'gender' => $this->filterGender,
            // Removed 'status' => $this->filterStatus,
            'office_id' => $this->filterOfficeId,
            'appointment_date_start' => $this->filterAppointmentDateStart,
            'appointment_date_end' => $this->filterAppointmentDateEnd,
            'dob_start' => $this->filterDobStart,
            'dob_end' => $this->filterDobEnd,
            'job_nature_id' => $this->filterJobNatureId,
            'qualification' => $this->filterQualification, // Pass qualification filter
            'specialization' => $this->filterSpecialization, // Pass specialization filter
            'termination_date_start' => $this->filterTerminationDateStart, // Pass termination date filter
            'termination_date_end' => $this->filterTerminationDateEnd, // Pass termination date filter
            // Add other filters supported by EmployeeService->applyFilters
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
        $filterParams['with'] = ['employeeType', 'employeeWorkStatus', 'jobNature', 'primaryOffice']; // Eager load jobNature directly

        return $employeeService->getPaginatedEmployees($filterParams);
    }

    // Render the view
    public function render(): mixed
    {
        \Log::info('[Livewire Render] Rendering Employee List.'); // <-- Add this line
        $employees = $this->getEmployees(app(EmployeeService::class)); // Inject service

        // Update selectAll state based on current page items
        $currentPageIds = $employees->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedEmployees));

        return view('livewire.employees.index', [
            'employees' => $employees, // Pass employees to the view
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
     * Listens for the 'start-employee-export' event dispatched from AlpineJS.
     *
     * @param EmployeeService $employeeService Injected service.
     * @param array $eventData Contains 'format' (string) and 'selectedColumns' (array).
     * @param array $eventData Contains 'format' (string) and 'selectedColumns' (array).
     * @return BinaryFileResponse|void
     */

    #[On('start-employee-export')] 
    public function handleEmployeeExport()
    {
        // Manually resolve EmployeeService
        $employeeService = app(EmployeeService::class);

        // Read data from public properties
        $format = $this->exportFormat;
        $selectedColumnsData = $this->exportColumnsData;

        // Reset properties after reading (optional but good practice)
        $this->exportFormat = null;
        $this->exportColumnsData = [];

        // Validate extracted data
        if (empty($format) || empty($selectedColumnsData)) {
            \Log::error('Employee Export Error: Invalid format or column data read from properties.', ['format' => $format, 'columns' => $selectedColumnsData]);
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
            // Add specific employee filters from component state
            'employee_type_id' => $this->filterEmployeeTypeId,
            'employee_work_status_id' => $this->filterWorkStatusId,
            'gender' => $this->filterGender,
            'office_id' => $this->filterOfficeId,
            'appointment_date_start' => $this->filterAppointmentDateStart,
            'appointment_date_end' => $this->filterAppointmentDateEnd,
            'dob_start' => $this->filterDobStart,
            'dob_end' => $this->filterDobEnd,
            'job_nature_id' => $this->filterJobNatureId,
            'qualification' => $this->filterQualification,
            'specialization' => $this->filterSpecialization,
            'termination_date_start' => $this->filterTerminationDateStart,
            'termination_date_end' => $this->filterTerminationDateEnd,
        ];

        // Clean up null/empty filters (similar to getEmployees method)
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

        // 2. Handle exporting only selected employees
        if ($this->exportingSelectedOnly && !empty($this->selectedEmployees)) {
            $filters['ids'] = array_map('intval', $this->selectedEmployees); // Add ID filter
        }

        // 3. Generate filename
        $timestamp = now()->format('Ymd_His');
        $filename = "employees_{$timestamp}.{$format}";

        // 4. Trigger download via EmployeeService
        try {
            $this->success('Export started... Please wait. ⏳');
            $this->showGenericExportModal = false; // Close modal immediately
            $this->exportingSelectedOnly = false; // Reset context flag

            // Call the service method with the correct arguments
            // The service method handles query building, filename generation, and relation loading internally based on filters and columns.
            return $employeeService->exportEmployees(
                $format,             // 1st arg: format (string)
                $filters,            // 2nd arg: filters (array)
                $selectedColumnsData, // 3rd arg: selectedColumns (array of {key, label})
                'Employee Report', // 4th arg: title (string)
                "Selected Employees", // 5th arg: subtitle (string)
            );
        } catch (\Exception $e) {
            \Log::error('Employee Export Error: ' . $e->getMessage(), [
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
        title="Employee Management" icon="o-users" icon-classes="bg-warning rounded-full p-1 w-8 h-8"
        subtitle="Total Employees: {{ $employees->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}"
        subtitle-class="mr-2 mt-0.5 ">

        <x-slot:middle class="!justify-end">
            <x-button label="Test Listener" onclick="Livewire.dispatch('test-event')" />

            <x-input placeholder="Search Employees..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add Employee"
                label="Add Employee" />
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

        @if (count($selectedEmployees))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Perform a bulk action" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'assignOffice', 'name' => 'Assign Office...'], // Added
                    ['id' => 'updateWorkStatus', 'name' => 'Update Work Status...'], // Added
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
                    x-on:change="$wire.handleBulkAction($event.target.value)" x-init="$watch('$wire.selectedEmployees', value => { if (value.length === 0) $el.value = ''; })"
                    x-on:reset-bulk-action.window="$el.value = ''" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedEmployees) }} selected
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
                @forelse($employees as $employee)
                    <tr wire:key="employee-{{ $employee->id }}"
                        class="{{ $employee->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150 ease-in-out">
                        {{-- Added transition --}}
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedEmployees" value="{{ (string) $employee->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        <!-- Employee Info (Combined) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-4"> {{-- Added space-x-4 --}}
                                <div class="flex-shrink-0">
                                    @php
                                        // Check if photo_path is an absolute URL or relative path
                                        $photoUrl = $employee->photo_path
                                            ? (Str::startsWith($employee->photo_path, ['http://', 'https://'])
                                                ? $employee->photo_path // Use directly if absolute URL
                                                : asset('storage/' . $employee->photo_path)) // Prepend storage if relative
                                            : 'https://ui-avatars.com/api/?name=' .
                                                urlencode($employee->first_name . ' ' . $employee->last_name) .
                                                '&color=FFFFFF&background=0D8ABC&bold=true&size=64'; // Fallback
                                    @endphp
                                    <img class="h-12 w-12 rounded-full object-cover shadow-sm"
                                        src="{{ $photoUrl }}"
                                        alt="{{ $employee->first_name }} {{ $employee->last_name }}">
                                </div>
                                <div class="flex-grow">
                                    {{-- Line 1: Name --}}
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </div>

                                    {{-- Line 2: ID and Type Badge --}}
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center space-x-2 flex-wrap">
                                        <span>ID: {{ $employee->employee_id ?? 'N/A' }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        {{-- Employee Type Badge --}}
                                        @php
                                            $type = $employee->employeeType?->name;
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
                                        {{ $employee->primaryOffice->first()?->name ?? 'No Office' }}
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
                                    {{ $employee->email ?? 'No Email' }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                    {{-- Reduced font size --}}
                                    <x-icon name="o-phone"
                                        class="w-3.5 h-3.5 mr-1.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    {{-- Adjusted icon size/margin --}}
                                    {{ $employee->phone ?? 'No Phone' }}
                                </div>
                            </div>
                        </td>
                        <!-- Job Nature -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <span class="text-xs text-gray-700 dark:text-gray-300">
                                {{ $employee->jobNature?->name ?? 'N/A' }} {{-- Display job nature directly --}}
                            </span>
                        </td>
                        <!-- Work Status - Adjusted Badge Style -->
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            {{-- Dynamic Badge Color for Work Status - Lighter Backgrounds --}}
                            @php
                                $status = $employee->employeeWorkStatus?->name;
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
                                <x-button icon="o-eye" wire:click="openViewModal({{ $employee->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" />

                                {{-- Added History Button --}}
                                <x-button icon="o-building-library"
                                    wire:click="openHistoryModal({{ $employee->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                    spinner tooltip-left="Employment History" />

                                @if (!$employee->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $employee->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner tooltip-left="Edit Employee" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $employee->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Delete Employee" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $employee->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner tooltip-left="Restore Employee" />
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $employee->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Permanently Delete Employee" />
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
                                <span class="text-lg font-medium">No employees found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new employee.' }}
                                </p>
                                @if ($search)
                                    <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm"
                                        label="Clear Search" />
                                @else
                                    <x-button 
                                        wire:click="openModal(null)"  
                                        class="mt-3 btn btn-primary btn-sm"
                                        spinner 
                                        tooltip-left="Add Your First Employee" 
                                        label="Add Your First Employee"
                                    />
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
        {{ $employees->links() }}
    </div>

    <!-- Add/Edit Employee Modal -->
    <x-modal wire:model="showModal" :title="$employeeId ? 'Edit Employee' : 'Add New Employee'" box-class="max-w-4xl" separator
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab">

                <!-- Basic Info Tab -->
                <x-tab name="basic" label="Basic Info" icon="o-identification" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                        <x-input wire:model="employee_id_field" label="Employee ID"
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
                        <x-select wire:model="employee_type_id" label="Employee Type" :options="$employeeTypes"
                            placeholder="Select type" icon="o-tag" inline required />
                        <x-select wire:model="employee_work_status_id" label="Work Status" :options="$employeeWorkStatuses"
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
                                <img src="{{ asset('storage/' . $photo_path) }}" alt="Employee Photo"
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
            <x-button type="submit" label="{{ $employeeId ? 'Update Employee' : 'Create Employee' }}"
                class="btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Employee Modal -->
    <x-modal wire:model="showViewModal" title="View Employee Details" separator box-class="max-w-4xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($viewEmployee)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div class="flex items-center mb-3 sm:mb-0">
                        @php
                            // Check if photo_path is an absolute URL or relative path
                            $viewPhotoUrl = $viewEmployee->photo_path
                                ? (Str::startsWith($viewEmployee->photo_path, ['http://', 'https://'])
                                    ? $viewEmployee->photo_path // Use directly if absolute URL
                                    : asset('storage/' . $viewEmployee->photo_path)) // Prepend storage if relative
                                : 'https://ui-avatars.com/api/?name=' .
                                    urlencode($viewEmployee->first_name . ' ' . $viewEmployee->last_name) .
                                    '&color=7F9CF5&background=EBF4FF'; // Fallback
                        @endphp
                        <img class="h-16 w-16 rounded-full object-cover mr-4 shadow-sm" src="{{ $viewPhotoUrl }}"
                            alt="{{ $viewEmployee->first_name }} {{ $viewEmployee->last_name }}">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $viewEmployee->first_name }} {{ $viewEmployee->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $viewEmployee->employee_id }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center justify-start sm:justify-end w-full sm:w-auto">
                        {{-- Dynamic Badge Color for Employee Type (View Modal) - Lighter Backgrounds --}}
                        @php
                            $viewType = $viewEmployee->employeeType?->name;
                            $viewTypeColorClasses = match ($viewType) {
                                'Permanent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'Contract' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'Intern' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            };
                        @endphp
                        <span title="Employee Type"
                            class="inline-flex items-center px-3 py-1 shadow-sm rounded-full text-xs font-medium {{ $viewTypeColorClasses }}">
                            {{-- Adjusted shadow/font --}}
                            <x-icon name="o-tag" class="h-3.5 w-3.5 mr-1" /> {{-- Slightly larger icon --}}
                            {{ $viewType ?? 'N/A' }}
                        </span>
                        {{-- Dynamic Badge Color for Job Nature (View Modal) --}}
                        @php
                            $viewJobNature = $viewEmployee->jobNature?->name;
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
                            $viewStatus = $viewEmployee->employeeWorkStatus?->name;
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
                        @if ($viewEmployee->deleted_at)
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
                                    {{ $viewEmployee->email ?: 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-900 dark:text-white font-semibold flex items-center mt-1">
                                    <x-icon name="o-phone" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" />
                                    {{ $viewEmployee->phone ?: 'Not specified' }}
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
                                    {{ $viewEmployee->gender ?: 'N/A' }}</p>
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
                                    {{ $viewEmployee->nic_no ?: 'Not specified' }}</p>
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
                                    {{ $viewEmployee->date_of_birth ? $viewEmployee->date_of_birth->format('M d, Y') : 'Not specified' }}
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
                                    {{ $viewEmployee->appointment_date ? $viewEmployee->appointment_date->format('M d, Y') : 'Not specified' }}
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
                                    {{ $viewEmployee->termination_date ? $viewEmployee->termination_date->format('M d, Y') : 'Not applicable' }}
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
                                    {{ $viewEmployee->qualification ?: 'Not specified' }}</p>
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
                                    {{ $viewEmployee->specialization ?: 'Not specified' }}</p>
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
                                    {{ $viewEmployee->primaryOffice->first()?->name ?? 'Not Assigned' }}
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
                                    {{ $viewEmployee->postal_address ?: 'Not specified' }}</p>
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
                                    {{ $viewEmployee->permanent_address ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        <!-- Bio -->
                        <div class="flex items-start">
                            <x-icon name="o-document-text"
                                class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                            <div class="flex-grow">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Bio</div>
                                <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap">
                                    {{ $viewEmployee->bio ?: 'Not specified' }}</p>
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
                                {{ $viewEmployee->created_at ? $viewEmployee->created_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <x-icon name="o-pencil-square" class="h-4 w-4" />
                            <span>Updated:
                                {{ $viewEmployee->updated_at ? $viewEmployee->updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        @if ($viewEmployee->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                <x-icon name="o-archive-box-x-mark" class="h-4 w-4" />
                                <span>Deleted: {{ $viewEmployee->deleted_at->format('M d, Y h:i A') }}</span>
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
                @if ($viewEmployee && !$viewEmployee->deleted_at)
                    <x-button label="Edit Employee" icon="o-pencil" wire:click="openModal({{ $viewEmployee->id }})"
                        class="btn-primary" spinner />
                @endif
                <x-button label="Close" wire:click="closeHistoryModal()" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Employment History Modal -->
    <x-modal wire:model="showHistoryModal" title="Employment History" separator box-class="max-w-3xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($historyEmployee)
            <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    History for: {{ $historyEmployee->first_name }} {{ $historyEmployee->last_name }}
                    ({{ $historyEmployee->employee_id }})
                </h3>

                @forelse($historyEmployee->offices->sortBy('pivot.start_date') as $officeRecord)
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
                        No employment history found for this employee.
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
    <x-modal wire:model="confirmingDeletion" title="Delete Employee" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this employee? This action will soft delete the record.
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
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Employees" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedEmployees) }} selected employees? This action
                    will soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedEmployees) }} Employees" wire:click="bulkDelete"
                class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Restore Confirmation -->
    <x-modal wire:model="confirmingRestore" title="Restore Employee" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this employee?
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
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Employees" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedEmployees) }} selected employees?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedEmployees) }} Employees" wire:click="bulkRestore"
                class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    <!-- Permanent Delete Confirmation -->
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Employee" separator
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this employee? This action cannot be undone.
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
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Employees" separator
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedEmployees) }} selected employees?
                    This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedEmployees) }} Employees"
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
                <x-select wire:model.live="filterEmployeeTypeId" label="Employee Type" :options="$employeeTypes"
                    placeholder="All Types" icon="o-tag" clearable inline />
                <x-select wire:model.live="filterWorkStatusId" label="Work Status" :options="$employeeWorkStatuses"
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
                        <x-datepicker wire:model.live="filterAppointmentDateStart" placeholder="From"
                            icon="o-calendar-days" clearable />
                        <x-datepicker wire:model.live="filterAppointmentDateEnd" placeholder="To"
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
    <x-modal wire:model="showGenericExportModal" title="Configure Export" separator box-class="max-w-3xl"
        class="backdrop-blur-sm">
        <div x-data="{
            availableColumns: {{ json_encode($employeeExportColumns) }},
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
                                Livewire.dispatch('start-employee-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleEmployeeExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-text"
                            class="w-10 h-10 text-red-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as PDF (Standard)</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generates a formatted PDF document.</p>
                        <div wire:loading wire:target="handleEmployeeExport" class="mt-2">
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
                                Livewire.dispatch('start-employee-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleEmployeeExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-table-cells"
                            class="w-10 h-10 text-green-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as Excel</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Creates a standard XLSX spreadsheet file.</p>
                        <div wire:loading wire:target="handleEmployeeExport" class="mt-2">
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
                                Livewire.dispatch('start-employee-export');
                            }
                        "
                        wire:loading.attr="disabled" wire:target="handleEmployeeExport"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
                        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
                        <x-icon name="o-document-chart-bar"
                            class="w-10 h-10 text-blue-500 mb-2 group-hover:scale-110 transition-transform" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as CSV</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exports data in comma-separated values format.</p>
                        <div wire:loading wire:target="handleEmployeeExport" class="mt-2">
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
