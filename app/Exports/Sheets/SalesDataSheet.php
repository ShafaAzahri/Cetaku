<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesDataSheet implements FromCollection, WithTitle, ShouldAutoSize, WithStyles
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

        // Judul Laporan
        $data[] = ['DATA LAPORAN HASIL PENJUALAN', '', '', ''];

        // Heading kolom
        $data[] = ['Tanggal Pesanan', 'Status', 'Total Harga'];

        // Data penjualan
        foreach ($this->salesData as $sale) {
            if (is_object($sale)) {
                $sale = (array) $sale;
            }

            $data[] = [
                $sale['tanggal_dipesan'] ?? '-',
                $sale['status'] ?? '-',
                number_format($sale['total_harga'] ?? 0, 2),
            ];
        }

        // Baris total penjualan
        $data[] = [
            'Total Penjualan',
            '',
            number_format($this->totalPrice, 2),
        ];

        return collect($data);
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Judul
            2 => ['font' => ['bold' => true]], // Heading
            count($this->salesData) + 3 => ['font' => ['bold' => true]], // Total row
        ];
    }
}