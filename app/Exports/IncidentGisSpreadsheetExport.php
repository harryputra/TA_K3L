<?php

namespace App\Exports;

use App\Models\IncidentReport;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentGisSpreadsheetExport
{
    public function download(Collection $reports, array $filters): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GIS Insiden');

        $headers = [
            'No',
            'No. Laporan',
            'Tanggal',
            'Waktu',
            'Nama Pelapor',
            'Nama Korban',
            'Judul Kejadian',
            'Kategori Insiden',
            'Severity',
            'Status',
            'Lokasi Final',
            'Detail Lokasi Final',
            'Area',
            'Latitude',
            'Longitude',
            'Catatan Luka',
        ];

        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->setCellValue('A1', 'Laporan GIS Insiden SIAGA POLMAN');
        $sheet->setCellValue('A2', 'Export data titik koordinat kejadian kecelakaan');
        $sheet->setCellValue('A3', 'Dibuat pada: ' . now()->format('d M Y H:i'));

        $sheet->fromArray($headers, null, 'A6');

        $row = 7;
        foreach ($reports->values() as $index => $report) {
            $sheet->fromArray($this->row($report, $index + 1), null, "A{$row}");
            $row++;
        }

        $summaryRow = $row + 1;
        $sheet->mergeCells("A{$summaryRow}:{$lastColumn}{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'Total data: ' . $reports->count() . ' | Filter: ' . $this->filterSummary($filters));

        $sheet->freezePane('A7');
        $sheet->setAutoFilter("A6:{$lastColumn}" . max(6, $row - 1));

        $this->styleSheet($spreadsheet, $row, $lastColumn);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan-gis-insiden-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($writer, $spreadsheet) {
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    protected function row(IncidentReport $report, int $number): array
    {
        $locationName = $report->verifiedLocation?->name ?? $report->location?->name ?? '-';
        $latitude = $report->verified_latitude ?? $report->latitude;
        $longitude = $report->verified_longitude ?? $report->longitude;
        $injuries = $report->injuries
            ->map(fn ($injury) => trim(($injury->injuryCategory?->name ?? '-') . ' - ' . ($injury->bodyPart?->name ?? '-') . ($injury->description ? ' - ' . $injury->description : '')))
            ->implode('; ');

        return [
            $number,
            $report->report_number,
            optional($report->incident_date)->format('d M Y'),
            $report->incident_time ? substr($report->incident_time, 0, 5) : '-',
            $report->reporter?->name ?? $report->reporter_name ?? '-',
            $report->victim_name ?: '-',
            $report->title,
            $report->category?->name ?? '-',
            $report->severity_level ?: '-',
            $report->status,
            $locationName,
            $report->verified_specific_location ?? $report->specific_location ?? '-',
            $locationName === 'Diluar Polman' ? 'Diluar Polman' : 'Di Dalam Polman',
            $latitude,
            $longitude,
            $injuries ?: '-',
        ];
    }

    protected function filterSummary(array $filters): string
    {
        $labels = [
            'q' => 'Nama/Laporan',
            'status' => 'Status',
            'category_id' => 'Kategori',
            'location_id' => 'Lokasi',
            'severity_level' => 'Severity',
            'scope' => 'Area',
            'date_from' => 'Dari',
            'date_to' => 'Sampai',
            'month' => 'Bulan',
            'year' => 'Tahun',
        ];

        $active = collect($filters)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $key) => ($labels[$key] ?? $key) . '=' . $value)
            ->values()
            ->implode('; ');

        return $active !== '' ? $active : 'Semua data';
    }

    protected function styleSheet(Spreadsheet $spreadsheet, int $rowAfterData, string $lastColumn): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A2:A3')->getFont()->getColor()->setRGB('475569');

        $sheet->getStyle("A6:{$lastColumn}6")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A4DB3'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $dataEndRow = max(6, $rowAfterData - 1);
        $sheet->getStyle("A6:{$lastColumn}{$dataEndRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        foreach (range(1, Coordinate::columnIndexFromString($lastColumn)) as $column) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(6)->setRowHeight(24);
    }
}
