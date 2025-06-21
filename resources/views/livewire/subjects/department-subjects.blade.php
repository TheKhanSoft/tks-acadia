<?php

use App\Models\DepartmentSubject;
use App\Models\Department; // For dropdown
use App\Models\Subject;    // For dropdown
use App\Services\DepartmentSubjectService;
use App\Services\ExportImportService;
// Assuming a DepartmentSubjectRequest will be created for validation
// use App\Http\Requests\DepartmentSubjectRequest;
use App\Enums\IsActiveFilter;
use App\Livewire\Actions\ExportAction;
use App\Livewire\Actions\ModalErrorAction;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

new #[Layout('layouts.app')] class extends Component {
    use Toast, WithPagination;

    protected DepartmentSubjectService $departmentSubjectService;
    protected ExportImportService $exportImportService;

    // Properties for search, sort, pagination, bulk actions, filters
    public string $search = ''; // Search might be by department name or subject name
    public array $sortBy = ['column' => 'department_id', 'direction' => 'asc']; // Default sort
    public int $perPage = 10;
    public array $selectedItems = [];
    public bool $selectAll = false;
    public string $isActiveFilter = IsActiveFilter::ALL->value;
    public ?int $departmentFilter = null;
    public ?int $subjectFilter = null;
    public bool $showTimestamps = false;
    public bool $showSoftDeleted = false;

    // Modal states
    public bool $createModal = false;
    public bool $editModal = false;
    public bool $viewModal = false;
    public bool $deleteModal = false;
    public bool $bulkDeleteModal = false;
    public bool $restoreModal = false;
    public bool $bulkRestoreModal = false;
    public bool $forceDeleteModal = false;
    public bool $bulkForceDeleteModal = false;

    #[Locked]
    public ?DepartmentSubject $editingDepartmentSubject = null;

    #[Locked]
    public ?DepartmentSubject $viewingDepartmentSubject = null;

    #[Locked]
    public ?DepartmentSubject $deletingDepartmentSubject = null;

    #[Locked]
    public ?DepartmentSubject $restoringDepartmentSubject = null;

    #[Locked]
    public ?DepartmentSubject $forceDeletingDepartmentSubject = null;

    // Form fields
    #[Rule('required|exists:departments,id')]
    public ?int $department_id = null;

    #[Rule('required|exists:subjects,id')]
    public ?int $subject_id = null;
    
    // Example of a field specific to the pivot, if any (e.g., semester, year)
    // #[Rule('nullable|integer|min:1')]
    // public ?int $semester = null;

    #[Rule('boolean')]
    public bool $is_active = true;

    public array $perPageOptions = [10, 25, 50, 100];
    public Collection $departmentOptions;
    public Collection $subjectOptions;


    public function boot(DepartmentSubjectService $departmentSubjectService, ExportImportService $exportImportService): void
    {
        $this->departmentSubjectService = $departmentSubjectService;
        $this->exportImportService = $exportImportService;
        // Assuming Department model has an 'is_active' scope or similar
        $this->departmentOptions = Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $this->subjectOptions = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'department.name', 'label' => 'Department', 'sortable' => true],
            ['key' => 'subject.name', 'label' => 'Subject', 'sortable' => true],
            // ['key' => 'semester', 'label' => 'Semester', 'sortable' => true], // If semester field exists
            ['key' => 'is_active', 'label' => 'Status', 'sortable' => true],
        ];

        if ($this->showTimestamps) {
            $headers[] = ['key' => 'created_at', 'label' => 'Assigned At', 'sortable' => true];
            $headers[] = ['key' => 'updated_at', 'label' => 'Updated At', 'sortable' => true];
            if ($this->showSoftDeleted) {
                $headers[] = ['key' => 'deleted_at', 'label' => 'Deactivated At', 'sortable' => true];
            }
        }
        $headers[] = ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'class' => 'w-32'];
        return $headers;
    }

    public function departmentSubjects(): LengthAwarePaginator
    {
        return $this->departmentSubjectService->paginate(
            search: $this->search,
            perPage: $this->perPage,
            sortBy: $this->sortBy['column'],
            sortDirection: $this->sortBy['direction'],
            isActiveFilter: IsActiveFilter::tryFrom($this->isActiveFilter),
            departmentId: $this->departmentFilter,
            subjectId: $this->subjectFilter,
            withTrashed: $this->showSoftDeleted
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedIsActiveFilter(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedDepartmentFilter(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSubjectFilter(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedShowSoftDeleted(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->isActiveFilter = IsActiveFilter::ALL->value;
        $this->departmentFilter = null;
        $this->subjectFilter = null;
        $this->showSoftDeleted = false;
        $this->resetPage();
        $this->success('Filters cleared.');
    }

    public function resetForm(): void
    {
        $this->department_id = null;
        $this->subject_id = null;
        // $this->semester = null; // If semester field exists
        $this->is_active = true;
        $this->editingDepartmentSubject = null;
        $this->viewingDepartmentSubject = null;
        $this->deletingDepartmentSubject = null;
        $this->restoringDepartmentSubject = null;
        $this->forceDeletingDepartmentSubject = null;
        $this->resetValidation();
    }

    // Create methods
    public function openCreateModal(): void
    {
        if (Gate::denies('department-subject.create')) {
            $this->error('You are not authorized to assign subjects to departments.');
            return;
        }
        $this->resetForm();
        $this->createModal = true;
    }

    public function create(): void
    {
        if (Gate::denies('department-subject.create')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to assign subjects.');
            return;
        }

        $validated = $this->validate([
            'department_id' => 'required|exists:departments,id',
            'subject_id' => 'required|exists:subjects,id|unique:department_subject,subject_id,NULL,id,department_id,' . $this->department_id,
            // 'semester' => 'nullable|integer|min:1', // If semester field exists
            'is_active' => 'boolean',
        ], [
            'subject_id.unique' => 'This subject is already assigned to the selected department.'
        ]);

        try {
            $this->departmentSubjectService->create($validated);
            $this->success('Subject assigned to department successfully.');
            $this->createModal = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Assigning Subject', $e->getMessage());
            $this->error('Failed to assign subject: ' . $e->getMessage());
        }
    }

    // Edit methods
    public function openEditModal(int $id): void
    {
        if (Gate::denies('department-subject.edit')) {
            $this->error('You are not authorized to edit department subject assignments.');
            return;
        }
        $this->editingDepartmentSubject = $this->departmentSubjectService->find($id);
        if (!$this->editingDepartmentSubject) {
            $this->error('Department Subject assignment not found.');
            return;
        }
        $this->department_id = $this->editingDepartmentSubject->department_id;
        $this->subject_id = $this->editingDepartmentSubject->subject_id;
        // $this->semester = $this->editingDepartmentSubject->semester; // If semester field exists
        $this->is_active = $this->editingDepartmentSubject->is_active;
        $this->resetValidation();
        $this->editModal = true;
    }

    public function update(): void
    {
        if (Gate::denies('department-subject.edit') || !$this->editingDepartmentSubject) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to edit this assignment or it does not exist.');
            return;
        }

        $validated = $this->validate([
            'department_id' => 'required|exists:departments,id',
            'subject_id' => 'required|exists:subjects,id|unique:department_subject,subject_id,' . $this->editingDepartmentSubject->id . ',id,department_id,' . $this->department_id,
            // 'semester' => 'nullable|integer|min:1', // If semester field exists
            'is_active' => 'boolean',
        ], [
            'subject_id.unique' => 'This subject is already assigned to the selected department (with a different assignment ID).'
        ]);

        try {
            $this->departmentSubjectService->update($this->editingDepartmentSubject, $validated);
            $this->success('Department Subject assignment updated successfully.');
            $this->editModal = false;
            $this->editingDepartmentSubject = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Updating Assignment', $e->getMessage());
            $this->error('Failed to update assignment: ' . $e->getMessage());
        }
    }

    // View method
    public function openViewModal(int $id): void
    {
        if (Gate::denies('department-subject.view')) {
            $this->error('You are not authorized to view department subject assignments.');
            return;
        }
        $this->viewingDepartmentSubject = $this->departmentSubjectService->findWithDetails($id, $this->showSoftDeleted, ['department', 'subject']);
        if (!$this->viewingDepartmentSubject) {
            $this->error('Assignment not found.');
            return;
        }
        $this->viewModal = true;
    }

    // Delete methods (Soft Delete)
    public function openDeleteModal(int $id): void
    {
        if (Gate::denies('department-subject.delete')) {
            $this->error('You are not authorized to delete assignments.');
            return;
        }
        $this->deletingDepartmentSubject = $this->departmentSubjectService->find($id);
        if (!$this->deletingDepartmentSubject) {
            $this->error('Assignment not found.');
            return;
        }
        $this->deleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        if (Gate::denies('department-subject.delete') || !$this->deletingDepartmentSubject) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to delete this assignment or it does not exist.');
            return;
        }
        try {
            $this->departmentSubjectService->delete($this->deletingDepartmentSubject);
            $this->success('Assignment deleted successfully.');
            $this->deleteModal = false;
            $this->removeSelectedItem($this->deletingDepartmentSubject->id ?? 0);
            $this->deletingDepartmentSubject = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Deleting Assignment', $e->getMessage());
            $this->error('Failed to delete assignment: ' . $e->getMessage());
        }
    }

    // Restore methods
    public function openRestoreModal(int $id): void
    {
        if (Gate::denies('department-subject.restore')) {
            $this->error('You are not authorized to restore assignments.');
            return;
        }
        $this->restoringDepartmentSubject = $this->departmentSubjectService->findOnlyTrashed($id);
        if (!$this->restoringDepartmentSubject) {
            $this->error('Assignment not found in trash or not soft deleted.');
            return;
        }
        $this->restoreModal = true;
    }

    public function restoreConfirmed(): void
    {
        if (Gate::denies('department-subject.restore') || !$this->restoringDepartmentSubject) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to restore this assignment or it does not exist in trash.');
            return;
        }
        try {
            $this->departmentSubjectService->restore($this->restoringDepartmentSubject);
            $this->success('Assignment restored successfully.');
            $this->restoreModal = false;
            $this->restoringDepartmentSubject = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Restoring Assignment', $e->getMessage());
            $this->error('Failed to restore assignment: ' . $e->getMessage());
        }
    }

    // Force Delete methods
    public function openForceDeleteModal(int $id): void
    {
        if (Gate::denies('department-subject.force-delete')) {
            $this->error('You are not authorized to permanently delete assignments.');
            return;
        }
        $this->forceDeletingDepartmentSubject = $this->departmentSubjectService->findWithDetails($id, true);
        if (!$this->forceDeletingDepartmentSubject) {
            $this->error('Assignment not found.');
            return;
        }
        $this->forceDeleteModal = true;
    }

    public function forceDeleteConfirmed(): void
    {
        if (Gate::denies('department-subject.force-delete') || !$this->forceDeletingDepartmentSubject) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to permanently delete this assignment or it does not exist.');
            return;
        }
        try {
            $this->departmentSubjectService->forceDelete($this->forceDeletingDepartmentSubject);
            $this->success('Assignment permanently deleted successfully.');
            $this->forceDeleteModal = false;
            $this->removeSelectedItem($this->forceDeletingDepartmentSubject->id ?? 0);
            $this->forceDeletingDepartmentSubject = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Permanently Deleting Assignment', $e->getMessage());
            $this->error('Failed to permanently delete assignment: ' . $e->getMessage());
        }
    }

    // Bulk actions
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedItems = $value ? $this->departmentSubjects()->pluck('id')->map(fn($id) => (string) $id)->toArray() : [];
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = count($this->selectedItems) === $this->departmentSubjects()->count() && $this->departmentSubjects()->count() > 0;
    }

    private function removeSelectedItem(int $id): void
    {
        $this->selectedItems = array_filter($this->selectedItems, fn($itemId) => $itemId != $id);
        if(empty($this->selectedItems)) {
            $this->selectAll = false;
        }
    }

    public function openBulkDeleteModal(): void
    {
        if (Gate::denies('department-subject.bulk-delete')) {
            $this->error('You are not authorized to bulk delete assignments.');
            return;
        }
        if (empty($this->selectedItems)) {
            $this->warning('No items selected for bulk deletion.');
            return;
        }
        $this->bulkDeleteModal = true;
    }

    public function bulkDeleteConfirmed(): void
    {
        if (Gate::denies('department-subject.bulk-delete')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk delete assignments.');
            return;
        }
        try {
            $this->departmentSubjectService->bulkDelete($this->selectedItems);
            $this->success(count($this->selectedItems) . ' assignment(s) deleted successfully.');
            $this->bulkDeleteModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Deleting Assignments', $e->getMessage());
            $this->error('Failed to bulk delete assignments: ' . $e->getMessage());
        }
    }

    public function openBulkRestoreModal(): void
    {
        if (Gate::denies('department-subject.bulk-restore')) {
            $this->error('You are not authorized to bulk restore assignments.');
            return;
        }
        if (empty($this->selectedItems)) {
            $this->warning('No items selected for bulk restoration.');
            return;
        }
        $this->bulkRestoreModal = true;
    }

    public function bulkRestoreConfirmed(): void
    {
        if (Gate::denies('department-subject.bulk-restore')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk restore assignments.');
            return;
        }
        try {
            $this->departmentSubjectService->bulkRestore($this->selectedItems);
            $this->success(count($this->selectedItems) . ' assignment(s) restored successfully.');
            $this->bulkRestoreModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Restoring Assignments', $e->getMessage());
            $this->error('Failed to bulk restore assignments: ' . $e->getMessage());
        }
    }

    public function openBulkForceDeleteModal(): void
    {
        if (Gate::denies('department-subject.bulk-force-delete')) {
            $this->error('You are not authorized to bulk permanently delete assignments.');
            return;
        }
        if (empty($this->selectedItems)) {
            $this->warning('No items selected for bulk permanent deletion.');
            return;
        }
        $this->bulkForceDeleteModal = true;
    }

    public function bulkForceDeleteConfirmed(): void
    {
        if (Gate::denies('department-subject.bulk-force-delete')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk permanently delete assignments.');
            return;
        }
        try {
            $this->departmentSubjectService->bulkForceDelete($this->selectedItems);
            $this->success(count($this->selectedItems) . ' assignment(s) permanently deleted successfully.');
            $this->bulkForceDeleteModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Permanently Deleting Assignments', $e->getMessage());
            $this->error('Failed to bulk permanently delete assignments: ' . $e->getMessage());
        }
    }

    // Export methods
    public function exportPdf()
    {
        if (Gate::denies('department-subject.export')) {
            $this->error('You are not authorized to export assignments.');
            return;
        }
        return ExportAction::export($this, $this->exportImportService, $this->departmentSubjectService, 'pdf', 'department_subjects', $this->search, $this->sortBy, $this->isActiveFilter, $this->showSoftDeleted, ['department_id' => $this->departmentFilter, 'subject_id' => $this->subjectFilter]);
    }

    public function exportXlsx()
    {
        if (Gate::denies('department-subject.export')) {
            $this->error('You are not authorized to export assignments.');
            return;
        }
        return ExportAction::export($this, $this->exportImportService, $this->departmentSubjectService, 'xlsx', 'department_subjects', $this->search, $this->sortBy, $this->isActiveFilter, $this->showSoftDeleted, ['department_id' => $this->departmentFilter, 'subject_id' => $this->subjectFilter]);
    }

    public function exportCsv()
    {
        if (Gate::denies('department-subject.export')) {
            $this->error('You are not authorized to export assignments.');
            return;
        }
        return ExportAction::export($this, $this->exportImportService, $this->departmentSubjectService, 'csv', 'department_subjects', $this->search, $this->sortBy, $this->isActiveFilter, $this->showSoftDeleted, ['department_id' => $this->departmentFilter, 'subject_id' => $this->subjectFilter]);
    }

    public function getIsActiveText(bool $isActive): string
    {
        return $isActive ? 'Active' : 'Inactive';
    }

    public function getIsActiveBadgeClass(bool $isActive): string
    {
        return $isActive ? 'badge-success' : 'badge-error';
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'departmentSubjects' => $this->departmentSubjects(),
            'isActiveFilterOptions' => IsActiveFilter::toSelectArray(),
            'departmentFilterOptions' => $this->departmentOptions->prepend('All Departments', '')->toArray(),
            'subjectFilterOptions' => $this->subjectOptions->prepend('All Subjects', '')->toArray(),
        ];
    }
}; ?>

