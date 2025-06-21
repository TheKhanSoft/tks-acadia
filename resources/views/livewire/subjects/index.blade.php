<?php

use App\Models\Subject;
use App\Models\SubjectType;
use App\Models\Office;
use App\Services\SubjectService;
use App\Http\Requests\SubjectRequest;
use Illuminate\Pagination\LengthAwarePaginator;
// use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Validator;

new #[Layout('layouts.app')] class extends Component {
    use Toast, WithPagination;

    public $filterParentDepartmentIds = [];
    public $filterDepartmentNames = [];

    // Subject Model Properties
    public $subjectId = null;
    public $name;
    public $code;
    public $description;
    public $credit_hours = '';
    public $subject_type_id;
    public $parent_department_id;
    public $is_active = true;

    // Options for selects
    public $subjectTypes = [];
    public $departments = [];

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
    public $selectedSubjects = [];
    public $selectAll = false;

    // Modals
    public $showModal = false;
    public $showViewModal = false;
    public $confirmDeleteModal = false;
    public $confirmRestoreModal = false;
    public $confirmForceDeleteModal = false;
    public $confirmBulkDeleteModal = false;
    public $confirmBulkRestoreModal = false;
    public $confirmBulkForceDeleteModal = false;
    public $confirmBulkToggleStatusModal = false;
    public $showFilterDrawer = false;
    public $showStatsModal = false;

    // Modal data
    public $viewSubject = null;
    public $deleteSubject = null;
    public $restoreSubject = null;
    public $forceDeleteSubject = null;

    // Filter properties

    public $filterSubjectTypeId = null;
    public $filterIsActive = '';
    public $filterCreditHours = null;
    public array $sortBy = ['column' => 'parentDepartment_name', 'direction' => 'asc'];

    // Stats
    public $stats = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'deleted' => 0,
        'by_department' => [],
        'by_type' => [],
        'avg_credits' => 0,
    ];

    public function headers()
    {
        return [
            ['key' => 'id', 'label' => '', 'class' => 'w-1', 'sortable' => false],
            ['key' => 'name', 'label' => 'Subject Info', 'sortBy' => 'subject_name', 'class' => 'min-w-48'], 
            ['key' => 'parentDepartment.name', 'label' => 'Department', 'class' => 'min-w-32', 'sortBy' => 'parentDepartment_name'],
            ['key' => 'offered_in', 'label' => 'Programs Offered In', 'class' => 'w-32 text-center'],
            ['key' => 'credit_hours', 'label' => 'Credits', 'sortable' => false, 'class' => 'w-20 text-center'], 
            ['key' => 'is_active', 'label' => 'Status', 'class' => 'w-24 text-center', 'sortBy' => 'subject_is_active'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ];
    }

    public function mount()
    {
        $this->loadOptions();
        $this->calculateStats();
    }

    private function loadOptions()
    {
        $this->subjectTypes = SubjectType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->departments = Office::where('is_active', true)
            ->departments()
            ->with('campus') // Eager load campus
            ->orderBy('short_name')
            ->get(['id', 'name', 'short_name', 'campus_id'])
            ->map(function ($office) {
                $office->display_name = "{$office->name} ({$office->short_name})";
                return $office;
            });
    }

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
        $this->selectedSubjects = [];
        $this->selectAll = false;
        $this->calculateStats();
    }
    public function updatedFilterSubjectTypeId()
    {
        $this->resetPage();
    }
    public function updatedFilterParentDepartmentId()
    {
        $this->resetPage();
    }
    public function updatedFilterIsActive()
    {
        $this->resetPage();
    }
    public function updatedFilterCreditHours()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $allowedSortFields = ['id', 'name', 'code', 'credit_hours', 'is_active', 'created_at', 'updated_at'];

        if (!in_array($field, $allowedSortFields)) {
            $field = 'name';
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
        $items = $this->getSubjects(app(SubjectService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedSubjects = array_unique(array_merge($this->selectedSubjects, $itemIds));
        } else {
            $this->selectedSubjects = array_diff($this->selectedSubjects, $itemIds);
        }
    }

    public function updatedSelectedSubjects($value)
    {
        $items = $this->getSubjects(app(SubjectService::class));
        $itemIds = $items->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($itemIds) && empty(array_diff($itemIds, $this->selectedSubjects));
    }

    // Modal management
    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->subjectId = $id;

        if ($id) {
            $subject = app(SubjectService::class)->getSubject($id, $this->showDeletedRecords);
            if ($subject) {
                $this->name = $subject->name;
                $this->code = $subject->code;
                $this->description = $subject->description;
                $this->credit_hours = $subject->credit_hours;
                $this->subject_type_id = $subject->subject_type_id;
                $this->parent_department_id = $subject->parent_department_id;
                $this->is_active = $subject->is_active;
            } else {
                $this->error('Subject not found.');
                return;
            }
        }

        $this->showModal = true;
    }

    public function openViewModal($id)
    {
        $this->viewSubject = app(SubjectService::class)->getSubject($id, true);
        if (!$this->viewSubject) {
            $this->error('Subject not found.');
            return;
        }
        $this->showViewModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->deleteSubject = app(SubjectService::class)->getSubject($id);
        if (!$this->deleteSubject) {
            $this->error('Subject not found.');
            return;
        }
        $this->confirmDeleteModal = true;
    }

    public function openRestoreModal($id)
    {
        $this->restoreSubject = app(SubjectService::class)->getSubject($id, true);
        if (!$this->restoreSubject || !$this->restoreSubject->trashed()) {
            $this->error('Subject not found in trash.');
            return;
        }
        $this->confirmRestoreModal = true;
    }

    public function openForceDeleteModal($id)
    {
        $this->forceDeleteSubject = app(SubjectService::class)->getSubject($id, true);
        if (!$this->forceDeleteSubject) {
            $this->error('Subject not found.');
            return;
        }
        $this->confirmForceDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showModal = false;
        $this->showViewModal = false;
        $this->confirmDeleteModal = false;
        $this->confirmRestoreModal = false;
        $this->confirmForceDeleteModal = false;
        $this->confirmBulkDeleteModal = false;
        $this->confirmBulkRestoreModal = false;
        $this->confirmBulkForceDeleteModal = false;
        $this->confirmBulkToggleStatusModal = false;
        $this->showStatsModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->subjectId = null;
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->credit_hours = '';
        $this->subject_type_id = null;
        $this->parent_department_id = null;
        $this->is_active = true;
        $this->viewSubject = null;
        $this->deleteSubject = null;
        $this->restoreSubject = null;
        $this->forceDeleteSubject = null;
    }

    // CRUD Operations
    public function save()
    {
        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'credit_hours' => $this->credit_hours,
            'subject_type_id' => $this->subject_type_id,
            'parent_department_id' => $this->parent_department_id,
            'is_active' => $this->is_active,
        ];

        try {
            app(SubjectService::class)->saveSubject($data, $this->subjectId);

            // icon: 'o-information-circle',       // Optional (any icon)
            // css: 'alert-info',                  // Optional (daisyUI classes)
            // timeout: 3000, 

            $title = $this->subjectId ? 'Updated!' : 'New Subject!';
            $message = $this->subjectId ? 'Subject updated successfully! 📚' : 'New Subject added successfully! ✨';
            $this->success(
                $title, 
                $message,
            );

            $this->closeModals();
            $this->calculateStats();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // The service will throw a validation exception, which Livewire will handle automatically.
            // We just need to re-throw it.
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Subject Save Error: ' . $e->getMessage(), ['exception' => $e]);
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'already exists')) {
                $this->error('Failed to save subject. A record with the same name and code already exists for this department.');
            } else {
                $this->error('An error occurred while saving the Subject: ' . $e->getMessage());
            }
        }
    }

    public function deleteConfirmed()
    {
        if (!$this->deleteSubject) {
            return;
        }

        $subjectId = $this->deleteSubject->id;

        try {
            app(SubjectService::class)->deleteSubject($this->deleteSubject);
            $this->warning('Subject deleted successfully! 🗑️');
            $this->closeModals();
            $this->calculateStats();
            $this->removeFromSelected($subjectId);
        } catch (\Exception $e) {
            \Log::error('Subject Delete Error: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Failed to delete subject. Please check the logs for more details.');
        }
    }

    public function restoreConfirmed()
    {
        if (!$this->restoreSubject) {
            return;
        }

        try {
            app(SubjectService::class)->restoreSubject($this->restoreSubject->id);
            $this->success('Subject restored successfully! ♻️');
            $this->closeModals();
            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error('Failed to restore subject: ' . $e->getMessage());
        }
    }

    public function forceDeleteConfirmed()
    {
        if (!$this->forceDeleteSubject) {
            return;
        }

        try {
            app(SubjectService::class)->permanentlyDeleteSubject($this->forceDeleteSubject->id);
            $this->success('Subject permanently deleted! ⚠️');
            $this->closeModals();
            $this->calculateStats();
            $this->removeFromSelected($this->forceDeleteSubject->id);
        } catch (\Exception $e) {
            $this->error('Failed to permanently delete subject: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->is_active = !$subject->is_active;
            $subject->save();

            if ($subject->is_active) {
                $this->success(
                    'Subject Activated', 
                    "The subject <strong>{$subject->name}</strong> has been activated successfully! ✅",
             
                );
            } else {
                $this->warning('Subject Deactivated', "The subject <strong>{$subject->name}</strong> has been deactivated. ❌");
            }

            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error('Error!', 'Failed to toggle subject status: ' . $e->getMessage());
        }
    }

    // Bulk Operations
    public function openBulkDeleteModal()
    {
        if (empty($this->selectedSubjects)) {
            $this->warning('No Subjects Selected', 'Please select at least one subject to delete. 🗑️');
            return;
        }
        $this->confirmBulkDeleteModal = true;
    }

    public function openBulkRestoreModal()
    {
        if (empty($this->selectedSubjects)) {
            $this->warning('No subjects selected for restoration.');
            return;
        }
        $this->confirmBulkRestoreModal = true;
    }

    public function openBulkForceDeleteModal()
    {
        if (empty($this->selectedSubjects)) {
            $this->warning('No subjects selected for permanent deletion.');
            return;
        }
        $this->confirmBulkForceDeleteModal = true;
    }

    public function openBulkToggleStatusModal()
    {
        if (empty($this->selectedSubjects)) {
            $this->warning('No subjects selected to toggle status.');
            return;
        }
        $this->confirmBulkToggleStatusModal = true;
    }

    public function bulkDeleteConfirmed()
    {
        try {
            app(SubjectService::class)->bulkDeleteSubjectByIds($this->selectedSubjects);
            $count = count($this->selectedSubjects);
            $this->warning("Bulk Delete Successful", "{$count} subject(s) deleted successfully! 🗑️");
            $this->selectedSubjects = [];
            $this->selectAll = false;
            $this->closeModals();
            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error("Error!", 'Failed to delete subjects: ' . $e->getMessage());
        }
    }

    public function bulkRestoreConfirmed()
    {
        try {
            app(SubjectService::class)->bulkRestoreSubjects($this->selectedSubjects);
            $count = count($this->selectedSubjects);
            $this->success("Bulk Restore Successful", "{$count} subject(s) restored successfully! ♻️");
            $this->selectedSubjects = [];
            $this->selectAll = false;
            $this->closeModals();
            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error("Error!", 'Failed to restore subjects: ' . $e->getMessage());
        }
    }

    public function bulkForceDeleteConfirmed()
    {
        try {
            app(SubjectService::class)->bulkPermanentDeleteSubjects($this->selectedSubjects);
            $count = count($this->selectedSubjects);
            $this->warning("Bulk Permanent Delete Successful", "{$count} subject(s) permanently deleted! ⚠️");
            $this->selectedSubjects = [];
            $this->selectAll = false;
            $this->closeModals();
            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error("Error!", 'Failed to permanently delete subjects: ' . $e->getMessage());
        }
    }

    public function bulkToggleStatusConfirmed()
    {
        try {
            app(SubjectService::class)->bulkToggleStatusSubjects($this->selectedSubjects);
            $count = count($this->selectedSubjects);
            $this->success("Bulk Toggle Successful", "Status for {$count} subject(s) toggled successfully! 🔄");
            $this->selectedSubjects = [];
            $this->selectAll = false;
            $this->closeModals();
            $this->calculateStats();
        } catch (\Exception $e) {
            $this->error("Error!", 'Failed to toggle subject statuses: ' . $e->getMessage());
        }
    }

    // Filter and utility methods
    public function clearFilters()
    {
        $this->reset(['search', 'filterSubjectTypeId', 'filterParentDepartmentIds', 'filterIsActive', 'filterCreditHours']);
        $this->resetPage();
    }

    public function export($format)
    {
        $filters = [
            'search_term' => $this->search,
            'search' => !empty($this->search),
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'subject_type_id' => $this->filterSubjectTypeId,
            'parent_department_id' => $this->filterParentDepartmentIds,
            'is_active' => $this->filterIsActive !== '' ? filter_var($this->filterIsActive, FILTER_VALIDATE_BOOLEAN) : null,
            'credit_hours' => $this->filterCreditHours,
            'with_trashed' => $this->showDeletedRecords,
        ];

        try {
            return app(SubjectService::class)->exportSubjects($format, $filters, [], 'Subjects Report');
        } catch (\Exception $e) {
            $this->error("Export Error!", 'Failed to export subjects: ' . $e->getMessage());
        }
    }

    private function removeFromSelected($id)
    {
        $this->selectedSubjects = array_filter($this->selectedSubjects, fn($itemId) => $itemId != $id);
        if (empty($this->selectedSubjects)) {
            $this->selectAll = false;
        }
    }

    private function calculateStats()
    {
        $subjectService = app(SubjectService::class);

        // Get all subjects including trashed
        $allSubjects = Subject::withTrashed()
            ->with(['parentDepartment', 'subjectType'])
            ->get();

        $activeSubjects = $allSubjects->whereNull('deleted_at');

        $totalCreditSum = $activeSubjects->reduce(function ($carry, $subject) {
            $credit_hours = $subject->credit_hours;
            if (is_string($credit_hours) && str_contains($credit_hours, '+')) {
                $parts = explode('+', $credit_hours);
                return $carry + array_sum(array_map('intval', $parts));
            }
            if (is_numeric($credit_hours)) {
                return $carry + (int) $credit_hours;
            }
            return $carry;
        }, 0);

        $avgCredits = $activeSubjects->count() > 0 ? $totalCreditSum / $activeSubjects->count() : 0;

        $this->stats = [
            'total' => $activeSubjects->count(),
            'active' => $activeSubjects->where('is_active', true)->count(),
            'inactive' => $activeSubjects->where('is_active', false)->count(),
            'deleted' => $allSubjects->whereNotNull('deleted_at')->count(),
            'by_department' => $activeSubjects
                ->groupBy(function ($s) {
                    return $s->parentDepartment ? "{$s->parentDepartment->name} ({$s->parentDepartment->short_name})" : 'N/A';
                })
                ->map->count()
                ->toArray(),
            'by_type' => $activeSubjects->groupBy(fn($s) => $s->subjectType?->name ?? 'N/A')->map->count()->toArray(),
            'avg_credits' => round($avgCredits, 2),
        ];
    }

    // Data fetching
    private function getSubjects(SubjectService $subjectService): LengthAwarePaginator
    {
        $filterParams = [
            'with_trashed' => $this->showDeletedRecords,
            'search' => !empty($this->search),
            'search_term' => $this->search,
            'sort_by' => $this->sortField,
            'sort_dir' => $this->sortDirection,
            'per_page' => $this->perPage,
            'subject_type_id' => $this->filterSubjectTypeId,
            'parent_department_id' => $this->filterParentDepartmentIds,
            'is_active' => $this->filterIsActive !== '' ? filter_var($this->filterIsActive, FILTER_VALIDATE_BOOLEAN) : null,
            'credit_hours' => $this->filterCreditHours,
        ];

        $filterParams = array_filter(
            $filterParams,
            function ($value, $key) {
                return ($value !== null && $value !== '') || in_array($key, ['search', 'with_trashed', 'sort_by', 'sort_dir', 'per_page']);
            },
            ARRAY_FILTER_USE_BOTH,
        );

        return $subjectService->getPaginatedSubjects($filterParams);
    }

    public function render(): mixed
    {
        $subjects = $this->getSubjects(app(SubjectService::class));

        $currentPageIds = $subjects->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($currentPageIds) && empty(array_diff($currentPageIds, $this->selectedSubjects));

        $hasActiveSelected = false;
        $hasTrashedSelected = false;

        if (!empty($this->selectedSubjects)) {
            $selectedSubjectsCollection = Subject::whereIn('id', $this->selectedSubjects)->withTrashed()->get();
            $hasActiveSelected = $selectedSubjectsCollection->whereNull('deleted_at')->isNotEmpty();
            $hasTrashedSelected = $selectedSubjectsCollection->whereNotNull('deleted_at')->isNotEmpty();
        }

        return view('livewire.subjects.index', [
            'subjects' => $subjects,
            'headers' => $this->headers(),
            'hasActiveSelected' => $hasActiveSelected,
            'hasTrashedSelected' => $hasTrashedSelected,
        ]);
    }
};

