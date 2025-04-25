<?php

use App\Models\OfficeType;
use App\Services\OfficeTypeService;
use App\Http\Requests\OfficeTypeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException; // Added for potential use

new class extends Component
{
    use WithPagination, Toast; // Enable pagination and toasts

    // Component properties matching campuses.blade.php structure
    public $officeTypeId = null;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    // UI state properties matching campuses.blade.php
    public $perPage = 10;
    #[Url]
    public $search = '';
    #[Url]
    public $sortField = 'name'; // Changed from sortBy
    #[Url]
    public $sortDirection = 'asc';
    #[Url]
    public $showDeletedRecords = false; // Changed from with_trashed/only_trashed
    public $selectedOfficeTypes = [];
    public $selectAll = false;

    // Modal state matching campuses.blade.php
    public $showModal = false; // Replaces officeTypeModal
    public $showViewModal = false;
    public $viewOfficeType = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false; // Added
    public $confirmingBulkPermanentDeletion = false; // Added
    public $confirmingRestore = false; // Added
    public $confirmingBulkRestore = false; // Added

    // Headers for the table (used in blade loop)
    public $headers = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'code', 'label' => 'Code'],
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'is_active', 'label' => 'Status'], // Combined status
    ];

    // Reset pagination on search or perPage change
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
        $this->selectedOfficeTypes = [];
        $this->selectAll = false;
    }

    // Sorting functionality matching campuses.blade.php
    public function sortBy($field)
    {
        // Ensure the field is valid for sorting to prevent errors
        $validSortFields = ['name', 'code', 'is_active', 'created_at', 'updated_at']; // Add other sortable fields if needed
        if (!in_array($field, $validSortFields)) {
            return; // Do nothing if the field is not valid
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Handle selection of all visible items matching campuses.blade.php
    public function updatedSelectAll($value)
    {
        // Get IDs of currently displayed items for select all
        $items = $this->getOfficeTypes()->paginate($this->perPage);
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            // Add current page IDs to selection, avoiding duplicates
            $this->selectedOfficeTypes = array_unique(array_merge($this->selectedOfficeTypes, $itemIds));
        } else {
            // Remove current page IDs from selection
            $this->selectedOfficeTypes = array_diff($this->selectedOfficeTypes, $itemIds);
        }
    }

    // Update selected items when changing individual selections matching campuses.blade.php
    public function updatedSelectedOfficeTypes($value)
    {
        // Check if all items on the current page are selected
        $items = $this->getOfficeTypes()->paginate($this->perPage);
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedOfficeTypes));
    }

    // Modal management matching campuses.blade.php
    public function openModal($officeTypeId = null)
    {
        $this->resetValidation();
        // Reset only specific properties, keeping filters/sorting intact
        $this->resetExcept(['search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords', 'selectedOfficeTypes', 'selectAll', 'headers']);

        $this->showModal = true;
        $this->officeTypeId = $officeTypeId;

        if ($officeTypeId) {
            $officeType = OfficeType::findOrFail($officeTypeId);
            $this->name = $officeType->name;
            $this->code = $officeType->code;
            $this->description = $officeType->description;
            $this->is_active = $officeType->is_active;
        } else {
             // Explicitly reset fields for new entry
            $this->name = '';
            $this->code = '';
            $this->description = '';
            $this->is_active = true;
        }
    }

    public function openViewModal($officeTypeId)
    {
        $this->viewOfficeType = OfficeType::withTrashed()->findOrFail($officeTypeId); // Include trashed for viewing
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
        $this->officeTypeId = null; // Reset ID when closing any modal
        $this->viewOfficeType = null; // Reset view object
    }

    // CRUD Operations adapted from campuses.blade.php
    public function save(OfficeTypeService $officeTypeService) // Inject service
    {
        // Use OfficeTypeRequest for validation rules, messages, attributes
        $request = new OfficeTypeRequest();
        $currentId = $this->officeTypeId;
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        // Data to validate from component state
        $dataToValidate = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        // Perform validation
        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        // Prepare code (uppercase) - similar to CampusRequest prepareForValidation
        $validatedData['code'] = strtoupper($validatedData['code']);

        try {
            if ($this->officeTypeId) {
                // Update existing office type
                $officeType = OfficeType::findOrFail($this->officeTypeId);
                $officeTypeService->updateOfficeType($officeType, $validatedData);
                $this->success('Office Type updated successfully! 🏢'); // Use MaryUI toast trait
            } else {
                // Create new office type
                $officeTypeService->createOfficeType($validatedData);
                $this->success('New Office Type added successfully! ✨'); // Use MaryUI toast trait
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error("Office Type Save Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('An error occurred while saving the Office Type.'); // Use MaryUI toast trait
        }
    }

    // Toggle active status matching campuses.blade.php
    public function toggleActive($officeTypeId, OfficeTypeService $officeTypeService)
    {
        try {
            $officeType = OfficeType::findOrFail($officeTypeId);
            // Assuming service has a method like this, or implement logic here
            $officeType->update(['is_active' => !$officeType->is_active]);
            $statusText = $officeType->is_active ? 'activated' : 'deactivated';
            $this->success("Office Type {$statusText} successfully! 🔄");
        } catch (\Exception $e) {
            \Log::error("Toggle Active Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status.');
        }
    }

     // Handle Bulk Actions Dropdown Change
    public function handleBulkAction($action)
    {
        if (!$action) return;

        if (empty($this->selectedOfficeTypes)) {
            $this->warning('Please select office types first 🤔');
            return;
        }

        // Map action value to confirmation modal property
        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'bulkToggleActive' => null, // Direct action, no confirmation needed here
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];

        if ($action === 'bulkToggleActive') {
            $this->bulkToggleActive(); // Call directly
        } elseif (isset($confirmationMap[$action])) {
            $this->{$confirmationMap[$action]} = true; // Show confirmation modal
        }

        // Reset the dropdown selection visually (optional, depends on desired UX)
        // $this->bulkAction = '';
    }


    // Bulk toggle active status matching campuses.blade.php
    public function bulkToggleActive(OfficeTypeService $officeTypeService)
    {
        if (empty($this->selectedOfficeTypes)) {
             // This check might be redundant if called via handleBulkAction, but good for direct calls
            $this->warning('Please select office types to toggle status 🤔');
            return;
        }

        try {
            // Determine target state (activate if more than half are inactive, otherwise deactivate)
            $officeTypes = OfficeType::whereIn('id', $this->selectedOfficeTypes)->get();
            $inactiveCount = $officeTypes->where('is_active', false)->count();
            $setActive = $inactiveCount >= $officeTypes->count() / 2;

            OfficeType::whereIn('id', $this->selectedOfficeTypes)->update(['is_active' => $setActive]);

            $statusText = $setActive ? 'activated' : 'deactivated';
            $this->success(count($this->selectedOfficeTypes) . " office types {$statusText} successfully! 🔄");
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Toggle Active Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status for selected office types.');
        }
    }

    // Soft delete operations matching campuses.blade.php
    public function confirmDelete($officeTypeId)
    {
        $this->officeTypeId = $officeTypeId;
        $this->confirmingDeletion = true;
    }

    public function delete(OfficeTypeService $officeTypeService)
    {
        try {
            $officeType = OfficeType::findOrFail($this->officeTypeId);
            $officeTypeService->deleteOfficeType($officeType); // Use service if available
            // Or direct delete: $officeType->delete();
            $this->confirmingDeletion = false;
            $this->success('Office Type deleted successfully! 🗑️');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Office Type Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false; // Close modal even on error
            $this->error('Failed to delete Office Type.');
        }
    }

    // confirmBulkDelete is triggered by handleBulkAction setting confirmingBulkDeletion = true

    public function bulkDelete(OfficeTypeService $officeTypeService)
    {
        try {
            // Use service or direct delete
            OfficeType::whereIn('id', $this->selectedOfficeTypes)->delete();
            $this->confirmingBulkDeletion = false;
            $this->success(count($this->selectedOfficeTypes) . ' office types deleted successfully! 🗑️');
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false; // Close modal even on error
            $this->error('Failed to delete selected office types.');
        }
    }

    // Restore operations matching campuses.blade.php
    public function confirmRestore($officeTypeId)
    {
        $this->officeTypeId = $officeTypeId;
        $this->confirmingRestore = true;
    }

    public function restore(OfficeTypeService $officeTypeService)
    {
        try {
            // Use service or direct restore
            OfficeType::withTrashed()->where('id', $this->officeTypeId)->restore();
            $this->confirmingRestore = false;
            $this->success('Office Type restored successfully! ♻️');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Office Type Restore Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false; // Close modal even on error
            $this->error('Failed to restore Office Type.');
        }
    }

    // confirmBulkRestore is triggered by handleBulkAction setting confirmingBulkRestore = true

    public function bulkRestore(OfficeTypeService $officeTypeService)
    {
        try {
            // Use service or direct restore
            OfficeType::withTrashed()->whereIn('id', $this->selectedOfficeTypes)->restore();
            $this->confirmingBulkRestore = false;
            $this->success(count($this->selectedOfficeTypes) . ' office types restored successfully! ♻️');
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Restore Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false; // Close modal even on error
            $this->error('Failed to restore selected office types.');
        }
    }

    // Permanent delete operations matching campuses.blade.php
    public function confirmPermanentDelete($officeTypeId)
    {
        $this->officeTypeId = $officeTypeId;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(OfficeTypeService $officeTypeService)
    {
        try {
            // Use service or direct force delete
             OfficeType::withTrashed()->where('id', $this->officeTypeId)->forceDelete();
            $this->confirmingPermanentDeletion = false;
            $this->success('Office Type permanently deleted! 💥');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Permanent Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete Office Type.');
        }
    }

    // confirmBulkPermanentDelete is triggered by handleBulkAction setting confirmingBulkPermanentDeletion = true

    public function bulkPermanentDelete(OfficeTypeService $officeTypeService)
    {
        try {
            // Use service or direct force delete
            OfficeType::withTrashed()->whereIn('id', $this->selectedOfficeTypes)->forceDelete();
            $this->confirmingBulkPermanentDeletion = false;
            $this->success(count($this->selectedOfficeTypes) . ' office types permanently deleted! 💥');
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Permanent Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete selected office types.');
        }
    }

    // Fetch office types with applied filters matching campuses.blade.php structure
    private function getOfficeTypes()
    {
        $query = OfficeType::query();

        // Handle soft deleted records
        if ($this->showDeletedRecords) {
            $query->withTrashed();
        }

        // Apply search filter across multiple fields
        if ($this->search) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        // Ensure the sort field is valid before applying
        $validSortFields = ['name', 'code', 'is_active', 'created_at', 'updated_at']; // Match fields used in sortBy
        if (in_array($this->sortField, $validSortFields)) {
             $query->orderBy($this->sortField, $this->sortDirection);
        } else {
             $query->orderBy('name', 'asc'); // Default sort if invalid field somehow selected
        }


        return $query;
    }

    // Render method adapted from campuses.blade.php
    public function render(): mixed
    {
        $officeTypesQuery = $this->getOfficeTypes();
        $officeTypes = $officeTypesQuery->paginate($this->perPage);

        // Update selectAll status based on current page items
        $currentPageIds = $officeTypes->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedOfficeTypes));

        return view('livewire.office-types', [
            'officeTypes' => $officeTypes,
            'headers' => $this->headers // Pass headers to the view
        ]);
    }
}

