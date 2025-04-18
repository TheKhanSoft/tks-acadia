<?php

use App\Models\Campus;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CampusRequest;
use Livewire\Attributes\On;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    use WithPagination;

    // Component properties
    public $campusId = null;
    public $name;
    public $code;
    public $location;
    public $description;
    public $address;
    public $phone; 
    public $email;
    public $website;
    public $founded_year;
    public $is_active = true;
    
    // UI state properties
    public $perPage = 10;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeletedRecords = false;
    public $selectedCampuses = [];
    public $selectAll = false;
    
    // Modal state
    public $showModal = false;
    public $showViewModal = false;
    public $viewCampus = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;
    
    // Querystring parameters to persist in the URL
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'showDeletedRecords' => ['except' => false],
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
        $this->selectedCampuses = [];
        $this->selectAll = false;
    }

    // Sorting functionality
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    // Handle selection of all visible items
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCampuses = $this->getCampuses()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedCampuses = [];
        }
    }
    
    // Update selected items when changing individual selections
    public function updatedSelectedCampuses($value)
    {
        $this->selectAll = count($this->selectedCampuses) === $this->getCampuses()->count();
    }

    // Modal management
    public function openModal($campusId = null)
    {
      
        $this->resetValidation();
        $this->resetExcept(['search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords', 'selectedCampuses']);
        
        $this->showModal = true;
        $this->campusId = $campusId;
        
        if ($campusId) {
            $campus = Campus::findOrFail($campusId);
            $this->name = $campus->name;
            $this->code = $campus->code;
            $this->location = $campus->location;
            $this->description = $campus->description;
            $this->address = $campus->address;
            $this->phone = $campus->phone;
            $this->email = $campus->email;
            $this->website = $campus->website;
            $this->founded_year = $campus->founded_year;
            $this->is_active = $campus->is_active;
        }
    }
    
    public function openViewModal($campusId)
    {
        $this->viewCampus = Campus::findOrFail($campusId);
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
    }

    // CRUD Operations
    public function save()
    {
        // Creating a new instance of CampusRequest to use its rules
        $campusRequest = new CampusRequest();
        
        // Get the validation rules
        $rules = $campusRequest->rules();
        
        // Perform validation using the rules from CampusRequest
        $validatedData = Validator::make(
            [
                'campusId' => $this->campusId,
                'name' => $this->name,
                'code' => $this->code,
                'location' => $this->location,
                'description' => $this->description,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'founded_year' => $this->founded_year,
                'is_active' => $this->is_active,
            ],
            $rules,
            $campusRequest->messages(),
            $campusRequest->attributes()
        )->validate();
        
        // Set code to uppercase as in prepareForValidation
        $validatedData['code'] = strtoupper($validatedData['code']);

        if ($this->campusId) {
            // Update existing campus
            $campus = Campus::findOrFail($this->campusId);
            $campus->update($validatedData);
            $this->dispatch('notify', [
                'message' => 'Campus updated successfully! 🎓',
                'type' => 'success',
            ]);
        } else {
            // Create new campus
            Campus::create($validatedData);
            $this->dispatch('notify', [
                'message' => 'New campus added successfully! 🏫',
                'type' => 'success',
            ]);
        }
        
        $this->closeModal();
    }
    
    // Toggle active status
    public function toggleActive($campusId)
    {
        $campus = Campus::findOrFail($campusId);
        $campus->update(['is_active' => !$campus->is_active]);
        
        $statusText = $campus->is_active ? 'activated' : 'deactivated';
        $this->dispatch('notify', [
            'message' => "Campus {$statusText} successfully! 🔄",
            'type' => 'success',
        ]);
    }
    
    // Bulk toggle active status
    public function bulkToggleActive()
    {
        $campuses = Campus::whereIn('id', $this->selectedCampuses)->get();
        
        // Determine whether to activate or deactivate based on majority
        $inactiveCount = $campuses->where('is_active', false)->count();
        $setActive = $inactiveCount >= $campuses->count() / 2;
        
        Campus::whereIn('id', $this->selectedCampuses)
            ->update(['is_active' => $setActive]);
        
        $statusText = $setActive ? 'activated' : 'deactivated';
        $this->dispatch('notify', [
            'message' => count($this->selectedCampuses) . " campuses {$statusText} successfully! 🔄",
            'type' => 'success',
        ]);
        
        $this->selectedCampuses = [];
        $this->selectAll = false;
    }
    
    // Soft delete operations
    public function confirmDelete($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingDeletion = true;
    }
    
    public function delete()
    {
        $campus = Campus::findOrFail($this->campusId);
        $campus->delete();
        
        $this->confirmingDeletion = false;
        $this->dispatch('notify', [
            'message' => 'Campus deleted successfully! 🗑️',
            'type' => 'success',
        ]);
        
        $this->campusId = null;
    }
    
    public function confirmBulkDelete()
    {
        if (empty($this->selectedCampuses)) {
            $this->dispatch('notify', [
                'message' => 'Please select campuses to delete 🤔',
                'type' => 'warning',
            ]);
            return;
        }
        
        $this->confirmingBulkDeletion = true;
    }
    
    public function bulkDelete()
    {
        Campus::whereIn('id', $this->selectedCampuses)->delete();
        
        $this->confirmingBulkDeletion = false;
        $this->dispatch('notify', [
            'message' => count($this->selectedCampuses) . ' campuses deleted successfully! 🗑️',
            'type' => 'success',
        ]);
        
        $this->selectedCampuses = [];
        $this->selectAll = false;
    }
    
    // Restore operations
    public function confirmRestore($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingRestore = true;
    }
    
    public function restore()
    {
        Campus::withTrashed()
            ->where('id', $this->campusId)
            ->restore();
            
        $this->confirmingRestore = false;
        $this->dispatch('notify', [
            'message' => 'Campus restored successfully! ♻️',
            'type' => 'success',
        ]);
        
        $this->campusId = null;
    }
    
    public function confirmBulkRestore()
    {
        if (empty($this->selectedCampuses)) {
            $this->dispatch('notify', [
                'message' => 'Please select campuses to restore 🤔',
                'type' => 'warning',
            ]);
            return;
        }
        
        $this->confirmingBulkRestore = true;
    }
    
    public function bulkRestore()
    {
        Campus::withTrashed()
            ->whereIn('id', $this->selectedCampuses)
            ->restore();
            
        $this->confirmingBulkRestore = false;
        $this->dispatch('notify', [
            'message' => count($this->selectedCampuses) . ' campuses restored successfully! ♻️',
            'type' => 'success',
        ]);
        
        $this->selectedCampuses = [];
        $this->selectAll = false;
    }
    
    // Permanent delete operations
    public function confirmPermanentDelete($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingPermanentDeletion = true;
    }
    
    public function permanentDelete()
    {
        Campus::withTrashed()
            ->where('id', $this->campusId)
            ->forceDelete();
            
        $this->confirmingPermanentDeletion = false;
        $this->dispatch('notify', [
            'message' => 'Campus permanently deleted! 💥',
            'type' => 'success',
        ]);
        
        $this->campusId = null;
    }
    
    public function confirmBulkPermanentDelete()
    {
        if (empty($this->selectedCampuses)) {
            $this->dispatch('notify', [
                'message' => 'Please select campuses to permanently delete 🤔',
                'type' => 'warning',
            ]);
            return;
        }
        
        $this->confirmingBulkPermanentDeletion = true;
    }
    
    public function bulkPermanentDelete()
    {
        Campus::withTrashed()
            ->whereIn('id', $this->selectedCampuses)
            ->forceDelete();
            
        $this->confirmingBulkPermanentDeletion = false;
        $this->dispatch('notify', [
            'message' => count($this->selectedCampuses) . ' campuses permanently deleted! 💥',
            'type' => 'success',
        ]);
        
        $this->selectedCampuses = [];
        $this->selectAll = false;
    }
    
    // Fetch campuses with applied filters
    private function getCampuses()
    {
        $query = Campus::query();
        
        // Handle soft deleted records
        if ($this->showDeletedRecords) {
            $query->withTrashed();
        }
        
        // Apply search filter across multiple fields
        if ($this->search) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply sorting
        return $query->orderBy($this->sortField, $this->sortDirection);
    }
    
    public function render(): mixed
    {
        $campuses = $this->getCampuses()->paginate($this->perPage);
        
        return view('livewire.campuses', [
            'campuses' => $campuses,
        ]);
    }
}
?>
<div>
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center pb-4 bg-white dark:bg-gray-900 px-4 pt-4 rounded-t-lg">
        <div class="w-full sm:w-auto mb-3 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Campus Management
                <span class="ml-2 text-xs bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full">{{ $campuses->total() }} campuses</span>
            </h1>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <!-- Search box -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    class="mayu-input pl-10"
                    placeholder="Search campuses..."
                >
            </div>
            
            <!-- Add new campus button -->
            <button 
                wire:click="openModal" 
                class="mayu-button-primary flex items-center justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Campus
            </button>
        </div>
    </div>
    
    <!-- Filters and Bulk Actions Row -->
    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <!-- Show deleted checkbox -->
            <label class="flex items-center space-x-2 text-sm">
                <input 
                    wire:model.live="showDeletedRecords" 
                    type="checkbox" 
                    class="mayu-checkbox"
                >
                <span class="text-gray-700 dark:text-gray-300">Show deleted</span>
            </label>
            
            <!-- Per page selector -->
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <select wire:model.live="perPage" class="mayu-select py-1 pl-2 pr-8 text-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        
        <!-- Bulk Actions -->
        <div class="flex items-center space-x-2">
            <select 
                x-data="{}"
                x-on:change="$event.target.value && $dispatch($event.target.value); $event.target.value = ''"
                class="mayu-select py-1 text-sm"
            >
                <option value="">Bulk actions</option>
                <option value="confirmBulkDelete">Delete Selected</option>
                <option value="bulkToggleActive">Toggle Active Status</option>
                @if($showDeletedRecords)
                    <option value="confirmBulkRestore">Restore Selected</option>
                    <option value="confirmBulkPermanentDelete">Permanently Delete</option>
                @endif
            </select>
            
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ count($selectedCampuses) }} selected
            </span>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-b-lg shadow">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="p-4 w-8">
                    <input
                        wire:model.live="selectAll"
                        type="checkbox"
                        class="mayu-checkbox"
                    >
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                    <div class="flex items-center space-x-1">
                        <span>Name</span>
                        @if($sortField === 'name')
                            <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('code')">
                    <div class="flex items-center space-x-1">
                        <span>Code</span>
                        @if($sortField === 'code')
                            <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('location')">
                    <div class="flex items-center space-x-1">
                        <span>Location</span>
                        @if($sortField === 'location')
                            <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Contact
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('founded_year')">
                    <div class="flex items-center space-x-1">
                        <span>Founded</span>
                        @if($sortField === 'founded_year')
                            <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($campuses as $campus)
                <tr class="{{ $campus->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} {{ !$campus->is_active ? 'bg-gray-50 dark:bg-gray-800/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors">
                    <td class="p-4 w-8">
                        <input
                            wire:model.live="selectedCampuses"
                            value="{{ $campus->id }}"
                            type="checkbox"
                            class="mayu-checkbox"
                        >
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $campus->name }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $campus->code }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($campus->location)
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $campus->location }}
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-600">Not specified</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($campus->email)
                                <div class="flex items-center mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $campus->email }}
                                </div>
                            @endif
                            @if($campus->phone)
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $campus->phone }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($campus->founded_year)
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $campus->founded_year }}
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400 dark:text-gray-600">Unknown</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $campus->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $campus->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($campus->deleted_at)
                            <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Deleted
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end items-center space-x-2">
                            <!-- View button -->
                            <button 
                                wire:click="openViewModal({{ $campus->id }})" 
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                title="View Details"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            
                            @if(!$campus->deleted_at)
                                <!-- Edit button -->
                                <button 
                                    wire:click="openModal({{ $campus->id }})" 
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                
                                <!-- Toggle active button -->
                                <button 
                                    wire:click="toggleActive({{ $campus->id }})" 
                                    class="{{ $campus->is_active ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }}"
                                    title="{{ $campus->is_active ? 'Deactivate' : 'Activate' }}"
                                >
                                    @if($campus->is_active)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </button>
                                
                                <!-- Delete button -->
                                <button 
                                    wire:click="confirmDelete({{ $campus->id }})" 
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @else
                                <!-- Restore button -->
                                <button 
                                    wire:click="confirmRestore({{ $campus->id }})" 
                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                    title="Restore"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                                
                                <!-- Permanent Delete button -->
                                <button 
                                    wire:click="confirmPermanentDelete({{ $campus->id }})" 
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Permanently Delete"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-lg font-medium">No campuses found</span>
                            <p class="text-sm mt-1">{{ $search ? 'Try adjusting your search term.' : 'Start by adding a new campus.' }}</p>
                            @if($search)
                                <button 
                                    wire:click="$set('search', '')" 
                                    class="mt-3 mayu-button-secondary text-sm py-1"
                                >
                                    Clear Search
                                </button>
                            @else
                                <button 
                                    wire:click="openModal" 
                                    class="mt-3 mayu-button-primary text-sm"
                                >
                                    Add Your First Campus
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
<div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
    {{ $campuses->links() }}
