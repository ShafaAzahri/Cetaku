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

        // Tambahkan baris total penjualan
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
                $highestRow = $sheet->getHighestRow(); // Baris terakhir (baris total)

                // Gaya header
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Gaya data
                $sheet->getStyle("A2:C" . ($highestRow - 1))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                ]);

                // Outline dan style untuk total
                $sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Gaya baris total penjualan
                $sheet->mergeCells("A{$highestRow}:B{$highestRow}");
                $sheet->getStyle("A{$highestRow}:C{$highestRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}