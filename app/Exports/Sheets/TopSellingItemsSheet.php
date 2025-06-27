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


class TopSellingItemsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Header
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Hilangkan border isi
                $sheet->getStyle("A2:C{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                ]);

                // Tambahkan outline
                $sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }



    protected $topSellingItems;

    public function __construct($topSellingItems)
    {
        $this->topSellingItems = $topSellingItems;
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
}
