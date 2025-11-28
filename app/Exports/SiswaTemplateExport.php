<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Return empty collection for template
     */
    public function collection()
    {
        // Return 3 baris contoh data
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $contohKelas = $kelasList->first();

        return collect([
            [
                'Rizky Maulana',
                '20244A001',
                '0034567801',
                'L',
                optional($contohKelas)->nama_kelas ?? '4A - SDN 03 Kebayoran',
                'Agus Maulana',
                '081234567890',
            ],
            [
                'Dewi Lestari',
                '20244A002',
                '0034567802',
                'P',
                optional($contohKelas)->nama_kelas ?? '4A - SDN 03 Kebayoran',
                'Rina Lestari',
                '081234567891',
            ],
            [
                'Bima Pratama',
                '20244A003',
                '0034567803',
                'L',
                optional($contohKelas)->nama_kelas ?? '4A - SDN 03 Kebayoran',
                'Slamet Pratama',
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
            'Nama Siswa',
            'NIS',
            'NISN',
            'Jenis Kelamin (L/P)',
            'Nama Kelas',
            'Nama Orang Tua',
            'No. Telepon Orang Tua',
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
