<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Return empty collection for template with sample data
     */
    public function collection()
    {
        // Return 3 baris contoh data
        return collect([
            [
                'John Doe',
                'john.doe@example.com',
                'password123',
                'admin',
                '081234567890',
            ],
            [
                'Jane Smith',
                'jane.smith@example.com',
                'password123',
                'guru',
                '081234567891',
            ],
            [
                'Robert Johnson',
                'robert.johnson@example.com',
                'password123',
                'kepala_sekolah',
                '081234567892',
            ],
        ]);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Password',
            'Peran (admin/guru/kepala_sekolah)',
            'No. Telepon',
        ];
    }

    /**
     * Style the header row
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
            ],
        ];
    }
}
