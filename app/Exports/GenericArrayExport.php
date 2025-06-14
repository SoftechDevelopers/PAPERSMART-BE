<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GenericArrayExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents
{
    protected $data;
    protected $headings;
    protected $title;

    public function __construct(array $data, array $headings, string $title)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function array(): array
    {
        $titleRow = [$this->title];
        for ($i = 1; $i < count($this->headings); $i++) {
            $titleRow[] = '';
        }

        return array_merge([
            $titleRow,
            $this->headings,
        ], $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->data) + 2;
        $colCount = count($this->headings);
        $lastColumn = Coordinate::stringFromColumnIndex($colCount);

        $range = "A1:{$lastColumn}{$rowCount}";

        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle("A2:{$lastColumn}2")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D3D3D3');

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $colCount = count($this->headings);
                $lastColumn = Coordinate::stringFromColumnIndex($colCount);

                $event->sheet->mergeCells("A1:{$lastColumn}1");
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getRowDimension(1)->setRowHeight(24);
            }
        ];
    }
}
