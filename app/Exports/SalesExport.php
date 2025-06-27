<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SalesDataSheet;
use App\Exports\Sheets\TopSellingItemsSheet;
use App\Exports\Sheets\DetailRincianSheet;

class SalesExport implements WithMultipleSheets
{
    protected $salesData, $totalPrice, $topSellingItems, $detailRincian;

    public function __construct($salesData, $totalPrice, $topSellingItems, $detailRincian)
    {
        $this->salesData = $salesData;
        $this->totalPrice = $totalPrice;
        $this->topSellingItems = $topSellingItems;
        $this->detailRincian = $detailRincian;
    }

    public function sheets(): array
    {
        return [
            new SalesDataSheet($this->salesData, $this->totalPrice),
            new TopSellingItemsSheet($this->topSellingItems),
            new DetailRincianSheet($this->detailRincian),
        ];
    }
}
