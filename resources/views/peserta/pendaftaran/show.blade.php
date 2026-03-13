@extends('layouts.app')
@section('title', 'Detail Pendaftaran')

@push('css')
<style>
.info-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.07); }
.info-card .card-header { background:#fff; border-bottom:1px solid #f0f0f0; border-radius:14px 14px 0 0 !important; }
.dok-chip {
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 14px; border-radius:10px; font-size:12px; font-weight:600; margin:4px;
}
.dok-ok  { background:#e8faf3; color:#1cc88a; border:1px solid #c3f0dc; }
.dok-no  { background:#fff5f5; color:#e74a3b; border:1px solid #fcd0cc; }
.timeline-step { display:flex; gap:16px; align-items:flex-start; padding:12px 0; border-bottom:1px dashed #f0f0f0; }
.timeline-step:last-child { border-bottom:none; }
.ts-dot { width:32px; height:32px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:13px; margin-top:2px; }
.ts-done  { background:#e8faf3; color:#1cc88a; }
.ts-aktif { background:#fff3cd; color:#f6a821; }
.ts-todo  { background:#f0f0f0; color:#bbb; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Detail Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Pendaftaran</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@php
    $sc = ['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger','lulus'=>'success','tidak_lulus'=>'danger'];
    $sl = ['menunggu'=>'Menunggu Verifikasi','diverifikasi'=>'Lolos Administrasi','ditolak'=>'Ditolak','lulus'=>'Lulus Final','tidak_lulus'=>'Tidak Lulus'];
@endphp

<div class="row">
    <div class="col-lg-8">

        {{-- Header Kartu Pendaftaran --}}
        <div class="card info-card mb-3" style="border-left:5px solid #cc0000;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div style="font-size:11px;color:#aaa;letter-spacing:1px;">NO. PENDAFTARAN</div>
                        <div style="font-size:24px;font-weight:900;color:#cc0000;">{{ $pendaftaran->no_pendaftaran }}</div>
                        <div class="text-muted small">{{ $pendaftaran->rekrutmen->nama ?? '-' }}</div>
                        <div class="text-muted small">Didaftarkan: {{ $pendaftaran->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-{{ $sc[$pendaftaran->status] ?? 'secondary' }} px-3 py-2" style="font-size:13px;border-radius:10px;">
                            {{ $sl[$pendaftaran->status] ?? $pendaftaran->status }}
                        </span>
                        @if(in_array($pendaftaran->status, ['diverifikasi', 'lulus']))
                        <div class="mt-2">
                            <a href="{{ route('peserta.pendaftaran.kartu', $pendaftaran) }}"
                               class="btn btn-sm btn-outline-danger" style="border-radius:8px;">
                                <i class="fas fa-print mr-1"></i> Cetak Kartu
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @if($pendaftaran->catatan_verifikasi)
                <div class="alert alert-warning mt-3 mb-0 py-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    <small><strong>Catatan Panitia:</strong> {{ $pendaftaran->catatan_verifikasi }}</small>
                </div>
                @endif
            </div>
        </div>

        {{-- Data Diri Snapshot --}}
        <div class="card info-card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-user mr-2 text-primary"></i>Data yang Didaftarkan</h4>
                <small class="text-muted">Data ini adalah snapshot saat mendaftar dan tidak bisa diubah</small>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted pl-3" style="width:40%">Nama Lengkap</td><td><strong>{{ $pendaftaran->nama_lengkap }}</strong></td></tr>
                    <tr><td class="text-muted pl-3">NIK</td><td>{{ $pendaftaran->nik }}</td></tr>
                    <tr><td class="text-muted pl-3">Jenis Kelamin</td><td>{{ $pendaftaran->jenis_kelamin == 'L' ? '♂ Putra' : '♀ Putri' }}</td></tr>
                    <tr><td class="text-muted pl-3">Tempat, Tgl Lahir</td>
                        <td>{{ $pendaftaran->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted pl-3">No. HP</td><td>{{ $pendaftaran->no_hp }}</td></tr>
                    <tr><td class="text-muted pl-3">Alamat</td><td>{{ $pendaftaran->alamat_lengkap }}</td></tr>
                    <tr><td class="text-muted pl-3">Sekolah</td>
                        <td>{{ $pendaftaran->nama_sekolah }} &mdash; {{ $pendaftaran->jenjang }} Kelas {{ $pendaftaran->kelas }}</td></tr>
                    <tr><td class="text-muted pl-3">Nilai Rata-rata</td><td>{{ $pendaftaran->nilai_rata }}</td></tr>
                    <tr><td class="text-muted pl-3">Tinggi / Berat</td><td>{{ $pendaftaran->tinggi_badan }} cm / {{ $pendaftaran->berat_badan }} kg</td></tr>
                    <tr><td class="text-muted pl-3">Orang Tua / Wali</td>
                        <td>{{ $pendaftaran->nama_ortu }} ({{ $pendaftaran->hubungan_ortu }}) &mdash; {{ $pendaftaran->hp_ortu }}</td></tr>
                    @if($pendaftaran->prestasi)
                    <tr><td class="text-muted pl-3">Prestasi</td><td>{{ $pendaftaran->prestasi }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Dokumen Snapshot --}}
        <div class="card info-card mb-3">
            <div class="card-header">
                <h4><i class="fas fa-folder-open mr-2 text-primary"></i>Dokumen Terlampir</h4>
            </div>
            <div class="card-body">
                @foreach($jenisList as $key => $label)
                @php $dok = $pendaftaran->dokumen->firstWhere('jenis', $key); @endphp
                <span class="dok-chip {{ $dok ? 'dok-ok' : 'dok-no' }}">
                    <i class="fas fa-{{ $dok ? 'check-circle' : 'times-circle' }}"></i>
                    {{ $label }}
                </span>
                @endforeach
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Dokumen disimpan saat mendaftar. Untuk melihat detail, hubungi panitia.
                    </small>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        {{-- Timeline Status --}}
        <div class="card info-card mb-3">
            <div class="card-header"><h4><i class="fas fa-stream mr-2 text-primary"></i>Alur Proses</h4></div>
            <div class="card-body">
                @php
                    $steps = [
                        [
                            'label' => 'Pendaftaran Dikirim',
                            'sub'   => $pendaftaran->created_at->format('d M Y'),
                            'done'  => true,
                        ],
                        [
                            'label' => 'Verifikasi Administrasi',
                            'sub'   => in_array($pendaftaran->status, ['diverifikasi','lulus','tidak_lulus']) ? 'Selesai' : 'Menunggu',
                            'done'  => in_array($pendaftaran->status, ['diverifikasi','lulus','tidak_lulus']),
                            'aktif' => $pendaftaran->status === 'menunggu',
                        ],
                        [
                            'label' => 'Proses Seleksi',
                            'sub'   => in_array($pendaftaran->status, ['lulus','tidak_lulus']) ? 'Selesai' : 'Menunggu verifikasi',
                            'done'  => in_array($pendaftaran->status, ['lulus','tidak_lulus']),
                            'aktif' => $pendaftaran->status === 'diverifikasi',
                        ],
                        [
                            'label' => 'Pengumuman Final',
                            'sub'   => $pendaftaran->is_lulus_final ? '🎉 Selamat, kamu lulus!' : 'Belum diumumkan',
                            'done'  => (bool) $pendaftaran->is_lulus_final,
                        ],
                    ];
                @endphp
                @foreach($steps as $s)
                <div class="timeline-step">
                    <div class="ts-dot {{ $s['done'] ? 'ts-done' : (($s['aktif'] ?? false) ? 'ts-aktif' : 'ts-todo') }}">
                        <i class="fas fa-{{ $s['done'] ? 'check' : (($s['aktif'] ?? false) ? 'clock' : 'circle') }}"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;">{{ $s['label'] }}</div>
                        <div style="font-size:11px;color:#aaa;">{{ $s['sub'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Nilai Akhir --}}
        @if($pendaftaran->nilai_akhir)
        <div class="card info-card mb-3 text-center">
            <div class="card-body py-4">
                <div style="font-size:11px;color:#aaa;letter-spacing:1px;">NILAI AKHIR SELEKSI</div>
                <div style="font-size:44px;font-weight:900;color:#cc0000;line-height:1.1;">
                    {{ number_format($pendaftaran->nilai_akhir, 2) }}
                </div>
                <a href="{{ route('peserta.hasil.index') }}" class="btn btn-sm btn-outline-primary mt-2" style="border-radius:8px;">
                    <i class="fas fa-trophy mr-1"></i> Detail Hasil Seleksi
                </a>
            </div>
        </div>
        @endif

        {{-- Lulus Final --}}
        @if($pendaftaran->is_lulus_final)
        <div class="card info-card mb-3" style="border:2px solid #1cc88a;">
            <div class="card-body text-center py-3">
                <div style="font-size:32px;">🎉</div>
                <div style="font-weight:800;color:#1cc88a;font-size:16px;">SELAMAT!</div>
                <div class="text-muted small">Kamu dinyatakan lulus seleksi Paskibra Kecamatan Compreng</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection