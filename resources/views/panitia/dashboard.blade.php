@extends('layouts.app')
@section('title', 'Dashboard Panitia')

@push('css')
<style>
.scard { border:none; border-radius:14px; padding:20px; color:#fff;
         box-shadow:0 4px 20px rgba(0,0,0,.12);
         display:flex; align-items:center; gap:16px; }
.scard-icon { width:48px; height:48px; border-radius:12px;
              background:rgba(255,255,255,.22);
              display:flex; align-items:center; justify-content:center;
              font-size:20px; flex-shrink:0; }
.scard-label { font-size:11px; opacity:.8; letter-spacing:1px;
               text-transform:uppercase; margin-bottom:3px; }
.scard-value { font-size:28px; font-weight:800; line-height:1; }
.sc-blue   { background:linear-gradient(135deg,#667eea,#764ba2); }
.sc-orange { background:linear-gradient(135deg,#f6a821,#f5576c); }
.sc-green  { background:linear-gradient(135deg,#43e97b,#38f9d7); }
.sc-green .scard-icon { background:rgba(0,0,0,.1); }
.sc-red    { background:linear-gradient(135deg,#cc0000,#8b0000); }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Dashboard Panitia</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
    </div>
</div>

{{-- Banner Rekrutmen Aktif --}}
@if($rekrutmenAktif)
<div class="alert mb-4" style="background:linear-gradient(135deg,#cc0000,#8b0000);color:#fff;border:none;border-radius:14px;padding:18px 24px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div style="font-size:11px;opacity:.75;letter-spacing:1px;">REKRUTMEN AKTIF</div>
            <div style="font-weight:800;font-size:16px;">{{ $rekrutmenAktif->nama }}</div>
            <div style="font-size:12px;opacity:.8;margin-top:4px;">
                <i class="fas fa-calendar mr-1"></i>
                {{ $rekrutmenAktif->tanggal_buka->format('d M Y') }} –
                {{ $rekrutmenAktif->tanggal_tutup->format('d M Y') }}
            </div>
        </div>
        <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-light btn-sm px-4"
           style="border-radius:10px;font-weight:700;color:#cc0000;">
            <i class="fas fa-tasks mr-1"></i> Kelola Verifikasi
        </a>
    </div>
</div>
@else
<div class="alert alert-warning mb-4">
    <i class="fas fa-exclamation-triangle mr-1"></i>
    Tidak ada rekrutmen aktif saat ini.
</div>
@endif

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-blue">
            <div class="scard-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="scard-label">Total Pendaftar</div>
                <div class="scard-value">{{ $totalPendaftar }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-orange">
            <div class="scard-icon"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="scard-label">Menunggu Verifikasi</div>
                <div class="scard-value">{{ $menungguVerifikasi }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-blue">
            <div class="scard-icon"><i class="fas fa-star"></i></div>
            <div>
                <div class="scard-label">Sudah Dinilai</div>
                <div class="scard-value">{{ $sudahDinilai }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-green">
            <div class="scard-icon"><i class="fas fa-trophy"></i></div>
            <div>
                <div class="scard-label">Lulus Final</div>
                <div class="scard-value">{{ $lulusFinal }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Tabel Pendaftar Menunggu --}}
    <div class="col-lg-8 mb-3">
        <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="border-radius:14px 14px 0 0;">
                <h4 class="mb-0"><i class="fas fa-clock mr-2 text-warning"></i>Menunggu Verifikasi</h4>
                <a href="{{ route('panitia.verifikasi.index', ['status' => 'menunggu']) }}"
                   class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
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
                                <td><strong>{{ $p->nama_lengkap }}</strong></td>
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
    <div class="col-lg-4 mb-3">
        <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
            <div class="card-header" style="border-radius:14px 14px 0 0;">
                <h4 class="mb-0"><i class="fas fa-bolt mr-2 text-warning"></i>Menu Cepat</h4>
            </div>
            <div class="card-body p-2">
                <a href="{{ route('panitia.verifikasi.index') }}"
                   class="btn btn-block btn-outline-primary mb-2" style="border-radius:10px;text-align:left;">
                    <i class="fas fa-clipboard-check mr-2"></i> Verifikasi Administrasi
                    @if($menungguVerifikasi > 0)
                    <span class="badge badge-warning float-right">{{ $menungguVerifikasi }}</span>
                    @endif
                </a>
                <a href="{{ route('panitia.seleksi.index') }}"
                   class="btn btn-block btn-outline-info mb-2" style="border-radius:10px;text-align:left;">
                    <i class="fas fa-star mr-2"></i> Input Nilai Seleksi
                </a>
                <a href="{{ route('panitia.hasil.index') }}"
                   class="btn btn-block btn-outline-success" style="border-radius:10px;text-align:left;">
                    <i class="fas fa-trophy mr-2"></i> Lihat Hasil Akhir
                </a>
            </div>
        </div>
    </div>
</div>
@endsection