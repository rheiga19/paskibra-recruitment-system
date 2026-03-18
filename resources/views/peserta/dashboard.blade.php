@extends('layouts.app')
@section('title', 'Dashboard Peserta')

@section('content')
<div class="section-header">
    <h1>Dashboard</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
    </div>
</div>

{{-- Rekrutmen Aktif Banner --}}
@if($rekrutmenAktif)
<div class="card card-primary mb-4">
    <div class="card-header">
        <h4><i class="fas fa-bullhorn mr-2"></i>Rekrutmen Aktif</h4>
        <div class="card-header-action">
            @if(!$pendaftaran)
            <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin ingin mendaftar ke {{ addslashes($rekrutmenAktif->nama) }}?')">
                    <i class="fas fa-paper-plane mr-1"></i> Daftar Sekarang
                </button>
            </form>
            @else
            <span class="badge badge-success px-3 py-2">
                <i class="fas fa-check mr-1"></i> Sudah Mendaftar
            </span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <p class="mb-1"><strong>{{ $rekrutmenAktif->nama }}</strong></p>
        <p class="text-muted mb-0">
            <i class="fas fa-calendar mr-1"></i>
            {{ $rekrutmenAktif->tanggal_buka->translatedFormat('d M Y') }} –
            {{ $rekrutmenAktif->tanggal_tutup->translatedFormat('d M Y') }}
        </p>
    </div>
</div>
@endif

