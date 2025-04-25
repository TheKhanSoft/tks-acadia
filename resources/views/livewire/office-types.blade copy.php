<?php

use App\Models\OfficeType;
use App\Services\OfficeTypeService;
use App\Http\Requests\OfficeTypeRequest; // Use the request for validation later
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast; // For notifications
use Illuminate\Support\Facades\Validator; // Ensure Validator facade is imported

use function Livewire\Volt\{state, computed, uses, mount, usesPagination, with};

uses(WithPagination::class, Toast::class); // Enable pagination and toasts

// Initial state variables
state([
    'headers' => [ // Re-add sortable where needed
        ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'code', 'label' => 'Code', 'sortable' => true],
        ['key' => 'is_active', 'label' => 'Active', 'sortable' => true],
        ['key' => 'description', 'label' => 'Description'],
    ],
    'officeTypeModal' => false, // Controls the create/edit modal
    'officeType' => null, // Holds the OfficeType being edited
    'name' => '',
    'code' => '',
    'description' => '',
    'is_active' => true,
    // Add filtering state variables here (removed sorting)
    'search_term' => '',
    'per_page' => 10,
    'with_trashed' => false,
    'only_trashed' => false,
    'lastActionError' => null, // Flag for errors in actions
    // Re-add sorting state
    'sortBy' => 'name', 
    'sortDirection' => 'asc',
]);

// Computed property to fetch office types using the service
$officeTypes = computed(function () {
    // Manually resolve the service from the container
    $officeTypeService = app(OfficeTypeService::class); 
    
    $params = [
        'search' => !empty($this->search_term),
        'search_term' => $this->search_term,
        // Re-add sorting params
        'sort_by' => $this->sortBy,
        'sort_dir' => $this->sortDirection,
        'per_page' => $this->per_page,
        'with_trashed' => $this->with_trashed,
        'only_trashed' => $this->only_trashed,
        'is_active' => null, // Add filtering by active status if needed later
    ];
    return $officeTypeService->getPaginatedOfficeTypes($params);
});

// Re-add sortBy action closure (Mary UI convention)
$sortBy = function (array $sort) {
    $this->sortBy = $sort['column'];
    $this->sortDirection = $sort['direction'];
};

// Provide computed property to the view
with(fn () => [
    'officeTypes' => $this->officeTypes,
]);

// Action to open the modal for creating a new office type
$create = function (): void {
    $this->officeType = null; // Ensure we are in create mode
    $this->name = '';
    $this->code = '';
    $this->description = '';
    $this->is_active = true;
    $this->officeTypeModal = true;
};

// Action to open the modal for editing an existing office type
$edit = function (OfficeType $officeType): void {
    $this->officeType = $officeType; // Set the office type being edited
    $this->name = $officeType->name;
    $this->code = $officeType->code;
    $this->description = $officeType->description;
    $this->is_active = $officeType->is_active;
    $this->officeTypeModal = true;
};

// Action to save (create or update) the office type
$save = function (OfficeTypeService $officeTypeService) {
    // Manually validate using component state and Form Request rules/messages/attributes
    $request = new OfficeTypeRequest();
    $currentId = $this->officeType?->id;
    $rules = $request->rules($currentId); // Get rules, pass ID if editing
    $messages = $request->messages();     // Get custom messages
    $attributes = $request->attributes(); // Get custom attributes

    // Data to validate from component state
    $dataToValidate = [
        'name' => $this->name,
        'code' => $this->code,
        'description' => $this->description,
        'is_active' => $this->is_active,
    ];

    // Perform validation, passing rules, messages, and attributes
    $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

    $this->lastActionError = null; // Reset error flag before trying

    try {
        if ($this->officeType) {
            // Update existing office type - Pass validated data array
            $officeTypeService->updateOfficeType($this->officeType, $validatedData);
            // Use dispatch with array payload
            $this->dispatch('mary-toast', ['type' => 'success', 'message' => 'Office Type updated successfully.']);
        } else {
            // Create new office type - Pass validated data array
            $officeTypeService->createOfficeType($validatedData);
             // Use dispatch with array payload
            $this->dispatch('mary-toast', ['type' => 'success', 'message' => 'Office Type created successfully.']);
        }
        $this->officeTypeModal = false; // Close modal on success
    } catch (\Exception $e) {
        // Log the detailed error for debugging
        \Log::error("Office Type Save Error: " . $e->getMessage(), ['exception' => $e]);
        // Set the error flag instead of dispatching here
        $this->lastActionError = 'An error occurred while saving.';
    }

    // Check the flag AFTER the try-catch and dispatch if needed
    if ($this->lastActionError) {
        // Use dispatch with array payload
        $this->dispatch('mary-toast', ['type' => 'error', 'message' => $this->lastActionError]);
        $this->lastActionError = null; // Reset flag
    }
};

