@extends('dashboard.layout')
@section('page_title', 'Siswa Saya')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Search Bar -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pencarian Siswa</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('kelas-saya.index') }}">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="search" class="form-label">Cari siswa berdasarkan nama, NIS, NISN, atau kelas:</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Masukkan kata kunci pencarian...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            @if($search ?? false)
                                <a href="{{ route('kelas-saya.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Orang Tua</th>
                        <th>No. Telepon Orang Tua</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($items ?? []) as $siswa)
                        <tr>
                            <td>{{ $siswa->nama_siswa }}</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                            <td>{{ $siswa->nisn ?? '-' }}</td>
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>
                                @switch($siswa->jenis_kelamin)
                                    @case('L')
                                        <span class="badge badge-primary">Laki-laki</span>
                                        @break
                                    @case('P')
                                        <span class="badge badge-danger">Perempuan</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">-</span>
                                @endswitch
                            </td>
                            <td>{{ $siswa->nama_orang_tua ?? '-' }}</td>
                            <td>{{ $siswa->no_telepon_orang_tua ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if($search ?? false)
                                    Tidak ada siswa yang ditemukan untuk pencarian "<strong>{{ $search }}</strong>".
                                @else
                                    Belum ada siswa yang diampu.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($items ?? null, 'links'))
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
                    @if($search ?? false)
                        <span class="text-info">(hasil pencarian untuk "{{ $search }}")</span>
                    @endif
                </div>
                <div>{{ $items->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection