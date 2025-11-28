<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;

class DemoSDN03Seeder extends Seeder
{
    public function run(): void
    {
        // Default guru (gunakan user 'guru' yang sudah ada)
        $guru = User::firstOrCreate(
            ['username' => 'guru'],
            [
                'nama_lengkap' => 'Guru SDN 03 Kebayoran',
                'password_hash' => Hash::make('password'),
                'role' => 'guru',
                'no_telepon' => '081234567899',
            ]
        );

        // Buat kelas 4A & 4B dengan nama sekolah pada nama_kelas untuk pembedaan
        $tahunAjaran = '2024/2025';
        $kelas4A = Kelas::firstOrCreate([
            'nama_kelas' => '4A - SDN 03 Kebayoran',
            'tahun_ajaran' => $tahunAjaran,
            'guru' => $guru->id,
        ]);
        $kelas4B = Kelas::firstOrCreate([
            'nama_kelas' => '4B - SDN 03 Kebayoran',
            'tahun_ajaran' => $tahunAjaran,
            'guru' => $guru->id,
        ]);

        // Daftar nama siswa (10 per kelas)
        // Format NIS: 2024 + Kelas (4A/4B) + Nomor Urut (001-010)
        // Format NISN: 10 digit angka unik nasional
        $siswa4A = [
            ['nama' => 'Rizky Maulana', 'jk' => 'L', 'ortu' => 'Agus Maulana', 'nis' => '20244A001', 'nisn' => '0034567801'],
            ['nama' => 'Dewi Lestari', 'jk' => 'P', 'ortu' => 'Rina Lestari', 'nis' => '20244A002', 'nisn' => '0034567802'],
            ['nama' => 'Bima Pratama', 'jk' => 'L', 'ortu' => 'Slamet Pratama', 'nis' => '20244A003', 'nisn' => '0034567803'],
            ['nama' => 'Aulia Rahma', 'jk' => 'P', 'ortu' => 'Siti Khotimah', 'nis' => '20244A004', 'nisn' => '0034567804'],
            ['nama' => 'Galih Saputra', 'jk' => 'L', 'ortu' => 'Teguh Saputra', 'nis' => '20244A005', 'nisn' => '0034567805'],
            ['nama' => 'Nadia Putri', 'jk' => 'P', 'ortu' => 'Sri Wahyuni', 'nis' => '20244A006', 'nisn' => '0034567806'],
            ['nama' => 'Fauzan Akbar', 'jk' => 'L', 'ortu' => 'Hendra Akbar', 'nis' => '20244A007', 'nisn' => '0034567807'],
            ['nama' => 'Intan Permata', 'jk' => 'P', 'ortu' => 'Riyanto', 'nis' => '20244A008', 'nisn' => '0034567808'],
            ['nama' => 'Yoga Pratama', 'jk' => 'L', 'ortu' => 'Wahyudi', 'nis' => '20244A009', 'nisn' => '0034567809'],
            ['nama' => 'Salsa Amelia', 'jk' => 'P', 'ortu' => 'Nurhayati', 'nis' => '20244A010', 'nisn' => '0034567810'],
        ];
        $siswa4B = [
            ['nama' => 'Rafi Hidayat', 'jk' => 'L', 'ortu' => 'Suharto', 'nis' => '20244B001', 'nisn' => '0034567811'],
            ['nama' => 'Citra Ayu', 'jk' => 'P', 'ortu' => 'Murniati', 'nis' => '20244B002', 'nisn' => '0034567812'],
            ['nama' => 'Dika Ananda', 'jk' => 'L', 'ortu' => 'Surya Ananda', 'nis' => '20244B003', 'nisn' => '0034567813'],
            ['nama' => 'Tiara Kirana', 'jk' => 'P', 'ortu' => 'Kartini', 'nis' => '20244B004', 'nisn' => '0034567814'],
            ['nama' => 'Rendra Wijaya', 'jk' => 'L', 'ortu' => 'Wibowo Wijaya', 'nis' => '20244B005', 'nisn' => '0034567815'],
            ['nama' => 'Aisyah Zahra', 'jk' => 'P', 'ortu' => 'Halimah', 'nis' => '20244B006', 'nisn' => '0034567816'],
            ['nama' => 'Rangga Pradana', 'jk' => 'L', 'ortu' => 'Sutrisno', 'nis' => '20244B007', 'nisn' => '0034567817'],
            ['nama' => 'Nabila Salsabila', 'jk' => 'P', 'ortu' => 'Dewi Sartika', 'nis' => '20244B008', 'nisn' => '0034567818'],
            ['nama' => 'Bagas Saputra', 'jk' => 'L', 'ortu' => 'Sudarsono', 'nis' => '20244B009', 'nisn' => '0034567819'],
            ['nama' => 'Putri Melati', 'jk' => 'P', 'ortu' => 'Yuniarti', 'nis' => '20244B010', 'nisn' => '0034567820'],
        ];

        // Helper untuk membuat siswa dan absensi mingguan
        $buatSiswaDanAbsensi = function(array $list, int $kelasId) {
            $today = Carbon::today();
            foreach ($list as $idx => $info) {
                $siswa = Siswa::firstOrCreate(
                    [
                        'nama_siswa' => $info['nama'],
                        'kelas_id' => $kelasId,
                    ],
                    [
                        'nama_siswa' => $info['nama'],
                        'nis' => $info['nis'] ?? null,
                        'nisn' => $info['nisn'] ?? null,
                        'jenis_kelamin' => $info['jk'],
                        'template_sidik_jari' => null,
                        'nama_orang_tua' => $info['ortu'],
                        'no_telepon_orang_tua' => '08'.str_pad((string)random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                        'kelas_id' => $kelasId,
                    ]
                );

                // Pola default: hadir 5x, izin 1x, alpa 1x dalam 7 hari terakhir
                // Buat variasi berdasarkan indeks siswa
                for ($d = 6; $d >= 0; $d--) {
                    $tanggal = $today->copy()->subDays($d);
                    // Tentukan status
                    $mod = ($idx + $d) % 7;
                    if ($mod === 1) {
                        $status = 'izin';
                    } elseif ($mod === 4) {
                        $status = 'alpa';
                    } else {
                        $status = 'hadir';
                    }

                    $wMasuk = null; $wPulang = null;
                    if ($status === 'hadir') {
                        $wMasuk = Carbon::createFromTime(7, random_int(0, 30), random_int(0, 59))->format('H:i:s');
                        $wPulang = Carbon::createFromTime(14, random_int(0, 30), random_int(0, 59))->format('H:i:s');
                    }

                    // Upsert sederhana: hindari duplikasi per (siswa,tanggal)
                    $exists = DB::table('absensi_harian')
                        ->where('siswa_id', $siswa->id)
                        ->whereDate('tanggal', $tanggal->toDateString())
                        ->exists();
                    if (!$exists) {
                        DB::table('absensi_harian')->insert([
                            'siswa_id' => $siswa->id,
                            'tanggal' => $tanggal->toDateString(),
                            'waktu_masuk' => $wMasuk,
                            'waktu_pulang' => $wPulang,
                            'status_kehadiran' => $status,
                            'keterangan' => null,
                            'perangkat_masuk_id' => null,
                            'perangkat_pulang_id' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        };

        $buatSiswaDanAbsensi($siswa4A, $kelas4A->id);
        $buatSiswaDanAbsensi($siswa4B, $kelas4B->id);
    }
}
