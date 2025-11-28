<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * Return all siswa data
     */
    public function collection()
    {
        return Siswa::with('kelas')->orderBy('nama_siswa')->get();
    }

    /**
     * Map data for each row
     */
    public function map($siswa): array
    {
        return [
            $siswa->nama_siswa,
            $siswa->nis ?? '-',
            $siswa->nisn ?? '-',
            $siswa->jenis_kelamin,
            optional($siswa->kelas)->nama_kelas ?? '-',
            $siswa->nama_orang_tua ?? '-',
            $siswa->no_telepon_orang_tua ?? '-',
            $siswa->finger_id ?? '-',
        ];
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
            'Jenis Kelamin',
            'Kelas',
            'Nama Orang Tua',
            'No. Telepon Orang Tua',
            'Finger ID',
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
