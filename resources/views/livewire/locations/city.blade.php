<?php

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Services\CityService;
use App\Http\Requests\CityRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithPagination, Toast;

    // City Model Properties
    public $cityId = null;
    public $name;
    public $state_id;

    // Options for selects
    public $states = [];

    // Table & Filtering properties
    #[Url]
    public $perPage = 10;
    #[Url]
    public $search = '';
    #[Url]
    public $sortField = 'name';
    #[Url]
    public $sortDirection = 'asc';
    #[Url]
    public $showDeletedRecords = false;
    public $selectedCities = [];
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $viewCity = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterStateId = null;
    #[Url]
    public $filterCountryId = null; // Add country filter for convenience

    // Options for filter selects (need countries for country filter)
    public $countries = [];

    // Define table headers
    public $headers = [
        ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ['key' => 'state.name', 'label' => 'State', 'sortable' => false], // Assuming 'state' relationship exists
        ['key' => 'country.name', 'label' => 'Country', 'sortable' => false], // Assuming 'state.country' relationship exists
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
        $this->selectedCities = [];
        $this->selectAll = false;
    }
    public function updatedFilterStateId()
    {
        $this->resetPage();
    }
    public function updatedFilterCountryId()
    {
        $this->filterStateId = null; // Reset state filter when country changes
        $this->resetPage();
    }

    public function sortBy($field)
    {
        // Allow sorting by related fields if the service supports it
        $allowedSortFields = ['name', 'state_id']; // Add 'state_id' for sorting by state

        if (!in_array($field, $allowedSortFields)) {
            $field = 'name'; // Default to name if invalid field
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
        $items = $this->getCities(app(CityService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray(); // Explicitly cast to array

        if ($value) {
            $this->selectedCities = array_unique(array_merge($this->selectedCities, $itemIds));
        } else {
            $this->selectedCities = array_diff($this->selectedCities, $itemIds);
        }
    }

    public function updatedSelectedCities($value)
    {
        $items = $this->getCities(app(CityService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray(); // Explicitly cast to array
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedCities));
    }

    public function mount()
    {
        // Pre-load options for selects
        $this->countries = Country::orderBy('name')->get(['id', 'name']);

        // Load states based on selected country filter, or all if no country filter
        $this->loadStatesForSelect();
    }

    // Helper to load states for the select dropdown
    public function loadStatesForSelect()
    {
        $query = State::orderBy('name');

        if ($this->filterCountryId) {
            $query->where('country_id', $this->filterCountryId);
        }

        $this->states = $query->get(['id', 'name', 'country_id']);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->resetExcept([
            'search',
            'sortField',
            'sortDirection',
            'perPage',
            'showDeletedRecords',
            'selectedCities',
            'selectAll',
            'headers',
            'states', // Keep states loaded
            'countries', // Keep countries loaded
            // Filter properties
            'filterStateId',
            'filterCountryId',
        ]);

        $this->showModal = true;
        $this->cityId = $id;

        if ($id) {
            $cityService = app(CityService::class);
            $city = $cityService->getCity($id, $this->showDeletedRecords);
            if ($city) {
                $this->name = $city->name;
                $this->state_id = $city->state_id;
                // If editing, ensure the correct states are loaded for the select
                if ($city->state) {
                    $this->filterCountryId = $city->state->country_id;
                    $this->loadStatesForSelect(); // Reload states based on the city's country
                }
            } else {
                $this->error('City not found.');
                $this->closeModal();
                return;
            }
        } else {
            $this->name = '';
            $this->state_id = null;
            // Reset country filter when adding a new city
            $this->filterCountryId = null;
            $this->loadStatesForSelect(); // Load all states initially for new city
        }
    }

    public function openViewModal($id, CityService $cityService)
    {
        $this->viewCity = $cityService->getCity($id, true, ['state.country']); // Eager load state and country
        if (!$this->viewCity) {
            $this->error('City not found.');
            return;
        }
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
        $this->cityId = null;
        $this->viewCity = null;
    }

    public function save(CityService $cityService)
    {
        $request = new CityRequest();
        $currentId = $this->cityId;
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'name' => $this->name,
            'state_id' => $this->state_id,
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        try {
            if ($this->cityId) {
                $city = $cityService->getCity($this->cityId);
                if ($city) {
                    $cityService->updateCity($city, $validatedData);
                    $this->success('City details updated successfully! 🏙️');
                } else {
                    $this->error('Oops! Could not find the city you were trying to update.');
                    $this->closeModal();
                    return;
                }
            } else {
                $cityService->createCity($validatedData);
                $this->success('Hooray! A new city has been added to the list. ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('City Save Error: ' . $e->getMessage(), ['exception' => $e]);
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('Looks like a city with that name already exists in the selected state. Please use a different name.');
            } else {
                $this->error('An unexpected error occurred while saving the city. Please try again.');
            }
        }
    }

    // --- Bulk Actions ---
    public function handleBulkAction($action)
    {
        if (!$action || empty($this->selectedCities)) {
            if (empty($this->selectedCities)) {
                $this->warning('Please select cities first 🤔');
            }
            $this->dispatch('reset-bulk-action');
            return;
        }

        $confirmationMap = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];

        if (isset($confirmationMap[$action]) && $confirmationMap[$action] !== null) {
            $this->{$confirmationMap[$action]} = true;
        }

        $this->dispatch('reset-bulk-action');
    }

    // --- Deletion ---
    public function confirmDelete($id)
    {
        $this->cityId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(CityService $cityService)
    {
        try {
            $successful = $cityService->deleteCityById($this->cityId);
            $this->confirmingDeletion = false;
            if ($successful) {
                $this->warning('City moved to trash. You can restore it from the "Show deleted" view. 🗑️');
            } else {
                $this->error('Hmm, could not delete the city. It might have already been removed or doesn\'t exist.');
            }
            $this->cityId = null;
        } catch (\Exception $e) {
            Log::error('City Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            // Add specific checks, e.g., if deletion is blocked due to relationships
            // Cities might be linked to Campuses or other entities
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete this city. It\'s still linked to other records (e.g., Campuses).');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkDelete(CityService $cityService)
    {
        try {
            $cityIds = array_map('intval', $this->selectedCities);
            $deletedCount = $cityService->bulkDeleteCityByIds($cityIds);
            $this->confirmingBulkDeletion = false;
            if ($deletedCount > 0) {
                $this->warning("{$deletedCount} cities moved to trash. 🗑️");
            } else {
                $this->error('No selected cities were deleted. They might have already been removed.');
            }
            $this->selectedCities = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false;
            $this->error('An error occurred during bulk deletion.');
        }
    }

    // --- Restoration ---
    public function confirmRestore($id)
    {
        $this->cityId = $id;
        $this->confirmingRestore = true;
    }

    public function restore(CityService $cityService)
    {
        try {
            $successful = $cityService->restoreCity($this->cityId);
            $this->confirmingRestore = false;
            if ($successful) {
                $this->success('City successfully restored! Welcome back. ♻️');
            } else {
                $this->error('Could not restore the city. It might not have been deleted or doesn\'t exist.');
            }
            $this->cityId = null;
        } catch (\Exception $e) {
            Log::error('City Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false;
            $this->error('An error occurred while trying to restore the city.');
        }
    }

    public function bulkRestore(CityService $cityService)
    {
        try {
            $cityIds = array_map('intval', $this->selectedCities);
            $restoredCount = $cityService->bulkRestoreCities($cityIds);
            $this->confirmingBulkRestore = false;
            if ($restoredCount > 0) {
                $this->success("{$restoredCount} cities successfully restored! ♻️");
            } else {
                $this->error('No selected cities were restored. They might not have been deleted.');
            }
            $this->selectedCities = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            Log::error('Bulk Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkRestore = false;
            $this->error('An error occurred during bulk restoration.');
        }
    }

    // --- Permanent Deletion ---
    public function confirmPermanentDelete($id)
    {
        $this->cityId = $id;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(CityService $cityService)
    {
        try {
            $successful = $cityService->permanentlyDelete($this->cityId);
            $this->confirmingPermanentDeletion = false;
            if ($successful) {
                $this->warning('City permanently removed from the system. Goodbye! 💥');
            } else {
                $this->error('Could not permanently delete the city. It might not exist.');
            }
            $this->cityId = null;
        } catch (\Exception $e) {
            Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false;
            // Add specific checks, e.g., if deletion is blocked due to relationships
            // Cities might be linked to Campuses or other entities
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete this city. It\'s still linked to other records (e.g., Campuses).');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkPermanentDelete(CityService $cityService)
    {
        try {
            $cityIds = array_map('intval', $this->selectedCities);
            $deletedCount = $cityService->bulkPermanentDelete($cityIds);
            $this->confirmingBulkPermanentDeletion = false;
            if ($deletedCount > 0) {
                $this->warning("{$deletedCount} cities permanently removed from the system. 💥");
            } else {
                $this->error('No selected cities were permanently deleted. They might not exist.');
            }
            $this->selectedCities = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete selected cities. Some are still linked to other records.');
            } else {
                $this->error('An error occurred during bulk permanent deletion.');
            }
        }
    }

    // --- Filters ---
    public function getActiveFiltersProperty()
    {
        $filters = [];

        if ($this->filterCountryId) {
            $countryName = $this->countries->firstWhere('id', $this->filterCountryId)?->name ?? 'Unknown Country';
            $filters[] = ['key' => 'filterCountryId', 'label' => 'Country', 'value' => $countryName];
        }
        if ($this->filterStateId) {
            $stateName = $this->states->firstWhere('id', $this->filterStateId)?->name ?? 'Unknown State';
            $filters[] = ['key' => 'filterStateId', 'label' => 'State', 'value' => $stateName];
        }

        return $filters;
    }

    public function removeFilter($filterKey)
    {
        $this->$filterKey = null;
        // If country filter is removed, also reset state filter
        if ($filterKey === 'filterCountryId') {
            $this->filterStateId = null;
            $this->loadStatesForSelect(); // Reload states to show all
        }
        $this->resetPage();
        $this->success('Filter removed.');
    }

    public function resetFilters()
    {
        $this->reset('filterStateId', 'filterCountryId');
        $this->loadStatesForSelect(); // Reload states to show all
        $this->resetPage();
        $this->success('Filters reset.');
    }

    // Fetch cities using the service
    private function getCities(CityService $cityService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
            // Add specific city filters
            'state_id' => $this->filterStateId,
            'country_id' => $this->filterCountryId, // Pass country filter to service
        ];

        $filterParams = array_filter(
            $filterParams,
            function ($value, $key) {
                return ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir', 'per_page']);
            },
            ARRAY_FILTER_USE_BOTH,
        );

        $filterParams['search'] = !empty($this->search);
        $filterParams['with_trashed'] = $this->showDeletedRecords;

        // Define default eager loads needed for the table/view
        $filterParams['with'] = ['state.country']; // Eager load state and country

        return $cityService->getPaginatedCities($filterParams);
    }

    // Render the view
    public function render(): mixed
    {
        $cities = $this->getCities(app(CityService::class));

        $currentPageIds = $cities->pluck('id')->map(fn($id) => (string) $id)->toArray(); // Explicitly cast to array
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedCities));

        return view('livewire.locations.city', [
            'cities' => $cities,
            'headers' => $this->headers,
        ]);
    }
};
?>

<!-- Main Container -->
<div wire:id="{{ $this->getId() }}">

    <!-- Header -->
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="City Management" icon="o-building-office-2" icon-classes="bg-yellow-500 rounded-full p-1 w-8 h-8 text-white"
        subtitle="Total Cities: {{ $cities->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}"
        subtitle-class="mr-2 mt-0.5 ">

        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Cities..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add City"
                label="Add City" />
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner tooltip-left="Filters" />
            {{-- Export Button (Optional for Cities, can add later) --}}
            {{-- <x-button icon="o-arrow-down-tray" wire:click="openExportModal"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner="openExportModal"
                tooltip-left="Export Data..." /> --}}
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

        @if (count($selectedCities))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Perform a bulk action" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ...$showDeletedRecords
                        ? [
                            ['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'],
                            ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete'],
                        ]
                        : [],
                ]"
                    class="select select-bordered select-sm py-0" id="bulk-action-select" x-data
                    x-on:change="$wire.handleBulkAction($event.target.value)" x-init="$watch('$wire.selectedCities', value => { if (value.length === 0) $el.value = ''; })"
                    x-on:reset-bulk-action.window="$el.value = ''" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedCities) }} selected
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
                                @if (($header['sortable'] ?? true) && $sortField === $header['key'])
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
                @forelse($cities as $city)
                    <tr wire:key="city-{{ $city->id }}"
                        class="{{ $city->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150 ease-in-out">
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedCities" value="{{ (string) $city->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        <!-- Name -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $city->name }}
                            </div>
                        </td>
                        <!-- State -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $city->state->name ?? 'N/A' }}
                            </div>
                        </td>
                        <!-- Country -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $city->state->country->name ?? 'N/A' }}
                            </div>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1.5">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $city->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" />

                                @if (!$city->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $city->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner tooltip-left="Edit City" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $city->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Delete City" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $city->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner tooltip-left="Restore City" />
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $city->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Permanently Delete City" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-16 w-16 text-gray-400 mb-4" />
                                <span class="text-lg font-medium">No cities found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term or filters.' : 'Start by adding a new city.' }}
                                </p>
                                @if ($search || $filterStateId || $filterCountryId)
                                    <x-button wire:click="resetFilters" class="mt-3 btn btn-secondary btn-sm"
                                        label="Clear Filters" />
                                @else
                                    <x-button wire:click="openModal" class="mt-3 btn btn-primary btn-sm"
                                        label="Add Your First City" />
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
        {{ $cities->links() }}
    </div>

    <!-- Add/Edit City Modal -->
    <x-modal wire:model="showModal" :title="$cityId ? 'Edit City' : 'Add New City'" box-class="max-w-xl" separator
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-4 p-4">
                {{-- Location Group --}}
                <div class="space-y-4">
                    <x-select wire:model.live="filterCountryId" label="Filter States by Country" :options="$countries"
                        placeholder="Select a Country" icon="o-globe-alt" clearable inline />
                    <x-select wire:model="state_id" label="State" :options="$states" placeholder="Select state"
                        icon="o-map" inline required />
                </div>

                {{-- City Name --}}
                <x-input wire:model="name" label="City Name" placeholder="Enter city name"
                    icon="o-building-office-2" inline required />
            </div>
        </x-form>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button type="submit" label="{{ $cityId ? 'Update City' : 'Create City' }}" class="btn-primary"
                wire:click="save" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View City Modal -->
    <x-modal wire:model="showViewModal" title="View City Details" separator box-class="max-w-xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($viewCity)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <x-icon name="o-building-office-2"
                            class="h-16 w-16 text-yellow-500 mr-4 flex-shrink-0 rounded-full bg-yellow-100 p-2" />
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $viewCity->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">State:
                                {{ $viewCity->state->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Country:
                                {{ $viewCity->state->country->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center justify-start sm:justify-end w-full sm:w-auto">
                        @if ($viewCity->deleted_at)
                            <span title="Deleted Status"
                                class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" />
                                Deleted
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 gap-6 pt-4">
                    <!-- Name -->
                    <div class="flex items-start">
                        <x-icon name="o-building-office-2"
                            class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                        <div class="flex-grow">
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">City Name
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                {{ $viewCity->name }}
                            </p>
                        </div>
                    </div>
                    <!-- State -->
                    <div class="flex items-start">
                        <x-icon name="o-map"
                            class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                        <div class="flex-grow">
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">State
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                {{ $viewCity->state->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <!-- Country -->
                    <div class="flex items-start">
                        <x-icon name="o-globe-alt"
                            class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                        <div class="flex-grow">
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Country
                            </div>
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                {{ $viewCity->state->country->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Timestamps Section -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1">
                            <x-icon name="o-calendar" class="h-4 w-4" />
                            <span>Created:
                                {{ $viewCity->created_at ? $viewCity->created_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <x-icon name="o-pencil-square" class="h-4 w-4" />
                            <span>Updated:
                                {{ $viewCity->updated_at ? $viewCity->updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        @if ($viewCity->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                <x-icon name="o-archive-box-x-mark" class="h-4 w-4" />
                                <span>Deleted: {{ $viewCity->deleted_at->format('M d, Y h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @else
            <!-- Skeleton Loader -->
            <div class="p-8 flex justify-center items-center">
                <div class="w-full max-w-xl animate-pulse space-y-6">
                    <!-- Header Skeleton -->
                    <div class="flex items-center pb-4 border-b dark:border-gray-700">
                        <div class="h-16 w-16 bg-gray-300 dark:bg-gray-700 rounded-full mr-4"></div>
                        <div class="flex-grow">
                            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                    </div>
                    <!-- Details Grid Skeleton -->
                    <div class="grid grid-cols-1 gap-6 pt-4">
                        @foreach (range(1, 3) as $_)
                            <div class="flex items-start">
                                <div class="h-5 w-5 bg-gray-300 dark:bg-gray-700 rounded mr-2 mt-0.5"></div>
                                <div class="flex-grow">
                                    <div class="h-3 bg-gray-300 dark:bg-gray-700 rounded w-20 mb-1.5"></div>
                                    <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"></div>
                                </div>
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
                @if ($viewCity && !$viewCity->deleted_at)
                    <x-button label="Edit City" icon="o-pencil" wire:click="openModal({{ $viewCity->id }})"
                        class="btn-primary" spinner />
                @endif
                <x-button label="Close" wire:click="closeModal()" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (Delete, Restore, Permanent Delete - Single & Bulk) -->
    <!-- Delete Confirmation -->
    <x-modal wire:model="confirmingDeletion" title="Delete City" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this city? This action will soft delete the record.
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
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Cities" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedCities) }} selected cities? This action
                    will soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedCities) }} Cities" wire:click="bulkDelete" class="btn-error"
                wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Restore Confirmation -->
    <x-modal wire:model="confirmingRestore" title="Restore City" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this city?
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
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Cities" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedCities) }} selected cities?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedCities) }} Cities" wire:click="bulkRestore"
                class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    <!-- Permanent Delete Confirmation -->
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete City" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this city? This action cannot be undone.
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
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Cities" separator
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedCities) }} selected cities?
                    This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedCities) }} Cities" wire:click="bulkPermanentDelete"
                class="btn-error" wire:loading.attr="disabled" wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Advanced Filter Drawer -->
    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button
        class="w-11/12 lg:w-1/3 bg-gray-50 dark:bg-gray-900">
        <div class="px-4 space-y-4">

            {{-- Section 1: Core Attributes --}}
            <div class="space-y-5">
                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Location Filters</div>
                <x-select wire:model.live="filterCountryId" label="Filter by Country" :options="$countries"
                    placeholder="All Countries" icon="o-globe-alt" clearable inline />
                <x-select wire:model.live="filterStateId" label="Filter by State" :options="$states"
                    placeholder="All States" icon="o-map" clearable inline />
            </div>

        </div>

        <x-slot:actions>
            <div
                class="flex justify-between w-full px-4 py-3 border-t dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                <x-button label="Reset Filters" icon="o-arrow-path" wire:click="resetFilters"
                    class="btn-ghost text-red-500" spinner />
                <x-button label="Apply Filters" icon="o-check" class="btn-primary"
                    @click="$wire.showFilterDrawer = false" />
            </div>
        </x-slot:actions>
    </x-drawer>

    {{-- No Export Modal for Cities yet --}}
    <x-modal wire:model="showGenericExportModal" title="Configure Export" separator persistent box-class="max-w-3xl"
        class="backdrop-blur-sm">
        <div x-data="{
            availableColumns: [], // Define available columns for City export
            selectedColumns: [], // Define default selected columns for City export
            // ... AlpineJS logic for column selection/reorder ...
        }">
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
                    {{-- PDF Export Button --}}
    <div x-on:click="triggerExport('pdf')" wire:loading.attr="disabled" wire:target="handleExport"
        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
        <x-icon name="o-document-text"
            class="w-10 h-10 text-red-500 mb-2 group-hover:scale-110 transition-transform" />
        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as PDF</span>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generates a formatted PDF document.</p>
        <div wire:loading wire:target="handleExport" class="mt-2">
            <x-loading class="loading-sm text-red-500" />
        </div>
    </div>

    {{-- Excel (XLSX) Export Button --}}
    <div x-on:click="triggerExport('xlsx')" wire:loading.attr="disabled" wire:target="handleExport"
        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
        <x-icon name="o-table-cells"
            class="w-10 h-10 text-green-500 mb-2 group-hover:scale-110 transition-transform" />
        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as Excel</span>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Creates a standard XLSX spreadsheet file.</p>
        <div wire:loading wire:target="handleExport" class="mt-2">
            <x-loading class="loading-sm text-green-500" />
        </div>
    </div>

    {{-- CSV Export Button --}}
    <div x-on:click="triggerExport('csv')" wire:loading.attr="disabled" wire:target="handleExport"
        :class="{ 'opacity-50 cursor-not-allowed': selectedColumns.length === 0 }"
        class="border dark:border-gray-700 rounded-lg p-4 flex flex-col items-center justify-center text-center hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer transition-colors duration-150 group">
        <x-icon name="o-document-chart-bar"
            class="w-10 h-10 text-blue-500 mb-2 group-hover:scale-110 transition-transform" />
        <span class="font-semibold text-gray-700 dark:text-gray-300">Export as CSV</span>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exports data in comma-separated values format.</p>
        <div wire:loading wire:target="handleExport" class="mt-2">
            <x-loading class="loading-sm text-blue-500" />
        </div>
    </div>
</div>
</div>

</div>
<x-slot:actions>
    <x-button label="Cancel" @click="$wire.showGenericExportModal = false" class="btn-ghost" />
</x-slot:actions>
</x-modal> --}}

</div>