?>

<div>
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center pb-4 bg-white dark:bg-gray-900 px-4 pt-4 rounded-t-lg">
        <div class="w-full sm:w-auto mb-3 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                {{-- Use a relevant icon for Office Types --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /> {{-- Building Icon --}}
                </svg>
                Office Type Management
                <span class="ml-2 text-xs bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full">
                    {{ $officeTypes->total() }} types
                    @if($showDeletedRecords)
                        <span class="ml-1 text-xs bg-red-100 text-red-800 py-1 px-2 rounded-full">
                            with trashed
                        </span>
                    @endif
                </span>
            </h1>
        </div>

        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <!-- Search box -->
             <div class="relative w-full sm:w-64">
                 {{-- Use MaryUI input but styled similarly if needed, or use standard input --}}
                 <x-input placeholder="Search Office Types..." wire:model.live.debounce.300ms="search" clearable icon="o-magnifying-glass" class="input input-bordered input-sm w-full pl-10"/>
                 <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                     <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                         <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                     </svg>
                 </div>
             </div>

            <!-- Add new office type button -->
            <button wire:click="openModal" class="btn btn-primary btn-sm flex items-center justify-center"> {{-- Use standard button classes --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Type
            </button>
        </div>
    </div>

    <!-- Filters and Bulk Actions Row -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <!-- Show deleted checkbox -->
            <label class="flex items-center space-x-2 text-sm cursor-pointer">
                <x-checkbox wire:model.live="showDeletedRecords" class="checkbox-sm checkbox-primary" right tight /> {{-- MaryUI checkbox --}}
                <span class="text-gray-700 dark:text-gray-300">Show deleted</span>
            </label>

            <!-- Per page selector -->
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                {{-- Use MaryUI select --}}
                <x-select wire:model.live="perPage" :options="[['id' => 5, 'name' => 5], ['id' => 10, 'name' => 10], ['id' => 25, 'name' => 25], ['id' => 50, 'name' => 50], ['id' => 100, 'name' => 100]]" class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="flex items-center space-x-2">
             {{-- Use MaryUI select for bulk actions --}}
            <x-select
                placeholder="Bulk actions"
                :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'bulkToggleActive', 'name' => 'Toggle Active Status'],
                    ...( $showDeletedRecords ? [
                        ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                        ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete']
                    ] : [])
                ]"
                class="select select-bordered select-sm py-0"
                wire:change="handleBulkAction($event.target.value)" {{-- Custom handler needed --}}
                wire:model="bulkActionTrigger" {{-- Dummy model to allow wire:change --}}
            />

            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ count($selectedOfficeTypes) }} selected
            </span>
        </div>
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
                    {{-- Dynamic Headers --}}
                    @foreach($headers as $header)
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer {{ $header['class'] ?? '' }}"
                            wire:click="sortBy('{{ $header['key'] }}')">
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                {{-- Sort Indicator --}}
                                @if($sortField === $header['key'])
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    {{-- Actions Header --}}
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($officeTypes as $officeType)
                    <tr wire:key="office-type-{{ $officeType->id }}" class="{{ $officeType->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} {{ !$officeType->is_active ? 'bg-gray-50 dark:bg-gray-800/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors">
                        {{-- Checkbox Cell --}}
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedOfficeTypes" value="{{ (string)$officeType->id }}" class="checkbox-sm checkbox-primary" />
                        </td>
                        {{-- Data Cells --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $officeType->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $officeType->code }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs" title="{{ $officeType->description }}">
                                {{ Str::limit($officeType->description, 50) ?: '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $officeType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $officeType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($officeType->deleted_at)
                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Deleted
                                </span>
                            @endif
                        </td>
                        {{-- Actions Cell --}}
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1">
                                <!-- View button -->
                                <button wire:click="openViewModal({{ $officeType->id }})" class="btn btn-ghost btn-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="View Details">
                                    <x-icon name="o-eye" class="h-4 w-4"/>
                                </button>

                                @if(!$officeType->deleted_at)
                                    <!-- Edit button -->
                                    <button wire:click="openModal({{ $officeType->id }})" class="btn btn-ghost btn-xs text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                        <x-icon name="o-pencil" class="h-4 w-4"/>
                                    </button>

                                    <!-- Toggle active button -->
                                    <button wire:click="toggleActive({{ $officeType->id }})" class="btn btn-ghost btn-xs {{ $officeType->is_active ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }}" title="{{ $officeType->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($officeType->is_active)
                                            <x-icon name="o-x-circle" class="h-4 w-4"/> {{-- Icon for deactivate --}}
                                        @else
                                            <x-icon name="o-check-circle" class="h-4 w-4"/> {{-- Icon for activate --}}
                                        @endif
                                    </button>

                                    <!-- Delete button -->
                                    <button wire:click="confirmDelete({{ $officeType->id }})" class="btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <x-icon name="o-trash" class="h-4 w-4"/>
                                    </button>
                                @else
                                    <!-- Restore button -->
                                    <button wire:click="confirmRestore({{ $officeType->id }})" class="btn btn-ghost btn-xs text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Restore">
                                        <x-icon name="o-arrow-path" class="h-4 w-4"/>
                                    </button>

                                    <!-- Permanent Delete button -->
                                    <button wire:click="confirmPermanentDelete({{ $officeType->id }})" class="btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Permanently Delete">
                                         <x-icon name="o-no-symbol" class="h-4 w-4"/> {{-- More destructive icon --}}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"> {{-- +2 for checkbox and actions --}}
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-12 w-12 text-gray-400 mb-3"/>
                                <span class="text-lg font-medium">No office types found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new office type.' }}
                                </p>
                                @if($search)
                                    <button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm">
                                        Clear Search
                                    </button>
                                @else
                                    <button wire:click="openModal" class="mt-3 btn btn-primary btn-sm">
                                        Add Your First Type
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
    <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
        {{ $officeTypes->links() }}
    </div>

    <!-- Add/Edit Office Type Modal -->
    {{-- Use MaryUI Modal but structure content similarly to campuses --}}
    <x-modal wire:model="showModal" :title="$officeTypeId ? 'Edit Office Type' : 'Add New Office Type'" separator persistent class="sm:max-w-2xl">
         <x-form wire:submit.prevent="save">
            {{-- Mimic grid layout from campuses modal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                <div>
                    <x-input wire:model="name" label="Type Name" placeholder="Enter type name" required />
                    {{-- Error display handled by MaryUI automatically or use @error block if needed --}}
                </div>
                <div>
                    <x-input wire:model="code" label="Type Code" placeholder="Enter type code (e.g. ACAD, ADMIN)" required />
                </div>
                <div class="md:col-span-2">
                    <x-textarea wire:model="description" label="Description" placeholder="Enter type description" rows="3" />
                </div>
                 <div>
                    <x-checkbox wire:model="is_active" label="Active Type" right tight/>
                </div>
            </div>
        </x-form>

        <x-slot:actions>
             {{-- Buttons styled like campuses modal footer --}}
            <button wire:click="closeModal" type="button" class="btn btn-ghost">
                Cancel
            </button>
            <button wire:click="save" type="button" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                 <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                {{ $officeTypeId ? 'Update Type' : 'Create Type' }}
            </button>
        </x-slot:actions>
    </x-modal>

    <!-- View Office Type Modal -->
    <x-modal wire:model="showViewModal" title="View Office Type" separator persistent class="sm:max-w-xl">
        @if($viewOfficeType)
            <div class="p-4 space-y-4">
                 <div class="flex justify-between items-start">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center flex-wrap gap-2">
                        <span class="mr-2 font-semibold">{{ $viewOfficeType->name }}</span>
                        <span class="px-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $viewOfficeType->code }}
                        </span>
                        <span class="px-2 text-xs leading-5 font-semibold rounded-full {{ $viewOfficeType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $viewOfficeType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                         @if($viewOfficeType->deleted_at)
                            <span class="px-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Deleted
                            </span>
                        @endif
                    </h3>
                </div>
                 <dl class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                     <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $viewOfficeType->description ?: 'No description provided' }}
                        </dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $viewOfficeType->created_at ? $viewOfficeType->created_at->format('M d, Y h:i A') : '-' }}
                        </dd>
                    </div>
                     <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                             {{ $viewOfficeType->updated_at ? $viewOfficeType->updated_at->format('M d, Y h:i A') : '-' }}
                        </dd>
                    </div>
                     @if($viewOfficeType->deleted_at)
                         <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deleted At</dt>
                            <dd class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $viewOfficeType->deleted_at->format('M d, Y h:i A') }}
                            </dd>
                        </div>
                    @endif
                 </dl>
            </div>
        @else
            <p class="p-4">Loading details...</p>
        @endif

        <x-slot:actions>
             {{-- Buttons styled like campuses modal footer --}}
            @if($viewOfficeType && !$viewOfficeType->deleted_at)
                <button wire:click="openModal({{ $viewOfficeType->id }})" type="button" class="btn btn-primary">
                    Edit Type
                </button>
            @endif
             <button wire:click="closeModal" type="button" class="btn btn-ghost">
                Close
            </button>
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (using MaryUI Modal, styled similarly) -->
    {{-- Delete Confirmation --}}
    <x-modal wire:model="confirmingDeletion" title="Delete Office Type" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this office type? This action will soft delete the record, and it can be restored later.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete" wire:click="delete" class="btn-error" wire:loading.attr="disabled" wire:target="delete" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Delete Confirmation --}}
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Office Types" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
             <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
             <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedOfficeTypes) }} selected office types? This action will soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedOfficeTypes) }} Types" wire:click="bulkDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    {{-- Restore Confirmation --}}
    <x-modal wire:model="confirmingRestore" title="Restore Office Type" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
             <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
             <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this office type?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore" wire:click="restore" class="btn-success" wire:loading.attr="disabled" wire:target="restore" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Restore Confirmation --}}
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Office Types" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
             <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
             <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedOfficeTypes) }} selected office types?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedOfficeTypes) }} Types" wire:click="bulkRestore" class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    {{-- Permanent Delete Confirmation --}}
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Office Type" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
             <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
             <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this office type? This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="permanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="permanentDelete" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Permanent Delete Confirmation --}}
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Office Types" separator persistent class="sm:max-w-lg">
         <div class="p-4 flex items-start">
             <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
             <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedOfficeTypes) }} selected office types? This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedOfficeTypes) }} Types" wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

</div>
