<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TopSellingItemsSheet implements FromCollection, WithHeadings
{
    protected $topSellingItems;

    public function __construct($topSellingItems)
    {
        $this->topSellingItems = $topSellingItems;
    }

    public function collection()
    {
        return collect($this->topSellingItems);
    }

    public function headings(): array
    {
        return ['Nama Item', 'Total Terjual', 'Total Pendapatan'];
    }
}
