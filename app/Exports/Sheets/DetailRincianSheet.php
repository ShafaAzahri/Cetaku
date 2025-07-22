<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;


class DetailRincianSheet implements FromCollection, WithTitle, WithEvents, ShouldAutoSize, WithHeadings
{
    protected $detailRincian;
    protected $adminName;
    protected $startDate;
    protected $endDate;
    protected $rowTracking = [];

    public function __construct($detailRincian, $adminName, $startDate, $endDate)
    {
        $this->detailRincian = $detailRincian;
        $this->adminName = $adminName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            'No', 'ID Pesanan', 'Tanggal Pesanan', 'Nama Pemesan',
            'Nama Produk', 'Harga Satuan', 'Jumlah', 'Biaya Desain',
            'Biaya Ongkir', 'Total Harga'
        ];
    }

    public function collection()
    {
        $data = [];
        $no = 1;
        $rowNum = 2; // baris pertama data (setelah heading)
        $grandTotal = 0;
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
                    $index === 0 ? Carbon::parse($item->tanggal_pesanan)->format('Y-m-d') : '',
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

            $this->rowTracking[] = ['row_start' => $startRow, 'row_end' => $rowNum - 1];

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
                

                // ============ Judul Audit ============
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A1', 'LAPORAN AUDIT RINCIAN PENJUALAN WEB CETAKU');
                $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y'));

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Geser semua tracking 2 baris ke bawah
                foreach ($this->rowTracking as &$track) {
                    foreach ($track as $k => $v) {
                        $track[$k] = $v + 2;
                    }
                }
                unset($track);

                $highestRow = $sheet->getHighestRow();

                // ============ Styling Header ============
                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDEBF7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ============ Styling Data ============
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

                unset($track);

                // Format rupiah & center
                foreach (['F', 'H', 'I', 'J'] as $col) {
                    $sheet->getStyle("{$col}4:{$col}{$highestRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                }
                foreach (['A', 'B', 'C', 'D', 'H'] as $col) {
                    $sheet->getStyle("{$col}4:{$col}{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ============ Footer Tanda Tangan ============
                $footerRow = $highestRow + 3;

                // Merge masing-masing baris tanda tangan
                $sheet->mergeCells("H{$footerRow}:J{$footerRow}"); // Tanggal
                $sheet->mergeCells("H" . ($footerRow + 1) . ":J" . ($footerRow + 1)); // Jabatan
                $sheet->mergeCells("H" . ($footerRow + 2) . ":J" . ($footerRow + 2)); // Kosong 1
                $sheet->mergeCells("H" . ($footerRow + 3) . ":J" . ($footerRow + 3)); // Kosong 2
                $sheet->mergeCells("H" . ($footerRow + 4) . ":J" . ($footerRow + 4)); // Kosong 3
                $sheet->mergeCells("H" . ($footerRow + 5) . ":J" . ($footerRow + 5)); // Nama

                // Set isi masing-masing
                $sheet->setCellValue("H{$footerRow}", 'Semarang, ' . Carbon::now()->locale('id')->isoFormat('D MMMM Y'));
                $sheet->setCellValue("H" . ($footerRow + 1), 'Superadmin');
                $sheet->setCellValue("H" . ($footerRow + 2), '');
                $sheet->setCellValue("H" . ($footerRow + 3), '');
                $sheet->setCellValue("H" . ($footerRow + 4), '');
                $sheet->setCellValue("H" . ($footerRow + 5), $this->adminName);

                // Rata tengah
                $sheet->getStyle("H{$footerRow}:J" . ($footerRow + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.3);
                $sheet->getPageMargins()->setLeft(0.3);
                $sheet->getPageMargins()->setBottom(0.5);

            },
        ];
    }
}