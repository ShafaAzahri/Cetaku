<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use DateTime;
use DateTimeZone;

class SalesDataSheet implements FromCollection, WithTitle, ShouldAutoSize, WithStyles
{
    protected $salesData;
    protected $totalPrice;

    public function __construct($salesData, $totalPrice)
    {
        $this->salesData = $salesData;
        $this->totalPrice = $totalPrice;

        // dd($salesData);
    }

    public function collection()
    {
        $data = [];

        // Judul Laporan
        $data[] = ['DATA LAPORAN HASIL PENJUALAN', '', '', ''];

        // Heading kolom
        $data[] = ['Tanggal Pesanan', 'Status', 'Total Harga'];

        // Data penjualan

        // dd($data);

        foreach ($this->salesData as $sale) {
            if (is_object($sale)) {
                $sale = (array) $sale;              
            }

            $utcTime = $sale['tanggal_dipesan'];

                // Buat objek DateTime dari string waktu UTC
                $date = new DateTime($utcTime, new DateTimeZone('UTC'));

                // Ubah zona waktu ke Asia/Jakarta (WIB)
                $date->setTimezone(new DateTimeZone('Asia/Jakarta'));

                // Format output waktu sesuai kebutuhan
                $bulanIndonesia = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                $hari = $date->format('d');
                $bulan = (int) $date->format('m');
                $tahun = $date->format('Y');

                $waktu_dipesan = $hari . ' ' . $bulanIndonesia[$bulan] . ' ' . $tahun;

            $data[] = [
                
                $waktu_dipesan ?? '-',
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