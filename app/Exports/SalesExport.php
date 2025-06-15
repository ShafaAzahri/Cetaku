<?php
namespace App\Exports;

use App\Models\Pesanan;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Exports\Sheets\SalesDataSheet;

class SalesExport implements WithMultipleSheets
{
    protected $salesData;
    protected $totalPrice;
    protected $topSellingItems;

    public function __construct($salesData, $totalPrice, $topSellingItems)
    {
        $this->salesData = collect($salesData);
        $this->totalPrice = $totalPrice;
        $this->topSellingItems = collect($topSellingItems);
    }

    public function sheets(): array
    {
        return [
            new SalesDataSheet($this->salesData, $this->totalPrice),
            new TopSellingItemsSheet($this->topSellingItems),
        ];
    }
}

// class SalesDataSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
// {
//     protected $salesData;
//     protected $totalPrice;

//     public function __construct($salesData, $totalPrice)
//     {
//         $this->salesData = $salesData;
//         $this->totalPrice = $totalPrice;
//     }

//     public function collection()
//     {
//         // Add title and headings to the sheet
//         $data = [
//             ['DATA LAPORAN HASIL PENJUALAN', '', '', ''], // Title row
//         ];

//         // Add headings row
//         $data[] = ['Tanggal Pesanan', 'Status', 'Total Harga'];

//         // Add sales data
//         foreach ($this->salesData as $sale) {
//             $data[] = [
//                 $sale['tanggal_dipesan'],
//                 $sale['status'],
//                 number_format($sale['total_harga'], 2),
//             ];
//         }

//         // Add the total price row at the end of the collection
//         $data[] = [
//             'Total Penjualan',
//             '',
//             number_format($this->totalPrice, 2),  // Adding total price in the last column
//         ];

//         return collect($data);
//     }

//     public function headings(): array
//     {
//         return [];
//     }

//     public function title(): string
//     {
//         return 'Laporan Penjualan';
//     }
// }

class TopSellingItemsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $topSellingItems;

    public function __construct($topSellingItems)
    {
        $this->topSellingItems = $topSellingItems;
    }

    public function collection()
    {
        $data = [
            ['DATA PRODUK UNGGULAN (TOP SELLING ITEMS)', '', '', ''], // Title row
        ];

        $data[] = ['Nama Produk', 'Total Terjual', 'Total Pendapatan'];

        foreach ($this->topSellingItems as $item) {
            $data[] = [
                $item->nama_item,
                $item->total_terjual,
                number_format($item->total_pendapatan, 2),
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Produk Unggulan';
    }
}
