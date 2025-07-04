<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DetailRincianSheet implements FromCollection, WithTitle, WithEvents, ShouldAutoSize
{
    protected $detailRincian;
    protected $rowTracking = [];

    public function __construct($detailRincian)
    {
        $this->detailRincian = $detailRincian;
    }

    public function collection()
    {
        $data = [];
        $no = 1;
        $grandTotal = 0;
        $rowNum = 2; // karena header di baris pertama
        $grouped = collect($this->detailRincian)->groupBy('pesanan_id');

        foreach ($grouped as $pesananId => $items) {
            $first = $items->first();
            $subtotal = 0;
            $startRow = $rowNum;
            $ongkir = $first->ongkos_kirim ?? 0;

            foreach ($items as $index => $item) {
                $data[] = [
                    $index === 0 ? $no : '',
                    $index === 0 ? $pesananId : '',
                    $index === 0 ? \Carbon\Carbon::parse($item->tanggal_pesanan)->format('Y-m-d') : '',
                    $index === 0 ? $item->nama_pemesan : '',
                    $item->nama_item,
                    $item->harga_satuan,
                    $item->jumlah,
                    $item->biaya_jasa > 0 ? $item->biaya_jasa : '-',
                    $index === 0 ? ($ongkir > 0 ? $ongkir : '-') : '',
                    $item->total,
                ];
                $subtotal += $item->total;
                $rowNum++;
            }

            $this->rowTracking[] = [
                'row_start' => $startRow,
                'row_end' => $rowNum - 1,
            ];

            // Tambah subtotal
            $data[] = ['Sub total', '', '', '', '', '', '', '', '', $subtotal];
            $this->rowTracking[] = ['subtotal' => $rowNum];
            $rowNum++;

            $grandTotal += $subtotal;
            $no++;
        }

        $data[] = ['Total Keseluruhan', '', '', '', '', '', '', '', '', $grandTotal];
        $this->rowTracking[] = ['total' => $rowNum];

        return collect($data);
    }

    public function title(): string
    {
        return 'Laporan Rincian';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Header
                $headers = [
                    'No', 'ID Pesanan', 'Tanggal Pesanan', 'Nama Pemesan',
                    'Nama Produk', 'Harga Satuan', 'Jumlah', 'Biaya Desain',
                    'Biaya Ongkir', 'Total Harga'
                ];
                $sheet->insertNewRowBefore(1, 1);
                foreach ($headers as $i => $header) {
                    $cell = chr(65 + $i) . '1'; // A1, B1, ...
                    $sheet->setCellValue($cell, $header);
                }

                $highestRow = $sheet->getHighestRow();

                // Style header
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Styling subtotal & total
                foreach ($this->rowTracking as $track) {
                    if (isset($track['subtotal'])) {
                        $row = $track['subtotal'];
                        $sheet->mergeCells("A{$row}:I{$row}");
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);
                    }

                    if (isset($track['total'])) {
                        $row = $track['total'];
                        $sheet->mergeCells("A{$row}:I{$row}");
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8CBAD']],
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);
                    }

                    if (isset($track['row_start'])) {
                        $start = $track['row_start'];
                        $end = $track['row_end'];

                        if ($start !== $end) {
                            foreach (['A', 'B', 'C', 'D', 'I'] as $col) {
                                $sheet->mergeCells("{$col}{$start}:{$col}{$end}");
                                $sheet->getStyle("{$col}{$start}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                            }
                        }
                    }
                }

                // Text align & wrap
                $sheet->getStyle("A1:J{$highestRow}")->getAlignment()->setWrapText(true);
                foreach (['A', 'B', 'C', 'D', 'H'] as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Format Rupiah untuk F, H, I, J
                foreach (['F', 'H', 'I', 'J'] as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0');
                }
            },
        ];
    }
}