<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericDataExport;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Excel as ExcelWriter; // Alias to avoid conflict

class ExportImportService
{
    protected string $pdfView = 'exports.generic-pdf'; // Default view for PDF

    /**
     * Export data to PDF.
     *
     * @param string $viewName The name of the Blade view for the PDF content.
     * @param array $data Data to pass to the view.
     * @param string $filename The desired filename for the downloaded PDF.
     * @param string $orientation 'portrait' or 'landscape'.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportPdf(string $viewName, array $data, string $filename = 'export.pdf', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($viewName, $data)->setPaper('a4', $orientation);
        return $pdf->download($filename);
    }

    /**
     * Export data to Excel (XLSX).
     *
     * @param Collection $data The data collection to export.
     * @param array $headings The column headings.
     * @param array $mapKeys The keys from the collection items to include in the export, in order.
     * @param string $filename The desired filename for the downloaded Excel file.
     * @param string|null $title Optional title for the sheet.
     * @param string|null $subtitle Optional subtitle for the sheet.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportExcel(
        Collection $data,
        array $headings,
        array $mapKeys,
        string $filename = 'export.xlsx',
        ?string $title = null,
        ?string $subtitle = null
    ) {
        $export = new GenericDataExport($data, $headings, $mapKeys, $title, $subtitle);
        return Excel::download($export, $filename, ExcelWriter::XLSX);
    }

    /**
     * Export data to CSV.
     *
     * @param Collection $data The data collection to export.
     * @param array $headings The column headings.
     * @param array $mapKeys The keys from the collection items to include in the export, in order.
     * @param string $filename The desired filename for the downloaded CSV file.
     * @param string|null $title Optional title for the sheet (might not be visually represented in standard CSV).
     * @param string|null $subtitle Optional subtitle for the sheet (might not be visually represented in standard CSV).
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(
        Collection $data,
        array $headings,
        array $mapKeys,
        string $filename = 'export.csv',
        ?string $title = null,
        ?string $subtitle = null
    ) {
        // Note: Titles/Subtitles might not render well or at all in basic CSV viewers
        // Consider if these are needed for CSV or if a simpler export is sufficient.
        // For simplicity, we use the same GenericDataExport, but styling/titles won't apply to CSV format itself.
        $export = new GenericDataExport($data, $headings, $mapKeys, $title, $subtitle);
        return Excel::download($export, $filename, ExcelWriter::CSV);
    }

    /**
     * Export data to PDF using a generic view.
     *
     * @param Collection $data The data collection to export.
     * @param array $headings The column headings for the table.
     * @param array $mapKeys The keys from the collection items to display in the PDF table, in order.
     * @param string $filename The desired filename for the downloaded PDF.
     * @param string|null $title Optional title for the PDF document.
     * @param string|null $subtitle Optional subtitle for the PDF document.
     * @param string $orientation 'portrait' or 'landscape'.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportGenericPdf(
        Collection $data,
        array $headings,
        array $mapKeys,
        string $filename = 'export.pdf',
        ?string $title = null,
        ?string $subtitle = null,
        string $orientation = 'portrait'
    ) {
        $sanitizeString = function ($value) {
            if (!is_string($value) || empty($value)) {
                return $value;
            }
            
            // Ensure the iconv extension is enabled in your PHP environment.
            if (function_exists('iconv')) {
                $cleanedValue = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                // iconv returns false on failure
                return $cleanedValue !== false ? $cleanedValue : $value; // Fallback to original if iconv fails
            }
            // Fallback to mb_convert_encoding if iconv is not available or preferred
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        };

        $viewData = [
            'title' => $sanitizeString($title),
            'subtitle' => $sanitizeString($subtitle),
            'headings' => array_map($sanitizeString, $headings),
            'mapKeys' => $mapKeys,
            // Clean string data for each item in the collection
            'data' => $data->map(function ($item) use ($mapKeys, $sanitizeString) {
                // Ensure $item is an object or array before iterating
                if (is_object($item) || is_array($item)) {
                    foreach ($mapKeys as $key) {
                        $originalValue = data_get($item, $key);
                        if (is_string($originalValue)) {
                            data_set($item, $key, $sanitizeString($originalValue));
                        }
                    }
                }
                return $item;
            }),
        ];

        // Enable the HTML5 parser and additional options
        $pdf = Pdf::loadView('exports.pdf-layout', $viewData)
        ->setPaper('a4', $orientation);
        return $pdf->download($filename);
    }

    /**
     * Set a custom Blade view for PDF generation.
     *
     * @param string $viewName
     * @return $this
     */
    public function setPdfView(string $viewName): self
    {
        $this->pdfView = $viewName;
        return $this;
    }

   
}
