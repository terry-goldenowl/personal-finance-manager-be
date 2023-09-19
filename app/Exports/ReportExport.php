<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithStyles, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    public function __construct(
        private int $month,
        private int $year,
        private int $userId,
    ) {
    }

    public function collection()
    {

        return Transaction::whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->where('transactions.user_id', $this->userId)
            ->join('categories', 'transactions.category_id', "=", "categories.id")
            ->join('wallets', 'transactions.wallet_id', "=", "wallets.id")
            ->select('date', 'title', 'amount', 'description',  'categories.type', 'categories.name')
            ->get();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Transactions of month ' . $this->month . '/' . $this->year);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' =>
                [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'E9D5FF']
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' =>
                [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'A855F7']
                ],
            ],
            'C' => [
                'font' => ['bold' => true]
            ],
            'E' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF00FF00'], // Green
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Title',
            'Amount',
            'Description',
            'Type',
            'Category'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'D' => 40
        ];
    }
}
