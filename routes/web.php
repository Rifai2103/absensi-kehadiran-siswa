<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\AbsensiHarianController;
use App\Http\Controllers\KelasSayaController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// Halaman Welcome (simple landing)
Route::view('/welcome', 'welcome')->name('welcome');

// Redirect root ke welcome
Route::get('/', function () {
    return redirect()->route('login');
});

// API untuk IoT (tanpa auth session; gunakan throttle)
Route::prefix('api/v1')->group(function () {
    Route::post('/absensi', [\App\Http\Controllers\Api\IoTAbsensiController::class, 'store'])
        ->middleware('throttle:60,1')
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
        ->name('api.absensi.store');
});

// Auth routes (SB Admin 2)
Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Route index dashboard dengan auto-redirect berdasarkan role via controller (butuh auth)
Route::middleware(['auth', 'role:admin,guru,kepala_sekolah'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Profil (semua role)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Grup dashboard dengan middleware role spesifik
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

        // CRUD Admin & Kepala Sekolah
        Route::resource('kelas', KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->names('kelas');

        // Siswa Import/Export routes (harus sebelum resource)
        Route::get('siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');
        Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::get('siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
        Route::resource('siswa', SiswaController::class)->names('siswa');

        Route::resource('perangkat', PerangkatController::class)->names('perangkat');

        // Users Import/Export routes (harus sebelum resource)
        Route::get('users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::resource('users', UserController::class)->names('users');
    });

    // Absensi dapat diakses Admin, Guru, dan Kepala Sekolah
    Route::middleware(['role:admin,guru,kepala_sekolah'])->group(function () {
        Route::resource('absensi-harian', AbsensiHarianController::class)->names('absensi-harian');
        // Rekap Absensi (semua role di grup ini)
        Route::get('/rekap-absensi', [RekapAbsensiController::class, 'index'])->name('rekap.index');
        Route::get('/rekap-absensi/export', [RekapAbsensiController::class, 'export'])->name('rekap.export');
        // Ringkasan per Kelas (agregat)
        Route::get('/rekap-kelas', [\App\Http\Controllers\RekapKelasController::class, 'index'])->name('rekap.kelas');
        Route::get('/rekap-kelas/export', [\App\Http\Controllers\RekapKelasController::class, 'export'])->name('rekap.kelas.export');
        Route::get('/rekap-kelas/{kelas}/detail', [\App\Http\Controllers\RekapKelasController::class, 'detail'])->name('rekap.kelas.detail');
    });

    // Rekap Semester dapat diakses Admin dan Kepala Sekolah
    Route::middleware(['role:admin,kepala_sekolah'])->group(function () {
        Route::get('/rekap-semester', [\App\Http\Controllers\RekapSemesterController::class, 'index'])->name('rekap.semester');
        Route::get('/rekap-semester/export', [\App\Http\Controllers\RekapSemesterController::class, 'export'])->name('rekap.semester.export');
    });

    Route::middleware(['role:guru'])->group(function () {
        Route::get('/dashboard/guru', [DashboardController::class, 'guru'])->name('dashboard.guru');
        // Kelas Saya (hanya guru)
        Route::get('/kelas-saya', [KelasSayaController::class, 'index'])->name('kelas-saya.index');
    });

    Route::middleware(['role:kepala_sekolah'])->group(function () {
        Route::get('/dashboard/kepala-sekolah', [DashboardController::class, 'kepalaSekolah'])->name('dashboard.kepala');
    });
});
