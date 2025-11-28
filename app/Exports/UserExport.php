<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Return all user data
     */
    public function collection()
    {
        return User::orderBy('nama_lengkap')->get();
    }

    /**
     * Map data for each row
     */
    public function map($user): array
    {
        return [
            $user->nama_lengkap,
            $user->email,
            ucfirst(str_replace('_', ' ', $user->role)),
            $user->no_telepon ?? '-',
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'Peran',
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
