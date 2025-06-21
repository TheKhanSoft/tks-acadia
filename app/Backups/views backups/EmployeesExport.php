<?php

namespace App\Exports;

use App\Models\Employee;
use App\Services\EmployeeService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr; // Import Arr facade

class EmployeesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;
    protected EmployeeService $employeeService;
    protected array $selectedColumns; // Added to store selected columns
    protected array $columnKeys; // Added to store just the keys for mapping
    protected array $columnLabels; // Added to store just the labels for headings

    public function __construct(array $filters, array $selectedColumns, EmployeeService $employeeService)
    {
        $this->filters = $filters;
        $this->selectedColumns = $selectedColumns; // Store the full selected columns array
        $this->employeeService = $employeeService;

        // Pre-process keys and labels for efficiency
        $this->columnKeys = Arr::pluck($this->selectedColumns, 'key');
        $this->columnLabels = Arr::pluck($this->selectedColumns, 'label');

        // Ensure necessary relations for *selected* columns are included in filters
        $this->ensureRelationsLoaded();
    }

    /**
     * Ensure relations needed for selected columns are included in the query filters.
     */
    protected function ensureRelationsLoaded(): void
    {
        $requiredRelations = [];
        foreach ($this->columnKeys as $key) {
            if (str_contains($key, '.')) {
                // Extract the relation name (e.g., 'employeeType' from 'employeeType.name')
                $relationName = explode('.', $key)[0];
                // Handle potential nested relations if needed in the future, for now just first level
                if (!in_array($relationName, $requiredRelations)) {
                    $requiredRelations[] = $relationName;
                }
            }
        }

        // Merge required relations with existing ones, ensuring defaults are kept if needed
        $defaultRelations = ['employeeType', 'employeeWorkStatus', 'jobNature', 'primaryOffice'];
        $existingRelations = $this->filters['with'] ?? [];
        $this->filters['with'] = array_unique(array_merge($defaultRelations, $existingRelations, $requiredRelations));
    }


    /**
     * Define the query for the export.
     */
    public function query(): Builder
    {
                // $relationsToLoad = $this->getRelationsFromSelectedColumns();

        // Use the new public service method to get the filtered and sorted query
        // Ensure necessary relations are included in the filters array passed to the constructor
        $this->filters['with'] = $this->filters['with'] ?? ['employeeType', 'employeeWorkStatus', 'jobNature', 'primaryOffice']; // Default relations if not set

        return $this->employeeService->getFilteredQueryForExport($this->filters);
        
    }

    /**
     * Define the headings for the export based on selected columns.
     */
    public function headings(): array
    {
        // Return the pre-processed labels
        return $this->columnLabels;
    }

    /**
     * Map the data for each row.
     *
     * Map the data for each row based on selected columns.
     *
     * @param Employee $employee
     */
    public function map($employee): array
    {
        $row = [];
        foreach ($this->columnKeys as $key) {
            // Use data_get for easy access to nested properties/relations
            $value = data_get($employee, $key);

            // Format dates if the value is a Carbon instance
            if ($value instanceof \Carbon\Carbon) {
                // Basic date format, adjust if specific columns need time
                if (in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
                     $value = $value->format('Y-m-d H:i:s');
                } else {
                     $value = $value->format('Y-m-d');
                }
            }

            // Handle specific cases like primary office (it's a collection)
            if ($key === 'primaryOffice.name') {
                 $value = $employee->primaryOffice->first()?->name;
            }

            $row[] = $value;
        }
        return $row;
    }

    // protected function getRelationsFromSelectedColumns(): array
    // {
    //     $relations = [];
    //     foreach ($this->selectedColumnsData as $column) {
    //         $key = $column['key'];
    //         if (str_contains($key, '.')) {
    //             // Extract the relation name (part before the first dot)
    //             $relationName = explode('.', $key)[0];
    //             // Handle potential nested relations like 'relation.nestedRelation.field' if needed
    //             // For now, just getting the first part is usually sufficient for direct relations.
    //             $relations[] = $relationName;
    //         }
    //     }
    //     // Ensure common relations needed by default are included
    //     // Make sure 'primaryOffice' is loaded if 'primaryOffice.name' is selected
    //     $defaultRelations = ['employeeType', 'employeeWorkStatus', 'jobNature'];
    //     if (in_array('primaryOffice.name', array_column($this->selectedColumnsData, 'key'))) {
    //          $defaultRelations[] = 'primaryOffice';
    //     }
    //     // Add other relations if needed, e.g., 'offices' if 'offices.name' is selected
    //     // if (in_array('offices.name', array_column($this->selectedColumnsData, 'key'))) {
    //     //     $defaultRelations[] = 'offices';
    //     // }

    //     return array_unique(array_merge($relations, $defaultRelations));
    // }
}
