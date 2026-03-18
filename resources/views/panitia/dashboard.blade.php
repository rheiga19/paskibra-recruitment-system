@extends('layouts.app')
@section('title', 'Dashboard Panitia')

@section('content')
<div class="section-header">
    <h1>Dashboard Panitia</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
    </div>
</div>

{{-- Banner Rekrutmen Aktif --}}
@if($rekrutmenAktif)
<div class="card card-primary mb-4">
    <div class="card-header">
        <h4><i class="fas fa-bullhorn mr-2"></i>Rekrutmen Aktif</h4>
        <div class="card-header-action">
            <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-tasks mr-1"></i> Kelola Verifikasi
            </a>
        </div>
    </div>
    <div class="card-body">
        <p class="font-weight-bold mb-1">{{ $rekrutmenAktif->nama }}</p>
        <p class="text-muted mb-0">
            <i class="fas fa-calendar mr-1"></i>
            {{ $rekrutmenAktif->tanggal_buka->format('d M Y') }} –
            {{ $rekrutmenAktif->tanggal_tutup->format('d M Y') }}
        </p>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-1"></i>
    Tidak ada rekrutmen aktif saat ini.
</div>
@endif

{{-- Stat Cards --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total Pendaftar</h4></div>
                <div class="card-body">{{ $totalPendaftar }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Menunggu Verifikasi</h4></div>
                <div class="card-body">{{ $menungguVerifikasi }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-info">
                <i class="fas fa-star"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Sudah Dinilai</h4></div>
                <div class="card-body">{{ $sudahDinilai }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Lulus Final</h4></div>
                <div class="card-body">{{ $lulusFinal }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Tabel Pendaftar Menunggu --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-clock mr-2"></i>Menunggu Verifikasi</h4>
                <div class="card-header-action">
                    <a href="{{ route('panitia.verifikasi.index', ['status' => 'menunggu']) }}"
                       class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>No. Daftar</th>
                                <th>Nama</th>
                                <th>JK</th>
                                <th>Sekolah</th>
                                <th>Tgl Daftar</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaranMenunggu as $p)
                            <tr>
                                <td><small class="text-muted">{{ $p->no_pendaftaran ?? '-' }}</small></td>
                                <td><b>{{ $p->nama_lengkap }}</b></td>
                                <td>{{ $p->jenis_kelamin === 'L' ? '♂' : '♀' }}</td>
                                <td><small>{{ $p->nama_sekolah }}</small></td>
                                <td><small>{{ $p->created_at->format('d M Y') }}</small></td>
                                <td>
                                    <a href="{{ route('panitia.verifikasi.show', $p) }}"
                                       class="btn btn-sm btn-primary btn-icon" title="Verifikasi">
                                        <i class="fas fa-check"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x d-block mb-2 text-success"></i>
                                    Semua pendaftaran sudah diverifikasi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Cepat --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-bolt mr-2"></i>Menu Cepat</h4>
            </div>
            <div class="card-body">
                <a href="{{ route('panitia.verifikasi.index') }}"
                   class="btn btn-block btn-outline-primary mb-2 text-left">
                    <i class="fas fa-clipboard-check mr-2"></i> Verifikasi Administrasi
                    @if($menungguVerifikasi > 0)
                    <span class="badge badge-warning float-right">{{ $menungguVerifikasi }}</span>
                    @endif
                </a>
                <a href="{{ route('panitia.seleksi.index') }}"
                   class="btn btn-block btn-outline-info mb-2 text-left">
                    <i class="fas fa-star mr-2"></i> Input Nilai Seleksi
                </a>
                <a href="{{ route('panitia.hasil.index') }}"
                   class="btn btn-block btn-outline-success text-left">
                    <i class="fas fa-trophy mr-2"></i> Lihat Hasil Akhir
                </a>
            </div>
        </div>
    </div>
</div>

@endsection