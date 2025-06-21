<?php

use App\Models\Faculty;
use App\Models\Campus;
use App\Models\Employee; // Assuming Employee model for Dean
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FacultyRequest; // Assuming FacultyRequest exists
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use WithPagination, Toast;

    // Component properties for Faculty
    public $facultyId = null;
    public $name;
    public $code;
    public $description;
    public $campus_id;
    public $head_id; // Changed from dean_id
    public $established_year;
    public $is_active = true;

    // Options for selects
    public $campuses = [];
    public $employees = []; // For Dean selection

    // UI state properties
    public $perPage = 10;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeletedRecords = false;
    public $selectedFaculties = [];
    public $selectAll = false;

    // Modal state
    public $showModal = false;
    public $showViewModal = false;
    public $viewFaculty = null;
    public $confirmingDeletion = false;
    public $confirmingBulkDeletion = false;
    public $confirmingPermanentDeletion = false;
    public $confirmingBulkPermanentDeletion = false;
    public $confirmingRestore = false;
    public $confirmingBulkRestore = false;

    // Filter Drawer
    public $showFilterDrawer = false;
    #[Url]
    public $filterCampusId = null;
    #[Url]
    public $filterHeadId = null; // Changed from filterDeanId
    #[Url]
    public $filterEstablishedYearStart = null;
    #[Url]
    public $filterEstablishedYearEnd = null;
    #[Url]
    public $filterStatus = ''; // '', 'active', 'inactive'

    // Modal tab control
    public $selectedTab = 'basic';

    // Define table headers
    public $headers = [
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'code', 'label' => 'Code', 'class' => 'hidden md:table-cell'],
        ['key' => 'head.first_name', 'label' => 'Head', 'class' => 'hidden lg:table-cell'], // Changed from dean.first_name to head.first_name, label to Head
        ['key' => 'established_year', 'label' => 'Established', 'class' => 'hidden md:table-cell'],
        ['key' => 'is_active', 'label' => 'Status']
    ];

    public function mount()
    {
        $this->campuses = Campus::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        // Assuming Employee model has a scope or way to identify potential deans, or just load all active employees
        $this->employees = Employee::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']); // Removed where('is_active', true)

        // Restore persisted state
        $this->search = session('faculty_search', $this->search);
        $this->sortField = session('faculty_sortField', $this->sortField);
        $this->sortDirection = session('faculty_sortDirection', $this->sortDirection);
        $this->perPage = session('faculty_perPage', $this->perPage);
        $this->showDeletedRecords = session('faculty_showDeletedRecords', $this->showDeletedRecords);
        $this->filterCampusId = session('faculty_filterCampusId', $this->filterCampusId);
        $this->filterHeadId = session('faculty_filterHeadId', $this->filterHeadId); // Changed from filterDeanId
        $this->filterEstablishedYearStart = session('faculty_filterEstablishedYearStart', $this->filterEstablishedYearStart);
        $this->filterEstablishedYearEnd = session('faculty_filterEstablishedYearEnd', $this->filterEstablishedYearEnd);
        $this->filterStatus = session('faculty_filterStatus', $this->filterStatus);
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }
    public function updatedShowDeletedRecords() { $this->resetPage(); $this->selectedFaculties = []; $this->selectAll = false; }
    public function updatedFilterCampusId() { $this->resetPage(); }
    public function updatedFilterHeadId() { $this->resetPage(); } // Changed from updatedFilterDeanId
    public function updatedFilterEstablishedYearStart() { $this->resetPage(); }
    public function updatedFilterEstablishedYearEnd() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }

    public function sortBy($field)
    {
        $validSortFields = ['name', 'code', 'campus.name', 'head.first_name', 'established_year', 'is_active', 'created_at', 'updated_at']; // Changed dean.first_name to head.first_name
        if (!in_array($field, $validSortFields)) {
            // Handle related fields by sorting on the primary table's foreign key or a joined column name if applicable
            if ($field === 'campus.name') $field = 'campus_id';
            elseif ($field === 'head.first_name') $field = 'head_id'; // Changed from dean.first_name to head.first_name, dean_id to head_id
            else return;
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
        $items = $this->getFaculties();
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        if ($value) {
            $this->selectedFaculties = array_unique(array_merge($this->selectedFaculties, $itemIds));
        } else {
            $this->selectedFaculties = array_diff($this->selectedFaculties, $itemIds);
        }
    }

    public function updatedSelectedFaculties()
    {
        $items = $this->getFaculties();
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedFaculties));
    }

    public function openModal($facultyId = null)
    {
        $this->resetValidation();
        // Preserve essential states, reset form fields
        $currentSearch = $this->search;
        $currentSortField = $this->sortField;
        $currentSortDirection = $this->sortDirection;
        $currentPerPage = $this->perPage;
        $currentShowDeleted = $this->showDeletedRecords;
        $currentFilterCampusId = $this->filterCampusId;
        $currentFilterHeadId = $this->filterHeadId; // Changed from currentFilterDeanId
        $currentFilterEstablishedYearStart = $this->filterEstablishedYearStart;
        $currentFilterEstablishedYearEnd = $this->filterEstablishedYearEnd;
        $currentFilterStatus = $this->filterStatus;

        $this->resetExcept(['campuses', 'employees', 'headers']); // Keep preloaded options and headers

        $this->search = $currentSearch;
        $this->sortField = $currentSortField;
        $this->sortDirection = $currentSortDirection;
        $this->perPage = $currentPerPage;
        $this->showDeletedRecords = $currentShowDeleted;
        $this->filterCampusId = $currentFilterCampusId;
        $this->filterHeadId = $currentFilterHeadId; // Changed from filterDeanId
        $this->filterEstablishedYearStart = $currentFilterEstablishedYearStart;
        $this->filterEstablishedYearEnd = $currentFilterEstablishedYearEnd;
        $this->filterStatus = $currentFilterStatus;


        $this->showModal = true;
        $this->facultyId = $facultyId;
        $this->selectedTab = 'basic';

        if ($facultyId) {
            $faculty = Faculty::findOrFail($facultyId);
            $this->name = $faculty->name;
            $this->code = $faculty->code;
            $this->description = $faculty->description;
            $this->campus_id = $faculty->campus_id;
            $this->head_id = $faculty->head_id; // Changed from dean_id
            $this->established_year = $faculty->established_year;
            $this->is_active = $faculty->is_active;
        } else {
            $this->name = '';
            $this->code = '';
            $this->description = '';
            $this->campus_id = null;
            $this->head_id = null; // Changed from dean_id
            $this->established_year = null;
            $this->is_active = true;
        }
    }

    public function openViewModal($facultyId)
    {
        $this->viewFaculty = Faculty::with(['campus', 'head'])->findOrFail($facultyId); // Eager load relations, changed dean to head
        $this->showViewModal = true;
    }

    public function closeModal()
    {
        session([
            'faculty_search' => $this->search,
            'faculty_sortField' => $this->sortField,
            'faculty_sortDirection' => $this->sortDirection,
            'faculty_perPage' => $this->perPage,
            'faculty_showDeletedRecords' => $this->showDeletedRecords,
            'faculty_filterCampusId' => $this->filterCampusId,
            'faculty_filterHeadId' => $this->filterHeadId, // Changed from faculty_filterDeanId
            'faculty_filterEstablishedYearStart' => $this->filterEstablishedYearStart,
            'faculty_filterEstablishedYearEnd' => $this->filterEstablishedYearEnd,
            'faculty_filterStatus' => $this->filterStatus,
        ]);

        $this->showModal = false;
        $this->showViewModal = false;
        $this->confirmingDeletion = false;
        $this->confirmingBulkDeletion = false;
        $this->confirmingPermanentDeletion = false;
        $this->confirmingBulkPermanentDeletion = false;
        $this->confirmingRestore = false;
        $this->confirmingBulkRestore = false;
        $this->facultyId = null;
        $this->viewFaculty = null;
    }

    public function save()
    {
        $facultyRequest = new FacultyRequest();
        $rules = $facultyRequest->rules($this->facultyId);
        $messages = $facultyRequest->messages();
        $attributes = $facultyRequest->attributes();

        $dataToValidate = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'campus_id' => $this->campus_id,
            'head_id' => $this->head_id, // Changed from dean_id
            'established_year' => $this->established_year,
            'is_active' => $this->is_active,
        ];

        $validatedData = Validator::make($dataToValidate, $rules, $messages, $attributes)->validate();
        $validatedData['code'] = strtoupper($validatedData['code']);

        try {
            if ($this->facultyId) {
                $faculty = Faculty::findOrFail($this->facultyId);
                $faculty->update($validatedData);
                $this->success('Faculty/College updated successfully! 🏛️');
            } else {
                Faculty::create($validatedData);
                $this->success('New Faculty/College added successfully! ✨');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Faculty Save Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('An error occurred while saving the Faculty/College.');
        }
    }

    public function toggleActive($facultyId)
    {
        try {
            $faculty = Faculty::findOrFail($facultyId);
            $faculty->update(['is_active' => !$faculty->is_active]);
            $statusText = $faculty->is_active ? 'activated' : 'deactivated';
            $this->success("Faculty/College {$statusText} successfully! 🔄");
        } catch (\Exception $e) {
            \Log::error('Toggle Active Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to toggle status.');
        }
    }

    public function handleBulkAction($action)
    {
        if (!$action || empty($this->selectedFaculties)) {
            if (empty($this->selectedFaculties)) $this->warning('Please select faculties first 🤔');
            return;
        }
        $map = [
            'confirmBulkDelete' => 'confirmingBulkDeletion',
            'bulkToggleActive' => null,
            'confirmBulkRestore' => 'confirmingBulkRestore',
            'confirmBulkPermanentDelete' => 'confirmingBulkPermanentDeletion',
        ];
        if ($action === 'bulkToggleActive') $this->bulkToggleActive();
        elseif (isset($map[$action])) $this->{$map[$action]} = true;
    }

    public function bulkToggleActive()
    {
        if (empty($this->selectedFaculties)) { $this->warning('Please select faculties first 🤔'); return; }
        try {
            $faculties = Faculty::whereIn('id', $this->selectedFaculties)->get();
            $setActive = $faculties->where('is_active', false)->count() >= $faculties->count() / 2;
            Faculty::whereIn('id', $this->selectedFaculties)->update(['is_active' => $setActive]);
            $this->success(count($this->selectedFaculties) . " faculties " . ($setActive ? 'activated' : 'deactivated') . " successfully! 🔄");
            $this->selectedFaculties = []; $this->selectAll = false;
        } catch (\Exception $e) { $this->error('Failed to toggle status for selected faculties.'); \Log::error('Bulk Toggle Active Error: ' . $e->getMessage()); }
    }

    public function confirmDelete($id) { $this->facultyId = $id; $this->confirmingDeletion = true; }
    public function delete()
    {
        try { Faculty::findOrFail($this->facultyId)->delete(); $this->warning('Faculty/College deleted! 🗑️'); }
        catch (\Exception $e) { $this->error('Failed to delete.'); \Log::error('Delete Error: ' . $e->getMessage()); }
        finally { $this->confirmingDeletion = false; $this->facultyId = null; }
    }

    public function bulkDelete()
    {
        try { Faculty::whereIn('id', $this->selectedFaculties)->delete(); $this->warning(count($this->selectedFaculties) . ' faculties deleted! 🗑️'); }
        catch (\Exception $e) { $this->error('Failed to delete selected.'); \Log::error('Bulk Delete Error: ' . $e->getMessage()); }
        finally { $this->confirmingBulkDeletion = false; $this->selectedFaculties = []; $this->selectAll = false; }
    }

    public function confirmRestore($id) { $this->facultyId = $id; $this->confirmingRestore = true; }
    public function restore()
    {
        try { Faculty::withTrashed()->find($this->facultyId)->restore(); $this->success('Faculty/College restored! ♻️'); }
        catch (\Exception $e) { $this->error('Failed to restore.'); \Log::error('Restore Error: ' . $e->getMessage()); }
        finally { $this->confirmingRestore = false; $this->facultyId = null; }
    }

    public function bulkRestore()
    {
        try { Faculty::withTrashed()->whereIn('id', $this->selectedFaculties)->restore(); $this->success(count($this->selectedFaculties) . ' faculties restored! ♻️'); }
        catch (\Exception $e) { $this->error('Failed to restore selected.'); \Log::error('Bulk Restore Error: ' . $e->getMessage()); }
        finally { $this->confirmingBulkRestore = false; $this->selectedFaculties = []; $this->selectAll = false; }
    }

    public function confirmPermanentDelete($id) { $this->facultyId = $id; $this->confirmingPermanentDeletion = true; }
    public function permanentDelete()
    {
        try { Faculty::withTrashed()->find($this->facultyId)->forceDelete(); $this->warning('Faculty/College permanently deleted! 💥'); }
        catch (\Exception $e) { $this->error('Failed to permanently delete.'); \Log::error('Permanent Delete Error: ' . $e->getMessage()); }
        finally { $this->confirmingPermanentDeletion = false; $this->facultyId = null; }
    }

    public function bulkPermanentDelete()
    {
        try { Faculty::withTrashed()->whereIn('id', $this->selectedFaculties)->forceDelete(); $this->warning(count($this->selectedFaculties) . ' faculties permanently deleted! 💥'); }
        catch (\Exception $e) { $this->error('Failed to permanently delete selected.'); \Log::error('Bulk Permanent Delete Error: ' . $e->getMessage()); }
        finally { $this->confirmingBulkPermanentDeletion = false; $this->selectedFaculties = []; $this->selectAll = false; }
    }

    public function getActiveFiltersProperty()
    {
        $filters = [];
        if ($this->filterCampusId) {
            $campusName = $this->campuses->firstWhere('id', $this->filterCampusId)?->name ?? 'Unknown';
            $filters[] = ['key' => 'filterCampusId', 'label' => 'Campus', 'value' => $campusName];
        }
        if ($this->filterHeadId) { // Changed from filterDeanId
            $head = $this->employees->firstWhere('id', $this->filterHeadId); // Changed from dean to head
            $headName = $head ? $head->first_name . ' ' . $head->last_name : 'Unknown'; // Changed from deanName to headName
            $filters[] = ['key' => 'filterHeadId', 'label' => 'Head', 'value' => $headName]; // Changed from filterDeanId to filterHeadId, Dean to Head
        }
        if ($this->filterEstablishedYearStart) $filters[] = ['key' => 'filterEstablishedYearStart', 'label' => 'Est. From', 'value' => $this->filterEstablishedYearStart];
        if ($this->filterEstablishedYearEnd) $filters[] = ['key' => 'filterEstablishedYearEnd', 'label' => 'Est. To', 'value' => $this->filterEstablishedYearEnd];
        if ($this->filterStatus !== '') $filters[] = ['key' => 'filterStatus', 'label' => 'Status', 'value' => ucfirst($this->filterStatus)];
        return $filters;
    }

    public function removeFilter($key) { $this->reset($key === 'filterStatus' ? 'filterStatus' : $key); $this->resetPage(); $this->success('Filter removed.'); }
    public function resetFilters() { $this->reset('filterCampusId', 'filterHeadId', 'filterEstablishedYearStart', 'filterEstablishedYearEnd', 'filterStatus'); $this->resetPage(); $this->success('Filters reset.'); } // Changed filterDeanId to filterHeadId

    private function getFaculties()
    {
        $query = Faculty::query()->with(['campus', 'head']); // Eager load for display and sorting, changed dean to head

        if ($this->showDeletedRecords) $query->withTrashed();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhereHas('campus', fn($sq) => $sq->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('head', function ($sq) { // Changed dean to head
                      $sq->where('first_name', 'like', '%' . $this->search . '%')
                         ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterCampusId) $query->where('campus_id', $this->filterCampusId);
        if ($this->filterHeadId) $query->where('head_id', $this->filterHeadId); // Changed from filterDeanId and dean_id
        if ($this->filterEstablishedYearStart) $query->where('established_year', '>=', $this->filterEstablishedYearStart);
        if ($this->filterEstablishedYearEnd) $query->where('established_year', '<=', $this->filterEstablishedYearEnd);
        if ($this->filterStatus === 'active') $query->where('is_active', true);
        elseif ($this->filterStatus === 'inactive') $query->where('is_active', false);

        // Handle sorting for related fields
        if ($this->sortField === 'campus_id') {
            $query->join('campuses', 'faculties.campus_id', '=', 'campuses.id')
                  ->orderBy('campuses.name', $this->sortDirection)
                  ->select('faculties.*'); // Ensure we select faculty columns
        } elseif ($this->sortField === 'head_id') { // Changed from dean_id
            $query->join('employees', 'faculties.head_id', '=', 'employees.id') // Changed from dean_id
                  ->orderBy('employees.first_name', $this->sortDirection) // or last_name, or a concatenation
                  ->select('faculties.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }


        return $query->paginate($this->perPage);
    }

    public function render(): mixed
    {
        $faculties = $this->getFaculties();
        $currentPageIds = $faculties->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedFaculties));

        return view('livewire.faculties.index', [
            'faculties' => $faculties,
            'headers' => $this->headers,
        ]);
    }
}
?>

<div>
    <x-header class="px-4 pt-4 !mb-2" title-class="text-2xl font-bold text-gray-800 dark:text-white"
        title="Faculty / College Management" icon="o-building-library" icon-classes="bg-teal-600 text-white rounded-full p-1 w-8 h-8"
        :subtitle="'Total Faculties: ' . $faculties->total() . ($showDeletedRecords ? ' (including deleted)' : '')"
        subtitle-class="mr-2 mt-0.5">

        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search Faculties..." wire:model.live.debounce.300ms="search" icon="o-magnifying-glass"
                clearable class="input-sm w-60" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-plus" wire:click="openModal(null)"
                class="btn btn-primary btn-sm" spinner
                tooltip-left="Add new Faculty/College" label="Add Faculty" />
            <x-button icon="o-funnel" wire:click="$toggle('showFilterDrawer')"
                class="btn btn-ghost btn-sm" spinner tooltip-left="Filters" />
        </x-slot:actions>
    </x-header>

    @if (count($this->activeFilters))
        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Active Filters:</span>
            @foreach ($this->activeFilters as $filter)
                <x-badge class="badge-primary badge-outline badge-sm font-semibold gap-1"
                    value="{{ $filter['label'] }}: {{ $filter['value'] }}" />
            @endforeach
            <x-button label="Clear All" wire:click="resetFilters" class="btn-ghost btn-xs text-red-500" spinner />
        </div>
    @endif

    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-t border-b dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center">
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-3 sm:mb-0">
            <x-toggle wire:model.live="showDeletedRecords" label="Show deleted" class="toggle-error toggle-sm"
                hint="{{ $showDeletedRecords ? 'Showing deleted' : 'Hiding deleted' }}" />
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <x-select wire:model.live="perPage" :options="[['id' => 5, 'name' => 5], ['id' => 10, 'name' => 10], ['id' => 25, 'name' => 25], ['id' => 50, 'name' => 50], ['id' => 100, 'name' => 100]]"
                    class="select select-bordered select-sm py-0 pl-2 pr-8" />
            </div>
        </div>
        @if (count($selectedFaculties))
            <div class="flex items-center space-x-2">
                <x-select placeholder="Bulk actions" icon="o-bolt" :options="[
                    ['id' => 'confirmBulkDelete', 'name' => 'Delete Selected'],
                    ['id' => 'bulkToggleActive', 'name' => 'Toggle Active Status'],
                    ...($showDeletedRecords ? [['id' => 'confirmBulkRestore', 'name' => 'Restore Selected'], ['id' => 'confirmBulkPermanentDelete', 'name' => 'Permanently Delete']] : [])
                ]" class="select select-bordered select-sm py-0" wire:change="handleBulkAction($event.target.value)" />
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($selectedFaculties) }} selected</span>
            </div>
        @endif
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-b-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="p-4 w-8"><x-checkbox wire:model.live="selectAll" class="checkbox-sm checkbox-primary" /></th>
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer {{ $header['class'] ?? '' }}"
                            wire:click="sortBy('{{ $header['key'] }}')">
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if ($sortField === $header['key'] || ($header['key'] === 'campus.name' && $sortField === 'campus_id') || ($header['key'] === 'head.first_name' && $sortField === 'head_id')) 
                                    <x-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}" class="w-3 h-3" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($faculties as $faculty)
                    <tr wire:key="faculty-{{ $faculty->id }}" class="{{ $faculty->deleted_at ? 'bg-red-50 dark:bg-red-900/10' : '' }} {{ !$faculty->is_active ? 'bg-gray-50 dark:bg-gray-800/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/70">
                        <td class="p-4 w-8"><x-checkbox wire:model.live="selectedFaculties" value="{{ (string)$faculty->id }}" class="checkbox-sm checkbox-primary" /></td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $faculty->name }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell"><x-badge value="{{ $faculty->code }}" class="badge-accent badge-outline" /></td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($faculty->campus)
                                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                    <x-icon name="o-academic-cap" class="h-4 w-4 mr-1 text-gray-400" /> {{ $faculty->campus->name }}
                                </div>
                            @else <span class="text-gray-400 dark:text-gray-600">N/A</span> @endif
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($faculty->head) 
                                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                    <x-icon name="o-user-circle" class="h-4 w-4 mr-1 text-gray-400" /> {{ $faculty->head->first_name }} {{ $faculty->head->last_name }}
                                </div>
                            @else <span class="text-gray-400 dark:text-gray-600">-</span> @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                            @if($faculty->established_year)
                                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                    <x-icon name="o-calendar-days" class="h-4 w-4 mr-1 text-gray-400" /> {{ $faculty->established_year }}
                                </div>
                            @else <span class="text-sm text-gray-400 dark:text-gray-600">Unknown</span> @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge value="{{ $faculty->is_active ? 'Active' : 'Inactive' }}" class="{{ $faculty->is_active ? 'badge-success' : 'badge-ghost' }} badge-sm" />
                            @if($faculty->deleted_at) <x-badge value="Deleted" class="badge-error badge-sm ml-1" /> @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-1">
                                <x-button icon="o-eye" wire:click="openViewModal({{ $faculty->id }})" class="btn btn-ghost btn-xs text-indigo-600" spinner tooltip-left="View" />
                                @if(!$faculty->deleted_at)
                                    <x-button icon="o-pencil" wire:click="openModal({{ $faculty->id }})" class="btn btn-ghost btn-xs text-blue-600" spinner tooltip-left="Edit" />
                                    <x-button icon="{{ $faculty->is_active ? 'o-x-circle' : 'o-check-circle' }}" wire:click="toggleActive({{ $faculty->id }})"
                                        class="btn btn-ghost btn-xs {{ $faculty->is_active ? 'text-yellow-600' : 'text-green-600' }}" spinner tooltip-left="{{ $faculty->is_active ? 'Deactivate' : 'Activate' }}" />
                                    <x-button icon="o-trash" wire:click="confirmDelete({{ $faculty->id }})" class="btn btn-ghost btn-xs text-red-600" spinner tooltip-left="Delete" />
                                @else
                                    <x-button icon="o-arrow-path" wire:click="confirmRestore({{ $faculty->id }})" class="btn btn-ghost btn-xs text-green-600" spinner tooltip-left="Restore" />
                                    <x-button icon="o-no-symbol" wire:click="confirmPermanentDelete({{ $faculty->id }})" class="btn btn-ghost btn-xs text-red-600" spinner tooltip-left="Delete Permanently" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 2 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-icon name="o-inbox" class="h-12 w-12 text-gray-400 mb-3" />
                                <span class="text-lg font-medium">No Faculties/Colleges found</span>
                                <p class="text-sm mt-1">{{ $search ? 'Try adjusting search.' : 'Add a new Faculty/College.' }}</p>
                                @if($search) <x-button wire:click="$set('search', '')" class="mt-3 btn btn-secondary btn-sm" label="Clear Search" />
                                @else <x-button wire:click="openModal(null)" class="mt-3 btn btn-primary btn-sm" spinner label="Add First Faculty" /> @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
        {{ $faculties->links() }}
    </div>

    <x-modal wire:model="showModal" :title="$facultyId ? 'Edit Faculty/College' : 'Add New Faculty/College'" box-class="max-w-3xl" separator>
        <x-form wire:submit.prevent="save">
            <x-tabs wire:model="selectedTab">
                <x-tab name="basic" label="Basic Info" icon="o-information-circle">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        <x-input wire:model="name" label="Name" placeholder="Faculty/College Name" icon="o-building-library" inline required />
                        <x-input wire:model="code" label="Code" placeholder="Unique Code (e.g. FOE, FAS)" icon="o-hashtag" inline required />
                        <x-select wire:model="campus_id" label="Campus" :options="$campuses" placeholder="Select Campus" icon="o-academic-cap" inline />
                        <x-input wire:model="established_year" label="Established Year" type="number" placeholder="YYYY" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" inline />
                        <div class="md:col-span-2">
                            <x-textarea wire:model="description" label="Description" placeholder="Faculty/College Description" rows="3" icon="o-document-text" inline />
                        </div>
                    </div>
                </x-tab>
                <x-tab name="head_details" label="Head & Status" icon="o-user-circle"> {{-- Changed dean_details to head_details, Dean to Head --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                        @php
                            $headOptions = $employees->map(fn($emp) => ['id' => $emp->id, 'name' => $emp->first_name . ' ' . $emp->last_name]); // Changed deanOptions to headOptions
                        @endphp
                        <x-select wire:model="head_id" label="Head" :options="$headOptions" placeholder="Select Head" icon="o-user-circle" inline /> {{-- Changed dean_id to head_id, Dean to Head, deanOptions to headOptions --}}
                        <div class="flex items-center pt-6"> {{-- Adjusted for alignment --}}
                             <x-toggle wire:model.live="is_active" label="Active Faculty" class="self-center" hint="{{ $is_active ? '🌟 Active' : '⏸️ Inactive' }}" checked />
                        </div>
                    </div>
                </x-tab>
            </x-tabs>
        </x-form>
        <x-slot:actions>
            <x-button wire:click="closeModal" label="Cancel" class="btn-ghost" />
            <x-button wire:click="save" type="submit" label="{{ $facultyId ? 'Update' : 'Create' }}" class="btn-primary" wire:loading.attr="disabled" wire:target="save" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="showViewModal" title="View Faculty/College Details" separator box-class="max-w-3xl">
        @if ($viewFaculty)
            <div class="p-6 space-y-6">
                <div class="flex justify-between items-start pb-4 border-b dark:border-gray-700">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $viewFaculty->name }}</h3>
                        <div class="flex flex-wrap gap-2 items-center">
                            <x-badge value="{{ $viewFaculty->code }}" class="badge-accent badge-outline" icon="o-tag" />
                            <x-badge value="{{ $viewFaculty->is_active ? 'Active' : 'Inactive' }}" class="{{ $viewFaculty->is_active ? 'badge-success' : 'badge-ghost' }}" icon="{{ $viewFaculty->is_active ? 'o-check-circle' : 'o-x-circle' }}" />
                            @if ($viewFaculty->deleted_at) <x-badge value="Deleted" class="badge-error" icon="o-archive-box-x-mark" /> @endif
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pt-4">
                    <div class="space-y-4">
                        <div><span class="font-semibold text-gray-500">Campus:</span> {{ $viewFaculty->campus->name ?? 'N/A' }}</div>
                        <div><span class="font-semibold text-gray-500">Head:</span> {{ $viewFaculty->head ? ($viewFaculty->head->first_name . ' ' . $viewFaculty->head->last_name) : '-' }}</div> {{-- Changed Dean to Head, dean to head, N/A to - --}}
                        <div><span class="font-semibold text-gray-500">Established:</span> {{ $viewFaculty->established_year ?? 'N/A' }}</div>
                    </div>
                    <div class="space-y-4">
                        <div class="font-semibold text-gray-500 mb-1">Description:</div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg shadow-inner max-h-40 overflow-y-auto">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $viewFaculty->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t text-xs text-gray-500 dark:text-gray-400">
                    Created: {{ $viewFaculty->created_at->format('M d, Y h:i A') }} | Updated: {{ $viewFaculty->updated_at->format('M d, Y h:i A') }}
                </div>
            </div>
        @else <div class="p-8 text-center"><x-loading class="loading-lg" /></div> @endif
        <x-slot:actions>
            @if ($viewFaculty && !$viewFaculty->deleted_at)
                <x-button label="Edit" icon="o-pencil" wire:click="openModal({{ $viewFaculty->id }})" class="btn-primary" spinner />
            @endif
            <x-button label="Close" wire:click="closeModal" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>

    {{-- Confirmation Modals (Delete, Restore, Permanent Delete) --}}
    <x-modal wire:model="confirmingDeletion" title="Delete Faculty/College" separator>
        <p class="p-4 text-gray-700 dark:text-gray-300">Are you sure you want to delete this Faculty/College? It can be restored later.</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete" wire:click="delete" class="btn-error" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkDeletion" title="Bulk Delete Faculties/Colleges" separator>
        <p class="p-4 text-gray-700 dark:text-gray-300">Delete {{ count($selectedFaculties) }} selected Faculties/Colleges?</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete Selected" wire:click="bulkDelete" class="btn-error" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingRestore" title="Restore Faculty/College" separator>
        <p class="p-4 text-gray-700 dark:text-gray-300">Restore this Faculty/College?</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Restore" wire:click="restore" class="btn-success" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkRestore" title="Bulk Restore Faculties/Colleges" separator>
        <p class="p-4 text-gray-700 dark:text-gray-300">Restore {{ count($selectedFaculties) }} selected Faculties/Colleges?</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Restore Selected" wire:click="bulkRestore" class="btn-success" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingPermanentDeletion" title="Permanently Delete Faculty/College" separator>
        <p class="p-4 text-red-600 dark:text-red-400">Permanently delete this Faculty/College? This cannot be undone.</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete Permanently" wire:click="permanentDelete" class="btn-error" /></x-slot:actions>
    </x-modal>
    <x-modal wire:model="confirmingBulkPermanentDeletion" title="Bulk Permanently Delete Faculties/Colleges" separator>
        <p class="p-4 text-red-600 dark:text-red-400">Permanently delete {{ count($selectedFaculties) }} selected Faculties/Colleges? This cannot be undone.</p>
        <x-slot:actions><x-button label="Cancel" wire:click="closeModal" class="btn-ghost" /><x-button label="Delete All Permanently" wire:click="bulkPermanentDelete" class="btn-error" /></x-slot:actions>
    </x-modal>

    <x-drawer wire:model="showFilterDrawer" title="Advanced Filters" right separator with-close-button class="w-11/12 lg:w-1/3">
        <x-form wire:submit.prevent="$toggle('showFilterDrawer')">
            <div class="p-4 space-y-6">
                <x-select wire:model.live="filterCampusId" label="Filter by Campus" :options="$campuses" placeholder="All Campuses" icon="o-academic-cap" clearable />
                @php
                    $headFilterOptions = $employees->map(fn($emp) => ['id' => $emp->id, 'name' => $emp->first_name . ' ' . $emp->last_name]); // Changed deanFilterOptions to headFilterOptions
                @endphp
                <x-select wire:model.live="filterHeadId" label="Filter by Head" :options="$headFilterOptions" placeholder="Any Head" icon="o-user-circle" clearable /> {{-- Changed filterDeanId to filterHeadId, Dean to Head, deanFilterOptions to headFilterOptions --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Established Year</label>
                    <div class="grid grid-cols-2 gap-4">
                        <x-input wire:model.live.debounce="filterEstablishedYearStart" type="number" placeholder="From Year" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" />
                        <x-input wire:model.live.debounce="filterEstablishedYearEnd" type="number" placeholder="To Year" min="1800" max="{{ date('Y') }}" icon="o-calendar-days" />
                    </div>
                </div>
                <x-select wire:model.live="filterStatus" label="Filter by Status" :options="[['id'=>'', 'name'=>'All'], ['id'=>'active', 'name'=>'Active'], ['id'=>'inactive', 'name'=>'Inactive']]" placeholder="Any Status" icon="o-adjustments-horizontal" clearable />
            </div>
        </x-form>
        <x-slot:actions>
            <x-button label="Reset Filters" icon="o-x-mark" wire:click="resetFilters" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" wire:click="$toggle('showFilterDrawer')" spinner />
        </x-slot:actions>
    </x-drawer>
</div>