{{-- Stat Cards (Stisla style) --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Dokumen</h4>
                </div>
                <div class="card-body">
                    {{ collect($dok)->filter()->count() }}/6
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon {{ $profilLengkap ? 'bg-success' : 'bg-warning' }}">
                <i class="fas fa-user{{ $profilLengkap ? '-check' : '' }}"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Profil</h4>
                </div>
                <div class="card-body">
                    {{ $profilLengkap ? 'Lengkap' : 'Belum' }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Status Daftar</h4>
                </div>
                <div class="card-body">
                    @if($pendaftaran)
                        @php $st=['menunggu'=>'Menunggu','diverifikasi'=>'Diterima','ditolak'=>'Ditolak','lulus'=>'Lulus','tidak_lulus'=>'Tdk Lulus'] @endphp
                        {{ $st[$pendaftaran->status] ?? $pendaftaran->status }}
                    @else
                        Belum Daftar
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon {{ $pendaftaran?->is_lulus_final ? 'bg-success' : 'bg-primary' }}">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Lulus Final</h4>
                </div>
                <div class="card-body">
                    {{ $pendaftaran?->is_lulus_final ? 'Ya!' : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Langkah Pendaftaran --}}
    <div class="col-lg-5 mb-3">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tasks mr-2"></i>Langkah Pendaftaran</h4>
            </div>
            @php $dokLengkap = collect($dok)->filter()->count() === 6; @endphp
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <tbody>

                            {{-- Step 1: Profil --}}
                            <tr>
                                <td style="width:40px;">
                                    @if($profilLengkap)
                                        <div class="badge badge-success">1</div>
                                    @else
                                        <div class="badge badge-warning">1</div>
                                    @endif
                                </td>
                                <td>
                                    <b>Lengkapi Biodata</b>
                                    <br><small class="text-muted">Data diri, sekolah, tinggi & berat badan</small>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('peserta.profil.edit') }}"
                                       class="btn btn-sm btn-{{ $profilLengkap ? 'outline-success' : 'warning' }}">
                                        {{ $profilLengkap ? 'Edit' : 'Isi Sekarang' }}
                                    </a>
                                </td>
                            </tr>

                            {{-- Step 2: Dokumen --}}
                            <tr>
                                <td>
                                    @if($dokLengkap)
                                        <div class="badge badge-success">2</div>
                                    @elseif($profilLengkap)
                                        <div class="badge badge-warning">2</div>
                                    @else
                                        <div class="badge badge-secondary">2</div>
                                    @endif
                                </td>
                                <td>
                                    <b>Upload Dokumen</b>
                                    <br><small class="text-muted">{{ collect($dok)->filter()->count() }}/6 dokumen terupload</small>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('peserta.dokumen.index') }}"
                                       class="btn btn-sm btn-{{ $dokLengkap ? 'outline-success' : 'primary' }}">
                                        {{ $dokLengkap ? 'Lihat' : 'Upload' }}
                                    </a>
                                </td>
                            </tr>

                            {{-- Step 3: Daftar --}}
                            <tr>
                                <td>
                                    @if($pendaftaran)
                                        <div class="badge badge-success">3</div>
                                    @elseif($profilLengkap && $dokLengkap)
                                        <div class="badge badge-warning">3</div>
                                    @else
                                        <div class="badge badge-secondary">3</div>
                                    @endif
                                </td>
                                <td>
                                    <b>Kirim Pendaftaran</b>
                                    <br><small class="text-muted">{{ $pendaftaran ? 'No: '.$pendaftaran->no_pendaftaran : 'Belum mendaftar' }}</small>
                                </td>
                                <td class="text-right">
                                    @if($pendaftaran)
                                        <a href="{{ route('peserta.pendaftaran.show', $pendaftaran) }}"
                                           class="btn btn-sm btn-outline-success">Detail</a>
                                    @elseif($profilLengkap && $dokLengkap && $rekrutmenAktif)
                                        <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin mendaftar?')">Daftar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            {{-- Step 4: Seleksi --}}
                            <tr>
                                <td>
                                    @if($pendaftaran?->is_lulus_final)
                                        <div class="badge badge-success">4</div>
                                    @else
                                        <div class="badge badge-secondary">4</div>
                                    @endif
                                </td>
                                <td>
                                    <b>Ikuti Seleksi</b>
                                    <br><small class="text-muted">Lihat pengumuman hasil seleksi</small>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('peserta.hasil.index') }}"
                                       class="btn btn-sm btn-outline-primary">Lihat</a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Pendaftaran --}}
    <div class="col-lg-7 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h4><i class="fas fa-info-circle mr-2"></i>Status Pendaftaran</h4>
            </div>
            <div class="card-body">
                @if($pendaftaran)
                @php
                    $sc = ['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger','lulus'=>'success','tidak_lulus'=>'danger'];
                    $sl = ['menunggu'=>'Menunggu Verifikasi','diverifikasi'=>'Lolos Administrasi','ditolak'=>'Ditolak','lulus'=>'Lulus','tidak_lulus'=>'Tidak Lulus'];
                @endphp
                <table class="table table-sm table-borderless mb-3">
                    <tr>
                        <td class="text-muted" style="width:40%">No. Pendaftaran</td>
                        <td><strong>{{ $pendaftaran->no_pendaftaran }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Rekrutmen</td>
                        <td>{{ $pendaftaran->rekrutmen->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Daftar</td>
                        <td>{{ $pendaftaran->created_at->translatedFormat('d F Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge badge-{{ $sc[$pendaftaran->status] ?? 'secondary' }}">
                                {{ $sl[$pendaftaran->status] ?? $pendaftaran->status }}
                            </span>
                        </td>
                    </tr>
                    @if($pendaftaran->catatan_verifikasi)
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td><small class="text-danger">{{ $pendaftaran->catatan_verifikasi }}</small></td>
                    </tr>
                    @endif
                    @if($pendaftaran->nilai_akhir ?? false)
                    <tr>
                        <td class="text-muted">Nilai Akhir</td>
                        <td><strong class="text-primary">{{ number_format($pendaftaran->nilai_akhir, 2) }}</strong></td>
                    </tr>
                    @endif
                </table>

                <div class="d-flex flex-wrap" style="gap:8px;">
                    <a href="{{ route('peserta.pendaftaran.show', $pendaftaran) }}"
                       class="btn btn-danger btn-sm">
                        <i class="fas fa-file-alt mr-1"></i> Detail Pendaftaran
                    </a>
                    @if(in_array($pendaftaran->status, ['diverifikasi','lulus']))
                    <a href="{{ route('peserta.pendaftaran.kartu', $pendaftaran) }}"
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-id-card mr-1"></i> Cetak Kartu Seleksi
                    </a>
                    <a href="{{ route('peserta.hasil.index') }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-trophy mr-1"></i> Lihat Hasil Seleksi
                    </a>
                    @endif
                </div>

                @else
                <div class="empty-state" data-height="300">
                    <div class="empty-state-icon">
                        <i class="fas fa-clipboard"></i>
                    </div>
                    <h2>Belum Mendaftar</h2>
                    <p class="lead">Kamu belum mendaftar ke rekrutmen apapun.</p>
                    @if($profilLengkap && collect($dok)->filter()->count() === 6 && $rekrutmenAktif)
                    <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger mt-3"
                                onclick="return confirm('Yakin ingin mendaftar ke {{ addslashes($rekrutmenAktif->nama) }}?')">
                            <i class="fas fa-paper-plane mr-2"></i> Daftar Sekarang
                        </button>
                    </form>
                    @else
                    <p class="text-muted small mt-2">Lengkapi profil dan dokumen terlebih dahulu.</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection