@extends('layouts.app')
@section('title', 'Dashboard Peserta')

@push('css')
<style>
.step-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.07); overflow:hidden; }
.step-item {
    display:flex; align-items:center; gap:16px;
    padding:14px 20px; border-bottom:1px solid #f5f5f5;
}
.step-item:last-child { border-bottom:none; }
.step-num {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px;
}
.step-done  { background:#e8faf3; color:#1cc88a; }
.step-aktif { background:#fff3cd; color:#f6a821; }
.step-todo  { background:#f0f0f0; color:#aaa; }
.step-body  { flex:1; }
.step-body strong { font-size:13px; display:block; color:#333; }
.step-body small  { color:#aaa; font-size:11px; }

.scard { border:none; border-radius:14px; padding:20px; color:#fff; box-shadow:0 4px 20px rgba(0,0,0,.12); display:flex; align-items:center; gap:16px; }
.scard-icon { width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.scard-label { font-size:11px; opacity:.8; letter-spacing:1px; text-transform:uppercase; margin-bottom:3px; }
.scard-value { font-size:22px; font-weight:800; line-height:1; }
.sc-blue  { background:linear-gradient(135deg,#667eea,#764ba2); }
.sc-green { background:linear-gradient(135deg,#43e97b,#38f9d7); }
.sc-green .scard-icon { background:rgba(0,0,0,.1); }
.sc-red   { background:linear-gradient(135deg,#cc0000,#8b0000); }
.sc-orange{ background:linear-gradient(135deg,#f093fb,#f5576c); }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Dashboard</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
    </div>
</div>

{{-- Rekrutmen Aktif Banner --}}
@if($rekrutmenAktif)
<div class="alert mb-4" style="background:linear-gradient(135deg,#cc0000,#8b0000);color:#fff;border:none;border-radius:14px;padding:18px 24px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div style="font-size:11px;opacity:.75;letter-spacing:1px;">REKRUTMEN AKTIF</div>
            <div style="font-weight:800;font-size:16px;">{{ $rekrutmenAktif->nama }}</div>
            <div style="font-size:12px;opacity:.8;margin-top:4px;">
                <i class="fas fa-calendar mr-1"></i>
                {{ $rekrutmenAktif->tanggal_buka->translatedFormat('d M Y') }} –
                {{ $rekrutmenAktif->tanggal_tutup->translatedFormat('d M Y') }}
            </div>
        </div>
        @if(!$pendaftaran)
        <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm px-4"
                    style="border-radius:10px;font-weight:700;color:#cc0000;"
                    onclick="return confirm('Yakin ingin mendaftar ke {{ addslashes($rekrutmenAktif->nama) }}?')">
                <i class="fas fa-paper-plane mr-1"></i> Daftar Sekarang
            </button>
        </form>
        @else
        <span class="badge badge-light px-3 py-2" style="font-size:12px;color:#cc0000;border-radius:10px;">
            <i class="fas fa-check mr-1"></i> Sudah Mendaftar
        </span>
        @endif
    </div>
</div>
@endif

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-blue">
            <div class="scard-icon"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="scard-label">Dokumen</div>
                <div class="scard-value">{{ collect($dok)->filter()->count() }}/6</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-{{ $profilLengkap ? 'green' : 'orange' }}">
            <div class="scard-icon"><i class="fas fa-user{{ $profilLengkap ? '-check' : '' }}"></i></div>
            <div>
                <div class="scard-label">Profil</div>
                <div class="scard-value">{{ $profilLengkap ? 'Lengkap' : 'Belum' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-red">
            <div class="scard-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="scard-label">Status Daftar</div>
                <div class="scard-value" style="font-size:15px;">
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
    <div class="col-6 col-xl-3 mb-3">
        <div class="scard sc-{{ $pendaftaran?->is_lulus_final ? 'green' : 'blue' }}">
            <div class="scard-icon"><i class="fas fa-trophy"></i></div>
            <div>
                <div class="scard-label">Lulus Final</div>
                <div class="scard-value">{{ $pendaftaran?->is_lulus_final ? 'Ya!' : '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Langkah-langkah --}}
    <div class="col-lg-5 mb-3">
        <div class="card step-card">
            <div class="card-header" style="border-bottom:1px solid #f0f0f0;">
                <h4><i class="fas fa-tasks mr-2 text-primary"></i>Langkah Pendaftaran</h4>
            </div>
            @php $dokLengkap = collect($dok)->filter()->count() === 6; @endphp
            <div class="card-body p-0">

                {{-- Step 1: Profil --}}
                <div class="step-item">
                    <div class="step-num {{ $profilLengkap ? 'step-done' : 'step-aktif' }}">
                        {!! $profilLengkap ? '<i class="fas fa-check"></i>' : '1' !!}
                    </div>
                    <div class="step-body">
                        <strong>Lengkapi Biodata</strong>
                        <small>Data diri, sekolah, tinggi & berat badan</small>
                    </div>
                    <a href="{{ route('peserta.profil.edit') }}"
                       class="btn btn-sm btn-{{ $profilLengkap ? 'outline-success' : 'warning' }}"
                       style="border-radius:8px;font-size:11px;">
                        {{ $profilLengkap ? 'Edit' : 'Isi Sekarang' }}
                    </a>
                </div>

                {{-- Step 2: Dokumen --}}
                <div class="step-item">
                    <div class="step-num {{ $dokLengkap ? 'step-done' : ($profilLengkap ? 'step-aktif' : 'step-todo') }}">
                        {!! $dokLengkap ? '<i class="fas fa-check"></i>' : '2' !!}
                    </div>
                    <div class="step-body">
                        <strong>Upload Dokumen</strong>
                        <small>{{ collect($dok)->filter()->count() }}/6 dokumen terupload</small>
                    </div>
                    <a href="{{ route('peserta.dokumen.index') }}"
                       class="btn btn-sm btn-{{ $dokLengkap ? 'outline-success' : 'primary' }}"
                       style="border-radius:8px;font-size:11px;">
                        {{ $dokLengkap ? 'Lihat' : 'Upload' }}
                    </a>
                </div>

                {{-- Step 3: Daftar --}}
                <div class="step-item">
                    <div class="step-num {{ $pendaftaran ? 'step-done' : (($profilLengkap && $dokLengkap) ? 'step-aktif' : 'step-todo') }}">
                        {!! $pendaftaran ? '<i class="fas fa-check"></i>' : '3' !!}
                    </div>
                    <div class="step-body">
                        <strong>Kirim Pendaftaran</strong>
                        <small>{{ $pendaftaran ? 'No: '.$pendaftaran->no_pendaftaran : 'Belum mendaftar' }}</small>
                    </div>
                    @if($pendaftaran)
                        <a href="{{ route('peserta.pendaftaran.show', $pendaftaran) }}"
                           class="btn btn-sm btn-outline-success" style="border-radius:8px;font-size:11px;">
                            Detail
                        </a>
                    @elseif($profilLengkap && $dokLengkap && $rekrutmenAktif)
                        <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger"
                                    style="border-radius:8px;font-size:11px;"
                                    onclick="return confirm('Yakin ingin mendaftar?')">
                                Daftar
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Step 4: Seleksi --}}
                <div class="step-item">
                    <div class="step-num {{ $pendaftaran?->is_lulus_final ? 'step-done' : 'step-todo' }}">
                        {!! $pendaftaran?->is_lulus_final ? '<i class="fas fa-check"></i>' : '4' !!}
                    </div>
                    <div class="step-body">
                        <strong>Ikuti Seleksi</strong>
                        <small>Lihat pengumuman hasil seleksi</small>
                    </div>
                    <a href="{{ route('peserta.hasil.index') }}"
                       class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:11px;">
                        Lihat
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Status Pendaftaran --}}
    <div class="col-lg-7 mb-3">
        <div class="card step-card h-100">
            <div class="card-header" style="border-bottom:1px solid #f0f0f0;">
                <h4><i class="fas fa-info-circle mr-2 text-primary"></i>Status Pendaftaran</h4>
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

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('peserta.pendaftaran.show', $pendaftaran) }}"
                       class="btn btn-danger btn-sm" style="border-radius:10px;">
                        <i class="fas fa-file-alt mr-1"></i> Detail Pendaftaran
                    </a>
                    @if(in_array($pendaftaran->status, ['diverifikasi','lulus']))
                    <a href="{{ route('peserta.hasil.index') }}"
                       class="btn btn-outline-primary btn-sm" style="border-radius:10px;">
                        <i class="fas fa-trophy mr-1"></i> Lihat Hasil Seleksi
                    </a>
                    @endif
                </div>

                @else
                <div class="text-center py-4">
                    <i class="fas fa-clipboard fa-3x d-block mb-3 text-muted" style="opacity:.4;"></i>
                    <p class="text-muted">Kamu belum mendaftar.</p>
                    @if($profilLengkap && collect($dok)->filter()->count() === 6 && $rekrutmenAktif)
                    <form action="{{ route('peserta.pendaftaran.apply', $rekrutmenAktif) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-5"
                                style="border-radius:10px;"
                                onclick="return confirm('Yakin ingin mendaftar ke {{ addslashes($rekrutmenAktif->nama) }}?')">
                            <i class="fas fa-paper-plane mr-2"></i> Daftar Sekarang
                        </button>
                    </form>
                    @else
                    <p class="text-muted small">Lengkapi profil dan dokumen terlebih dahulu.</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection