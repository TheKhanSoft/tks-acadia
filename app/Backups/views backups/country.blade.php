<?php

use App\Models\Country;
use App\Services\CountryService;
use App\Http\Requests\CountryRequest;
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

    // Country Model Properties
    public $countryId = null;
    public $name;
    public $iso3;
    public $iso2;
    public $numeric_code;
    public $phonecode;
    public $capital;
    public $currency;
    public $currency_name;
    public $currency_symbol;
    public $tld;
    public $native;
    public $region;
    public $subregion;
    public $nationality;
    public $latitude;
    public $longitude;
    public $emoji;
    public $emojiU;

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
    public $selectedCountries = [];
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $viewCountry = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    public $showFilterDrawer = false; // Add this public property

    public $activeFilters = [];

    // Define table headers
    public $headers = [['key' => 'name', 'label' => 'Name', 'sortable' => true], ['key' => 'iso2', 'label' => 'Region | Sub Region', 'sortable' => true], ['key' => 'capital', 'label' => 'Capital', 'sortable' => true], ['key' => 'currency', 'label' => 'Currency', 'sortable' => true]];

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
        $this->selectedCountries = [];
        $this->selectAll = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        $items = $this->getCountries(app(CountryService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedCountries = array_unique(array_merge($this->selectedCountries, $itemIds));
        } else {
            $this->selectedCountries = array_diff($this->selectedCountries, $itemIds);
        }
    }

    public function updatedSelectedCountries($value)
    {
        $items = $this->getCountries(app(CountryService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedCountries));
    }

    public function mount()
    {
        // No specific data to load on mount for countries yet
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->resetExcept(['search', 'sortField', 'sortDirection', 'perPage', 'showDeletedRecords', 'selectedCountries', 'selectAll', 'headers', 'showFilterDrawer']);

        $this->showModal = true;
        $this->countryId = $id;

        if ($id) {
            $countryService = app(CountryService::class);
            $country = $countryService->getCountry($id, $this->showDeletedRecords);
            if ($country) {
                $this->name = $country->name;
                $this->iso3 = $country->iso3;
                $this->iso2 = $country->iso2;
                $this->numeric_code = $country->numeric_code;
                $this->phonecode = $country->phonecode;
                $this->capital = $country->capital;
                $this->currency = $country->currency;
                $this->currency_name = $country->currency_name;
                $this->currency_symbol = $country->currency_symbol;
                $this->tld = $country->tld;
                $this->native = $country->native;
                $this->region = $country->region;
                $this->subregion = $country->subregion;
                $this->nationality = $country->nationality;
                $this->latitude = $country->latitude;
                $this->longitude = $country->longitude;
                $this->emoji = $country->emoji;
                $this->emojiU = $country->emojiU;
            } else {
                $this->error('Country not found.');
                $this->closeModal();
                return;
            }
        } else {
            $this->name = '';
            $this->iso3 = '';
            $this->iso2 = '';
            $this->numeric_code = '';
            $this->phonecode = '';
            $this->capital = '';
            $this->currency = '';
            $this->currency_name = '';
            $this->currency_symbol = '';
            $this->tld = '';
            $this->native = '';
            $this->region = '';
            $this->subregion = '';
            $this->nationality = '';
            $this->latitude = '';
            $this->longitude = '';
            $this->emoji = '';
            $this->emojiU = '';
        }
    }

    public function openViewModal($id, CountryService $countryService)
    {
        $this->viewCountry = $countryService->getCountry($id, true);
        if (!$this->viewCountry) {
            $this->error('Country not found.');
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
        $this->countryId = null;
        $this->viewCountry = null;
    }

    public function save(CountryService $countryService)
    {
        $request = new CountryRequest();
        $currentId = $this->countryId;
        $rules = $request->rules($currentId);
        $messages = $request->messages();
        $attributes = $request->attributes();

        $dataToValidate = [
            'name' => $this->name,
            'iso3' => $this->iso3,
            'iso2' => $this->iso2,
            'numeric_code' => $this->numeric_code,
            'phonecode' => $this->phonecode,
            'capital' => $this->capital,
            'currency' => $this->currency,
            'currency_name' => $this->currency_name,
            'currency_symbol' => $this->currency_symbol,
            'tld' => $this->tld,
            'native' => $this->native,
            'region' => $this->region,
            'subregion' => $this->subregion,
            'nationality' => $this->nationality,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'emoji' => $this->emoji,
            'emojiU' => $this->emojiU,
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();

        try {
            if ($this->countryId) {
                $country = $countryService->getCountry($this->countryId);
                if ($country) {
                    $countryService->updateCountry($country, $validatedData);
                    $this->success('Fantastic! Country details updated successfully. 🌍');
                } else {
                    $this->error('Oops! Could not find the country you were trying to update.');
                    $this->closeModal();
                    return;
                }
            } else {
                $countryService->createCountry($validatedData);
                $this->success('Hooray! A new country has been added to the list. ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('Country Save Error: ' . $e->getMessage(), ['exception' => $e]);
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->error('Looks like a country with that name or code already exists. Please try a different one.');
            } else {
                $this->error('An unexpected error occurred while saving the country. Please try again.');
            }
        }
    }

    // --- Bulk Actions ---
    public function handleBulkAction($action)
    {
        if (!$action || empty($this->selectedCountries)) {
            if (empty($this->selectedCountries)) {
                $this->warning('Please select countries first 🤔');
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
        $this->countryId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete(CountryService $countryService)
    {
        try {
            $successful = $countryService->deleteCountryById($this->countryId);
            $this->confirmingDeletion = false;
            if ($successful) {
                $this->warning('Country moved to trash. You can restore it from the "Show deleted" view. 🗑️');
            } else {
                $this->error('Hmm, could not delete the country. It might have already been removed or doesn\'t exist.');
            }
            $this->countryId = null;
        } catch (\Exception $e) {
            Log::error('Country Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingDeletion = false;
            $this->error('An error occurred while trying to delete the country.');
        }
    }

    public function bulkDelete(CountryService $countryService)
    {
        try {
            $countryIds = array_map('intval', $this->selectedCountries);
            $deletedCount = $countryService->bulkDeleteCountryByIds($countryIds);
            $this->confirmingBulkDeletion = false;
            if ($deletedCount > 0) {
                $this->warning("{$deletedCount} countries moved to trash. 🗑️");
            } else {
                $this->error('No selected countries were deleted. They might have already been removed.');
            }
            $this->selectedCountries = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            Log::error('Bulk Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete selected countries. Some are still linked to other records.');
            } else {
                $this->error('An error occurred during bulk deletion.');
            }
        }
    }

    // --- Restoration ---
    public function confirmRestore($id)
    {
        $this->countryId = $id;
        $this->confirmingRestore = true;
    }

    public function restore(CountryService $countryService)
    {
        try {
            $successful = $countryService->restoreCountry($this->countryId);
            $this->confirmingRestore = false;
            if ($successful) {
                $this->success('Country successfully restored! Welcome back. ♻️');
            } else {
                $this->error('Could not restore the country. It might not have been deleted or doesn\'t exist.');
            }
            $this->countryId = null;
        } catch (\Exception $e) {
            Log::error('Country Restore Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingRestore = false;
            $this->error('An error occurred while trying to restore the country.');
        }
    }

    public function bulkRestore(CountryService $countryService)
    {
        try {
            $countryIds = array_map('intval', $this->selectedCountries);
            $restoredCount = $countryService->bulkRestoreCountries($countryIds);
            $this->confirmingBulkRestore = false;
            if ($restoredCount > 0) {
                $this->success("{$restoredCount} countries successfully restored! ♻️");
            } else {
                $this->error('No selected countries were restored. They might not have been deleted.');
            }
            $this->selectedCountries = [];
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
        $this->countryId = $id;
        $this->confirmingPermanentDeletion = true;
    }

    public function permanentDelete(CountryService $countryService)
    {
        try {
            $successful = $countryService->permanentlyDelete($this->countryId);
            $this->confirmingPermanentDeletion = false;
            if ($successful) {
                $this->warning('Country permanently removed from the system. Goodbye! 💥');
            } else {
                $this->error('Could not permanently delete the country. It might not exist.');
            }
            $this->countryId = null;
        } catch (\Exception $e) {
            Log::error('Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete this country. It\'s still linked to other records (like states or cities).');
            } else {
                $this->error('An error occurred during permanent deletion.');
            }
        }
    }

    public function bulkPermanentDelete(CountryService $countryService)
    {
        try {
            $countryIds = array_map('intval', $this->selectedCountries);
            $deletedCount = $countryService->bulkPermanentDelete($countryIds);
            $this->confirmingBulkPermanentDeletion = false;
            if ($deletedCount > 0) {
                $this->warning("{$deletedCount} countries permanently removed from the system. 💥");
            } else {
                $this->error('No selected countries were permanently deleted. They might not exist.');
            }
            $this->selectedCountries = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            Log::error('Bulk Permanent Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->confirmingBulkPermanentDeletion = false;
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->error('Cannot permanently delete selected countries. Some are still linked to other records.');
            } else {
                $this->error('An error occurred during bulk permanent deletion.');
            }
        }
    }

    // No specific filters for Country yet, but can add later if needed

    // Fetch countries using the service
    private function getCountries(CountryService $countryService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
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

        return $countryService->getPaginatedCountries($filterParams);
    }

    // Render the view
    public function render(): mixed
    {
        $countries = $this->getCountries(app(CountryService::class));

        $currentPageIds = $countries->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedCountries));

        return view('livewire.locations.country', [
            'countries' => $countries,
            'headers' => $this->headers,
        ]);
    }
};

?>

<!-- Main Container -->
<div wire:id="{{ $this->getId() }}">

    <!-- Header -->
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Country Management" icon="o-globe-alt" icon-classes="bg-blue-500 rounded-full p-1 w-8 h-8 text-white"
        subtitle="Total Countries: {{ $countries->total() }} {{ $showDeletedRecords ? 'including deleted' : '' }}"
        subtitle-class="mr-2 mt-0.5 ">

        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Countries..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm flex items-center justify-center" spinner tooltip-left="Add Country"
                label="Add Country" />
            {{-- No filters for Country yet, hide filter button --}}
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner tooltip-left="Filters" />
            {{-- Export Button (Optional for Countries, can add later) --}}
            <x-button icon="o-arrow-down-tray" wire:click="openExportModal"
                class="btn btn-ghost btn-sm flex items-center justify-center" spinner="openExportModal"
                tooltip-left="Export Data..." />
        </x-slot:actions>
    </x-header>

    <!-- Active Filters Display (Hidden for Countries initially) -->
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
                    class="select select-bordered select-sm font-medium py-0 pl-2 pr-8" />

                <label class="text-sm text-gray-700 dark:text-gray-300">per page</label>

            </div>
        </div>

        @if (count($selectedCountries))
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
                    x-on:change="$wire.handleBulkAction($event.target.value)" x-init="$watch('$wire.selectedCountries', value => { if (value.length === 0) $el.value = ''; })"
                    x-on:reset-bulk-action.window="$el.value = ''" />

                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($selectedCountries) }} selected
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
                @forelse($countries as $country)
                    <tr wire:key="country-{{ $country->id }}"
                        class="{{ $country->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150 ease-in-out">
                        <td class="p-4 w-8">
                            <x-checkbox wire:model.live="selectedCountries" value="{{ (string) $country->id }}"
                                class="checkbox-sm checkbox-primary" />
                        </td>
                        <!-- Name -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $country->name }}
                                <span class="text-sm text-gray-600 dark:text-white">({{ $country->native }})</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ISO2: <span
                                    class="font-medium text-blue-400 dark:text-blue-300">{{ $country->iso2 }}</span> |
                                ISO3: <span
                                    class="font-medium text-blue-400 dark:text-blue-300">{{ $country->iso3 }}</span>
                            </div>
                        </td>
                        <!-- Region | Subregion -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $country->region }} | {{ $country->subregion }}
                            </div>
                        </td>
                        <!-- Capital -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $country->capital }}
                            </div>
                        </td>
                        <!-- Currency -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $country->currency }} | {{ $country->currency_symbol }}
                            </div>
                        </td>
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1.5">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $country->id }})"
                                    class="btn btn-ghost btn-xs !h-7 !w-7 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    spinner tooltip-left="View Details" />

                                @if (!$country->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $country->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        spinner tooltip-left="Edit Country" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $country->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Delete Country" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $country->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        spinner tooltip-left="Restore Country" />
                                    <x-button icon="o-no-symbol"
                                        wire:click="confirmPermanentDelete({{ $country->id }})"
                                        class="btn btn-ghost btn-xs !h-7 !w-7 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        spinner tooltip-left="Permanently Delete Country" />
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
                                <span class="text-lg font-medium">No countries found</span>
                                <p class="text-sm mt-1">
                                    {{ $search ? 'Try adjusting your search term.' : 'Start by adding a new country.' }}
                                </p>
                                @if ($search)
                                    <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm"
                                        label="Clear Search" />
                                @else
                                    <x-button wire:click="openModal" class="mt-3 btn btn-primary btn-sm"
                                        label="Add Your First Country" />
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
        {{ $countries->links() }}
    </div>

    <!-- Add/Edit Country Modal -->
    <x-modal wire:model="showModal" :title="$countryId ? 'Edit Country' : 'Add New Country'" box-class="max-w-4xl" separator
        class="mx-auto rounded-xl shadow-2xl mx-10">
        <x-form wire:submit.prevent="save">

            <div class="grid grid-cols-1 sm:grid-cols-2 p-4">
                {{-- General Information --}}
                <div class="col-span-2 md:col-span-2 text-lg font-semibold border-b pb-2 mb-2">General Information
                </div>
                <x-input wire:model="name" label="Country Name" placeholder="Enter country name" icon="o-globe-alt"
                    inline required class="w-auto" />
                <x-input wire:model="nationality" label="Nationality" placeholder="Enter nationality (e.g. American)"
                    icon="o-user-group" inline class="w-auto" />
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4">
                {{-- Codes --}}
                <div class="col-span-5 text-lg font-semibold border-b pb-2 mb-2 mt-4">Codes</div>
                <x-input wire:model="iso3" label="ISO3 Code" placeholder="ISO3 code (e.g. PAK)" icon="o-tag"
                    inline class="col-span-1" />
                <x-input wire:model="iso2" label="ISO2 Code" placeholder="ISO2 code (e.g. PK)" icon="o-tag" inline
                    class="col-span-2" />
                <x-input wire:model="numeric_code" label="Numeric Code" placeholder="Enter numeric code (e.g. 840)"
                    icon="o-hashtag" inline classes="col-span-2" type="number" /> {{-- Change to number input --}}
                <x-input wire:model="phonecode" label="Phone Code" placeholder="Phone code (e.g. 93)" icon="o-phone"
                    inline class="col-span-2" type="number" /> {{-- Change to number input --}}
                <x-input wire:model="tld" label="TLD" placeholder="Top-level domain (e.g., .pk)"
                    icon="o-globe-alt" inline class="col-span-2" />

                {{-- Geography --}}
                <div class="col-span-5 text-lg font-semibold border-b pb-2 mb-2 mt-4">Geography</div>
                <x-input wire:model="capital" label="Capital" placeholder="Capital city" icon="o-building-office"
                    inline class="col-span-2" />
                <x-input wire:model="region" label="Region" placeholder="Enter region (e.g. Asia)" icon="o-map"
                    inline class="col-span-2" />
                <x-input wire:model="subregion" label="Subregion" placeholder="Enter subregion (e.g. South Asia)"
                    icon="o-map" inline class="col-span-2" />
                <x-input wire:model="latitude" label="Latitude" placeholder="Enter latitude" icon="o-map-pin" inline
                    class="w-auto" />
                <x-input wire:model="longitude" label="Longitude" placeholder="Enter longitude" icon="o-map-pin"
                    inline class="w-auto" />

                {{-- Currency --}}
                <div class="col-span-5 text-lg font-semibold border-b pb-2 mb-2 mt-4">Currency</div>
                <x-input wire:model="currency" label="Currency" placeholder="Enter currency code (e.g. PKR)"
                    icon="o-currency-dollar" inline class="w-auto" />
                <x-input wire:model="currency_name" label="Currency Name"
                    placeholder="Enter currency name (e.g. Pakistani Rupee)" icon="o-currency-dollar" inline
                    class="col-span-2" />
                <x-input wire:model="currency_symbol" label="Currency Symbol" placeholder="Currency symbol (e.g. ₨)"
                    icon="o-currency-dollar" inline class="w-auto" />

                {{-- Other --}}
                <div class="col-span-5 text-lg font-semibold border-b pb-2 mb-2 mt-4">Other</div>
                <x-input wire:model="native" label="Native Name" placeholder="Enter native name" icon="o-language"
                    inline class="col-span-2" />
                <x-input wire:model="emoji" label="Emoji" placeholder="Enter emoji (e.g. 🇵🇰)" icon="o-face-smile"
                    inline class="w-auto" />
                <x-input wire:model="emojiU" label="Emoji Unicode" placeholder="Emoji unicode (e.g. U+1F1F5 U+1F1F0)"
                    icon="o-face-smile" inline class="col-span-2" />
            </div>
        </x-form>

        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button type="submit" label="{{ $countryId ? 'Update Country' : 'Create Country' }}"
                class="btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <!-- View Country Modal -->
    <x-modal wire:model="showViewModal" title="View Country Details" separator box-class="max-w-xl"
        class="fixed text-gray-500 flex items-center justify-center overflow-auto z-50 bg-black bg-opacity-40 left-0 right-0 top-0 bottom-0">
        @if ($viewCountry)
            <div class="p-6 space-y-6 bg-white dark:bg-gray-900 rounded-lg">

                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <x-icon name="o-globe-alt"
                            class="h-16 w-16 text-blue-500 mr-4 flex-shrink-0 rounded-full bg-blue-100 p-2" />
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $viewCountry->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Native Name:
                                {{ $viewCountry->native }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center justify-start sm:justify-end w-full sm:w-auto">
                        @if ($viewCountry->deleted_at)
                            <span title="Deleted Status"
                                class="inline-flex items-center px-3 shadow py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <x-icon name="o-archive-box-x-mark" class="h-3 w-3 mr-1" />
                                Deleted
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="space-y-6 pt-4"> {{-- Use space-y for vertical spacing between sections --}}

                    {{-- Basic Information --}}
                    <div>
                        <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-800 dark:text-white">Basic
                            Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"> {{-- Use a grid for fields within the section --}}
                            {{-- Name --}}
                            <div class="flex items-start">
                                <x-icon name="o-globe-alt"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Country Name</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->name }}</p>
                                </div>
                            </div>
                            {{-- Nationality --}}
                            <div class="flex items-start sm:col-span-2"> {{-- Span two columns --}}
                                <x-icon name="o-user-group"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Nationality</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->nationality }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Geography --}}
                    <div>
                        <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-800 dark:text-white">Geography
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Capital --}}
                            <div class="flex items-start sm:col-span-2">
                                <x-icon name="o-building-office"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Capital</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->capital }}</p>
                                </div>
                            </div>
                            {{-- Region --}}
                            <div class="flex items-start">
                                <x-icon name="o-map"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Region</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->region }}</p>
                                </div>
                            </div>
                            {{-- Subregion --}}
                            <div class="flex items-start">
                                <x-icon name="o-map"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Subregion</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->subregion }}</p>
                                </div>
                            </div>
                            {{-- Latitude --}}
                            <div class="flex items-start">
                                <x-icon name="o-map-pin"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Latitude</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->latitude }}</p>
                                </div>
                            </div>
                            {{-- Longitude --}}
                            <div class="flex items-start">
                                <x-icon name="o-map-pin"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Longitude</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->longitude }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Currency --}}
                    <div>
                        <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-800 dark:text-white">Currency
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Currency --}}
                            <div class="flex items-start">
                                <x-icon name="o-currency-dollar"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Currency</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->currency }}</p>
                                </div>
                            </div>
                            {{-- Currency Name --}}
                            <div class="flex items-start sm:col-span-2">
                                <x-icon name="o-currency-dollar"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Currency Name</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->currency_name }}</p>
                                </div>
                            </div>
                            {{-- Currency Symbol --}}
                            <div class="flex items-start">
                                <x-icon name="o-currency-dollar"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                                        Currency Symbol</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->currency_symbol }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Other --}}
                    <div>
                        <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-800 dark:text-white">Other</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Emoji --}}
                            <div class="flex items-start">
                                <x-icon name="o-face-smile"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Emoji
                                    </div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->emoji }}</p>
                                </div>
                            </div>
                            {{-- Emoji Unicode --}}
                            <div class="flex items-start sm:col-span-2">
                                <x-icon name="o-face-smile"
                                    class="h-5 w-5 mr-2 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Emoji
                                        Unicode</div>
                                    <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $viewCountry->emojiU }}</p>
                                </div>
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
                                {{ $viewCountry->created_at ? $viewCountry->created_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <x-icon name="o-pencil-square" class="h-4 w-4" />
                            <span>Updated:
                                {{ $viewCountry->updated_at ? $viewCountry->updated_at->format('M d, Y h:i A') : '-' }}</span>
                        </div>
                        @if ($viewCountry->deleted_at)
                            <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                                <x-icon name="o-archive-box-x-mark" class="h-4 w-4" />
                                <span>Deleted: {{ $viewCountry->deleted_at->format('M d, Y h:i A') }}</span>
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
                        @foreach (range(1, 2) as $_)
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
                @if ($viewCountry && !$viewCountry->deleted_at)
                    <x-button label="Edit Country" icon="o-pencil" wire:click="openModal({{ $viewCountry->id }})"
                        class="btn-primary" spinner />
                @endif
                <x-button label="Close" wire:click="closeModal()" class="btn-ghost" />
            </div>
        </x-slot:actions>
    </x-modal>

    <!-- Confirmation Modals (Delete, Restore, Permanent Delete - Single & Bulk) -->
    <!-- Delete Confirmation -->
    <x-modal wire:model="confirmingDeletion" title="Delete Country" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete this country? This action will soft delete the record.
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
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Countries" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-exclamation-triangle" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to delete {{ count($selectedCountries) }} selected countries? This action
                    will soft delete these records.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Delete {{ count($selectedCountries) }} Countries" wire:click="bulkDelete"
                class="btn-error" wire:loading.attr="disabled" wire:target="bulkDelete" />
        </x-slot:actions>
    </x-modal>

    <!-- Restore Confirmation -->
    <x-modal wire:model="confirmingRestore" title="Restore Country" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore this country?
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
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Countries" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-arrow-path" class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to restore {{ count($selectedCountries) }} selected countries?
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Restore {{ count($selectedCountries) }} Countries" wire:click="bulkRestore"
                class="btn-success" wire:loading.attr="disabled" wire:target="bulkRestore" />
        </x-slot:actions>
    </x-modal>

    <!-- Permanent Delete Confirmation -->
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Country" separator class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete this country? This action cannot be undone.
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
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Countries" separator
        class="">
        <div class="p-4 flex items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-icon name="o-no-symbol" class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Are you sure you want to permanently delete {{ count($selectedCountries) }} selected countries?
                    This action cannot be undone.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="closeModal" class="btn-ghost" />
            <x-button label="Permanently Delete {{ count($selectedCountries) }} Countries"
                wire:click="bulkPermanentDelete" class="btn-error" wire:loading.attr="disabled"
                wire:target="bulkPermanentDelete" />
        </x-slot:actions>
    </x-modal>

    {{-- No Filter Drawer for Countries yet --}}
    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button
        class="w-11/12 lg:w-1/3 bg-gray-50 dark:bg-gray-900">
        <div class="px-4 space-y-4">
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

    {{-- No Export Modal for Countries yet --}}
    <x-modal wire:model="showGenericExportModal" title="Configure Export" separator box-class="max-w-3xl"
        class="backdrop-blur-sm">
        <div x-data="{
            availableColumns: [], // Define available columns for Country export
            selectedColumns: [], // Define default selected columns for Country export
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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Generates a formatted PDF document.
                        </p>
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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Creates a standard XLSX spreadsheet
                            file.</p>
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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exports data in comma-separated values
                            format.</p>
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
    </x-modal>

</div>
