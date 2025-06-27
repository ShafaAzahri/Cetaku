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
        $rowNum = 2; // karena header di A1-H1
        $grouped = collect($this->detailRincian)->groupBy('pesanan_id');

        foreach ($grouped as $pesananId => $items) {
            $first = $items->first();
            $subtotal = 0;
            $startRow = $rowNum;

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
                    $item->total_harga,
                ];
                $subtotal += $item->total_harga;
                $rowNum++;
            }

            // Simpan merge info untuk baris berulang
            $this->rowTracking[] = [
                'row_start' => $startRow,
                'row_end' => $rowNum - 1,
            ];

            // Tambahkan subtotal
            $data[] = ['', '', '', '', 'Sub total', '', '', '', $subtotal];
            $this->rowTracking[] = ['subtotal' => $rowNum];
            $rowNum++;
            $grandTotal += $subtotal;
            $no++;
        }

        $data[] = ['', '', '', '', 'Total Keseluruhan', '', '', '', $grandTotal];
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
                $headers = ['No', 'ID Pesanan', 'Tanggal Pesanan', 'Nama Pemesan', 'Nama Produk', 'Harga Satuan', 'Jumlah', 'Biaya Desain', 'Total Harga'];
                $sheet->insertNewRowBefore(1, 1);
                foreach ($headers as $index => $header) {
                    $cell = chr(65 + $index) . '1'; // A1, B1, C1, ...
                    $sheet->setCellValue($cell, $header);
                }


                $highestRow = $sheet->getHighestRow();

                // Gaya untuk header
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Gaya baris subtotal dan total
                foreach ($this->rowTracking as $track) {
                    if (isset($track['subtotal'])) {
                        $row = $track['subtotal'];
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        ]);
                    }
                    if (isset($track['total'])) {
                        $row = $track['total'];
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8CBAD']],
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        ]);
                    }

                    // Merge sel identik untuk no, id, tanggal, pemesan
                    if (isset($track['row_start'])) {
                        $start = $track['row_start'];
                        $end = $track['row_end'];

                        if ($start !== $end) {
                            foreach (['A', 'B', 'C', 'D'] as $col) {
                                $sheet->mergeCells("{$col}{$start}:{$col}{$end}");
                                $sheet->getStyle("{$col}{$start}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                            }
                        }
                    }
                }

                // Auto wrap text
                $sheet->getStyle("A1:I{$highestRow}")->getAlignment()->setWrapText(true);
                // Set kolom A dan B (No dan ID Pesanan) menjadi align left
                $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C2:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            },
        ];
    }
}
