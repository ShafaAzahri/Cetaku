<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesDataSheet implements FromCollection, WithHeadings
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

        // Add sales data
        foreach ($this->salesData as $sale) {
            $data[] = [
                $sale->tanggal_dipesan,
                $sale->status,
                number_format($sale->total_harga, 2),
            ];
        }

        // Add total price row at the end
        $data[] = [
            'Total Penjualan',
            '',
            number_format($this->totalPrice, 2),
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return ['Tanggal Pesanan', 'Status', 'Total Harga'];
    }
}
