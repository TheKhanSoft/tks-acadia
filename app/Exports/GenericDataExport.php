<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenericDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle, WithCustomStartCell
{
    protected Collection $data;
    protected array $headings;
    protected array $mapKeys;
    protected ?string $title;
    protected ?string $subtitle;
    protected int $startRow = 1; // Default start row

    public function __construct(Collection $data, array $headings, array $mapKeys, ?string $title = null, ?string $subtitle = null)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->mapKeys = $mapKeys; // Keys to map from the collection items
        $this->title = $title;
        $this->subtitle = $subtitle;

        // Adjust start row if title or subtitle exists
        if ($this->title) {
            $this->startRow++;
        }
        if ($this->subtitle) {
            $this->startRow++;
        }
        // Add an extra empty row for spacing if both title and subtitle exist
        if ($this->title && $this->subtitle) {
             $this->startRow++;
        } elseif ($this->title || $this->subtitle) {
            // Add spacing if only one exists before headers
             $this->startRow++;
        }
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $mappedRow = [];
        foreach ($this->mapKeys as $key) {
            // Handle nested data using dot notation if necessary, or direct access
            $value = data_get($row, $key);

            // Format dates if they are Carbon instances or similar date objects
            if ($value instanceof \Carbon\Carbon) {
                $mappedRow[] = $value->format('Y-m-d H:i:s'); // Or 'Y-m-d' based on needs
            } elseif (is_object($value) || is_array($value)) {
                 // Attempt to convert simple objects/arrays to string representation
                 // This might need adjustment based on the actual object types
                 $mappedRow[] = json_encode($value);
            } else {
                $mappedRow[] = $value;
            }
        }
        return $mappedRow;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Use provided title or default
        return $this->title ?? 'Export';
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        // Data table starts after title/subtitle rows
        return 'A' . ($this->startRow + 1); // +1 because headings() adds a row
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        $headerRow = $this->startRow + 1; // Row where the actual headings are
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));

        $styles = [
            // Style the header row
            $headerRow => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // White text
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F4F4F'], // Dark gray background
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];

        // Style the title row
        if ($this->title) {
            $sheet->mergeCells('A1:' . $lastColumn . '1');
            $styles[1] = [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
        }

        // Style the subtitle row
        if ($this->subtitle) {
            $subtitleRow = $this->title ? 2 : 1; // Adjust row based on title presence
            $sheet->mergeCells('A' . $subtitleRow . ':' . $lastColumn . $subtitleRow);
             $styles[$subtitleRow] = [
                'font' => ['bold' => false, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
        }

        // Add borders to the entire data table including headers
        $firstDataRow = $headerRow + 1;
        $lastDataRow = $headerRow + $this->collection()->count();
        if ($lastDataRow >= $firstDataRow) { // Ensure there is data
            $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastDataRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }


        return $styles;
    }

    /**
     * Register events to modify the sheet after creation.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));
                $currentRow = 1; // Start from the first row

                // Write Title if exists
                if ($this->title) {
                    $sheet->setCellValue('A' . $currentRow, $this->title);
                    $currentRow++; // Move to the next row
                }

                // Write Subtitle if exists
                if ($this->subtitle) {
                    $sheet->setCellValue('A' . $currentRow, $this->subtitle);
                     $currentRow++; // Move to the next row
                }

                // Add empty row for spacing if needed
                 if (($this->title || $this->subtitle) && $this->startRow > $currentRow) {
                     $currentRow = $this->startRow; // Jump to the calculated start row for headers
                 }

                // Headers are automatically added by WithHeadings at startCell() + 1
                // The styles method handles the formatting

                // Freeze the header row
                $headerRowNum = $this->startRow + 1;
                $sheet->freezePane('A' . ($headerRowNum + 1)); // Freeze rows above the first data row

                // Optional: Set specific column widths if needed
                // foreach (range('A', $lastColumn) as $columnID) {
                //     $sheet->getColumnDimension($columnID)->setAutoSize(true); // Already handled by ShouldAutoSize
                // }
            },
        ];
    }
}
