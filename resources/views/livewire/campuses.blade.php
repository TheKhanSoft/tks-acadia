<?php

use App\Models\Campus;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CampusRequest;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use WithPagination, Toast;

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

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterLocation = '';
    #[Url]
    public $filterFoundedYearStart = null;
    #[Url]
    public $filterFoundedYearEnd = null;
    #[Url]
    public $filterStatus = ''; // '', 'active', 'inactive'

    // Modal tab control
    public $selectedTab = 'basic'; // Default to the 'basic' tab

    // Define table headers
    public $headers = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'code', 'label' => 'Code', 'class' => 'hidden md:table-cell'],
        ['key' => 'location', 'label' => 'Location', 'class' => 'hidden lg:table-cell'],
        ['key' => 'contact', 'label' => 'Contact', 'class' => 'hidden lg:table-cell'],
        ['key' => 'founded_year', 'label' => 'Founded', 'class' => 'hidden md:table-cell'],
        ['key' => 'is_active', 'label' => 'Status']
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

    // Reset pagination when filters change
    public function updatedFilterLocation()
    {
        $this->resetPage();
    }
    public function updatedFilterFoundedYearStart()
    {
        $this->resetPage();
    }
    public function updatedFilterFoundedYearEnd()
    {
        $this->resetPage();
    }
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    // Sorting functionality
    public function sortBy($field)
    {
        $validSortFields = ['name', 'code', 'location', 'founded_year', 'is_active', 'created_at', 'updated_at'];
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

    // Handle selection of all visible items
    public function updatedSelectAll($value)
    {
        $items = $this->getCampuses(); // Get current page items
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedCampuses = array_unique(array_merge($this->selectedCampuses, $itemIds));
        } else {
            // Deselect only items on the current page if selectAll was for this page
            $this->selectedCampuses = array_diff($this->selectedCampuses, $itemIds);
        }
    }

    // Update selectAll state when individual items are selected/deselected
    public function updatedSelectedCampuses()
    {
        $items = $this->getCampuses();
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedCampuses));
    }


    // Modal management
    public function openModal($campusId = null)
    {
        $this->resetValidation();
        $this->resetExcept(array_keys(get_object_vars($this))); // Resets all public properties except those needed
        // Keep essential state:
        $this->search = session('campus_search', $this->search);
        $this->sortField = session('campus_sortField', $this->sortField);
        $this->sortDirection = session('campus_sortDirection', $this->sortDirection);
        $this->perPage = session('campus_perPage', $this->perPage);
        $this->showDeletedRecords = session('campus_showDeletedRecords', $this->showDeletedRecords);
        // Keep filter states
        $this->filterLocation = session('campus_filterLocation', $this->filterLocation);
        $this->filterFoundedYearStart = session('campus_filterFoundedYearStart', $this->filterFoundedYearStart);
        $this->filterFoundedYearEnd = session('campus_filterFoundedYearEnd', $this->filterFoundedYearEnd);
        $this->filterStatus = session('campus_filterStatus', $this->filterStatus);


        $this->showModal = true;
        $this->campusId = $campusId;
        $this->selectedTab = 'basic'; // Ensure 'basic' tab is selected

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
        } else {
            // Reset all form fields for a new entry
            $this->name = '';
            $this->code = '';
            $this->location = '';
            $this->description = '';
            $this->address = '';
            $this->phone = '';
            $this->email = '';
            $this->website = '';
            $this->founded_year = null;
            $this->is_active = true;
        }
    }

    public function openViewModal($campusId)
    {
        $this->viewCampus = Campus::findOrFail($campusId);
        $this->showViewModal = true;
    }

    public function closeModal()
    {
        // Persist table state
        session([
            'campus_search' => $this->search,
            'campus_sortField' => $this->sortField,
            'campus_sortDirection' => $this->sortDirection,
            'campus_perPage' => $this->perPage,
            'campus_showDeletedRecords' => $this->showDeletedRecords,
            'campus_filterLocation' => $this->filterLocation,
            'campus_filterFoundedYearStart' => $this->filterFoundedYearStart,
            'campus_filterFoundedYearEnd' => $this->filterFoundedYearEnd,
            'campus_filterStatus' => $this->filterStatus,
        ]);

        $this->showModal = false;
        $this->showViewModal = false;
        $this->confirmingDeletion = false;
        $this->confirmingBulkDeletion = false;
        $this->confirmingPermanentDeletion = false;
        $this->confirmingBulkPermanentDeletion = false;
        $this->confirmingRestore = false;
        $this->confirmingBulkRestore = false;
        $this->campusId = null;
        $this->viewCampus = null;
    }

    // CRUD Operations
    public function save()
    {
        $campusRequest = new CampusRequest();
        $rules = $campusRequest->rules($this->campusId); // Pass campusId for unique rule handling
        $messages = $campusRequest->messages();
        $attributes = $campusRequest->attributes();

        $dataToValidate = [
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
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();
        $validatedData['code'] = strtoupper($validatedData['code']);

        try {
            if ($this->campusId) {
                $campus = Campus::findOrFail($this->campusId);
                $campus->update($validatedData);
                $this->success('Campus updated successfully! 🎓');
            } else {
                Campus::create($validatedData);
                $this->success('New campus added successfully! 🏫');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Campus Save Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('An error occurred while saving the campus.');
        }
    }

    // Toggle active status
    public function toggleActive($campusId)
    {
        try {
            $campus = Campus::findOrFail($campusId);
            $campus->update(['is_active' => !$campus->is_active]);
            $statusText = $campus->is_active ? 'activated' : 'deactivated';
            $this->success("Campus {$statusText} successfully! 🔄");
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

        if (empty($this->selectedCampuses)) {
            $this->warning('Please select campuses first 🤔');
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'bulkToggleActive' => null, // Direct action
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];

        if ($action === 'bulkToggleActive') {
            $this->bulkToggleActive();
        } elseif (isset($confirmationMap[$action])) {
            $this->{$confirmationMap[$action]} = true;
        }
        // Reset the dropdown selection visually
        // This might require JavaScript or a specific way MaryUI handles select reset if $event.target.value = '' doesn't work with wire:change
    }


    // Bulk toggle active status
    public function bulkToggleActive()
    {
        if (empty($this->selectedCampuses)) {
            $this->warning('Please select campuses to toggle status 🤔');
            return;
        }
        try {
            $campuses = Campus::whereIn('id', $this->selectedCampuses)->get();
            $inactiveCount = $campuses->where('is_active', false)->count();
            $setActive = $inactiveCount >= $campuses->count() / 2;

            Campus::whereIn('id', $this->selectedCampuses)->update(['is_active' => $setActive]);

            $statusText = $setActive ? 'activated' : 'deactivated';
            $this->success(count($this->selectedCampuses) . " campuses {$statusText} successfully! 🔄");
            $this->selectedCampuses = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            \Log::error('Bulk Toggle Active Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status for selected campuses.');
        }
    }

    // Soft delete operations
    public function confirmDelete($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        try {
            $campus = Campus::findOrFail($this->campusId);
            $campus->delete();
            $this->warning('Campus deleted successfully! 🗑️');
        } catch (\Exception $e) {
            \Log::error('Campus Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to delete campus.');
        } finally {
            $this->confirmingDeletion = false;
            $this->campusId = null;
        }
    }

    public function bulkDelete()
    {
        try {
            Campus::whereIn('id', $this->selectedCampuses)->delete();
            $this->warning(count($this->selectedCampuses) . ' campuses deleted successfully! 🗑️');
        } catch (\Exception $e) {
            \Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to delete selected campuses.');
        } finally {
            $this->confirmingBulkDeletion = false;
            $this->selectedCampuses = [];
            $this->selectAll = false;
        }
    }

    // Restore operations
    public function confirmRestore($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingRestore = true;
    }

    public function restore()
    {
        try {
            Campus::withTrashed()->where('id', $this->campusId)->restore();
            $this->success('Campus restored successfully! ♻️');
        } catch (\Exception $e) {
            \Log::error('Campus Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to restore campus.');
        } finally {
            $this->confirmingRestore = false;
            $this->campusId = null;
        }
    }

    public function bulkRestore()
    {
        try {
            Campus::withTrashed()->whereIn('id', $this->selectedCampuses)->restore();
            $this->success(count($this->selectedCampuses) . ' campuses restored successfully! ♻️');
        } catch (\Exception $e) {
            \Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to restore selected campuses.');
        } finally {
            $this->confirmingBulkRestore = false;
            $this->selectedCampuses = [];
            $this->selectAll = false;
        }
    }

    // Permanent delete operations
    public function confirmPermanentDelete($campusId)
    {
        $this->campusId = $campusId;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete()
    {
        try {
            Campus::withTrashed()->where('id', $this->campusId)->forceDelete();
            $this->warning('Campus permanently deleted! 💥');
        } catch (\Exception $e) {
            \Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to permanently delete campus.');
        } finally {
            $this->confirmingPermanentDeletion = false;
            $this->campusId = null;
        }
    }

    public function bulkPermanentDelete()
    {
        try {
            Campus::withTrashed()->whereIn('id', $this->selectedCampuses)->forceDelete();
            $this->warning(count($this->selectedCampuses) . ' campuses permanently deleted! 💥');
        } catch (\Exception $e) {
            \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to permanently delete selected campuses.');
        } finally {
            $this->confirmingBulkPermanentDeletion = false;
            $this->selectedCampuses = [];
            $this->selectAll = false;
        }
    }

    // Computed property to get active filters for display
    public function getActiveFiltersProperty()
    {
        $filters = [];
        if ($this->filterLocation) {
            $filters[] = ['key' => 'filterLocation', 'label' => 'Location', 'value' => $this->filterLocation];
        }
        if ($this->filterFoundedYearStart) {
            $filters[] = ['key' => 'filterFoundedYearStart', 'label' => 'Founded From', 'value' => $this->filterFoundedYearStart];
        }
        if ($this->filterFoundedYearEnd) {
            $filters[] = ['key' => 'filterFoundedYearEnd', 'label' => 'Founded To', 'value' => $this->filterFoundedYearEnd];
        }
        if ($this->filterStatus !== '') {
            $filters[] = ['key' => 'filterStatus', 'label' => 'Status', 'value' => ucfirst($this->filterStatus)];
        }
        return $filters;
    }

    // Method to remove a specific filter
    public function removeFilter($filterKey)
    {
        if ($filterKey === 'filterStatus') {
            $this->reset('filterStatus');
        } else {
            $this->reset($filterKey);
        }
        $this->resetPage();
        $this->success('Filter removed.');
    }

    public function resetFilters()
    {
        $this->reset('filterLocation', 'filterFoundedYearStart', 'filterFoundedYearEnd', 'filterStatus');
        $this->resetPage();
        $this->success('Filters reset.');
    }

    // Fetch campuses with applied filters
    private function getCampuses()
    {
        $query = Campus::query();

        if ($this->showDeletedRecords) {
            $query->withTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('website', 'like', '%' . $this->search . '%');
            });
        }

        // Apply advanced filters
        if ($this->filterLocation) {
            $query->where('location', 'like', '%' . $this->filterLocation . '%');
        }
        if ($this->filterFoundedYearStart) {
            $query->where('founded_year', '>=', $this->filterFoundedYearStart);
        }
        if ($this->filterFoundedYearEnd) {
            $query->where('founded_year', '<=', $this->filterFoundedYearEnd);
        }
        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
    }

    public function render(): mixed
    {
        $campuses = $this->getCampuses();
        
        // Ensure selectAll checkbox reflects current page selection state
        $currentPageIds = $campuses->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedCampuses));

        return view('livewire.campuses', [
            'campuses' => $campuses,
            'headers' => $this->headers, // Pass headers to the view
        ]);
    }
}
?>