?>

<div>
    {{-- Header with Search and Actions --}}
    <x-header title="Subjects Management" subtitle="Manage academic subjects and their details" separator
        progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search subjects..." wire:model.live.debounce.300ms="search" clearable
                icon="o-magnifying-glass" class="w-64" />
        </x-slot:middle>
        <x-slot:actions>
            {{-- Stats Button --}}
            <x-button wire:click="$set('showStatsModal', true)" icon="o-chart-bar" class="btn-ghost" />

            {{-- Export Dropdown --}}
            <x-dropdown icon="o-document-arrow-down" class="btn-ghost">
                <x-menu-item title="Export PDF" wire:click="export('pdf')" icon="o-document-text" />
                <x-menu-item title="Export Excel" wire:click="export('xlsx')" icon="o-table-cells" />
                <x-menu-item title="Export CSV" wire:click="export('csv')" icon="o-archive-box" />
            </x-dropdown>

            {{-- Bulk Actions --}}
            @if (!empty($selectedSubjects))
                <x-dropdown label="Bulk Actions ({{ count($selectedSubjects) }})" icon="o-ellipsis-vertical"
                    class="btn-ghost">
                    @if ($hasActiveSelected)
                        <x-menu-item title="Toggle Status" wire:click="openBulkToggleStatusModal" icon="o-arrow-path" />
                        <x-menu-item title="Delete Selected" wire:click="openBulkDeleteModal" icon="o-trash" />
                    @endif
                    @if ($hasTrashedSelected)
                        @if ($hasActiveSelected)
                            <x-menu-separator />
                        @endif
                        <x-menu-item title="Restore Selected" wire:click="openBulkRestoreModal"
                            icon="o-arrow-uturn-left" />
                        <x-menu-item title="Force Delete Selected" wire:click="openBulkForceDeleteModal" icon="o-fire"
                            class="text-error" />
                    @endif
                </x-dropdown>
            @endif

            {{-- Filter Toggle --}}
            <x-button wire:click="$toggle('showFilterDrawer')" icon="o-funnel"
                class="btn-ghost {{ $showFilterDrawer ? 'btn-active' : '' }}" />

            {{-- Create Button --}}
            <x-button label="Add Subject" wire:click="openModal" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-stat title="Total Subjects" value="{{ $stats['total'] }}" icon="o-academic-cap" />
        <x-stat title="Active" value="{{ $stats['active'] }}" icon="o-check-circle" class="text-success" />
        <x-stat title="Inactive" value="{{ $stats['inactive'] }}" icon="o-x-circle" class="text-warning" />
        <x-stat title="Deleted" description="Something" value="{{ $stats['deleted'] }}" icon="o-trash" class="text-error" />
    </div>

    <x-card>
        {{-- Filters Drawer --}}
        <x-drawer wire:model="showFilterDrawer" title="Filters" right separator with-close-button
            class="w-11/12 lg:w-1/3">

            <div class="space-y-4 p-2 gap-3">
                <x-choices label="Department(s)" placeholder="All Departments"
                    hint="Filter subject with departments"
                    wire:model.live="filterParentDepartmentIds"  option-value="id" :options="$departments" 
                    clearable searchable inline>
                    {{-- Item slot --}}
                    @scope('item', $department)
                        <x-list-item :item="$department" sub-value="campus.name" />
                    @endscope
                 
                    {{-- Selection slot--}}
                    @scope('selection', $department)
                        {{ $department->name }} ({{  $department->campus->name ?? 'N/A' }})
                    @endscope
                </x-choices>

            
                <x-select label="Subject Type" :options="$subjectTypes" option-value="id" option-label="name"
                    hint="Filter subject with subject type"
                    wire:model.live="filterSubjectTypeId" placeholder="All Types" inline 
                />

                <x-select label="Status" option-value="id" option-label="name"
                    hint="Filter subject with active/inactive status"
                    :options="[['id' => '1', 'name' => 'Active'], ['id' => '0', 'name' => 'Inactive']]" 
                    wire:model.live="filterIsActive" placeholder="All Statuses" inline />

                <x-input label="Credit Hours" wire:model.live="filterCreditHours" type="text"
                    hint="Filter subject with credit hours"
                    placeholder="Filter by credits" inline />
            </div>

            <x-slot:actions>
                <x-button label="Clear Filters" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost" />
            </x-slot:actions>

        </x-drawer>

        {{-- Toolbar --}}
        <div class="md:flex justify-between mb-4">
            <div class="flex items-center gap-4">
                <div class="w-32">
                    <x-select wire:model.live="perPage" :options="[
                        ['id' => 10, 'name' => '10 per page'],
                        ['id' => 15, 'name' => '15 per page'],
                        ['id' => 25, 'name' => '25 per page'],
                        ['id' => 50, 'name' => '50 per page'],
                        ['id' => 100, 'name' => '100 per page'],
                        ['id' => 'all', 'name' => 'Show All'],
                    ]" label="Per Page" inline />
                </div>
            </div>
            <div>
                <x-toggle label="Show Deleted" wire:model.live="showDeletedRecords" right tight />
            </div>
        </div>

        {{-- Active Filters --}}
        @php
            $hasFilters = $filterSubjectTypeId || $filterIsActive !== '' || $filterCreditHours || $filterParentDepartmentIds;
        @endphp

        @if ($hasFilters)
            <div class="mb-4 p-4 bg-base-200 rounded-lg flex items-center gap-4">
                <div class="flex-grow flex flex-wrap items-center gap-2">
                    <span class="font-semibold">Active Filters:</span>   
                    @if ($filterParentDepartmentIds)
                        @php
                            $filterDepartments = $departments->whereIn('id', $filterParentDepartmentIds);
                            foreach ($filterDepartments as $filterDepartment) {
                                
                                $departmentName = $filterDepartment?->short_name ?? 'N/A';
                                $campusName = $filterDepartment?->campus?->short_name ?? '';

                                $campusDisplay = $campusName ? " ($campusName)" : '';

                                $filterDepartmentNames[] = $departmentName . $campusDisplay;
                            }
                            $departmentCount = $filterDepartments?->count() ?? 0;
                        @endphp
                        @foreach ($filterDepartmentNames as $departmentName)
                            <x-badge :value="$departmentName" class="badge-primary badge-soft badge-xs" />
                        @endforeach
                    @endif
                    @if ($filterSubjectTypeId)
                        @php
                            $typeName = $subjectTypes->firstWhere('id', $filterSubjectTypeId)?->name ?? 'N/A';
                        @endphp
                        <x-badge :value="'Type: ' . $typeName" class="badge-secondary badge-soft badge-xs" />
                    @endif
                    @if ($filterIsActive !== '')
                        <x-badge :value="$filterIsActive == 1 ? 'Status: Active' : 'Status: Inactive'" class="badge-accent badge-soft badge-xs" />
                    @endif
                    @if ($filterCreditHours)
                        <x-badge :value="'Credits: ' . $filterCreditHours" class="badge-info badge-soft badge-xs" />
                    @endif
                </div>
                <x-button label="Clear" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost btn-sm" />
            </div>
        @endif

        {{-- Table --}}
        <x-table 
            :headers="$headers" 
            :rows="$subjects" 
            striped :sort-by="$sortBy"
            with-pagination
            >
            @scope('header_id', $header)
                <x-checkbox wire:model.live="selectAll" />
            @endscope

            @scope('header_name', $header)
                {{ $header['label'] }} <x-icon name="s-question-mark-circle" />
            @endscope

            @scope('cell_id', $subject)
                <x-checkbox :value="(string) $subject->id" wire:model.live="selectedSubjects" />
            @endscope

            @scope('cell_name', $subject)
                <div class="flex flex-col">
                    <span class="font-semibold text-base-content">{{ $subject->name }}</span>
                    <div class="flex items-center gap-3 mt-1">
                        <div class="flex items-center gap-1">
                            <x-icon name="o-hashtag" class="w-3 h-3 text-primary" />
                            <span class="text-xs text-base-content/60 font-mono">{{ $subject->code }}</span>
                        </div>
                        @if ($subject->subjectType)
                            |
                            <div class="flex items-center gap-1">
                                <x-icon name="o-tag" class="w-3 h-3 text-secondary" />
                                <x-badge value="{{ $subject->subjectType->name }}" class="badge-secondary badge-soft badge-xs" />
                            </div>
                        @endif
                    </div>
                    @if ($subject->description)
                        <span class="text-xs text-base-content/50 mt-1">{{ Str::limit($subject->description, 50) }}</span>
                    @endif
                </div>
            @endscope

            @scope('cell_parentDepartment.name', $subject)
                @if ($subject->parentDepartment)
                    <div class="flex flex-col">
                        <span class="font-semibold">{{ $subject->parentDepartment->name }}</span>
                        <span class="text-xs text-base-content/60">{{ $subject->parentDepartment->campus?->name }}</span>
                    </div>
                @else
                    <span class="text-base-content/50">No Department</span>
                @endif
            @endscope

            @scope('cell_offered_in', $subject)
                <div class="flex flex-col items-center justify-center text-xs">
                    <div>
                        <x-icon name="o-building-office-2" class="w-3 h-3 text-primary" />
                        <span class="text-base-content/80">Parent Dept</span>
                        <span class="font-bold">: {{ $subject->programs_in_same_department_count }}</span>
                    </div>
                    <div>
                        <x-icon name="o-building-library" class="w-3 h-3 text-secondary" />
                        <span class="text-base-content/80">Other Depts</span>
                        <span class="font-bold">: {{ $subject->programs_in_other_departments_count }}</span>
                    </div>
                </div>
            @endscope

            @scope('cell_credit_hours', $subject)
                <div class="text-center">
                    <x-badge value="{{ $subject->credit_hours }}" class="badge-primary badge-soft badge-sm" />
                </div>
            @endscope

            @scope('cell_is_active', $subject)
                <div class="text-center">
                    <x-badge :value="$subject->is_active ? 'Active' : 'Inactive'" :class="$subject->is_active ? 'badge-success badge-soft badge-sm' : 'badge-warning badge-soft badge-sm'" />
                </div>
            @endscope

            @scope('cell_actions', $subject)
                <div class="flex items-center gap-1">
                    <x-button wire:click="openViewModal({{ $subject->id }})" icon="o-eye" class="btn-xs btn-ghost"
                        tooltip="View Details" />

                    @if (!$subject->trashed())
                        <x-button wire:click="toggleStatus({{ $subject->id }})" :icon="$subject->is_active ? 'o-x-circle' : 'o-check-circle'"
                            class="btn-xs btn-ghost {{ $subject->is_active ? 'text-warning' : 'text-success' }}"
                            :tooltip="$subject->is_active ? 'Deactivate' : 'Activate'" />
                        <x-button wire:click="openModal({{ $subject->id }})" icon="o-pencil-square"
                            class="btn-xs btn-ghost" tooltip="Edit" />
                        <x-button wire:click="openDeleteModal({{ $subject->id }})" icon="o-trash"
                            class="btn-xs btn-ghost text-error" tooltip="Delete" />
                    @else
                        <x-button wire:click="openRestoreModal({{ $subject->id }})" icon="o-arrow-uturn-left"
                            class="btn-xs btn-ghost text-success" tooltip="Restore" />
                        <x-button wire:click="openForceDeleteModal({{ $subject->id }})" icon="o-fire"
                            class="btn-xs btn-ghost text-error" tooltip="Permanent Delete" />
                    @endif
                </div>
            @endscope

            <x-slot:empty>
                <div class="text-center py-12">
                    <x-icon name="o-academic-cap" class="w-16 h-16 mx-auto text-base-content/30" />
                    <h3 class="mt-4 text-lg font-semibold">No subjects found</h3>
                    <p class="text-base-content/60">Get started by creating your first subject.</p>
                    @if (empty($search) && !$filterSubjectTypeId && !$filterParentDepartmentIds)
                        <x-button label="Add New Subject" wire:click="openModal" icon="o-plus"
                            class="btn-primary btn-sm mt-4" />
                    @endif
                </div>
            </x-slot:empty>
        </x-table>

      

    </x-card>

    {{-- Create/Edit Modal --}}
    <x-modal wire:model="showModal" title="{{ $subjectId ? 'Edit Subject' : 'Add New Subject' }}"
        separator class="backdrop-blur" box-class="max-w-3xl" max-width="max-w-3xl">
        <x-form wire:submit="save">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Left Column --}}
                    <div class="space-y-4">
                        <x-input label="Subject Name" wire:model="name" placeholder="e.g., Advanced Mathematics"
                            icon="o-academic-cap" required inline />
                        <x-input label="Subject Code" wire:model="code" placeholder="e.g., MATH-401"
                            icon="o-hashtag" required inline />
                        <x-input label="Credit Hours" wire:model="credit_hours" type="text"
                            placeholder="e.g., 3 or 3+1" icon="o-clock" required inline />
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-4">
                        <x-select label="Department" :options="$departments" option-value="id" option-label="short_name"
                            wire:model="parent_department_id" placeholder="Select Department"
                            icon="o-building-office-2" required searchable inline />
                        <x-select label="Subject Type" :options="$subjectTypes" option-value="id" option-label="name"
                            wire:model="subject_type_id" placeholder="Select Type" icon="o-tag" required inline />
                        <div class="">
                            <x-toggle label="Active Status" wire:model="is_active"  />
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <x-textarea label="Description" wire:model="description"
                        placeholder="Provide an optional description for the subject..." rows="3"
                        icon="o-document-text" inline />
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
                <x-button label="{{ $subjectId ? 'Update Subject' : 'Create Subject' }}" type="submit"
                    class="btn-primary" icon="{{ $subjectId ? 'o-arrow-path' : 'o-plus' }}" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Modal --}}
    <x-modal wire:model="showViewModal" title="Subject Details" separator class="backdrop-blur"
        max-width="max-w-3xl" box-class="max-w-3xl">
        @if ($viewSubject)
            <div class="p-4 space-y-4">
                {{-- Main Info --}}
                <div class="p-4 bg-base-200 rounded-lg">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-primary/10 rounded-lg">
                            <x-icon name="o-academic-cap" class="w-8 h-8 text-primary" />
                        </div>
                        <div>
                            <div class="font-bold text-xl">{{ $viewSubject->name }}</div>
                            <div class="text-sm text-base-content/70">{{ $viewSubject->code }}</div>
                        </div>
                    </div>
                    @if ($viewSubject->description)
                        <div class="mt-4 text-sm text-base-content/80">
                            {{ $viewSubject->description }}
                        </div>
                    @endif
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-base-200 rounded-lg space-y-2">
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <x-icon name="o-building-office-2" class="w-5 h-5" />
                            <span>Department</span>
                        </div>
                        <div class="font-semibold">
                            {{ $viewSubject->parentDepartment?->name ?? 'Not assigned' }}
                            @if ($viewSubject->parentDepartment?->short_name)
                                <span class="text-sm text-base-content/70">({{ $viewSubject->parentDepartment->short_name }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 bg-base-200 rounded-lg space-y-2">
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <x-icon name="o-tag" class="w-5 h-5" />
                            <span>Subject Type</span>
                        </div>
                        <div class="font-semibold">{{ $viewSubject->subjectType?->name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="p-4 bg-base-200 rounded-lg space-y-2">
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <x-icon name="o-clock" class="w-5 h-5" />
                            <span>Credit Hours</span>
                        </div>
                        <div class="font-semibold">{{ $viewSubject->credit_hours }}</div>
                    </div>
                    <div class="p-4 bg-base-200 rounded-lg space-y-2">
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <x-icon name="o-shield-check" class="w-5 h-5" />
                            <span>Status</span>
                        </div>
                        <x-badge :value="$viewSubject->is_active ? 'Active' : 'Inactive'" :class="$viewSubject->is_active ? 'badge-success' : 'badge-error'" />
                    </div>
                </div>

                {{-- Timestamps --}}
                <div class="text-xs text-center text-base-content/50 pt-4">
                    <span>Created: {{ $viewSubject->created_at?->format('M d, Y, h:i A') }}</span> |
                    <span>Updated: {{ $viewSubject->updated_at?->format('M d, Y, h:i A') }}</span>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Close" @click="$wire.closeModals()" class="btn-ghost" />
            @if ($viewSubject && !$viewSubject->trashed())
                <x-button label="Edit" wire:click="openModal({{ $viewSubject->id }})" icon="o-pencil-square"
                    class="btn-primary" />
            @endif
        </x-slot:actions>
    </x-modal>

    {{-- Stats Modal --}}
    <x-modal wire:model="showStatsModal" title="Subject Statistics" separator class="backdrop-blur"
        max-width="max-w-3xl" box-class="max-w-3xl">
        <div class="p-4 space-y-6">
            {{-- Overview Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat title="Total Subjects" value="{{ $stats['total'] }}" icon="o-academic-cap"
                    class="bg-base-200 p-4 rounded-lg" />
                <x-stat title="Active" value="{{ $stats['active'] }}" icon="o-check-circle"
                    class="text-success bg-base-200 p-4 rounded-lg" />
                <x-stat title="Inactive" value="{{ $stats['inactive'] }}" icon="o-x-circle"
                    class="text-warning bg-base-200 p-4 rounded-lg" />
                <x-stat title="Deleted" value="{{ $stats['deleted'] }}" icon="o-trash"
                    class="text-error bg-base-200 p-4 rounded-lg" />
            </div>

            <div class="text-center bg-base-200 p-4 rounded-lg">
                <x-stat title="Average Credit Hours" value="{{ $stats['avg_credits'] }}" icon="o-clock" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- By Department --}}
                @if (!empty($stats['by_department']))
                    <div class="bg-base-200 p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 text-lg flex items-center gap-2">
                            <x-icon name="o-building-office-2" class="w-5 h-5" />
                            <span>By Department</span>
                        </h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach ($stats['by_department'] as $dept => $count)
                                <div class="flex justify-between items-center p-2 rounded-md hover:bg-base-300">
                                    <span class="truncate">{{ $dept }}</span>
                                    <x-badge value="{{ $count }}" class="badge-primary" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- By Type --}}
                @if (!empty($stats['by_type']))
                    <div class="bg-base-200 p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 text-lg flex items-center gap-2">
                            <x-icon name="o-tag" class="w-5 h-5" />
                            <span>By Type</span>
                        </h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach ($stats['by_type'] as $type => $count)
                                <div class="flex justify-between items-center p-2 rounded-md hover:bg-base-300">
                                    <span class="truncate">{{ $type }}</span>
                                    <x-badge value="{{ $count }}" class="badge-secondary" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Close" @click="$wire.closeModals()" class="btn-primary" />
        </x-slot:actions>
    </x-modal>

    {{-- Confirmation Modals --}}
    <x-modal wire:model="confirmDeleteModal" title="Confirm Deletion" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-trash" class="w-16 h-16 text-error mb-4" />
            @if ($deleteSubject)
                <p class="text-lg font-semibold">Are you sure you want to delete
                    <strong>{{ $deleteSubject->name }}</strong>?
                </p>
                <p class="text-sm text-base-content/70 mt-2">This action can be undone by restoring the subject from
                    the
                    trash.</p>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Delete" wire:click="deleteConfirmed" class="btn-error" icon="o-trash" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmRestoreModal" title="Confirm Restoration" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-arrow-uturn-left" class="w-16 h-16 text-success mb-4" />
            @if ($restoreSubject)
                <p class="text-lg font-semibold">Are you sure you want to restore
                    <strong>{{ $restoreSubject->name }}</strong>?
                </p>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Restore" wire:click="restoreConfirmed" class="btn-success" icon="o-arrow-uturn-left" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmForceDeleteModal" title="Confirm Permanent Deletion" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-fire" class="w-16 h-16 text-error mb-4" />
            <div class="text-error font-bold text-lg mb-2">This action cannot be undone!</div>
            @if ($forceDeleteSubject)
                <p>Are you sure you want to permanently delete <strong>{{ $forceDeleteSubject->name }}</strong>?</p>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="forceDeleteConfirmed" class="btn-error"
                icon="o-fire" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmBulkDeleteModal" title="Confirm Bulk Deletion" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-trash" class="w-16 h-16 text-error mb-4" />
            <p class="text-lg font-semibold">Are you sure you want to delete
                <strong>{{ count($selectedSubjects) }}</strong> selected subjects?
            </p>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Delete All" wire:click="bulkDeleteConfirmed" class="btn-error" icon="o-trash" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmBulkRestoreModal" title="Confirm Bulk Restoration" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-arrow-uturn-left" class="w-16 h-16 text-success mb-4" />
            <p class="text-lg font-semibold">Are you sure you want to restore
                <strong>{{ count($selectedSubjects) }}</strong> selected subjects?
            </p>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Restore All" wire:click="bulkRestoreConfirmed" class="btn-success"
                icon="o-arrow-uturn-left" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmBulkForceDeleteModal" title="Confirm Bulk Permanent Deletion" separator
        class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-fire" class="w-16 h-16 text-error mb-4" />
            <div class="text-error font-bold text-lg mb-2">This action cannot be undone!</div>
            <p class="text-lg font-semibold">Are you sure you want to permanently delete
                <strong>{{ count($selectedSubjects) }}</strong> selected subjects?
            </p>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Permanently Delete All" wire:click="bulkForceDeleteConfirmed" class="btn-error"
                icon="o-fire" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="confirmBulkToggleStatusModal" title="Confirm Status Toggle" separator class="backdrop-blur">
        <div class="flex flex-col items-center text-center p-4">
            <x-icon name="o-arrow-path" class="w-16 h-16 text-info mb-4" />
            <p class="text-lg font-semibold">Are you sure you want to toggle the status for
                <strong>{{ count($selectedSubjects) }}</strong> selected subjects?
            </p>
            <p class="text-sm text-base-content/70 mt-2">This will activate any inactive subjects and deactivate any
                active ones in the selection.</p>
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModals()" class="btn-ghost" />
            <x-button label="Toggle Status" wire:click="bulkToggleStatusConfirmed" class="btn-info"
                icon="o-arrow-path" />
        </x-slot:actions>
    </x-modal>
</div>