</div>

<!-- Add/Edit Campus Modal -->
<div
    x-data="{ 
        show: @entangle('showModal'), 
        activeTab: 'basic'
    }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
        >
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            {{ $campusId ? 'Edit Campus' : 'Add New Campus' }}
                        </h3>
                        
                        <!-- Tabs -->
                        <div class="mt-4 border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                                <button
                                    type="button"
                                    @click="activeTab = 'basic'"
                                    :class="{ 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300': activeTab === 'basic', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'basic' }"
                                    class="py-2 px-1 border-b-2 font-medium text-sm"
                                >
                                    Basic Info
                                </button>
                                <button
                                    type="button"
                                    @click="activeTab = 'contact'"
                                    :class="{ 'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-300': activeTab === 'contact', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'contact' }"
                                    class="py-2 px-1 border-b-2 font-medium text-sm"
                                >
                                    Contact & Details
                                </button>
                            </nav>
                        </div>
                        
                        <div class="mt-4">
                            <form wire:submit.prevent="save">
                                <!-- Basic Info Tab -->
                                <div x-show="activeTab === 'basic'">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Name Field -->
                                        <div>
                                            <x-input 
                                                wire:model="name" 
                                                label="Campus Name" 
                                                placeholder="Enter campus name"
                                                required
                                            />
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Code Field -->
                                        <div>
                                            <x-input 
                                                wire:model="code" 
                                                label="Campus Code" 
                                                placeholder="Enter campus code (e.g. MAIN, EAST)"
                                                required
                                            />
                                            @error('code')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Location Field -->
                                        <div>
                                            <x-input 
                                                wire:model="location" 
                                                label="Location" 
                                                placeholder="Enter campus location"
                                                required
                                            />
                                            @error('location')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Founded Year Field -->
                                        <div>
                                            <x-input 
                                                wire:model="founded_year" 
                                                label="Founded Year" 
                                                type="number"
                                                placeholder="YYYY"
                                                min="1800"
                                                max="{{ date('Y') }}"
                                            />
                                            @error('founded_year')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Description Field -->
                                        <div class="md:col-span-2">
                                            <x-textarea 
                                                wire:model="description" 
                                                label="Description" 
                                                placeholder="Enter campus description"
                                                rows="3"
                                            />
                                            @error('description')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contact & Details Tab -->
                                <div x-show="activeTab === 'contact'" x-cloak>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Address Field -->
                                        <div class="md:col-span-2">
                                            <x-textarea 
                                                wire:model="address" 
                                                label="Address" 
                                                placeholder="Enter full address"
                                                rows="2"
                                            />
                                            @error('address')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Phone Field -->
                                        <div>
                                            <x-input 
                                                wire:model="phone" 
                                                label="Phone Number" 
                                                placeholder="Enter phone number"
                                            />
                                            @error('phone')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Email Field -->
                                        <div>
                                            <x-input 
                                                wire:model="email" 
                                                label="Email Address" 
                                                type="email"
                                                placeholder="Enter email address"
                                            />
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Website Field -->
                                        <div>
                                            <x-input 
                                                wire:model="website" 
                                                label="Website" 
                                                placeholder="Enter website URL"
                                            />
                                            @error('website')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Active Status Field -->
                                        <div>
                                            <x-checkbox 
                                                wire:model="is_active" 
                                                label="Active Campus"
                                            />
                                            @error('is_active')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="save" type="button" class="mayu-button-primary w-full sm:w-auto sm:ml-3">
                    {{ $campusId ? 'Update Campus' : 'Create Campus' }}
                </button>
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Campus Modal -->
<div
    x-data="{ show: @entangle('showViewModal') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
        >
            @if($viewCampus)
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center" id="modal-title">
                        <span class="mr-2">{{ $viewCampus->name }}</span>
                        <span class="px-2 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $viewCampus->code }}
                        </span>
                        <span class="ml-2 px-2 text-xs leading-5 font-semibold rounded-full {{ $viewCampus->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $viewCampus->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </h3>
                </div>
                
                <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $viewCampus->location ?: 'Not specified' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Founded Year</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $viewCampus->founded_year ?: 'Unknown' }}
                            </dd>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $viewCampus->address ?: 'No address provided' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $viewCampus->email ?: 'No email provided' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $viewCampus->phone ?: 'No phone provided' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                {{ $viewCampus->website ?: 'No website provided' }}
                            </dd>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $viewCampus->description ?: 'No description provided' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                @if($viewCampus && !$viewCampus->deleted_at)
                <button wire:click="openModal({{ $viewCampus->id }})" type="button" class="mayu-button-primary w-full sm:w-auto sm:ml-3">
                    Edit Campus
                </button>
                @endif
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div
    x-data="{ show: @entangle('confirmingDeletion') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full bg-base-100"
        >
            <div class="p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                        <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-error" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium" id="modal-title">
                            Delete Campus
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm opacity-70">
                                Are you sure you want to delete this campus? This action will soft delete the record, and it can be restored later.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-base-200 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <x-button 
                    wire:click="delete" 
                    label="Delete"
                    class="btn-error sm:ml-3"
                />
                <x-button 
                    wire:click="closeModal" 
                    label="Cancel"
                    class="btn-ghost mt-3 sm:mt-0"
                />
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div
    x-data="{ show: @entangle('confirmingBulkDeletion') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true" >
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full bg-base-100"
        >
            <div class="p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                        <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-error" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium" id="modal-title">
                            Bulk Delete Campuses
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm opacity-70">
                                Are you sure you want to delete {{ count($selectedCampuses) }} selected campuses? This action will soft delete these records, and they can be restored later.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-base-200 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <x-button 
                    wire:click="bulkDelete" 
                    label="Delete {{ count($selectedCampuses) }} Campuses" 
                    class="btn-error sm:ml-3"
                />
                <x-button 
                    wire:click="closeModal" 
                    label="Cancel"
                    class="btn-ghost mt-3 sm:mt-0" 
                />
            </div>
        </div>
    </div>
