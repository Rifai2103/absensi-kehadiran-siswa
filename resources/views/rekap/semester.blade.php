@extends('dashboard.layout')
@section('page_title', $page_title ?? $title ?? 'Rekap Semester')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <!-- <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1> -->
        <a href="{{ route('rekap.semester.export', ['semester' => $semester, 'tahun' => $tahun, 'kelas_id' => $kelasId]) }}"
           class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Excel
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Rekap</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('rekap.semester') }}" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="semester" class="mr-2">Semester:</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester 1 (Juli - Desember)</option>
                        <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester 2 (Januari - Juni)</option>
                    </select>
                </div>

                <div class="form-group mr-3 mb-2">
                    <label for="tahun" class="mr-2">Tahun:</label>
                    <select name="tahun" id="tahun" class="form-control">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}/{{ $y + 1 }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group mr-3 mb-2">
                    <label for="kelas_id" class="mr-2">Kelas:</label>
                    <select name="kelas_id" id="kelas_id" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-filter"></i> Tampilkan
                </button>
            </form>

            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Periode: <strong>{{ $startDate->format('d F Y') }}</strong> s/d <strong>{{ $endDate->format('d F Y') }}</strong>
                </small>
            </div>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Rekap Absensi Semester {{ $semester }} Tahun {{ $tahun }}/{{ $tahun + 1 }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Nama Siswa</th>
                            <th rowspan="2" class="align-middle">NIS</th>
                            <th rowspan="2" class="align-middle">NISN</th>
                            <th rowspan="2" class="align-middle">Kelas</th>
                            <th colspan="5" class="text-center">Status Kehadiran</th>
                            <th rowspan="2" class="align-middle">Total</th>
                            <th rowspan="2" class="align-middle">Hari Kerja</th>
                            <th rowspan="2" class="align-middle">Persentase</th>
                        </tr>
                        <tr>
                            <th class="text-center bg-success text-white">Hadir</th>
                            <th class="text-center bg-info text-white">Izin</th>
                            <th class="text-center bg-warning text-white">Sakit</th>
                            <th class="text-center bg-danger text-white">Alpa</th>
                            <th class="text-center bg-secondary text-white">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row->nama_siswa }}</td>
                                <td>{{ $row->nis ?? '-' }}</td>
                                <td>{{ $row->nisn ?? '-' }}</td>
                                <td>{{ $row->nama_kelas ?? '-' }}</td>
                                <td class="text-center">{{ $row->total_hadir }}</td>
                                <td class="text-center">{{ $row->total_izin }}</td>
                                <td class="text-center">{{ $row->total_sakit }}</td>
                                <td class="text-center">{{ $row->total_alpa }}</td>
                                <td class="text-center">{{ $row->total_terlambat }}</td>
                                <td class="text-center font-weight-bold">{{ $row->total_kehadiran }}</td>
                                <td class="text-center">{{ $row->total_hari_kerja }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $row->persentase_kehadiran >= 80 ? 'success' : ($row->persentase_kehadiran >= 60 ? 'warning' : 'danger') }}">
                                        {{ $row->persentase_kehadiran }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">
                                    Tidak ada data rekap untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rekap->count() > 0)
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">TOTAL:</td>
                                <td class="text-center">{{ $rekap->sum('total_hadir') }}</td>
                                <td class="text-center">{{ $rekap->sum('total_izin') }}</td>
                                <td class="text-center">{{ $rekap->sum('total_sakit') }}</td>
                                <td class="text-center">{{ $rekap->sum('total_alpa') }}</td>
                                <td class="text-center">{{ $rekap->sum('total_terlambat') }}</td>
                                <td class="text-center">{{ $rekap->sum('total_kehadiran') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($rekap->count() > 0)
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        <strong>Keterangan:</strong>
                        <span class="badge badge-success">≥ 80%</span> Sangat Baik |
                        <span class="badge badge-warning">60-79%</span> Cukup |
                        <span class="badge badge-danger">< 60%</span> Kurang
                    </small>
                </div>
            @endif
        </div>
    </div>
@endsection
