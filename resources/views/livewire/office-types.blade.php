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
use Illuminate\Validation\ValidationException;

new class extends Component
{
    use WithPagination, Toast; 

    public $officeTypeId = null;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    public $perPage = 10;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeletedRecords = false; 
    public $selectedOfficeTypes = [];
    public $selectAll = false;

    public $showModal = false; 
    public $showViewModal = false;
    public $viewOfficeType = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false; 
    public $confirmingBulkPermanentDeletion = false; 
    public $confirmingRestore = false; 
    public $confirmingBulkRestore = false; 

    public $headers = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'code', 'label' => 'Code'],
        ['key' => 'description', 'label' => 'Description'],
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
        $this->selectedOfficeTypes = [];
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
        $items = $this->getOfficeTypes(new OfficeTypeService);
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        
        if ($value) {
            $this->selectedOfficeTypes = array_unique(array_merge($this->selectedOfficeTypes, $itemIds));
        } else {
            $this->selectedOfficeTypes = array_diff($this->selectedOfficeTypes, $itemIds);
        }
    }

    public function updatedSelectedOfficeTypes($value)
    {   
        $items = $this->getOfficeTypes(new OfficeTypeService);
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedOfficeTypes));
    }

    public function openModal($officeTypeId = null, OfficeTypeService $officeTypeService)
    {
        $this->resetValidation();
        $this->resetExcept(['search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords', 'selectedOfficeTypes', 'selectAll', 'headers']);

        $this->showModal = true;
        $this->officeTypeId = $officeTypeId;

        if ($officeTypeId) {
            $officeType = $officeTypeService->getOfficeType($officeTypeId, true);
            $this->name = $officeType->name;
            $this->code = $officeType->code;
            $this->description = $officeType->description;
            $this->is_active = $officeType->is_active;
        } else {
            $this->name = '';
            $this->code = '';
            $this->description = '';
            $this->is_active = true;
        }
    }

    public function openViewModal($officeTypeId, OfficeTypeService $officeTypeService)
    {
        $this->viewOfficeType = $officeTypeService->getOfficeType($officeTypeId, true);; 
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
        $this->officeTypeId = null; 
        $this->viewOfficeType = null;
    }

    public function save(OfficeTypeService $officeTypeService) 
    {
        $request = new OfficeTypeRequest();
        $currentId = $this->officeTypeId;
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        $validatedData['code'] = strtoupper($validatedData['code']);

        try {
            if ($this->officeTypeId) {
                $officeType = $officeTypeService->getOfficeType($this->officeTypeId);
                $officeTypeService->updateOfficeType($officeType, $validatedData);
                $this->success('Office Type updated successfully! 🏢'); 
            } else {
                $officeTypeService->createOfficeType($validatedData);
                $this->success('New Office Type added successfully! ✨'); 
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error("Office Type Save Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('An error occurred while saving the Office Type.'); 
        }
    }

    public function toggleActive($officeTypeId, OfficeTypeService $officeTypeService)
    {
        try {
            $officeType = $officeTypeService->getOfficeType($officeTypeId);
            $officeTypeService->toggleActiveStatus($officeType);
            $statusText = $officeType->is_active ? 'activated' : 'deactivated';
            $this->success("Office Type {$statusText} successfully! 🔄");
        } catch (\Exception $e) {
            \Log::error("Toggle Active Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status.');
        }
    }
    
    public function handleBulkAction($action)
    {
        if (!$action) return;

        if (empty($this->selectedOfficeTypes)) {
            $this->warning('Please select office types first 🤔');
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'bulkToggleActive' => null,
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];
        ;
        if ($action === 'bulkToggleActive') {
            $this->bulkToggleActive(new OfficeTypeService()); 
        } elseif (isset($confirmationMap[$action])) {
            $this->{$confirmationMap[$action]} = true; 
        }

        // Reset the dropdown selection visually (optional, depends on desired UX)
        $this->handleBulkAction = '';
    }

    public function bulkToggleActive(OfficeTypeService $officeTypeService)
    {
        if (empty($this->selectedOfficeTypes)) {
            $this->warning('Please select office types to toggle status 🤔');
            return;
        }

        try {
            $officeTypes = $officeTypeService->getOfficeTypes($this->selectedOfficeTypes)->get();
            $toggledStatuses = $officeTypeService->bulkToggleActiveStatus($officeTypes);
            $this->success(
                "Bulk toggle operation for Office Type successful🔄", 
                "<br />Total Toggles: <b>$toggledStatuses[totalToggledCount]</b><br />
                Activated: <b>$toggledStatuses[activatedCount]</b><br />
                Deactivated: <b>$toggledStatuses[deactivatedCount]</b><br />
                " 
            );
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
            $this->handleBulkAction = '';
            
        } catch (\Exception $e) {
            \Log::error("Bulk Toggle Active Error: " . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status for selected office types.');
        }
    }

    public function confirmDelete($officeTypeId)
    {
        $this->officeTypeId = $officeTypeId;
        $this->confirmingDeletion = true;
    }

    public function delete(OfficeTypeService $officeTypeService)
    {
        try {
            $successful = $officeTypeService->deleteOfficeTypeById($this->officeTypeId); 
            $this->confirmingDeletion = false;
            $successful ? $this->warning('Office Type deleted successfully! 🗑️') : $this->error('Failed to delete Office Type.');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Office Type Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('Failed to delete Office Type.');
        }
    }

    public function bulkDelete(OfficeTypeService $officeTypeService)
    {
        try {
            $successful = $officeTypeService->bulkDeleteOfficeTypeByIds($this->selectedOfficeTypes);
            $this->confirmingBulkDeletion = false;
            $successful ? $this->warning(count($this->selectedOfficeTypes) . ' office types deleted successfully! 🗑️') : $this->error('Failed to delete selected office types.');
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
            $successful = $officeTypeService->restoreOfficeType($this->officeTypeId);
            $this->confirmingRestore = false;
            $successful ? $this->success('Office Type restored successfully! ♻️') : $this->error('Failed to restore Office Type.');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Office Type Restore Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false; // Close modal even on error
            $this->error('Failed to restore Office Type.');
        }
    }

    public function bulkRestore(OfficeTypeService $officeTypeService)
    {
        try {
            $successful = $officeTypeService->bulkRestoreOfficeTypes($this->selectedOfficeTypes);
            $this->confirmingBulkRestore = false;
            $successful ? $this->success(count($this->selectedOfficeTypes) . ' office types restored successfully! ♻️') : $this->error('Failed to restore selected office types.');
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Restore Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false; // Close modal even on error
            $this->error('Failed to restore selected office types.', "Bulk Restore Error: " . $e->getMessage(), ['exception' => $e]);
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
            $successful = $officeTypeService->permanentlyDelete($this->officeTypeId);
            $this->confirmingPermanentDeletion = false;
            $successful ? $this->warning('Office Type permanently deleted! 💥') : $this->error('Failed to permanently delete Office Type.');
            $this->officeTypeId = null;
        } catch (\Exception $e) {
            \Log::error("Permanent Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete Office Type.');
        }
    }

    public function bulkPermanentDelete(OfficeTypeService $officeTypeService)
    {
        try {
            $successful = $officeTypeService->bulkPermanentDelete($this->selectedOfficeTypes);
            $this->confirmingBulkPermanentDeletion = false;
            $successful ? $this->warning(count($this->selectedOfficeTypes) . ' office types permanently deleted! 💥') : $this->error('Failed to permanently delete selected office types');
           
            $this->selectedOfficeTypes = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error("Bulk Permanent Delete Error: " . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false; // Close modal even on error
            $this->error('Failed to permanently delete selected office types.');
        }
    }

    // Fetch office types with applied filters matching campuses.blade.php structure
    private function getOfficeTypes(OfficeTypeService $officeTypeService)
    {
        $filteringSorting = [
            'with_trashed' => $this->showDeletedRecords,
            'only_trashed' => false,
            'search' => $this->search ? true : false,
            'search_term' => $this->search ,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage
        ];

        return $officeTypeService->getPaginatedOfficeTypes($filteringSorting);
    }

    public function render(): mixed
    {
        $officeTypes = $this->getOfficeTypes(new OfficeTypeService);
        // $officeTypes = $officeTypesQuery->paginate($this->perPage);

        $currentPageIds = $officeTypes->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedOfficeTypes));

        return view('livewire.office-types', [
            'officeTypes' => $officeTypes,
            'headers' => $this->headers 
        ]);
    }
}

?>

<div>

    <x-header 
        class="px-4 pt-4 !mb-2"
        title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Office Type Management" 
        icon="o-bolt" 
        icon-classes="bg-warning rounded-full p-1 w-6 h-6" 
        subtitle="Total Office Types: {{ $officeTypes->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}" >

        <x-slot:middle class="!justify-end">
        <x-input 
            placeholder="Search Office Types..." 
            wire:model.live.debounce="search" 
            icon="o-magnifying-glass" 
            @keydown.enter="$wire.drawer = false"
            class="input-sm w-60" 
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-button 
                icon="o-plus" 
                wire:click="openModal"  
                class="btn btn-primary btn-sm flex items-center justify-center"
                spinner 
                tooltip-left="Add Office Type" 
                label="Add Office Type"
            />
        </x-slot:actions>
    </x-header>

    <!-- Filters and Bulk Actions Row -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <!-- Show deleted checkbox -->
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error" hint="{{ $showDeletedRecords ? 'Showing deleted' : '' }}" />

            <!-- Per page selector -->
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <x-select wire:model.live="perPage" :options="[['id' => 5, 'name' => 5], ['id' => 10, 'name' => 10], ['id' => 25, 'name' => 25], ['id' => 50, 'name' => 50], ['id' => 100, 'name' => 100]]" class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>

        @if(count($selectedOfficeTypes))
            <div class="flex items-center space-x-2">
                <x-select
                    placeholder="Perform a bulk action"
                    icon="o-bolt" 
                    :options="[
                        ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                        ['id' => 'bulkToggleActive', 'name' => 'Toggle Active Status'],
                        ...( $showDeletedRecords ? [
                            ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                            ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete']
                        ] : [])
                    ]"
                    class="select select-bordered select-sm py-0"
                    wire:change="handleBulkAction($event.target.value)" 
                />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedOfficeTypes) }} selected
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
                                <x-button icon="o-eye" wire:click="openViewModal({{ $officeType->id }})"  
                                    class="btn btn-ghost btn-xs h-6 w-6  text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" 
                                />

                                @if(!$officeType->deleted_at)
                                    <!-- Edit button -->
                                    <x-button 
                                        icon="o-pencil" 
                                        wire:click="openModal({{ $officeType->id }})"  
                                        class="h-6 w-6  btn btn-ghost btn-xs text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner 
                                        tooltip-left="Edit Office Type" 
                                    />

                                    <!-- Toggle active button -->
                                    <x-button 
                                        icon="{{ $officeType->is_active ? 'o-x-circle' : 'o-check-circle' }}" 
                                        wire:click="toggleActive({{ $officeType->id }})"  
                                        class="h-6 w-6 btn btn-ghost btn-xs {{ $officeType->is_active ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }}"
                                        spinner 
                                        tooltip-left="{{ $officeType->is_active ? 'Deactivate' : 'Activate' }} Office Type" 
                                    />
                                    <!-- Delete button -->
                                    <x-button 
                                        icon="o-trash" 
                                        wire:click="confirmDelete({{ $officeType->id }})"  
                                        class="h-6 w-6 btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner 
                                        tooltip-left="Delete Office Type" 
                                    />
                                @else
                                    <!-- Restore button -->
                                    <x-button 
                                        icon="o-arrow-path" 
                                        wire:click="confirmRestore({{ $officeType->id }})"  
                                        class="h-6 w-6 btn btn-ghost btn-xs text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner 
                                        tooltip-left="Restore Office Type" 
                                    />
                                    <!-- Permanent Delete button -->
                                    <x-button 
                                        icon="o-no-symbol" 
                                        wire:click="confirmPermanentDelete({{ $officeType->id }})"  
                                        class="h-6 w-6 btn btn-ghost btn-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner 
                                        tooltip-left="Permanently Delete Type" 
                                    />
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
    <x-modal wire:model="showModal" :title="$officeTypeId ? 'Edit Office Type' : 'Add New Office Type'" box-class="max-w-xl" 
        separator persistent class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            {{-- Mimic grid layout from campuses modal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-2">
                <div class="md:col-span-2">
                    <x-input wire:model="name" label="Type Name" inline placeholder="Enter type name" required />
                </div>
                <div class="md:col-span-2">
                    <x-input wire:model="code" label="Type Code" inline placeholder="Enter type code (e.g. ACAD, ADMIN)" required />
                </div>
                <div class="md:col-span-2">
                    <x-textarea wire:model="description" label="Description" inline placeholder="Enter type description" rows="4" />
                </div>
                <div class="md:col-span-2">
                    <x-toggle wire:model.live="is_active" label="Active Status" class="self-start" hint="{{ $is_active ? '🌟 Ready to use - This office type is fully active and available' : '⏸️ Currently inactive - This office type is temporarily restricted' }}" checked />
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
    <x-modal wire:model="showViewModal" title="View Office Type" separator box-class="max-w-2xl" class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0" >
        @if($viewOfficeType)
            <div class="p-4 space-y-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $viewOfficeType->name }}
                    </h3>
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="inline-flex items-center px-3 py-1 shadow rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <x-icon name="o-tag" class="h-3 w-3 mr-1" />
                            {{ $viewOfficeType->code }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 shadow rounded-full text-xs font-semibold {{ $viewOfficeType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            <x-icon name="{{ $viewOfficeType->is_active ? 'o-check-circle' : 'o-x-circle' }}" class="h-3 w-3 mr-1" />
                            {{ $viewOfficeType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($viewOfficeType->deleted_at)
                            <span class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" />
                                Deleted
                            </span>
                        @endif
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">
                        Description
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 shadow">
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                            {{ $viewOfficeType->description ?: 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-3">
                        Details
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Created At --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                Created At
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOfficeType->created_at ? $viewOfficeType->created_at->format('M d, Y h:i A') : '-' }}
                            </p>
                        </div>

                        {{-- Last Updated At --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                Last Updated
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $viewOfficeType->updated_at ? $viewOfficeType->updated_at->format('M d, Y h:i A') : '-' }}
                            </p>
                        </div>

                        {{-- Deleted At (Conditional) --}}
                        @if($viewOfficeType->deleted_at)
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 shadow"> 
                                <div class="text-xs text-red-600 dark:text-red-400 uppercase">
                                    Deleted At
                                </div>
                                <p class="text-sm text-red-800 dark:text-red-300">
                                    {{ $viewOfficeType->deleted_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div> {{-- End p-4 space-y-6 --}}
        @else
            {{-- Skeleton Loader --}}
            <div class="p-8 flex justify-center items-center">
                <div class="w-full max-w-xs animate-pulse space-y-6">
                    <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="flex gap-2">
                        <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-1/4"></div>
                        <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-1/4"></div>
                        @if($showDeletedRecords) {{-- Conditionally show skeleton for deleted badge --}}
                            <div class="h-5 bg-gray-300 dark:bg-gray-700 rounded-full w-1/4"></div>
                        @endif
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/4 mb-2"></div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2">
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-full"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-5/6"></div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/4 mb-3"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-1">
                                <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <div class="flex justify-end gap-3 w-full">
                {{-- Edit button --}}
                @if($viewOfficeType && !$viewOfficeType->deleted_at)
                    <x-button
                        label="Edit Type"
                        icon="o-pencil"
                        wire:click="openModal({{ $viewOfficeType->id }})"
                        class="btn-primary"
                        spinner
                    />
                @endif
                {{-- Close button --}}
                <x-button
                    label="Close"
                    wire:click="closeModal"
                    class="btn-ghost"
                />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (using MaryUI Modal, styled similarly) -->
    <x-modal wire:model="confirmingDeletion" title="Delete Office Type" separator persistent class="">
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
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Office Types" separator persistent class="">
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
    <x-modal wire:model="confirmingRestore" title="Restore Office Type" separator persistent class="">
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
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Office Types" separator persistent class="">
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
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Office Type" separator persistent class="">
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
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Office Types" separator persistent class="">
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

