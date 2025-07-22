<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class SalesDataSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    protected $salesData;
    protected $totalPrice;
    protected $startDate;
    protected $endDate;
    protected $adminName;

    public function __construct($salesData, $totalPrice, $startDate, $endDate, $adminName)
    {
        $this->salesData = $salesData;
        $this->totalPrice = $totalPrice;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->adminName = $adminName;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->salesData as $sale) {
            $data[] = [
                Carbon::parse($sale->created_at)->format('Y-m-d'),
                $sale->status,
                $sale->total,
            ];
        }

        $data[] = ['Total Penjualan', '', $this->totalPrice];

        return collect($data);
    }

    public function headings(): array
    {
        return ['Tanggal Pesanan', 'Status', 'Total Harga'];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $highestRow = $sheet->getHighestRow();

                // Judul laporan audit dan periode
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');

                $sheet->setCellValue('A1', 'LAPORAN AUDIT PENJUALAN WEB CETAKU');
                $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y'));
                $sheet->setCellValue('A3', '');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Header styling
                $sheet->getStyle('A5:C5')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Data styling
                $sheet->getStyle("A6:C" . ($highestRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Baris total
                $totalRow = $highestRow + 4;
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->getStyle("A{$totalRow}:C{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Tanda tangan
                $signatureRow = $totalRow + 3;
                // Merge semua baris tanda tangan
                $sheet->mergeCells("B{$signatureRow}:C{$signatureRow}"); // Tanggal
                $sheet->mergeCells("B" . ($signatureRow + 1) . ":C" . ($signatureRow + 1)); // Jabatan
                $sheet->mergeCells("B" . ($signatureRow + 2) . ":C" . ($signatureRow + 2)); // Kosong 1
                $sheet->mergeCells("B" . ($signatureRow + 3) . ":C" . ($signatureRow + 3)); // Kosong 2
                $sheet->mergeCells("B" . ($signatureRow + 4) . ":C" . ($signatureRow + 4)); // Kosong 3
                $sheet->mergeCells("B" . ($signatureRow + 5) . ":C" . ($signatureRow + 5)); // Nama

                // Isi nilai
                $sheet->setCellValue("B{$signatureRow}", 'Semarang, ' . Carbon::now()->locale('id')->isoFormat('D MMMM Y'));
                $sheet->setCellValue("B" . ($signatureRow + 1), 'Superadmin');
                $sheet->setCellValue("B" . ($signatureRow + 2), '');
                $sheet->setCellValue("B" . ($signatureRow + 3), '');
                $sheet->setCellValue("B" . ($signatureRow + 4), '');
                $sheet->setCellValue("B" . ($signatureRow + 5), $this->adminName);

                // Atur rata tengah
                $sheet->getStyle("B{$signatureRow}:C" . ($signatureRow + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.3);
                $sheet->getPageMargins()->setLeft(0.3);
                $sheet->getPageMargins()->setBottom(0.5);

            },
        ];
    }
}