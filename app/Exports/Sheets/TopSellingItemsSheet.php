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

class TopSellingItemsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    protected $topSellingItems;
    protected $adminName;
    protected $startDate;
    protected $endDate;

    public function __construct($topSellingItems, $adminName, $startDate, $endDate)
    {
        $this->topSellingItems = $topSellingItems;
        $this->adminName = $adminName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->topSellingItems as $item) {
            $data[] = [
                $item->nama_item,
                $item->total_terjual,
                $item->total_pendapatan,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['Nama Produk', 'Total Terjual', 'Total Pendapatan'];
    }

    public function title(): string
    {
        return 'Produk Unggulan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $highestRow = $sheet->getHighestRow();

                // Header laporan audit
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');

                $sheet->setCellValue('A1', 'LAPORAN AUDIT PRODUK UNGGULAN WEB CETAKU');
                $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y'));

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Header kolom
                $sheet->getStyle('A5:C5')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Border data
                $sheet->getStyle("A6:C" . ($highestRow + 4))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Tanda tangan
                // Tentukan baris awal untuk tanda tangan
                $signatureRow = $highestRow + 7;

                // Merge setiap baris tanda tangan
                $sheet->mergeCells("B{$signatureRow}:C{$signatureRow}");     // Tanggal
                $sheet->mergeCells("B" . ($signatureRow + 1) . ":C" . ($signatureRow + 1)); // Jabatan
                $sheet->mergeCells("B" . ($signatureRow + 2) . ":C" . ($signatureRow + 2)); // Kosong 1
                $sheet->mergeCells("B" . ($signatureRow + 3) . ":C" . ($signatureRow + 3)); // Kosong 2
                $sheet->mergeCells("B" . ($signatureRow + 4) . ":C" . ($signatureRow + 4)); // Kosong 3
                $sheet->mergeCells("B" . ($signatureRow + 5) . ":C" . ($signatureRow + 5)); // Nama

                // Isi konten
                $sheet->setCellValue("B{$signatureRow}", 'Semarang, ' . Carbon::now()->locale('id')->isoFormat('D MMMM Y'));
                $sheet->setCellValue("B" . ($signatureRow + 1), 'Superadmin');
                $sheet->setCellValue("B" . ($signatureRow + 2), '');
                $sheet->setCellValue("B" . ($signatureRow + 3), '');
                $sheet->setCellValue("B" . ($signatureRow + 4), '');
                $sheet->setCellValue("B" . ($signatureRow + 5), $this->adminName);

                // Style rata tengah
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