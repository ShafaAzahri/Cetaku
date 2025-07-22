<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SalesDataSheet;
use App\Exports\Sheets\TopSellingItemsSheet;
use App\Exports\Sheets\DetailRincianSheet;

class SalesExport implements WithMultipleSheets
{
    protected $salesData;
    protected $totalPrice;
    protected $topSellingItems;
    protected $detailRincian;
    protected $adminName;
    protected $startDate;
    protected $endDate;

    public function __construct($salesData, $totalPrice, $topSellingItems, $detailRincian, $adminName, $startDate, $endDate)
    {
        $this->salesData = $salesData;
        $this->totalPrice = $totalPrice;
        $this->topSellingItems = $topSellingItems;
        $this->detailRincian = $detailRincian;
        $this->adminName = $adminName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new SalesDataSheet(
                $this->salesData,
                $this->totalPrice,
                $this->startDate,
                $this->endDate,
                $this->adminName
            ),
            new TopSellingItemsSheet(
                $this->topSellingItems,
                $this->adminName,
                $this->startDate,
                $this->endDate
            ),
            new DetailRincianSheet(
                $this->detailRincian,
                $this->adminName,
                $this->startDate,
                $this->endDate
            ),
        ];
    }
}