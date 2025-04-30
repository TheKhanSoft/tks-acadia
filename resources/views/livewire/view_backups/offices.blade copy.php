<?php

use App\Models\Office;
use App\Services\OfficeService;
use App\Http\Requests\OfficeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\OfficeType;
use App\Models\Employee;

new class extends Component {
    use WithPagination, Toast;

    public $officeId = null;
    public $name;
    public $code;
    public $description;
    public $office_type_id;
    public $head_id;
    public $head_appointment_date;
    public $office_location;
    public $contact_email;
    public $contact_phone;
    public $established_year;
    public $parent_office_id;
    public $is_active = true;

    // Options for selects
    public $officeTypes = [];
    public $employees = [];
    public $parentOffices = [];

    // Table & Filtering properties
    public $perPage = 10;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeletedRecords = false;
    public $selectedOffices = [];
    public $selectAll = false;

    public $showModal = false;
    public $showViewModal = false;
    public $viewOffice = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // Modal tab control
    public $selectedTab = 'basic'; // Default to the 'basic' tab

    // Define table headers
    public $headers = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'head.first_name', 'label' => 'Head', 'class' => 'hidden lg:table-cell'], 
        ['key' => 'contact', 'label' => 'Contact', 'class' => 'hidden lg:table-cell'],
        ['key' => 'office_location', 'label' => 'Location', 'class' => 'hidden md:table-cell min-w-40'],
        ['key' => 'is_active', 'label' => 'Status'],
    ];

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
        $this->selectedOffices = [];
        $this->selectAll = false;
    }

    public function sortBy($field)
    {
        $validSortFields = ['name', 'code', 'is_active', 'created_at', 'updated_at'];
        if (!in_array($field, $validSortFields)) {
            return;
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
        $items = $this->getOffices(new OfficeService());
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedOffices = array_unique(array_merge($this->selectedOffices, $itemIds));
        } else {
            $this->selectedOffices = array_diff($this->selectedOffices, $itemIds);
        }
    }

    public function updatedSelectedOffices($value)
    {
        $items = $this->getOffices(new OfficeService());
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedOffices));
    }

    public function mount()
    {
        // Pre-load options for selects to avoid repeated queries
        $this->officeTypes = OfficeType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $this->employees = Employee::where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        // Exclude the current office being edited from parent options
        $this->parentOffices = Office::where('is_active', true)
            ->when($this->officeId, fn($q) => $q->where('id', '!=', $this->officeId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function openModal($officeId = null, OfficeService $officeService)
    {
        $this->resetValidation();
        $this->resetExcept(['search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords', 'selectedOffices', 'selectAll', 'headers', 'officeTypes', 'employees']); // Keep pre-loaded options

        // Refresh parent offices excluding the potential current one
        $this->parentOffices = Office::where('is_active', true)
            ->when($officeId, fn($q) => $q->where('id', '!=', $officeId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->showModal = true;
        $this->officeId = $officeId;
        $this->selectedTab = 'basic'; // Ensure 'basic' tab is selected when opening

        if ($officeId) {
            $office = $officeService->getOffice($officeId, true);
            $this->name = $office->name;
            $this->code = $office->code;
            $this->office_type_id = $office->office_type_id;
            $this->description = $office->description;
            $this->head_id = $office->head_id;
            $this->head_appointment_date = $office->head_appointment_date ? $office->head_appointment_date->format('Y-m-d') : null;
            $this->office_location = $office->office_location;
            $this->contact_email = $office->contact_email;
            $this->contact_phone = $office->contact_phone;
            $this->established_year = $office->established_year;
            $this->parent_office_id = $office->parent_office_id;
            $this->is_active = $office->is_active;
        } else {
            // Reset all form fields for a new entry
            $this->name = '';
            $this->code = '';
            $this->office_type_id = null;
            $this->description = '';
            $this->head_id = null;
            $this->head_appointment_date = null;
            $this->office_location = '';
            $this->contact_email = '';
            $this->contact_phone = '';
            $this->established_year = null;
            $this->parent_office_id = null;
            $this->is_active = true;
        }
    }

    public function openViewModal($officeId, OfficeService $officeService)
    {
        $this->viewOffice = $officeService->getOffice($officeId, true);
        $this->showViewModal = true;
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
        $this->officeId = null;
        $this->viewOffice = null;
    }

    public function save(OfficeService $officeService)
    {
        $request = new OfficeRequest();
        $currentId = $this->officeId;
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'name' => $this->name,
            'code' => $this->code,
            'office_type_id' => $this->office_type_id,
            'description' => $this->description,
            'head_id' => $this->head_id,
            'head_appointment_date' => $this->head_appointment_date,
            'office_location' => $this->office_location,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'established_year' => $this->established_year,
            'parent_office_id' => $this->parent_office_id,
            'is_active' => $this->is_active,
        ];

        // Use a temporary request instance for validation rules if needed, or define rules here
        // For simplicity, assuming OfficeRequest handles all fields now.
        // Ensure OfficeRequest is updated to include rules for all new fields.
        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        $validatedData['code'] = strtoupper($validatedData['code']);

        try {
            if ($this->officeId) {
                $office = $officeService->getOffice($this->officeId);
                $officeService->updateOffice($office, $validatedData);
                $this->success('Office updated successfully! 🏢');
            } else {
                $officeService->createOffice($validatedData);
                $this->success('New Office added successfully! ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Office Save Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('An error occurred while saving the Office.');
        }
    }

    public function toggleActive($officeId, OfficeService $officeService)
    {
        try {
            $office = $officeService->getOffice($officeId);
            $officeService->toggleActiveStatus($office);
            $statusText = $office->is_active ? 'activated' : 'deactivated';
            $this->success("Office {$statusText} successfully! 🔄");
        } catch (\Exception $e) {
            \Log::error('Toggle Active Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status.');
        }
    }

    public function handleBulkAction($action)
    {
        if (!$action) {
            return;
        }

        if (empty($this->selectedOffices)) {
            $this->warning('Please select offices first 🤔');
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'bulkToggleActive' => null,
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];
        if ($action === 'bulkToggleActive') {
            $this->bulkToggleActive(new OfficeService());
        } elseif (isset($confirmationMap[$action])) {
            $this->{$confirmationMap[$action]} = true;
        }

        // Reset the dropdown selection visually (optional, depends on desired UX)
        $this->handleBulkAction = '';
    }

    public function bulkToggleActive(OfficeService $officeService)
    {
        if (empty($this->selectedOffices)) {
            $this->warning('Please select offices to toggle status 🤔');
            return;
        }

        try {
            $offices = $officeService->getOffices($this->selectedOffices)->get();
            $toggledStatuses = $officeService->bulkToggleActiveStatus($offices);
            $this->success(
                'Bulk toggle operation for Office successful🔄',
                "<br />Total Toggles: <b>$toggledStatuses[totalToggledCount]</b><br />
                Activated: <b>$toggledStatuses[activatedCount]</b><br />
                Deactivated: <b>$toggledStatuses[deactivatedCount]</b><br />
                ",
            );
            $this->selectedOffices = [];
            $this->selectAll = false;
            $this->handleBulkAction = '';
        } catch (\Exception $e) {
            \Log::error('Bulk Toggle Active Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status for selected offices.');
        }
    }

    public function confirmDelete($officeId)
    {
        $this->officeId = $officeId;
        $this->confirmingDeletion = true;
    }

    public function delete(OfficeService $officeService)
    {
        try {
            $successful = $officeService->deleteOfficeById($this->officeId);
            $this->confirmingDeletion = false;
            $successful ? $this->warning('Office deleted successfully! 🗑️') : $this->error('Failed to delete Office.');
            $this->officeId = null;
        } catch (\Exception $e) {
            \Log::error('Office Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('Failed to delete Office.');
        }
    }

    public function bulkDelete(OfficeService $officeService)
    {
        try {
            $successful = $officeService->bulkDeleteOfficeByIds($this->selectedOffices);
            $this->confirmingBulkDeletion = false;
            $successful ? $this->warning(count($this->selectedOffices) . ' offices deleted successfully! 🗑️') : $this->error('Failed to delete selected offices.');
            $this->selectedOffices = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false; // Close modal even on error
            $this->error('Failed to delete selected offices.');
        }
    }

    // Restore operations matching campuses.blade.php
    public function confirmRestore($officeId)
    {
        $this->officeId = $officeId;
        $this->confirmingRestore = true;
    }

    public function restore(OfficeService $officeService)
    {
        try {
            $successful = $officeService->restoreOffice($this->officeId);
            $this->confirmingRestore = false;
            $successful ? $this->success('Office restored successfully! ♻️') : $this->error('Failed to restore Office.');
            $this->officeId = null;
        } catch (\Exception $e) {
            \Log::error('Office Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false; // Close modal even on error
            $this->error('Failed to restore Office.');
        }
    }

    public function bulkRestore(OfficeService $officeService)
    {
        try {
            $successful = $officeService->bulkRestoreOffices($this->selectedOffices);
            $this->confirmingBulkRestore = false;
            $successful ? $this->success(count($this->selectedOffices) . ' offices restored successfully! ♻️') : $this->error('Failed to restore selected offices.');
            $this->selectedOffices = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false; // Close modal even on error
            $this->error('Failed to restore selected offices.', 'Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    // Permanent delete operations matching campuses.blade.php
    public function confirmPermanentDelete($officeId)
    {
        $this->officeId = $officeId;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(OfficeService $officeService)
    {
        try {
            $successful = $officeService->permanentlyDelete($this->officeId);
            $this->confirmingPermanentDeletion = false;
            $successful ? $this->warning('Office permanently deleted! 💥') : $this->error('Failed to permanently delete Office.');
            $this->officeId = null;
        } catch (\Exception $e) {
            \Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete Office.');
        }
    }

    public function bulkPermanentDelete(OfficeService $officeService)
    {
        try {
            $successful = $officeService->bulkPermanentDelete($this->selectedOffices);
            $this->confirmingBulkPermanentDeletion = false;
            $successful ? $this->warning(count($this->selectedOffices) . ' offices permanently deleted! 💥') : $this->error('Failed to permanently delete selected offices');

            $this->selectedOffices = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete selected offices.');
        }
    }

    // Fetch offices with applied filters matching campuses.blade.php structure
    private function getOffices(OfficeService $officeService)
    {
        $filteringSorting = [
            'with_trashed' => $this->showDeletedRecords,
            'only_trashed' => false,
            'search' => $this->search ? true : false,
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
        ];

        return $officeService->getPaginatedOffices($filteringSorting);
    }

    public function render(): mixed
    {
        $offices = $this->getOffices(new OfficeService());
        // $offices = $officesQuery->paginate($this->perPage);

        $currentPageIds = $offices->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedOffices));

        return view('livewire.offices', [
            'offices' => $offices,
            'headers' => $this->headers,
        ]);
    }
};

?>

<div>

    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Office Management" icon="o-bolt" icon-classes="bg-warning rounded-full p-1 w-6 h-6"
        subtitle="Total Offices: {{ $offices->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}">

        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Offices..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            {{-- Explicitly pass null for officeId when adding a new office --}}
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add Office"
                label="Add Office" />
        </x-slot:actions>
    </x-header>

    <!-- Filters and Bulk Actions Row -->
    <div
        class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <!-- Filters -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <!-- Show deleted checkbox -->
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error"
                hint="{{ $showDeletedRecords ? 'Showing deleted' : '' }}" />

            <!-- Per page selector -->
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

        @if (count($selectedOffices))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Perform a bulk action" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'bulkToggleActive', 'name' => 'Toggle Active Status'],
                    ...$showDeletedRecords
                        ? [
                            ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                            ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete'],
                        ]
                        : [],
                ]"
                    class="select select-bordered select-sm py-0" wire:change="handleBulkAction($event.target.value)" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedOffices) }} selected
                </span>
            </div>
        @endif
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-b-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    {{-- Checkbox Header --}}
                    <th scope="col" class="p-4 w-8">
                        <x-checkbox wire:model.live="selectAll" class="checkbox-sm checkbox-primary" />
                    </th>

                    @foreach ($headers as $header)
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer {{ $header['class'] ?? '' }}"
                            wire:click="sortBy('{{ $header['key'] }}')">
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                {{-- Sort Indicator --}}
                                @if ($sortField === $header['key'])
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    {{-- Actions Header --}}
                    <th scope="col"
                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($offices as $office)
                    <tr wire:key="office-{{ $office->id }}"
                        class="{{ $office->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} {{ !$office->is_active ? 'bg-gray-50 dark:bg-gray-800/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors">
                        {{-- Checkbox Cell --}}
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedOffices" value="{{ (string) $office->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        {{-- Data Cells --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $office->name }}
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0 shadow rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <x-icon name="o-tag" class="h-3 w-3 mr-1" />
                                {{ $office->officeType?->name }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 shadow rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <x-icon name='o-check-circle' class="h-3 w-3 mr-1" />
                                {{ $office->code }}
                            </span>
                        </td>
                        {{-- Head --}}
                        <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $office->head ? $office->head->first_name . ' ' . $office->head->last_name : '-' }}
                            </span>
                        </td>
                        {{-- Contact --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                @if ($office->contact_phone)
                                    <div class="flex items-center">
                                        <x-icon name="o-phone" class="h-4 w-4 mr-1 text-gray-400" />
                                        {{ $office->contact_phone }}
                                    </div>
                                @elseif($office->contact_email)
                                    <div class="flex items-center">
                                        <x-icon name="o-envelope" class="h-4 w-4 mr-1 text-gray-400" />
                                        {{ $office->contact_email }}
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        {{-- Location --}}
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 min-w-40"> 
                            {{ $office->office_location ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $office->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $office->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if ($office->deleted_at)
                                <span
                                    class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Deleted
                                </span>
                            @endif
                        </td>
                        {{-- Actions Cell --}}
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1">
                                <!-- View button -->
                                <x-button icon="o-eye" wire:click="openViewModal({{ $office->id }})"
                                    class="btn btn-ghost btn-xs h-6 w-6  text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" />

                                @if (!$office->deleted_at)
                                    <!-- Edit button -->
                                    <x-button icon="o-pencil" wire:click="openModal({{ $office->id }})"
                                        class="h-6 w-6  btn btn-ghost btn-xs text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner tooltip-left="Edit Office" />

                                    <!-- Toggle active button -->
                                    <x-button icon="{{ $office->is_active ? 'o-x-circle' : 'o-check-circle' }}"
                                        wire:click="toggleActive({{ $office->id }})"
                                        class="h-6 w-6 btn btn-ghost btn-xs {{ $office->is_active ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }}"
                                        spinner
                                        tooltip-left="{{ $office->is_active ? 'Deactivate' : 'Activate' }} Office" />
                                    <!-- Delete button -->
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $office->id }})"
                                        class="h-6 w-6 btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Delete Office" />
                                @else
                                    <!-- Restore button -->
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $office->id }})"
                                        class="h-6 w-6 btn btn-ghost btn-xs text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner tooltip-left="Restore Office" />
                                    <!-- Permanent Delete button -->
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $office->id }})"
                                        class="h-6 w-6 btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Permanently Delete Office" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}"
                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"> {{-- +2 for checkbox and actions --}}
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-12 w-12 text-gray-400 mb-3" />
                                <span class="text-lg font-medium">No offices found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new office.' }}
                                </p>
                                @if ($search)
                                    <button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm">
                                        Clear Search
                                    </button>
                                @else
                                    <button wire:click="openModal" class="mt-3 btn btn-primary btn-sm">
                                        Add Your First Office
                                    </button>
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
        {{ $offices->links() }}
    </div>

    <!-- Add/Edit Office Modal -->
    <x-modal wire:model="showModal" :title="$officeId ? 'Edit Office' : 'Add New Office'" box-class="max-w-2xl" separator persistent
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab"> {{-- Ensure wire:model binds correctly --}}

                {{-- Basic Info Tab --}}
                <x-tab name="basic" label="Basic Info" icon="o-information-circle" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name Field -->
                        <div>
                            <x-input wire:model="name" label="Office Name" placeholder="Enter office name" inline
                                required />
                        </div>

                        <!-- Code Field -->
                        <div>
                            <x-input wire:model="code" label="Office Code" placeholder="Enter unique code (e.g. REG, VPA)"
                                inline required />
                        </div>

                        <!-- Office Type Field -->
                        <div>
                            <x-select wire:model="office_type_id" label="Office Type" :options="$officeTypes"
                                placeholder="Select type" inline required />
                        </div>

                        <!-- Location Field -->
                        <div>
                            <x-input wire:model="office_location" label="Location" placeholder="e.g. Admin Bldg, Room 201"
                                inline />
                        </div>

                        <!-- Established Year Field -->
                        <div>
                            <x-input wire:model="established_year" label="Established Year" type="number"
                                placeholder="YYYY" min="1800" max="{{ date('Y') }}" inline />
                        </div>

                        <!-- Active Status Field -->
                        <div class="flex items-center"> {{-- Align toggle with label --}}
                            <x-toggle wire:model.live="is_active" label="Active Status" class="self-center"
                                hint="{{ $is_active ? '🌟 Ready to use' : '⏸️ Currently inactive' }}" checked />
                        </div>

                        <!-- Description Field -->
                        <div class="md:col-span-2">
                            <x-textarea wire:model="description" label="Description" placeholder="Enter office details"
                                rows="3" inline />
                        </div>
                    </div>
                </x-tab>

                {{-- Hierarchy & Head Tab --}}
                <x-tab name="hierarchy" label="Hierarchy & Head" icon="o-users" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Head of Office Field -->
                        <div>
                            <x-select wire:model="head_id" label="Head of Office" :options="$employees" option-label="full_name"
                                placeholder="Select head" inline />
                        </div>

                        <!-- Head Appointment Date Field -->
                        <div>
                            <x-datepicker wire:model="head_appointment_date" label="Appointment Date" icon="o-calendar"
                                inline />
                        </div>

                        <!-- Parent Office Field -->
                        <div class="md:col-span-2">
                            <x-select wire:model="parent_office_id" label="Parent Office" :options="$parentOffices"
                                placeholder="Select parent (if any)" inline />
                        </div>
                    </div>
                </x-tab>

                {{-- Contact Info Tab --}}
                <x-tab name="contact" label="Contact Info" icon="o-phone" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Contact Phone Field -->
                        <div>
                            <x-input wire:model="contact_phone" label="Contact Phone" placeholder="Enter phone number"
                                inline />
                        </div>

                        <!-- Contact Email Field -->
                        <div>
                            <x-input wire:model="contact_email" label="Contact Email" type="email"
                                placeholder="Enter email address" inline />
                        </div>
                    </div>
                </x-tab>

            </x-tabs>
        </x-form>

        <x-slot:actions>
            {{-- Buttons styled like campuses modal footer --}}
            <button wire:click="closeModal" type="button" class="btn btn-ghost">
                Cancel
            </button>
            <button wire:click="save" type="button" class="btn btn-primary" wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                {{ $officeId ? 'Update Office' : 'Create Office' }}
            </button>
        </x-slot:actions>
    </x-modal>

    <!-- View Office Modal -->
    <x-modal wire:model="showViewModal" title="View Office Details" separator box-class="max-w-3xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($viewOffice)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">

                {{-- Header Section --}}
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $viewOffice->name }}
                        </h3>
                        <div class="flex flex-wrap gap-2 items-center">
                            <span title="Office Type"
                                class="inline-flex items-center px-3 py-1 shadow rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                <x-icon name="o-building-office-2" class="h-3 w-3 mr-1" />
                                {{ $viewOffice->officeType?->name ?? 'N/A' }}
                            </span>
                            <span title="Office Code"
                                class="inline-flex items-center px-3 py-1 shadow rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <x-icon name="o-tag" class="h-3 w-3 mr-1" />
                                {{ $viewOffice->code }}
                            </span>
                            
                            <span title="Office Active Status"
                                class="inline-flex items-center px-3 py-1 shadow rounded-full text-xs font-semibold {{ $viewOffice->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                <x-icon name="{{ $viewOffice->is_active ? 'o-check-circle' : 'o-x-circle' }}"
                                    class="h-3 w-3 mr-1" />
                                {{ $viewOffice->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if ($viewOffice->deleted_at)
                                <span
                                    class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" />
                                    Deleted
                                </span>
                            @endif
                        </div>
                    </div>
                    {{-- Optional: Add logo or icon here --}}
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">

                    {{-- Left Column --}}
                    <div class="space-y-4">
                        {{-- Head of Office --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-user-circle" class="inline-block h-4 w-4 mr-1" /> Head of Office
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->head ? $viewOffice->head->first_name . ' ' . $viewOffice->head->last_name : 'Not Assigned' }}
                                @if ($viewOffice->head_appointment_date)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                        (Appointed: {{ $viewOffice->head_appointment_date->format('M d, Y') }})
                                    </span>
                                @endif
                            </p>
                        </div>

                        {{-- Parent Office --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-arrow-up-circle" class="inline-block h-4 w-4 mr-1" /> Parent Office
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->parentOffice?->name ?? 'None' }}
                            </p>
                        </div>

                        {{-- Location --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-map-pin" class="inline-block h-4 w-4 mr-1" /> Location
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->office_location ?: 'Not specified' }}
                            </p>
                        </div>

                        {{-- Established Year --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-calendar-days" class="inline-block h-4 w-4 mr-1" /> Established
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->established_year ?: 'Not specified' }}
                            </p>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-4">
                        {{-- Contact Info --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-phone" class="inline-block h-4 w-4 mr-1" /> Contact Info
                            </div>
                            <div class="text-sm text-gray-900 dark:text-white space-y-1">
                                @if ($viewOffice->contact_phone)
                                    <div class="flex items-center">
                                        <x-icon name="o-phone" class="h-4 w-4 mr-2 text-gray-400" />
                                        {{ $viewOffice->contact_phone }}
                                    </div>
                                @endif
                                @if ($viewOffice->contact_email)
                                    <div class="flex items-center">
                                        <x-icon name="o-envelope" class="h-4 w-4 mr-2 text-gray-400" />
                                        {{ $viewOffice->contact_email }}
                                    </div>
                                @endif
                                @if (!$viewOffice->contact_phone && !$viewOffice->contact_email)
                                    Not specified
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                <x-icon name="o-document-text" class="inline-block h-4 w-4 mr-1" /> Description
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow-inner">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $viewOffice->description ?: 'No description provided.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timestamps Section --}}
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center sm:text-left">
                        {{-- Created At --}}
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Created At</div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->created_at ? $viewOffice->created_at->format('M d, Y h:i A') : '-' }}
                            </p>
                        </div>

                        {{-- Last Updated At --}}
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Last Updated</div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOffice->updated_at ? $viewOffice->updated_at->format('M d, Y h:i A') : '-' }}
                            </p>
                        </div>

                        {{-- Deleted At (Conditional) --}}
                        @if ($viewOffice->deleted_at)
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2 sm:p-0 sm:bg-transparent sm:dark:bg-transparent">
                                <div class="text-xs text-red-600 dark:text-red-400 uppercase">Deleted At</div>
                                <p class="text-sm text-red-800 dark:text-red-300">
                                    {{ $viewOffice->deleted_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div> {{-- End p-6 space-y-6 --}}
        @else
            {{-- Skeleton Loader --}}
            <div class="p-8 flex justify-center items-center">
                <div class="w-full max-w-md animate-pulse space-y-6">
                    {{-- Header Skeleton --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-48 mb-2"></div>
                            <div class="flex gap-2">
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-20"></div>
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-16"></div>
                                <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-16"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Details Grid Skeleton --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        {{-- Left Column Skeleton --}}
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/4"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-2/3"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/4"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/3"></div>
                            </div>
                        </div>
                        {{-- Right Column Skeleton --}}
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-2/3"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/4 mb-1"></div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-full"></div>
                                    <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Timestamps Skeleton --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mx-auto sm:mx-0"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mx-auto sm:mx-0"></div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mx-auto sm:mx-0"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mx-auto sm:mx-0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <div class="flex justify-end gap-3 w-full">
                {{-- Edit button --}}
                @if ($viewOffice && !$viewOffice->deleted_at)
                    <x-button label="Edit Office" icon="o-pencil" wire:click="openModal({{ $viewOffice->id }})"
                        class="btn-primary" spinner />
                @endif
                {{-- Close button --}}
                <x-button label="Close" wire:click="closeModal" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (using MaryUI Modal, styled similarly) -->
    <x-modal wire:model="confirmingDeletion" title="Delete Office" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this office? This action will soft delete the record, and it can be
                    restored later.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete" wire:click="delete" class="btn-error" wire:loading.attr="disabled"
                wire:target="delete" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Delete Confirmation --}}
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Offices" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedOffices) }} selected offices? This action will
                    soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedOffices) }} Offices" wire:click="bulkDelete" class="btn-error"
                wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    {{-- Restore Confirmation --}}
    <x-modal wire:model="confirmingRestore" title="Restore Office" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this office?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore" wire:click="restore" class="btn-success" wire:loading.attr="disabled"
                wire:target="restore" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Restore Confirmation --}}
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Offices" separator persistent class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedOffices) }} selected offices?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedOffices) }} Offices" wire:click="bulkRestore"
                class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    {{-- Permanent Delete Confirmation --}}
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Office" separator persistent
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this office? This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="permanentDelete" class="btn-error"
                wire:loading.attr="disabled" wire:target="permanentDelete" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Permanent Delete Confirmation --}}
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Offices" separator persistent
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedOffices) }} selected offices? This
                    action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedOffices) }} Offices"
                wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled"
                wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

</div>
