<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapSemesterExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $rekap;
    protected $semester;
    protected $tahun;
    protected $startDate;
    protected $endDate;

    public function __construct($rekap, $semester, $tahun, $startDate, $endDate)
    {
        $this->rekap = $rekap;
        $this->semester = $semester;
        $this->tahun = $tahun;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Return collection
     */
    public function collection()
    {
        return $this->rekap;
    }

    /**
     * Map data for each row
     */
    public function map($row): array
    {
        return [
            $row->nama_siswa,
            $row->nis ?? '-',
            $row->nisn ?? '-',
            $row->nama_kelas ?? '-',
            $row->total_hadir,
            $row->total_izin,
            $row->total_sakit,
            $row->total_alpa,
            $row->total_terlambat,
            $row->total_kehadiran,
            $row->total_hari_kerja,
            $row->persentase_kehadiran . '%',
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
            'Kelas',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa',
            'Terlambat',
            'Total Kehadiran',
            'Total Hari Kerja',
            'Persentase Kehadiran',
        ];
    }

    /**
     * Style the header row
     */
    public function styles(Worksheet $sheet)
    {
        // Add title rows
        $sheet->insertNewRowBefore(1, 3);

        $semesterText = 'Semester ' . $this->semester;
        $periodeText = 'Periode: ' . $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y');

        $sheet->setCellValue('A1', 'REKAP ABSENSI SEMESTER');
        $sheet->setCellValue('A2', $semesterText . ' Tahun Ajaran ' . $this->tahun . '/' . ($this->tahun + 1));
        $sheet->setCellValue('A3', $periodeText);

        // Merge cells for title
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        return [
            // Title rows
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['size' => 10],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            // Header row
            4 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Rekap Semester ' . $this->semester;
    }
}
