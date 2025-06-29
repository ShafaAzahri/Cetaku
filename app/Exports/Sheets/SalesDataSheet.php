<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


class SalesDataSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow(); // Misalnya total di baris 11

                // Gaya Header
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Hilangkan border isi
                $sheet->getStyle("A2:C" . ($highestRow - 1))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                ]);

                // Tambahkan outline border
                $sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Gaya total penjualan (baris terakhir)
                // Merge kolom A dan B di baris total
                $sheet->mergeCells("A{$highestRow}:B{$highestRow}");

                // Set isi teks dan gaya
                $sheet->setCellValue("A{$highestRow}", 'Total Penjualan:');
                $sheet->getStyle("A{$highestRow}:C{$highestRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

            },
        ];
    }


    protected $salesData;
    protected $totalPrice;

    public function __construct($salesData, $totalPrice)
    {
        $this->salesData = $salesData;
        $this->totalPrice = $totalPrice;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->salesData as $sale) {
            $data[] = [
                $sale->created_at->format('Y-m-d'),
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
}