// Action to delete an office type (soft delete)
$delete = function (OfficeTypeService $officeTypeService, OfficeType $officeType): void {
    $this->lastActionError = null; // Reset error flag

    try {
        $officeTypeService->deleteOfficeType($officeType);
        // Use dispatch with array payload
        $this->dispatch('mary-toast', ['type' => 'success', 'message' => 'Office Type deleted successfully.']);
    } catch (\Exception $e) {
        \Log::error("Office Type Delete Error: " . $e->getMessage(), ['exception' => $e]);
        // Set the error flag
        $this->lastActionError = 'Failed to delete Office Type.';
    }

    // Check the flag AFTER the try-catch and dispatch if needed
    if ($this->lastActionError) {
        // Use dispatch with array payload
        $this->dispatch('mary-toast', ['type' => 'error', 'message' => $this->lastActionError]);
        $this->lastActionError = null; // Reset flag
    }
};

// Action to restore a soft-deleted office type
$restore = function (OfficeTypeService $officeTypeService, int $id): void {
    $this->lastActionError = null; // Reset error flag

    try {
        $officeTypeService->restoreOfficeType($id);
        // Use dispatch with array payload
        $this->dispatch('mary-toast', ['type' => 'success', 'message' => 'Office Type restored successfully.']);
    } catch (\Exception $e) {
        \Log::error("Office Type Restore Error: " . $e->getMessage(), ['exception' => $e]);
        // Set the error flag
        $this->lastActionError = 'Failed to restore Office Type.';
    }

    // Check the flag AFTER the try-catch and dispatch if needed
    if ($this->lastActionError) {
        // Use dispatch with array payload
        $this->dispatch('mary-toast', ['type' => 'error', 'message' => $this->lastActionError]);
        $this->lastActionError = null; // Reset flag
    }
};

?>

<div>
    <div  class="flex flex-col sm:flex-row justify-between items-center pb-4 bg-white dark:bg-gray-900 px-4 pt-4 rounded-t-lg">
    <div class="w-full sm:w-auto mb-3 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Office Types Management
                <span class="ml-2 text-xs bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full">
                    {{ $officeTypes->total() }} Office Types
                   
                </span>
            </h1>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <!-- Search box -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <x-input placeholder="Search Office Type" wire:model.live.debounce.300ms="search" clearable icon="o-magnifying-glass" />
            </div>

            <!-- Add new Office Type button -->
            <x-button label="Add Office Type" wire:click="create" responsive icon="o-plus" class="btn-primary" />
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />

        </div>
    </div>
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-b-lg shadow">
        <!-- TABLE -->
        <x-card>
            <x-table :headers="$headers" :rows="$officeTypes" with-pagination>
                @scope('cell_is_active', $officeType)
                    <x-badge :value="$officeType->is_active ? 'Active' : 'Inactive'" :class="$officeType->is_active ? 'badge-success' : 'badge-ghost'" />
                @endscope

                @scope('cell_description', $officeType)
                    {{ Str::limit($officeType->description, 50) }}
                @endscope

                {{-- Actions column --}}
                @scope('actions', $officeType)
                    <div class="flex justify-end items-center space-x-1">
                        @if($officeType->trashed())
                            <x-button icon="o-arrow-path" wire:click="restore({{ $officeType->id }})" spinner class="btn-sm btn-ghost" tooltip-left="Restore" />
                        @else
                            <x-button icon="o-eye"  wire:click="view({{ $officeType->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 btn-sm btn-ghost"
                                 spinner tooltip-left="View Details"/>
                            <x-button icon="o-pencil" wire:click="edit({{ $officeType->id }})" spinner class="btn-sm btn-ghost" tooltip-left="Edit" />
                            <x-button icon="{{ $officeType->is_active ? 'o-eye-slash' : 'o-eye-slash' }}" wire:click="toggleStatus({{ $officeType->id }})" spinner class="btn-sm btn-ghost" tooltip-left="{{ $officeType->is_active ? 'Deactivate' : 'Activate' }}" />   
                            <x-button icon="o-trash" wire:click="delete({{ $officeType->id }})" wire:confirm="Are you sure?" spinner class="btn-sm btn-ghost text-red-500" tooltip-left="Delete" />
                        @endif
                    </div>
                @endscope
            </x-table>
        </x-card>
    </div>

    

    <!-- CREATE/EDIT MODAL -->
    <x-modal wire:model="officeTypeModal" :title="$officeType ? 'Edit Office Type' : 'Create Office Type'" separator>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required />
            <x-input label="Code" wire:model="code" required />
            <x-textarea label="Description" wire:model="description" hint="Max 1000 chars." rows="3" inline />
            <x-checkbox label="Is Active" wire:model="is_active" right tight />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.officeTypeModal = false" />
                <x-button label="Save" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- TODO: Add Drawer for Filters --}}
    {{-- <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select label="Country" wire:model.live="country_id" :options="$countries" placeholder="Select Country" placeholder-value="0" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer> --}}

    {{-- Debugging: Alpine listener for mary-toast event --}}
    <div x-data x-on:mary-toast.window="console.log('Mary Toast Event Received:', $event.detail)"></div>
</div>
