<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapSemesterController extends Controller
{
    /**
     * Menampilkan rekap semester
     */
    public function index(Request $request)
    {
        // Default semester: semester saat ini
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Semester 1: Juli - Desember (7-12)
        // Semester 2: Januari - Juni (1-6)
        $defaultSemester = $currentMonth >= 7 ? 1 : 2;
        $defaultTahun = $currentMonth >= 7 ? $currentYear : $currentYear;

        $semester = $request->input('semester', $defaultSemester);
        $tahun = $request->input('tahun', $defaultTahun);
        $kelasId = $request->input('kelas_id');
        $user = auth()->user();

        // Tentukan range tanggal berdasarkan semester
        if ($semester == 1) {
            // Semester 1: Juli - Desember
            $startDate = Carbon::create($tahun, 7, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
        } else {
            // Semester 2: Januari - Juni
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 6, 30)->endOfDay();
        }

        // Query rekap per siswa
        $query = DB::table('siswa as s')
            ->leftJoin('kelas as k', 's.kelas_id', '=', 'k.id')
            ->leftJoin('absensi_harian as a', function($join) use ($startDate, $endDate) {
                $join->on('s.id', '=', 'a.siswa_id')
                     ->whereBetween('a.tanggal', [$startDate, $endDate]);
            })
            ->select(
                's.id as siswa_id',
                's.nama_siswa',
                's.nis',
                's.nisn',
                'k.nama_kelas',
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'hadir' THEN 1 END) as total_hadir"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'izin' THEN 1 END) as total_izin"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'sakit' THEN 1 END) as total_sakit"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'alpa' THEN 1 END) as total_alpa"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'terlambat' THEN 1 END) as total_terlambat"),
                DB::raw("COUNT(a.id) as total_kehadiran")
            )
            ->groupBy('s.id', 's.nama_siswa', 's.nis', 's.nisn', 'k.nama_kelas');

        // Role-based filtering
        if ($user->role === 'guru') {
            // Teachers can only see students from their supervised classes
            $query->where('k.guru', $user->id);
        }

        // Filter by kelas if selected
        if ($kelasId) {
            $query->where('s.kelas_id', $kelasId);
        }

        $rekap = $query->orderBy('k.nama_kelas')->orderBy('s.nama_siswa')->get();

        // Hitung persentase kehadiran
        $rekap = $rekap->map(function($item) use ($startDate, $endDate) {
            // Hitung jumlah hari kerja (Senin-Jumat) dalam periode
            $hariKerja = $this->hitungHariKerja($startDate, $endDate);

            $item->total_hari_kerja = $hariKerja;
            $item->persentase_kehadiran = $hariKerja > 0
                ? round(($item->total_hadir / $hariKerja) * 100, 1)
                : 0;

            return $item;
        });

        // Get kelas list for filter (role-based)
        if ($user->role === 'guru') {
            // Teachers only see their supervised classes
            $kelasList = Kelas::where('guru', $user->id)->orderBy('nama_kelas')->get();
        } else {
            // Admin and kepala sekolah see all classes
            $kelasList = Kelas::orderBy('nama_kelas')->get();
        }

        return view('rekap.semester', [
            'title' => 'Rekap Semester',
            'page_title' => 'Rekap Absensi Semester',
            'rekap' => $rekap,
            'semester' => $semester,
            'tahun' => $tahun,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Hitung jumlah hari kerja (Senin-Jumat) dalam periode
     */
    private function hitungHariKerja($startDate, $endDate)
    {
        $hariKerja = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // 1 = Senin, 5 = Jumat
            if ($current->dayOfWeek >= 1 && $current->dayOfWeek <= 5) {
                $hariKerja++;
            }
            $current->addDay();
        }

        return $hariKerja;
    }

    /**
     * Export rekap semester ke Excel
     */
    public function export(Request $request)
    {
        $semester = $request->input('semester', 1);
        $tahun = $request->input('tahun', now()->year);
        $kelasId = $request->input('kelas_id');
        $user = auth()->user();

        // Tentukan range tanggal
        if ($semester == 1) {
            $startDate = Carbon::create($tahun, 7, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 12, 31)->endOfDay();
        } else {
            $startDate = Carbon::create($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::create($tahun, 6, 30)->endOfDay();
        }

        // Query sama seperti index
        $query = DB::table('siswa as s')
            ->leftJoin('kelas as k', 's.kelas_id', '=', 'k.id')
            ->leftJoin('absensi_harian as a', function($join) use ($startDate, $endDate) {
                $join->on('s.id', '=', 'a.siswa_id')
                     ->whereBetween('a.tanggal', [$startDate, $endDate]);
            })
            ->select(
                's.id as siswa_id',
                's.nama_siswa',
                's.nis',
                's.nisn',
                'k.nama_kelas',
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'hadir' THEN 1 END) as total_hadir"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'izin' THEN 1 END) as total_izin"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'sakit' THEN 1 END) as total_sakit"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'alpa' THEN 1 END) as total_alpa"),
                DB::raw("COUNT(CASE WHEN a.status_kehadiran = 'terlambat' THEN 1 END) as total_terlambat"),
                DB::raw("COUNT(a.id) as total_kehadiran")
            )
            ->groupBy('s.id', 's.nama_siswa', 's.nis', 's.nisn', 'k.nama_kelas');

        // Role-based filtering
        if ($user->role === 'guru') {
            // Teachers can only export students from their supervised classes
            $query->where('k.guru', $user->id);
        }

        if ($kelasId) {
            $query->where('s.kelas_id', $kelasId);
        }

        $rekap = $query->orderBy('k.nama_kelas')->orderBy('s.nama_siswa')->get();

        // Hitung persentase
        $hariKerja = $this->hitungHariKerja($startDate, $endDate);
        $rekap = $rekap->map(function($item) use ($hariKerja) {
            $item->total_hari_kerja = $hariKerja;
            $item->persentase_kehadiran = $hariKerja > 0
                ? round(($item->total_hadir / $hariKerja) * 100, 1)
                : 0;
            return $item;
        });

        // Export using Excel
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RekapSemesterExport($rekap, $semester, $tahun, $startDate, $endDate),
            'rekap_semester_' . $semester . '_tahun_' . $tahun . '.xlsx'
        );
    }
}
