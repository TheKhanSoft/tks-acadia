<?php

use App\Models\SubjectLearningOutcome;
use App\Models\Subject; // For dropdown
use App\Services\SubjectLearningOutcomeService;
use App\Services\ExportImportService;
// Assuming a SubjectLearningOutcomeRequest will be created for validation
// use App\Http\Requests\SubjectLearningOutcomeRequest;
use App\Enums\IsActiveFilter; // If SLOs have an active status, otherwise remove
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

    protected SubjectLearningOutcomeService $sloService;
    protected ExportImportService $exportImportService;

    // Properties for search, sort, pagination, bulk actions, filters
    public string $search = ''; // Search by outcome description or code
    public array $sortBy = ['column' => 'subject_id', 'direction' => 'asc'];
    public int $perPage = 10;
    public array $selectedItems = [];
    public bool $selectAll = false;
    public ?int $subjectFilter = null; // Filter by subject
    // public string $isActiveFilter = IsActiveFilter::ALL->value; // If SLOs have an active status
    public bool $showTimestamps = false;
    public bool $showSoftDeleted = false; // If SLOs use SoftDeletes

    // Modal states
    public bool $createModal = false;
    public bool $editModal = false;
    public bool $viewModal = false;
    public bool $deleteModal = false;
    public bool $bulkDeleteModal = false;
    public bool $restoreModal = false; // If SoftDeletes
    public bool $bulkRestoreModal = false; // If SoftDeletes
    public bool $forceDeleteModal = false; // If SoftDeletes
    public bool $bulkForceDeleteModal = false; // If SoftDeletes

    #[Locked]
    public ?SubjectLearningOutcome $editingSLO = null;

    #[Locked]
    public ?SubjectLearningOutcome $viewingSLO = null;

    #[Locked]
    public ?SubjectLearningOutcome $deletingSLO = null;

    #[Locked]
    public ?SubjectLearningOutcome $restoringSLO = null; // If SoftDeletes

    #[Locked]
    public ?SubjectLearningOutcome $forceDeletingSLO = null; // If SoftDeletes

    // Properties for the error modal, set by ModalErrorAction
    public bool $showErrorModal = false;
    public string $errorTitle = 'Error';
    public string $errorMessage = 'An unexpected error occurred.';

    // Form fields
    #[Rule('required|exists:subjects,id')]
    public ?int $subject_id = null;

    #[Rule('required|string|max:1000')]
    public string $description = '';

    #[Rule('nullable|string|max:20')]
    public string $code = ''; // e.g., CLO-1, PLO-A

    // #[Rule('boolean')] // If SLOs have an active status
    // public bool $is_active = true;

    public array $perPageOptions = [10, 25, 50, 100];
    public Collection $subjectOptions;

    public function boot(SubjectLearningOutcomeService $sloService, ExportImportService $exportImportService): void
    {
        $this->sloService = $sloService;
        $this->exportImportService = $exportImportService;
        $this->subjectOptions = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'subject.name', 'label' => 'Subject', 'sortable' => true],
            ['key' => 'code', 'label' => 'Code', 'sortable' => true, 'class' => 'w-24'],
            ['key' => 'description', 'label' => 'Description', 'sortable' => true],
            // ['key' => 'is_active', 'label' => 'Status', 'sortable' => true], // If SLOs have an active status
        ];

        if ($this->showTimestamps) {
            $headers[] = ['key' => 'created_at', 'label' => 'Created At', 'sortable' => true];
            $headers[] = ['key' => 'updated_at', 'label' => 'Updated At', 'sortable' => true];
            // Add deleted_at if using SoftDeletes and showSoftDeleted is true
            if ($this->showSoftDeleted && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) {
                 $headers[] = ['key' => 'deleted_at', 'label' => 'Deleted At', 'sortable' => true];
            }
        }
        $headers[] = ['key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'class' => 'w-32'];
        return $headers;
    }

    public function learningOutcomes(): LengthAwarePaginator
    {
        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
        return $this->sloService->paginate(
            search: $this->search,
            perPage: $this->perPage,
            sortBy: $this->sortBy['column'],
            sortDirection: $this->sortBy['direction'],
            subjectId: $this->subjectFilter,
            // isActiveFilter: IsActiveFilter::tryFrom($this->isActiveFilter), // If SLOs have an active status
            withTrashed: $usesSoftDeletes && $this->showSoftDeleted
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

    public function updatedSubjectFilter(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    // public function updatedIsActiveFilter(): void // If SLOs have an active status
    // {
    //     $this->resetPage();
    //     $this->selectedItems = [];
    //     $this->selectAll = false;
    // }

    public function updatedShowSoftDeleted(): void
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->subjectFilter = null;
        // $this->isActiveFilter = IsActiveFilter::ALL->value; // If SLOs have an active status
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) {
            $this->showSoftDeleted = false;
        }
        $this->resetPage();
        $this->success('Filters cleared.');
    }

    public function resetForm(): void
    {
        $this->subject_id = null;
        $this->description = '';
        $this->code = '';
        // $this->is_active = true; // If SLOs have an active status
        $this->editingSLO = null;
        $this->viewingSLO = null;
        $this->deletingSLO = null;
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) {
            $this->restoringSLO = null;
            $this->forceDeletingSLO = null;
        }
        $this->resetValidation();
    }

    // Create methods
    public function openCreateModal(): void
    {
        if (Gate::denies('slo.create')) { // Assuming 'slo' permission prefix
            $this->error('You are not authorized to create learning outcomes.');
            return;
        }
        $this->resetForm();
        $this->createModal = true;
    }

    public function create(): void
    {
        if (Gate::denies('slo.create')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to create learning outcomes.');
            return;
        }

        $rules = [
            'subject_id' => 'required|exists:subjects,id',
            'description' => 'required|string|max:1000',
            'code' => 'nullable|string|max:20|unique:subject_learning_outcomes,code,NULL,id,subject_id,' . $this->subject_id,
            // 'is_active' => 'boolean', // If SLOs have an active status
        ];
        $messages = [
            'code.unique' => 'This code is already used for the selected subject.'
        ];

        $validated = $this->validate($rules, $messages);

        try {
            $this->sloService->create($validated);
            $this->success('Learning Outcome created successfully.');
            $this->createModal = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Creating Learning Outcome', $e->getMessage());
            $this->error('Failed to create learning outcome: ' . $e->getMessage());
        }
    }

    // Edit methods
    public function openEditModal(int $id): void
    {
        if (Gate::denies('slo.edit')) {
            $this->error('You are not authorized to edit learning outcomes.');
            return;
        }
        $this->editingSLO = $this->sloService->find($id);
        if (!$this->editingSLO) {
            $this->error('Learning Outcome not found.');
            return;
        }
        $this->subject_id = $this->editingSLO->subject_id;
        $this->description = $this->editingSLO->description;
        $this->code = $this->editingSLO->code ?? '';
        // $this->is_active = $this->editingSLO->is_active; // If SLOs have an active status
        $this->resetValidation();
        $this->editModal = true;
    }

    public function update(): void
    {
        if (Gate::denies('slo.edit') || !$this->editingSLO) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to edit this learning outcome or it does not exist.');
            return;
        }

        $rules = [
            'subject_id' => 'required|exists:subjects,id',
            'description' => 'required|string|max:1000',
            'code' => 'nullable|string|max:20|unique:subject_learning_outcomes,code,' . $this->editingSLO->id . ',id,subject_id,' . $this->subject_id,
            // 'is_active' => 'boolean', // If SLOs have an active status
        ];
         $messages = [
            'code.unique' => 'This code is already used for the selected subject.'
        ];

        $validated = $this->validate($rules, $messages);

        try {
            $this->sloService->update($this->editingSLO, $validated);
            $this->success('Learning Outcome updated successfully.');
            $this->editModal = false;
            $this->editingSLO = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Updating Learning Outcome', $e->getMessage());
            $this->error('Failed to update learning outcome: ' . $e->getMessage());
        }
    }

    // View method
    public function openViewModal(int $id): void
    {
        if (Gate::denies('slo.view')) {
            $this->error('You are not authorized to view learning outcomes.');
            return;
        }
        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
        $this->viewingSLO = $this->sloService->findWithDetails($id, $usesSoftDeletes && $this->showSoftDeleted, ['subject']);
        if (!$this->viewingSLO) {
            $this->error('Learning Outcome not found.');
            return;
        }
        $this->viewModal = true;
    }

    // Delete methods
    public function openDeleteModal(int $id): void
    {
        if (Gate::denies('slo.delete')) {
            $this->error('You are not authorized to delete learning outcomes.');
            return;
        }
        $this->deletingSLO = $this->sloService->find($id);
        if (!$this->deletingSLO) {
            $this->error('Learning Outcome not found.');
            return;
        }
        $this->deleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        if (Gate::denies('slo.delete') || !$this->deletingSLO) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to delete this learning outcome or it does not exist.');
            return;
        }
        try {
            $this->sloService->delete($this->deletingSLO);
            $this->success('Learning Outcome deleted successfully.');
            $this->deleteModal = false;
            $this->removeSelectedItem($this->deletingSLO->id ?? 0);
            $this->deletingSLO = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Deleting Learning Outcome', $e->getMessage());
            $this->error('Failed to delete learning outcome: ' . $e->getMessage());
        }
    }

    // Restore methods (Only if SoftDeletes is used)
    public function openRestoreModal(int $id): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.restore')) {
            $this->error('You are not authorized to restore learning outcomes.');
            return;
        }
        $this->restoringSLO = $this->sloService->findOnlyTrashed($id);
        if (!$this->restoringSLO) {
            $this->error('Learning Outcome not found in trash or not soft deleted.');
            return;
        }
        $this->restoreModal = true;
    }

    public function restoreConfirmed(): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.restore') || !$this->restoringSLO) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to restore this learning outcome or it does not exist in trash.');
            return;
        }
        try {
            $this->sloService->restore($this->restoringSLO);
            $this->success('Learning Outcome restored successfully.');
            $this->restoreModal = false;
            $this->restoringSLO = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Restoring Learning Outcome', $e->getMessage());
            $this->error('Failed to restore learning outcome: ' . $e->getMessage());
        }
    }

    // Force Delete methods (Only if SoftDeletes is used)
    public function openForceDeleteModal(int $id): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.force-delete')) {
            $this->error('You are not authorized to permanently delete learning outcomes.');
            return;
        }
        $this->forceDeletingSLO = $this->sloService->findWithDetails($id, true);
        if (!$this->forceDeletingSLO) {
            $this->error('Learning Outcome not found.');
            return;
        }
        $this->forceDeleteModal = true;
    }

    public function forceDeleteConfirmed(): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.force-delete') || !$this->forceDeletingSLO) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to permanently delete this learning outcome or it does not exist.');
            return;
        }
        try {
            $this->sloService->forceDelete($this->forceDeletingSLO);
            $this->success('Learning Outcome permanently deleted successfully.');
            $this->forceDeleteModal = false;
            $this->removeSelectedItem($this->forceDeletingSLO->id ?? 0);
            $this->forceDeletingSLO = null;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Permanently Deleting Learning Outcome', $e->getMessage());
            $this->error('Failed to permanently delete learning outcome: ' . $e->getMessage());
        }
    }

    // Bulk actions
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedItems = $value ? $this->learningOutcomes()->pluck('id')->map(fn($id) => (string) $id)->toArray() : [];
    }

    public function updatedSelectedItems(): void
    {
        $this->selectAll = count($this->selectedItems) === $this->learningOutcomes()->count() && $this->learningOutcomes()->count() > 0;
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
        if (Gate::denies('slo.bulk-delete')) {
            $this->error('You are not authorized to bulk delete learning outcomes.');
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
        if (Gate::denies('slo.bulk-delete')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk delete learning outcomes.');
            return;
        }
        try {
            $this->sloService->bulkDelete($this->selectedItems);
            $this->success(count($this->selectedItems) . ' learning outcome(s) deleted successfully.');
            $this->bulkDeleteModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Deleting Learning Outcomes', $e->getMessage());
            $this->error('Failed to bulk delete learning outcomes: ' . $e->getMessage());
        }
    }

    public function openBulkRestoreModal(): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.bulk-restore')) {
            $this->error('You are not authorized to bulk restore learning outcomes.');
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
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.bulk-restore')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk restore learning outcomes.');
            return;
        }
        try {
            $this->sloService->bulkRestore($this->selectedItems);
            $this->success(count($this->selectedItems) . ' learning outcome(s) restored successfully.');
            $this->bulkRestoreModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Restoring Learning Outcomes', $e->getMessage());
            $this->error('Failed to bulk restore learning outcomes: ' . $e->getMessage());
        }
    }

    public function openBulkForceDeleteModal(): void
    {
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.bulk-force-delete')) {
            $this->error('You are not authorized to bulk permanently delete learning outcomes.');
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
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class))) return;
        if (Gate::denies('slo.bulk-force-delete')) {
            ModalErrorAction::dispatch($this, 'Authorization Error', 'You are not authorized to bulk permanently delete learning outcomes.');
            return;
        }
        try {
            $this->sloService->bulkForceDelete($this->selectedItems);
            $this->success(count($this->selectedItems) . ' learning outcome(s) permanently deleted successfully.');
            $this->bulkForceDeleteModal = false;
            $this->selectedItems = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            ModalErrorAction::dispatch($this, 'Error Bulk Permanently Deleting Learning Outcomes', $e->getMessage());
            $this->error('Failed to bulk permanently delete learning outcomes: ' . $e->getMessage());
        }
    }

    // Export methods
    public function exportPdf()
    {
        if (Gate::denies('slo.export')) {
            $this->error('You are not authorized to export learning outcomes.');
            return;
        }
        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
        return ExportAction::export($this, $this->exportImportService, $this->sloService, 'pdf', 'learning_outcomes', $this->search, $this->sortBy, /*$this->isActiveFilter,*/ null, $usesSoftDeletes && $this->showSoftDeleted, ['subject_id' => $this->subjectFilter]);
    }

    public function exportXlsx()
    {
        if (Gate::denies('slo.export')) {
            $this->error('You are not authorized to export learning outcomes.');
            return;
        }
        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
        return ExportAction::export($this, $this->exportImportService, $this->sloService, 'xlsx', 'learning_outcomes', $this->search, $this->sortBy, /*$this->isActiveFilter,*/ null, $usesSoftDeletes && $this->showSoftDeleted, ['subject_id' => $this->subjectFilter]);
    }

    public function exportCsv()
    {
        if (Gate::denies('slo.export')) {
            $this->error('You are not authorized to export learning outcomes.');
            return;
        }
        $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
        return ExportAction::export($this, $this->exportImportService, $this->sloService, 'csv', 'learning_outcomes', $this->search, $this->sortBy, /*$this->isActiveFilter,*/ null, $usesSoftDeletes && $this->showSoftDeleted, ['subject_id' => $this->subjectFilter]);
    }

    // public function getIsActiveText(bool $isActive): string // If SLOs have an active status
    // {
    //     return $isActive ? 'Active' : 'Inactive';
    // }

    // public function getIsActiveBadgeClass(bool $isActive): string // If SLOs have an active status
    // {
    //     return $isActive ? 'badge-success' : 'badge-error';
    // }
    
    public function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(SubjectLearningOutcome::class));
    }

    public function with(): array
    {
        $data = [
            'headers' => $this->headers(),
            'learningOutcomes' => $this->learningOutcomes(),
            'subjectFilterOptions' => $this->subjectOptions->prepend('All Subjects', '')->toArray(),
            'usesSoftDeletes' => $this->usesSoftDeletes(),
        ];
        // if (property_exists(SubjectLearningOutcome::class, 'is_active')) { // Check if model has is_active
        //     $data['isActiveFilterOptions'] = IsActiveFilter::toSelectArray();
        // }
        return $data;
    }
}; ?>