<div>
    <x-header title="Department Subjects" subtitle="Manage subject assignments to departments.">
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce.500ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @canany(['department-subject.export'])
                <x-dropdown label="Export" icon="o-document-arrow-down" class="btn-ghost">
                    <x-menu-item title="PDF" wire:click="exportPdf" icon="o-document-text" />
                    <x-menu-item title="XLSX" wire:click="exportXlsx" icon="o-table-cells" />
                    <x-menu-item title="CSV" wire:click="exportCsv" icon="o-archive-box" />
                </x-dropdown>
            @endcanany

            @canany(['department-subject.bulk-delete', 'department-subject.bulk-restore', 'department-subject.bulk-force-delete'])
                <x-dropdown label="Bulk Actions" icon="o-ellipsis-vertical" class="btn-ghost" :disabled="empty($selectedItems)">
                    @can('department-subject.bulk-delete')
                        <x-menu-item title="Delete Selected" wire:click="openBulkDeleteModal" icon="o-trash" :disabled="empty($selectedItems) || $showSoftDeleted" />
                    @endcan
                    @can('department-subject.bulk-restore')
                         <x-menu-item title="Restore Selected" wire:click="openBulkRestoreModal" icon="o-arrow-uturn-left" :disabled="empty($selectedItems) || !$showSoftDeleted" />
                    @endcan
                    @can('department-subject.bulk-force-delete')
                        <x-menu-item title="Force Delete Selected" wire:click="openBulkForceDeleteModal" icon="o-trash" class="text-error" :disabled="empty($selectedItems)" />
                    @endcan
                </x-dropdown>
            @endcanany

            @can('department-subject.create')
                <x-button label="Assign Subject" wire:click="openCreateModal" icon="o-plus" class="btn-primary" />
            @endcan
        </x-slot:actions>
    </x-header>

    <x-card>
        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
            <x-select label="Department" :options="$departmentFilterOptions" wire:model.live="departmentFilter" placeholder="All Departments" />
            <x-select label="Subject" :options="$subjectFilterOptions" wire:model.live="subjectFilter" placeholder="All Subjects" />
            <x-select label="Status" :options="$isActiveFilterOptions" wire:model.live="isActiveFilter" placeholder="All Statuses" />
            <x-toggle label="Show Timestamps" wire:model.live="showTimestamps" />
            <x-toggle label="Show Deactivated" wire:model.live="showSoftDeleted" />
            <div class="col-span-1 md:col-start-6 flex justify-end items-center">
                 <x-button label="Clear Filters" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost text-sm" />
            </div>
        </div>

        <x-table :headers="$headers" :rows="$departmentSubjects" striped with-pagination :sort-by="$sortBy" wire:sortable="true" :per-page-options="$perPageOptions" wire:model:perPage="perPage">
            @scope('header_id', $header)
                <x-checkbox wire:model.live="selectAll" :value="$departmentSubjects->count() > 0 && count($selectedItems) === $departmentSubjects->total()" />
            @endscope
            @scope('cell_id', $item)
                <x-checkbox :value="(string)$item->id" wire:model.live="selectedItems" />
            @endscope

            @scope('cell_department.name', $item)
                <span class="font-semibold">{{ $item->department?->name ?? 'N/A' }}</span>
            @endscope

            @scope('cell_subject.name', $item)
                <span class="font-semibold">{{ $item->subject?->name ?? 'N/A' }}</span>
                <div class="text-xs text-gray-500">{{ $item->subject?->code ?? '' }}</div>
            @endscope
            
            {{-- @scope('cell_semester', $item) --}}
            {{--    {{ $item->semester ?? 'N/A' }} --}}
            {{-- @endscope --}}

            @scope('cell_is_active', $item)
                <x-badge :value="$this->getIsActiveText($item->is_active)" :class="$this->getIsActiveBadgeClass($item->is_active) . ' badge-sm'" />
            @endscope

            @scope('cell_created_at', $item)
                {{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-' }}
            @endscope

            @scope('cell_updated_at', $item)
                {{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : '-' }}
            @endscope
            
            @scope('cell_deleted_at', $item)
                {{ $item->deleted_at ? $item->deleted_at->format('Y-m-d H:i') : '-' }}
            @endscope

            @scope('actions', $item)
                <div class="flex space-x-1">
                    @can('department-subject.view')
                        <x-button wire:click="openViewModal({{ $item->id }})" icon="o-eye" class="btn-xs btn-ghost" tooltip-left="View" />
                    @endcan
                    @if(!$item->trashed())
                        @can('department-subject.edit')
                            <x-button wire:click="openEditModal({{ $item->id }})" icon="o-pencil" class="btn-xs btn-ghost" tooltip-left="Edit" />
                        @endcan
                        @can('department-subject.delete')
                            <x-button wire:click="openDeleteModal({{ $item->id }})" icon="o-trash" class="btn-xs btn-ghost text-error" tooltip-left="Deactivate" />
                        @endcan
                    @else
                        @can('department-subject.restore')
                            <x-button wire:click="openRestoreModal({{ $item->id }})" icon="o-arrow-uturn-left" class="btn-xs btn-ghost text-success" tooltip-left="Reactivate" />
                        @endcan
                    @endif
                    @can('department-subject.force-delete')
                         <x-button wire:click="openForceDeleteModal({{ $item->id }})" icon="o-archive-box-x-mark" class="btn-xs btn-ghost text-error" tooltip-left="Permanently Delete" />
                    @endcan
                </div>
            @endscope
        </x-table>
        @if($departmentSubjects->isEmpty())
            <div class="text-center py-8">
                <x-icon name="o-academic-cap" class="w-12 h-12 mx-auto text-gray-400" /> {{-- Changed icon --}}
                <p class="mt-2 text-sm text-gray-500">No subjects assigned to departments found.</p>
                @if(empty($search) && $isActiveFilter === IsActiveFilter::ALL->value && !$departmentFilter && !$subjectFilter && !$showSoftDeleted)
                    @can('department-subject.create')
                    <x-button label="Assign Subject to Department" wire:click="openCreateModal" icon="o-plus" class="btn-primary mt-4" />
                    @endcan
                @endif
            </div>
        @endif
    </x-card>

    {{-- Create Modal --}}
    <x-modal wire:model="createModal" title="Assign Subject to Department" persistent class="backdrop-blur">
        <x-form wire:submit="create">
            <x-select label="Department" wire:model="department_id" :options="$departmentOptions" placeholder="Select Department" required />
            <x-select label="Subject" wire:model="subject_id" :options="$subjectOptions" placeholder="Select Subject" required />
            {{-- <x-input label="Semester (Optional)" wire:model="semester" type="number" min="1" placeholder="Enter semester" /> --}}
            <x-toggle label="Active" wire:model="is_active" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.createModal = false" class="btn-ghost" />
                <x-button label="Assign" type="submit" class="btn-primary" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Department Subject Assignment" persistent class="backdrop-blur">
        <x-form wire:submit="update">
            <x-select label="Department" wire:model="department_id" :options="$departmentOptions" placeholder="Select Department" required />
            <x-select label="Subject" wire:model="subject_id" :options="$subjectOptions" placeholder="Select Subject" required />
            {{-- <x-input label="Semester (Optional)" wire:model="semester" type="number" min="1" placeholder="Enter semester" /> --}}
            <x-toggle label="Active" wire:model="is_active" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.editModal = false" class="btn-ghost" />
                <x-button label="Update" type="submit" class="btn-primary" spinner="update" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Modal --}}
    <x-modal wire:model="viewModal" title="View Department Subject Assignment" class="backdrop-blur">
        @if($viewingDepartmentSubject)
            <div class="space-y-2">
                <p><strong>ID:</strong> {{ $viewingDepartmentSubject->id }}</p>
                <p><strong>Department:</strong> {{ $viewingDepartmentSubject->department?->name ?? 'N/A' }}</p>
                <p><strong>Subject:</strong> {{ $viewingDepartmentSubject->subject?->name ?? 'N/A' }} ({{ $viewingDepartmentSubject->subject?->code ?? 'N/A' }})</p>
                {{-- <p><strong>Semester:</strong> {{ $viewingDepartmentSubject->semester ?? 'N/A' }}</p> --}}
                <p><strong>Status:</strong> <x-badge :value="$this->getIsActiveText($viewingDepartmentSubject->is_active)" :class="$this->getIsActiveBadgeClass($viewingDepartmentSubject->is_active) . ' badge-sm'" /></p>
                <p><strong>Assigned At:</strong> {{ $viewingDepartmentSubject->created_at ? $viewingDepartmentSubject->created_at->format('M d, Y H:i A') : 'N/A' }}</p>
                <p><strong>Updated At:</strong> {{ $viewingDepartmentSubject->updated_at ? $viewingDepartmentSubject->updated_at->format('M d, Y H:i A') : 'N/A' }}</p>
                @if($viewingDepartmentSubject->trashed())
                    <p><strong>Deactivated At:</strong> {{ $viewingDepartmentSubject->deleted_at ? $viewingDepartmentSubject->deleted_at->format('M d, Y H:i A') : 'N/A' }}</p>
                @endif
            </div>
        @else
            <p>No assignment details available.</p>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal = false" class="btn-primary" />
        </x-slot:actions>
    </x-modal>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="deleteModal" title="Confirm Deactivation" persistent class="backdrop-blur">
        <p>Are you sure you want to deactivate the assignment of <strong>{{ $deletingDepartmentSubject?->subject?->name }}</strong> to <strong>{{ $deletingDepartmentSubject?->department?->name }}</strong>?</p>
        <p class="text-sm text-gray-600">This action will soft delete the record. It can be reactivated later.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.deleteModal = false" class="btn-ghost" />
            <x-button label="Deactivate" wire:click="deleteConfirmed" class="btn-error" spinner="deleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Restore Confirmation Modal --}}
    <x-modal wire:model="restoreModal" title="Confirm Reactivation" persistent class="backdrop-blur">
        <p>Are you sure you want to reactivate the assignment of <strong>{{ $restoringDepartmentSubject?->subject?->name }}</strong> to <strong>{{ $restoringDepartmentSubject?->department?->name }}</strong>?</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.restoreModal = false" class="btn-ghost" />
            <x-button label="Reactivate" wire:click="restoreConfirmed" class="btn-success" spinner="restoreConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Force Delete Confirmation Modal --}}
    <x-modal wire:model="forceDeleteModal" title="Confirm Permanent Deletion" persistent class="backdrop-blur">
        <p class="font-bold text-error">This action is irreversible!</p>
        <p>Are you sure you want to permanently delete the assignment of <strong>{{ $forceDeletingDepartmentSubject?->subject?->name }}</strong> to <strong>{{ $forceDeletingDepartmentSubject?->department?->name }}</strong>?</p>
        <p class="text-sm text-gray-600">All associated data might be lost.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.forceDeleteModal = false" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="forceDeleteConfirmed" class="btn-error" spinner="forceDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Delete Confirmation Modal --}}
    <x-modal wire:model="bulkDeleteModal" title="Confirm Bulk Deactivation" persistent class="backdrop-blur">
        <p>Are you sure you want to deactivate <strong>{{ count($selectedItems) }}</strong> selected assignment(s)?</p>
        <p class="text-sm text-gray-600">This action will soft delete the records. They can be reactivated later.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkDeleteModal = false" class="btn-ghost" />
            <x-button label="Deactivate Selected" wire:click="bulkDeleteConfirmed" class="btn-error" spinner="bulkDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Restore Confirmation Modal --}}
    <x-modal wire:model="bulkRestoreModal" title="Confirm Bulk Reactivation" persistent class="backdrop-blur">
        <p>Are you sure you want to reactivate <strong>{{ count($selectedItems) }}</strong> selected assignment(s)?</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkRestoreModal = false" class="btn-ghost" />
            <x-button label="Reactivate Selected" wire:click="bulkRestoreConfirmed" class="btn-success" spinner="bulkRestoreConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Force Delete Confirmation Modal --}}
    <x-modal wire:model="bulkForceDeleteModal" title="Confirm Bulk Permanent Deletion" persistent class="backdrop-blur">
        <p class="font-bold text-error">This action is irreversible!</p>
        <p>Are you sure you want to permanently delete <strong>{{ count($selectedItems) }}</strong> selected assignment(s)?</p>
        <p class="text-sm text-gray-600">All associated data might be lost.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkForceDeleteModal = false" class="btn-ghost" />
            <x-button label="Permanently Delete Selected" wire:click="bulkForceDeleteConfirmed" class="btn-error" spinner="bulkForceDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal for displaying errors --}}
    <x-modal wire:model="showErrorModal" title="{{ $errorTitle ?? 'Error' }}" persistent class="backdrop-blur">
        <p>{{ $errorMessage ?? 'An unexpected error occurred.' }}</p>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.showErrorModal = false" class="btn-primary" />
        </x-slot:actions>
    </x-modal>
</div>