<div>
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Campus Management" icon="o-academic-cap" icon-classes="bg-indigo-600 text-white rounded-full p-1 w-8 h-8"
        :subtitle="'Total Campuses: ' . $campuses->total() . ($showDeletedRecords ? ' (including deleted)' : '')"
        subtitle-class="mr-2 mt-0.5">

        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Campuses..." wire:model.live.debounce.300ms="search" icon="o-magnifying-glass"
                clearable class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner
                tooltip-left="Add a new campus" label="Add Campus" />
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner tooltip-left="Filters" />
        </x-slot:actions>
    </x-header>

    <!-- Active Filters Display -->
    @if (count($this->activeFilters))
        <div
            class="px-4 py-2 bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Active Filters:</span>
            @foreach ($this->activeFilters as $filter)
                <x-badge class="badge-primary badge-outline badge-sm font-semibold gap-1"
                    value="{{ $filter['label'] }}: {{ $filter['value'] }}" />
            @endforeach
            <x-button label="Clear All" wire:click="resetFilters" class="btn-ghost btn-xs text-red-500" spinner />
        </div>
    @endif

    <!-- Filters and Bulk Actions Row -->
    <div
        class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error toggle-sm"
                hint="{{ $showDeletedRecords ? 'Showing deleted records' : 'Showing active records' }}" />

            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <x-select wire:model.live="perPage" :options="[
                    ['id' => 5, 'name' => 5],
                    ['id' => 10, 'name' => 10],
                    ['id' => 25, 'name' => 25],
                    ['id' => 50, 'name' => 50],
                    ['id' => 100, 'name' => 100],
                ]" class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>

        @if (count($selectedCampuses))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Bulk actions" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'bulkToggleActive', 'name' => 'Toggle Active Status'],
                    ...($showDeletedRecords
                        ? [
                            ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                            ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete'],
                          ]
                        : []),
                ]" class="select select-bordered select-sm py-0"
                    wire:change="handleBulkAction($event.target.value)" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedCampuses) }} selected
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
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer {{ $header['class'] ?? '' }}"
                            wire:click="sortBy('{{ $header['key'] }}')">
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if ($sortField === $header['key'])
                                    <x-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}"
                                        class="w-3 h-3" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col"
                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($campuses as $campus)
                    <tr wire:key="campus-{{ $campus->id }}"
                        class="{{ $campus->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} {{ !$campus->is_active ? 'bg-gray-50 dark:bg-gray-800/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors">
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedCampuses" value="{{ (string) $campus->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $campus->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                            <x-badge value="{{ $campus->code }}" class="badge-info badge-outline" />
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                @if ($campus->location)
                                    <x-icon name="o-map-pin" class="h-4 w-4 mr-1 text-gray-400" />
                                    {{ $campus->location }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">Not specified</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                @if ($campus->email)
                                    <div class="flex items-center mb-1">
                                        <x-icon name="o-envelope" class="h-4 w-4 mr-1 text-gray-400" />
                                        {{ $campus->email }}
                                    </div>
                                @endif
                                @if ($campus->phone)
                                    <div class="flex items-center">
                                        <x-icon name="o-phone" class="h-4 w-4 mr-1 text-gray-400" />
                                        {{ $campus->phone }}
                                    </div>
                                @endif
                                @if (!$campus->email && !$campus->phone)
                                    <span class="text-gray-400 dark:text-gray-600">No contact</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                            @if ($campus->founded_year)
                                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                    <x-icon name="o-calendar-days" class="h-4 w-4 mr-1 text-gray-400" />
                                    {{ $campus->founded_year }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-600">Unknown</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge value="{{ $campus->is_active ? 'Active' : 'Inactive' }}"
                                class="{{ $campus->is_active ? 'badge-success' : 'badge-ghost' }} badge-sm" />
                            @if ($campus->deleted_at)
                                <x-badge value="Deleted" class="badge-error badge-sm ml-1" />
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $campus->id }})"
                                    class="btn btn-ghost btn-xs text-indigo-600 hover:text-indigo-900" spinner
                                    tooltip-left="View Details" />
                                @if (!$campus->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $campus->id }})"
                                        class="btn btn-ghost btn-xs text-blue-600 hover:text-blue-900" spinner
                                        tooltip-left="Edit Campus" />
                                    <x-button icon="{{ $campus->is_active ? 'o-x-circle' : 'o-check-circle' }}"
                                        wire:click="toggleActive({{ $campus->id }})"
                                        class="btn btn-ghost btn-xs {{ $campus->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                        spinner tooltip-left="{{ $campus->is_active ? 'Deactivate' : 'Activate' }}" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $campus->id }})"
                                        class="btn btn-ghost btn-xs text-red-600 hover:text-red-900" spinner
                                        tooltip-left="Delete Campus" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $campus->id }})"
                                        class="btn btn-ghost btn-xs text-green-600 hover:text-green-900" spinner
                                        tooltip-left="Restore Campus" />
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $campus->id }})"
                                        class="btn btn-ghost btn-xs text-red-600 hover:text-red-900" spinner
                                        tooltip-left="Permanently Delete" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}"
                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-12 w-12 text-gray-400 mb-3" />
                                <span class="text-lg font-medium">No campuses found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new campus.' }}
                                </p>
                                @if ($search)
                                    <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm"
                                        label="Clear Search" />
                                @else
                                    <x-button wire:click="openModal(null)" class="mt-3 btn btn-primary btn-sm" spinner
                                        tooltip-left="Add Your First Campus" label="Add Your First Campus" />
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
        {{ $campuses->links() }}
    </div>

    <!-- Add/Edit Campus Modal -->
    <x-modal wire:model="showModal" :title="$campusId ? 'Edit Campus' : 'Add New Campus'" box-class="max-w-4xl" separator
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab">
                <x-tab name="basic" label="Basic Info" icon="o-information-circle">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        <x-input wire:model="name" label="Campus Name" placeholder="Enter campus name" icon="o-identification" inline required />
                        <x-input wire:model="code" label="Campus Code" placeholder="Enter campus code (e.g. MAIN)" icon="o-hashtag" inline required />
                        <x-input wire:model="location" label="Location" placeholder="Enter campus location" icon="o-map-pin" inline required />
                        <x-input wire:model="founded_year" label="Founded Year" type="number" placeholder="YYYY" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" inline />
                        <div class="md:col-span-2">
                            <x-textarea wire:model="description" label="Description" placeholder="Enter campus description" rows="3" icon="o-document-text" inline />
                        </div>
                    </div>
                </x-tab>
                <x-tab name="contact" label="Contact & Details" icon="o-phone">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        <div class="md:col-span-2">
                            <x-textarea wire:model="address" label="Address" placeholder="Enter full address" rows="2" icon="o-map" inline />
                        </div>
                        <x-input wire:model="phone" label="Phone Number" placeholder="Enter phone number" icon="o-phone" inline />
                        <x-input wire:model="email" label="Email Address" type="email" placeholder="Enter email address" icon="o-envelope" inline />
                        <x-input wire:model="website" label="Website" placeholder="Enter website URL" icon="o-globe-alt" inline />
                        <div class="flex items-center">
                             <x-toggle wire:model.live="is_active" label="Active Campus" class="self-center" hint="{{ $is_active ? '🌟 Campus is active' : '⏸️ Campus is inactive' }}" checked />
                        </div>
                    </div>
                </x-tab>
            </x-tabs>
        </x-form>
        <x-slot:actions>
            <x-button wire:click="closeModal" label="Cancel" class="btn-ghost" />
            <x-button wire:click="save" type="submit" label="{{ $campusId ? 'Update Campus' : 'Create Campus' }}"
                class="btn-primary" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Campus Modal -->
    <x-modal wire:model="showViewModal" title="View Campus Details" separator box-class="max-w-3xl">
        @if ($viewCampus)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">
                <div class="flex justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $viewCampus->name }}</h3>
                        <div class="flex flex-wrap gap-2 items-center">
                            <x-badge value="{{ $viewCampus->code }}" class="badge-info badge-outline" icon="o-tag" />
                            <x-badge value="{{ $viewCampus->is_active ? 'Active' : 'Inactive' }}"
                                class="{{ $viewCampus->is_active ? 'badge-success' : 'badge-ghost' }}"
                                icon="{{ $viewCampus->is_active ? 'o-check-circle' : 'o-x-circle' }}" />
                            @if ($viewCampus->deleted_at)
                                <x-badge value="Deleted" class="badge-error" icon="o-archive-box-x-mark" />
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pt-4">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <x-icon name="o-map-pin" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500">Location</div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $viewCampus->location ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-calendar-days" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500">Founded Year</div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $viewCampus->founded_year ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-map" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500">Address</div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $viewCampus->address ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <x-icon name="o-phone" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500">Contact</div>
                                <div class="text-sm text-gray-900 dark:text-white space-y-1">
                                    @if ($viewCampus->phone) <div><x-icon name="o-phone" class="inline h-4 w-4 mr-1" /> {{ $viewCampus->phone }}</div> @endif
                                    @if ($viewCampus->email) <div><x-icon name="o-envelope" class="inline h-4 w-4 mr-1" /> {{ $viewCampus->email }}</div> @endif
                                    @if (!$viewCampus->phone && !$viewCampus->email) N/A @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-globe-alt" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500">Website</div>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    @if($viewCampus->website)
                                    <a href="{{ $viewCampus->website }}" target="_blank" class="text-blue-500 hover:underline">{{ $viewCampus->website }}</a>
                                    @else N/A @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-icon name="o-document-text" class="h-5 w-5 mr-2 mt-0.5 text-gray-400" />
                            <div>
                                <div class="text-xs font-semibold uppercase text-gray-500 mb-1">Description</div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 shadow-inner max-h-40 overflow-y-auto">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $viewCampus->description ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1"><x-icon name="o-calendar" class="h-4 w-4" /><span>Created: {{ $viewCampus->created_at ? $viewCampus->created_at->format('M d, Y h:i A') : '-' }}</span></div>
                        <div class="flex items-center gap-1"><x-icon name="o-pencil-square" class="h-4 w-4" /><span>Updated: {{ $viewCampus->updated_at ? $viewCampus->updated_at->format('M d, Y h:i A') : '-' }}</span></div>
                        @if ($viewCampus->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400"><x-icon name="o-archive-box-x-mark" class="h-4 w-4" /><span>Deleted: {{ $viewCampus->deleted_at->format('M d, Y h:i A') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="p-8 flex justify-center items-center"><x-loading class="loading-lg" /></div>
        @endif
        <x-slot:actions>
            @if ($viewCampus && !$viewCampus->deleted_at)
                <x-button label="Edit Campus" icon="o-pencil" wire:click="openModal({{ $viewCampus->id }})" class="btn-primary" spinner />
            @endif
            <x-button label="Close" wire:click="closeModal" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals -->
    <x-modal wire:model="confirmingDeletion" title="Delete Campus" separator>
        <div class="p-4 flex items-start">
            <x-icon name="o-exclamation-triangle" class="h-10 w-10 text-red-500 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to delete this campus? This action will soft delete the record, and it can be restored later.</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete" wire:click="delete" class="btn-error" wire:loading.attr="disabled" wire:target="delete" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Campuses" separator>
         <div class="p-4 flex items-start">
            <x-icon name="o-exclamation-triangle" class="h-10 w-10 text-red-500 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to delete {{ count($selectedCampuses) }} selected campuses? This action will soft delete these records.</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedCampuses) }} Campuses" wire:click="bulkDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmingRestore" title="Restore Campus" separator>
        <div class="p-4 flex items-start">
            <x-icon name="o-arrow-path" class="h-10 w-10 text-green-500 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to restore this campus?</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore" wire:click="restore" class="btn-success" wire:loading.attr="disabled" wire:target="restore" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Campuses" separator>
        <div class="p-4 flex items-start">
            <x-icon name="o-arrow-path" class="h-10 w-10 text-green-500 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to restore {{ count($selectedCampuses) }} selected campuses?</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedCampuses) }} Campuses" wire:click="bulkRestore" class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Campus" separator>
        <div class="p-4 flex items-start">
            <x-icon name="o-no-symbol" class="h-10 w-10 text-red-700 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to permanently delete this campus? This action cannot be undone.</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="permanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="permanentDelete" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Campuses" separator>
        <div class="p-4 flex items-start">
            <x-icon name="o-no-symbol" class="h-10 w-10 text-red-700 mr-3" />
            <div>
                <p class="text-gray-700 dark:text-gray-300">Are you sure you want to permanently delete {{ count($selectedCampuses) }} selected campuses? This action cannot be undone.</p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedCampuses) }} Campuses" wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled" wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Advanced Filter Drawer -->
    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button class="w-11/12 lg:w-1/3">
        <x-form wire:submit.prevent="$toggle('showFilterDrawer')">
            <div class="p-4 space-y-6">
                <x-input wire:model.live.debounce.300ms="filterLocation" label="Filter by Location" placeholder="Enter location" icon="o-map-pin" clearable />
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Founded Year Range</label>
                    <div class="grid grid-cols-2 gap-4">
                        <x-input wire:model.live.debounce.300ms="filterFoundedYearStart" type="number" placeholder="From Year" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" />
                        <x-input wire:model.live.debounce.300ms="filterFoundedYearEnd" type="number" placeholder="To Year" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" />
                    </div>
                </div>
                <div>
                    @php
                        $status_options = [
                            ['id' => '', 'name' => 'All Statuses'],
                            ['id' => 'active', 'name' => 'Active'],
                            ['id' => 'inactive', 'name' => 'Inactive'],
                        ];
                    @endphp
                    <x-select wire:model.live="filterStatus" label="Filter by Status" :options="$status_options" placeholder="Select status" icon="o-adjustments-horizontal" clearable />
                </div>
            </div>
        </x-form>
        <x-slot:actions>
            <x-button label="Reset Filters" icon="o-x-mark" wire:click="resetFilters" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" wire:click="$toggle('showFilterDrawer')" spinner />
        </x-slot:actions>
    </x-drawer>
</div>
