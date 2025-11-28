<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $importedCount = 0;
    protected $skippedCount = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cari kelas berdasarkan nama
        $kelas = Kelas::where('nama_kelas', $row['nama_kelas'])->first();

        if (!$kelas) {
            $this->skippedCount++;
            Log::warning("Kelas tidak ditemukan: " . $row['nama_kelas']);
            return null;
        }

        // Cek apakah siswa dengan NIS atau NISN sudah ada
        $existingByNis = !empty($row['nis']) ? Siswa::where('nis', $row['nis'])->first() : null;
        $existingByNisn = !empty($row['nisn']) ? Siswa::where('nisn', $row['nisn'])->first() : null;

        if ($existingByNis || $existingByNisn) {
            $this->skippedCount++;
            Log::warning("Siswa sudah ada dengan NIS/NISN: " . ($row['nis'] ?? $row['nisn']));
            return null;
        }

        $this->importedCount++;

        return new Siswa([
            'nama_siswa' => $row['nama_siswa'],
            'nis' => $row['nis'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'jenis_kelamin' => strtoupper($row['jenis_kelamin_lp']),
            'nama_orang_tua' => $row['nama_orang_tua'] ?? null,
            'no_telepon_orang_tua' => $row['no_telepon_orang_tua'] ?? null,
            'kelas_id' => $kelas->id,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|size:10',
            'jenis_kelamin_lp' => 'required|in:L,P,l,p',
            'nama_kelas' => 'required|string',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_telepon_orang_tua' => 'nullable|string|max:20',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'jenis_kelamin_lp.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin_lp.in' => 'Jenis kelamin harus L atau P',
            'nama_kelas.required' => 'Nama kelas wajib diisi',
            'nisn.size' => 'NISN harus 10 digit',
        ];
    }

    /**
     * Get imported count
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
