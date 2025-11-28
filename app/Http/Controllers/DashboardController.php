<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $role = $user->role;

        if ($role === 'guru') {
            return $this->guru();
        }
        if ($role === 'kepala_sekolah') {
            return $this->kepalaSekolah();
        }
        return $this->admin();
    }

    public function admin()
    {
        try {
            // Get attendance data for the last 7 days
            $attendanceData = DB::table('absensi_harian')
                ->select(
                    'tanggal',
                    'status_kehadiran',
                    DB::raw('COUNT(*) as count')
                )
                ->whereDate('tanggal', '>=', Carbon::now()->subDays(6))
                ->groupBy('tanggal', 'status_kehadiran')
                ->orderBy('tanggal')
                ->get();

            // Prepare data for Chart.js
            $dates = [];
            $hadirData = [];
            $izinData = [];
            $sakitData = [];
            $alpaData = [];
            $terlambatData = [];

            // Initialize arrays for last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $dates[] = Carbon::now()->subDays($i)->format('d/m');
                $hadirData[$date] = 0;
                $izinData[$date] = 0;
                $sakitData[$date] = 0;
                $alpaData[$date] = 0;
                $terlambatData[$date] = 0;
            }

            // Fill data from database
            foreach ($attendanceData as $record) {
                $date = $record->tanggal;
                if (isset($hadirData[$date])) {
                    switch ($record->status_kehadiran) {
                        case 'hadir':
                            $hadirData[$date] = $record->count;
                            break;
                        case 'izin':
                            $izinData[$date] = $record->count;
                            break;
                        case 'sakit':
                            $sakitData[$date] = $record->count;
                            break;
                        case 'alpa':
                            $alpaData[$date] = $record->count;
                            break;
                        case 'terlambat':
                            $terlambatData[$date] = $record->count;
                            break;
                    }
                }
            }

            $data = [
                'title' => 'Dashboard Admin',
                'totalSiswa' => DB::table('siswa')->count(),
                'totalKelas' => DB::table('kelas')->count(),
                'totalPerangkat' => DB::table('perangkat')->count(),
                'totalAbsensiToday' => DB::table('absensi_harian')->whereDate('tanggal', Carbon::today())->count(),
                'attendanceChart' => [
                    'dates' => $dates,
                    'hadir' => array_values($hadirData),
                    'izin' => array_values($izinData),
                    'sakit' => array_values($sakitData),
                    'alpa' => array_values($alpaData),
                    'terlambat' => array_values($terlambatData),
                ],
            ];
        } catch (QueryException $e) {
            $data = [
                'title' => 'Dashboard Admin',
                'totalSiswa' => 0,
                'totalKelas' => 0,
                'totalPerangkat' => 0,
                'totalAbsensiToday' => 0,
                'attendanceChart' => [
                    'dates' => [],
                    'hadir' => [],
                    'izin' => [],
                    'sakit' => [],
                    'alpa' => [],
                    'terlambat' => [],
                ],
            ];
        }
        return view('dashboard.admin', $data);
    }

    public function guru()
    {
        $user = Auth::user();
        $waliId = $user?->id ?? 0;
        try {
            // NOTE: sesuaikan kolom foreign key di tabel kelas jika berbeda
            $kelasIds = DB::table('kelas')->where('guru', $waliId)->pluck('id');
            $siswaCount = DB::table('siswa')->whereIn('kelas_id', $kelasIds)->count();
            $absensiToday = DB::table('absensi_harian')
                ->whereDate('tanggal', Carbon::today())
                ->whereIn('siswa_id', function ($q) use ($kelasIds) {
                    $q->select('id')->from('siswa')->whereIn('kelas_id', $kelasIds);
                })->count();

            $data = [
                'title' => 'Dashboard Guru',
                'totalSiswaWali' => $siswaCount,
                'totalKelasDiwalikan' => count($kelasIds),
                'totalAbsensiTodayWali' => $absensiToday,
            ];
        } catch (QueryException $e) {
            $data = [
                'title' => 'Dashboard Guru',
                'totalSiswaWali' => 0,
                'totalKelasDiwalikan' => 0,
                'totalAbsensiTodayWali' => 0,
            ];
        }
        return view('dashboard.guru', $data);
    }

    public function kepalaSekolah()
    {
        try {
            // Get attendance data for the last 7 days
            $attendanceData = DB::table('absensi_harian')
                ->select(
                    'tanggal',
                    'status_kehadiran',
                    DB::raw('COUNT(*) as count')
                )
                ->whereDate('tanggal', '>=', Carbon::now()->subDays(6))
                ->groupBy('tanggal', 'status_kehadiran')
                ->orderBy('tanggal')
                ->get();

            // Prepare data for Chart.js
            $dates = [];
            $hadirData = [];
            $izinData = [];
            $sakitData = [];
            $alpaData = [];
            $terlambatData = [];

            // Initialize arrays for last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $dates[] = Carbon::now()->subDays($i)->format('d/m');
                $hadirData[$date] = 0;
                $izinData[$date] = 0;
                $sakitData[$date] = 0;
                $alpaData[$date] = 0;
                $terlambatData[$date] = 0;
            }

            // Fill data from database
            foreach ($attendanceData as $record) {
                $date = $record->tanggal;
                if (isset($hadirData[$date])) {
                    switch ($record->status_kehadiran) {
                        case 'hadir':
                            $hadirData[$date] = $record->count;
                            break;
                        case 'izin':
                            $izinData[$date] = $record->count;
                            break;
                        case 'sakit':
                            $sakitData[$date] = $record->count;
                            break;
                        case 'alpa':
                            $alpaData[$date] = $record->count;
                            break;
                        case 'terlambat':
                            $terlambatData[$date] = $record->count;
                            break;
                    }
                }
            }

            $data = [
                'title' => 'Dashboard Kepala Sekolah',
                'totalSiswa' => DB::table('siswa')->count(),
                'totalKelas' => DB::table('kelas')->count(),
                'totalPerangkat' => DB::table('perangkat')->count(),
                'totalAbsensiToday' => DB::table('absensi_harian')->whereDate('tanggal', Carbon::today())->count(),
                'attendanceChart' => [
                    'dates' => $dates,
                    'hadir' => array_values($hadirData),
                    'izin' => array_values($izinData),
                    'sakit' => array_values($sakitData),
                    'alpa' => array_values($alpaData),
                    'terlambat' => array_values($terlambatData),
                ],
            ];
        } catch (QueryException $e) {
            $data = [
                'title' => 'Dashboard Kepala Sekolah',
                'totalSiswa' => 0,
                'totalKelas' => 0,
                'totalPerangkat' => 0,
                'totalAbsensiToday' => 0,
                'attendanceChart' => [
                    'dates' => [],
                    'hadir' => [],
                    'izin' => [],
                    'sakit' => [],
                    'alpa' => [],
                    'terlambat' => [],
                ],
            ];
        }
        return view('dashboard.kepala_sekolah', $data);
    }
}