<div>
    <x-header title="Subject Learning Outcomes" subtitle="Manage learning outcomes for subjects.">
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search by code or description..." wire:model.live.debounce.500ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @canany(['slo.export'])
                <x-dropdown label="Export" icon="o-document-arrow-down" class="btn-ghost">
                    <x-menu-item title="PDF" wire:click="exportPdf" icon="o-document-text" />
                    <x-menu-item title="XLSX" wire:click="exportXlsx" icon="o-table-cells" />
                    <x-menu-item title="CSV" wire:click="exportCsv" icon="o-archive-box" />
                </x-dropdown>
            @endcanany

            @canany(['slo.bulk-delete', 'slo.bulk-restore', 'slo.bulk-force-delete'])
                <x-dropdown label="Bulk Actions" icon="o-ellipsis-vertical" class="btn-ghost" :disabled="empty($selectedItems)">
                    @can('slo.bulk-delete')
                        <x-menu-item title="Delete Selected" wire:click="openBulkDeleteModal" icon="o-trash" :disabled="empty($selectedItems) || ($usesSoftDeletes && $showSoftDeleted)" />
                    @endcan
                    @if($usesSoftDeletes)
                        @can('slo.bulk-restore')
                            <x-menu-item title="Restore Selected" wire:click="openBulkRestoreModal" icon="o-arrow-uturn-left" :disabled="empty($selectedItems) || !$showSoftDeleted" />
                        @endcan
                        @can('slo.bulk-force-delete')
                            <x-menu-item title="Force Delete Selected" wire:click="openBulkForceDeleteModal" icon="o-trash" class="text-error" :disabled="empty($selectedItems)" />
                        @endcan
                    @endif
                </x-dropdown>
            @endcanany

            @can('slo.create')
                <x-button label="Create SLO" wire:click="openCreateModal" icon="o-plus" class="btn-primary" />
            @endcan
        </x-slot:actions>
    </x-header>

    <x-card>
        {{-- Filters --}}
        <div class="grid grid-cols-1 {{ $usesSoftDeletes ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4 mb-4">
            <x-select label="Subject" :options="$subjectFilterOptions" wire:model.live="subjectFilter" placeholder="All Subjects" />
            {{-- @if(isset($isActiveFilterOptions)) --}}
            {{-- <x-select label="Status" :options="$isActiveFilterOptions" wire:model.live="isActiveFilter" placeholder="All Statuses" /> --}}
            {{-- @endif --}}
            <x-toggle label="Show Timestamps" wire:model.live="showTimestamps" />
            @if($usesSoftDeletes)
            <x-toggle label="Show Deleted" wire:model.live="showSoftDeleted" />
            @endif
            <div class="col-span-1 {{ $usesSoftDeletes ? 'md:col-start-4' : 'md:col-start-3' }} flex justify-end items-center">
                 <x-button label="Clear Filters" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost text-sm" />
            </div>
        </div>

        <x-table :headers="$headers" :rows="$learningOutcomes" striped with-pagination :sort-by="$sortBy" wire:sortable="true" :per-page-options="$perPageOptions" wire:model:perPage="perPage">
            @scope('header_id', $header)
                <x-checkbox wire:model.live="selectAll" :value="$learningOutcomes->count() > 0 && count($selectedItems) === $learningOutcomes->total()" />
            @endscope
            @scope('cell_id', $slo)
                <x-checkbox :value="(string)$slo->id" wire:model.live="selectedItems" />
            @endscope

            @scope('cell_subject.name', $slo)
                <span class="font-semibold">{{ $slo->subject?->name ?? 'N/A' }}</span>
                <div class="text-xs text-gray-500">{{ $slo->subject?->code ?? '' }}</div>
            @endscope

            @scope('cell_code', $slo)
                {{ $slo->code ?? 'N/A' }}
            @endscope
            
            @scope('cell_description', $slo)
                {{ Str::limit($slo->description, 100) }}
            @endscope

            {{-- @scope('cell_is_active', $slo) --}}
            {{-- @if(property_exists($slo, 'is_active')) --}}
            {{-- <x-badge :value="$this->getIsActiveText($slo->is_active)" :class="$this->getIsActiveBadgeClass($slo->is_active) . ' badge-sm'" /> --}}
            {{-- @else --}}
            {{-- N/A --}}
            {{-- @endif --}}
            {{-- @endscope --}}

            @scope('cell_created_at', $slo)
                {{ $slo->created_at ? $slo->created_at->format('Y-m-d H:i') : '-' }}
            @endscope

            @scope('cell_updated_at', $slo)
                {{ $slo->updated_at ? $slo->updated_at->format('Y-m-d H:i') : '-' }}
            @endscope
            
            @if($usesSoftDeletes)
                @scope('cell_deleted_at', $slo)
                    {{ $slo->deleted_at ? $slo->deleted_at->format('Y-m-d H:i') : '-' }}
                @endscope
            @endif

            @scope('actions', $slo)
                <div class="flex space-x-1">
                    @can('slo.view')
                        <x-button wire:click="openViewModal({{ $slo->id }})" icon="o-eye" class="btn-xs btn-ghost" tooltip-left="View" />
                    @endcan
                    @if(!$usesSoftDeletes || !$slo->trashed())
                        @can('slo.edit')
                            <x-button wire:click="openEditModal({{ $slo->id }})" icon="o-pencil" class="btn-xs btn-ghost" tooltip-left="Edit" />
                        @endcan
                        @can('slo.delete')
                            <x-button wire:click="openDeleteModal({{ $slo->id }})" icon="o-trash" class="btn-xs btn-ghost text-error" tooltip-left="{{ $usesSoftDeletes ? 'Soft Delete' : 'Delete' }}" />
                        @endcan
                    @elseif($usesSoftDeletes && $slo->trashed())
                        @can('slo.restore')
                            <x-button wire:click="openRestoreModal({{ $slo->id }})" icon="o-arrow-uturn-left" class="btn-xs btn-ghost text-success" tooltip-left="Restore" />
                        @endcan
                    @endif
                    @if($usesSoftDeletes)
                        @can('slo.force-delete')
                            <x-button wire:click="openForceDeleteModal({{ $slo->id }})" icon="o-archive-box-x-mark" class="btn-xs btn-ghost text-error" tooltip-left="Force Delete" />
                        @endcan
                    @endif
                </div>
            @endscope
        </x-table>
        @if($learningOutcomes->isEmpty())
            <div class="text-center py-8">
                <x-icon name="o-academic-cap" class="w-12 h-12 mx-auto text-gray-400" />
                <p class="mt-2 text-sm text-gray-500">No learning outcomes found.</p>
                @if(empty($search) && !$subjectFilter /* && (isset($isActiveFilterOptions) ? $isActiveFilter === IsActiveFilter::ALL->value : true) */ && (!$usesSoftDeletes || !$showSoftDeleted) )
                    @can('slo.create')
                    <x-button label="Create Learning Outcome" wire:click="openCreateModal" icon="o-plus" class="btn-primary mt-4" />
                    @endcan
                @endif
            </div>
        @endif
    </x-card>

    {{-- Create Modal --}}
    <x-modal wire:model="createModal" title="Create Subject Learning Outcome" persistent class="backdrop-blur">
        <x-form wire:submit="create">
            <x-select label="Subject" wire:model="subject_id" :options="$subjectOptions" placeholder="Select Subject" required />
            <x-input label="Code (Optional)" wire:model="code" placeholder="e.g., CLO-1, PLO-A" />
            <x-textarea label="Description" wire:model="description" placeholder="Enter learning outcome description" rows="4" required />
            {{-- @if(property_exists(SubjectLearningOutcome::class, 'is_active')) --}}
            {{-- <x-toggle label="Active" wire:model="is_active" /> --}}
            {{-- @endif --}}

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.createModal = false" class="btn-ghost" />
                <x-button label="Create" type="submit" class="btn-primary" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Subject Learning Outcome" persistent class="backdrop-blur">
        <x-form wire:submit="update">
            <x-select label="Subject" wire:model="subject_id" :options="$subjectOptions" placeholder="Select Subject" required />
            <x-input label="Code (Optional)" wire:model="code" placeholder="e.g., CLO-1, PLO-A" />
            <x-textarea label="Description" wire:model="description" placeholder="Enter learning outcome description" rows="4" required />
            {{-- @if(property_exists(SubjectLearningOutcome::class, 'is_active')) --}}
            {{-- <x-toggle label="Active" wire:model="is_active" /> --}}
            {{-- @endif --}}

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.editModal = false" class="btn-ghost" />
                <x-button label="Update" type="submit" class="btn-primary" spinner="update" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- View Modal --}}
    <x-modal wire:model="viewModal" title="View Subject Learning Outcome" class="backdrop-blur">
        @if($viewingSLO)
            <div class="space-y-2">
                <p><strong>ID:</strong> {{ $viewingSLO->id }}</p>
                <p><strong>Subject:</strong> {{ $viewingSLO->subject?->name ?? 'N/A' }} ({{ $viewingSLO->subject?->code ?? 'N/A' }})</p>
                <p><strong>Code:</strong> {{ $viewingSLO->code ?? 'N/A' }}</p>
                <p><strong>Description:</strong></p>
                <div class="prose prose-sm max-w-none">{!! nl2br(e($viewingSLO->description)) !!}</div>
                {{-- @if(property_exists($viewingSLO, 'is_active')) --}}
                {{-- <p><strong>Status:</strong> <x-badge :value="$this->getIsActiveText($viewingSLO->is_active)" :class="$this->getIsActiveBadgeClass($viewingSLO->is_active) . ' badge-sm'" /></p> --}}
                {{-- @endif --}}
                <p><strong>Created At:</strong> {{ $viewingSLO->created_at ? $viewingSLO->created_at->format('M d, Y H:i A') : 'N/A' }}</p>
                <p><strong>Updated At:</strong> {{ $viewingSLO->updated_at ? $viewingSLO->updated_at->format('M d, Y H:i A') : 'N/A' }}</p>
                @if($usesSoftDeletes && $viewingSLO->trashed())
                    <p><strong>Deleted At:</strong> {{ $viewingSLO->deleted_at ? $viewingSLO->deleted_at->format('M d, Y H:i A') : 'N/A' }}</p>
                @endif
            </div>
        @else
            <p>No learning outcome details available.</p>
        @endif
        <x-slot:actions>
            <x-button label="Close" @click="$wire.viewModal = false" class="btn-primary" />
        </x-slot:actions>
    </x-modal>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="deleteModal" title="Confirm Deletion" persistent class="backdrop-blur">
        <p>Are you sure you want to delete the learning outcome: <strong>{{ Str::limit($deletingSLO?->description, 50) }} ({{ $deletingSLO?->code }})</strong>?</p>
        @if($usesSoftDeletes)
        <p class="text-sm text-gray-600">This action will soft delete the record. It can be restored later.</p>
        @endif
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.deleteModal = false" class="btn-ghost" />
            <x-button label="Delete" wire:click="deleteConfirmed" class="btn-error" spinner="deleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    @if($usesSoftDeletes)
    {{-- Restore Confirmation Modal --}}
    <x-modal wire:model="restoreModal" title="Confirm Restoration" persistent class="backdrop-blur">
        <p>Are you sure you want to restore the learning outcome: <strong>{{ Str::limit($restoringSLO?->description, 50) }} ({{ $restoringSLO?->code }})</strong>?</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.restoreModal = false" class="btn-ghost" />
            <x-button label="Restore" wire:click="restoreConfirmed" class="btn-success" spinner="restoreConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Force Delete Confirmation Modal --}}
    <x-modal wire:model="forceDeleteModal" title="Confirm Permanent Deletion" persistent class="backdrop-blur">
        <p class="font-bold text-error">This action is irreversible!</p>
        <p>Are you sure you want to permanently delete the learning outcome: <strong>{{ Str::limit($forceDeletingSLO?->description, 50) }} ({{ $forceDeletingSLO?->code }})</strong>?</p>
        <p class="text-sm text-gray-600">All associated data might be lost.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.forceDeleteModal = false" class="btn-ghost" />
            <x-button label="Permanently Delete" wire:click="forceDeleteConfirmed" class="btn-error" spinner="forceDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>
    @endif

    {{-- Bulk Delete Confirmation Modal --}}
    <x-modal wire:model="bulkDeleteModal" title="Confirm Bulk Deletion" persistent class="backdrop-blur">
        <p>Are you sure you want to delete <strong>{{ count($selectedItems) }}</strong> selected learning outcome(s)?</p>
        @if($usesSoftDeletes)
        <p class="text-sm text-gray-600">This action will soft delete the records. They can be restored later.</p>
        @endif
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkDeleteModal = false" class="btn-ghost" />
            <x-button label="Delete Selected" wire:click="bulkDeleteConfirmed" class="btn-error" spinner="bulkDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>

    @if($usesSoftDeletes)
    {{-- Bulk Restore Confirmation Modal --}}
    <x-modal wire:model="bulkRestoreModal" title="Confirm Bulk Restoration" persistent class="backdrop-blur">
        <p>Are you sure you want to restore <strong>{{ count($selectedItems) }}</strong> selected learning outcome(s)?</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkRestoreModal = false" class="btn-ghost" />
            <x-button label="Restore Selected" wire:click="bulkRestoreConfirmed" class="btn-success" spinner="bulkRestoreConfirmed" />
        </x-slot:actions>
    </x-modal>

    {{-- Bulk Force Delete Confirmation Modal --}}
    <x-modal wire:model="bulkForceDeleteModal" title="Confirm Bulk Permanent Deletion" persistent class="backdrop-blur">
        <p class="font-bold text-error">This action is irreversible!</p>
        <p>Are you sure you want to permanently delete <strong>{{ count($selectedItems) }}</strong> selected learning outcome(s)?</p>
        <p class="text-sm text-gray-600">All associated data might be lost.</p>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.bulkForceDeleteModal = false" class="btn-ghost" />
            <x-button label="Permanently Delete Selected" wire:click="bulkForceDeleteConfirmed" class="btn-error" spinner="bulkForceDeleteConfirmed" />
        </x-slot:actions>
    </x-modal>
    @endif

    {{-- Modal for displaying errors --}}
    <x-modal wire:model="showErrorModal" title="{{ $errorTitle ?? 'Error' }}" persistent class="backdrop-blur">
        <p>{{ $errorMessage ?? 'An unexpected error occurred.' }}</p>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.showErrorModal = false" class="btn-primary" />
        </x-slot:actions>
    </x-modal>
</div>