</div>

<div
    x-data="{ show: @entangle('confirmingRestore') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        {{-- Vertical alignment helper --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Restore Campus
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to restore this campus?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="restore" type="button" class="mayu-button-success w-full sm:w-auto sm:ml-3">
                    Restore
                </button>
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div
    x-data="{ show: @entangle('confirmingBulkRestore') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        {{-- Vertical alignment helper --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Bulk Restore Campuses
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to restore {{ count($selectedCampuses) }} selected campuses?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="bulkRestore" type="button" class="mayu-button-success w-full sm:w-auto sm:ml-3">
                    Restore {{ count($selectedCampuses) }} Campuses
                </button>
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div
    x-data="{ show: @entangle('confirmingPermanentDeletion') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        {{-- Vertical alignment helper --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Permanently Delete Campus
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to permanently delete this campus? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="permanentDelete" type="button" class="mayu-button-danger w-full sm:w-auto sm:ml-3">
                    Permanently Delete
                </button>
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<div
    x-data="{ show: @entangle('confirmingBulkPermanentDeletion') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
        ></div>

        {{-- Vertical alignment helper --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Bulk Permanently Delete Campuses
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to permanently delete {{ count($selectedCampuses) }} selected campuses? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="bulkPermanentDelete" type="button" class="mayu-button-danger w-full sm:w-auto sm:ml-3">
                    Permanently Delete {{ count($selectedCampuses) }} Campuses
                </button>
                <button wire:click="closeModal" type="button" class="mayu-button-secondary mt-3 sm:mt-0 w-full sm:w-auto">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- </div> -->

