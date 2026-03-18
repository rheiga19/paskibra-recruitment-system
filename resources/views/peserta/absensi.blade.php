@extends('layouts.app')
@section('title', 'Absensi Saya')

@section('content')
<div class="section-header">
    <h1>Absensi Latihan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Absensi</div>
    </div>
</div>

@if(!$pendaftaran)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-1"></i>
    Fitur absensi hanya tersedia untuk peserta yang dinyatakan lulus seleksi.
</div>
@else

{{-- Stat Cards (Stisla style) --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Jadwal</h4>
                </div>
                <div class="card-body">
                    {{ $totalAll }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Hadir</h4>
                </div>
                <div class="card-body">
                    {{ $totalHadir }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Tidak Hadir</h4>
                </div>
                <div class="card-body">
                    {{ $totalAll - $totalHadir }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon {{ $persen >= 80 ? 'bg-success' : ($persen >= 60 ? 'bg-warning' : 'bg-danger') }}">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Kehadiran</h4>
                </div>
                <div class="card-body">
                    {{ $persen }}%
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Banner Kartu --}}
<div class="card mb-4" style="border:none;border-radius:14px;background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;overflow:hidden;">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:16px;">
            <div>
                <h5 class="font-weight-bold mb-1">
                    <i class="fas fa-id-card mr-2"></i> Kartu Anggota Paskibra
                </h5>
                <p class="mb-3" style="opacity:.8;font-size:13px;">
                    Wajib dibawa saat latihan. Panitia akan scan QR code untuk mencatat kehadiran.
                </p>
                <div class="d-flex flex-wrap" style="gap:8px;">
                    <a href="{{ route('peserta.kartu') }}" target="_blank"
                       class="btn btn-light btn-sm" style="border-radius:8px;font-weight:700;color:#1a1a2e;">
                        <i class="fas fa-eye mr-1"></i> Lihat Kartu
                    </a>
                </div>
            </div>
            <div style="opacity:.15;font-size:80px;line-height:1;">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
    </div>
</div>

{{-- Panduan Download lewat HP --}}
<div class="card mb-4" style="border:2px dashed #dee2e6;border-radius:14px;background:#fafbff;">
    <div class="card-body">
        <h6 class="font-weight-bold mb-3">
            <i class="fas fa-mobile-alt mr-2 text-primary"></i>
            Cara Simpan Kartu sebagai PDF lewat HP
        </h6>
        <div class="row">
            {{-- Android --}}
            <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center mb-2">
                    <div style="width:28px;height:28px;background:#a4c639;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;" class="mr-2">
                        <i class="fab fa-android text-white" style="font-size:14px;"></i>
                    </div>
                    <strong style="font-size:13px;">Android (Chrome)</strong>
                </div>
                <ol class="pl-3 mb-0" style="font-size:12px;color:#555;line-height:2;">
                    <li>Tap tombol <strong>Lihat Kartu</strong> di atas</li>
                    <li>Tap ikon <strong>⋮</strong> (titik tiga) di pojok kanan atas</li>
                    <li>Pilih <strong>"Bagikan"</strong> atau <strong>"Print"</strong></li>
                    <li>Pilih <strong>"Simpan sebagai PDF"</strong></li>
                    <li>Tap <strong>Simpan</strong> — kartu tersimpan di HP</li>
                </ol>
            </div>
            {{-- iPhone --}}
            <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center mb-2">
                    <div style="width:28px;height:28px;background:#555;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;" class="mr-2">
                        <i class="fab fa-apple text-white" style="font-size:14px;"></i>
                    </div>
                    <strong style="font-size:13px;">iPhone (Safari)</strong>
                </div>
                <ol class="pl-3 mb-0" style="font-size:12px;color:#555;line-height:2;">
                    <li>Tap tombol <strong>Lihat Kartu</strong> di atas</li>
                    <li>Tap ikon <strong>Share</strong> (kotak dengan panah ke atas)</li>
                    <li>Scroll ke bawah, pilih <strong>"Print"</strong></li>
                    <li>Cubit layar preview untuk memperbesar</li>
                    <li>Tap ikon <strong>Share</strong> lagi → <strong>"Save to Files"</strong></li>
                </ol>
            </div>
        </div>
        <div class="alert alert-warning mb-0 mt-2 py-2" style="border-radius:8px;font-size:12px;">
            <i class="fas fa-lightbulb mr-1"></i>
            <strong>Tips:</strong> Kalau pakai PC/laptop, cukup klik <strong>Download PDF</strong> — file langsung tersimpan di folder Downloads.
        </div>
    </div>
</div>

{{-- Tabel Riwayat (Stisla style) --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-history mr-2"></i>Riwayat Kehadiran</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-md">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sesi Latihan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapAbsensi as $i => $abs)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><b>{{ $abs->jadwal->nama }}</b></td>
                        <td>{{ $abs->jadwal->tanggal->translatedFormat('d F Y') }}</td>
                        <td>
                            <div class="badge badge-{{ $abs->badgeStatus() }}">
                                {{ $abs->labelStatus() }}
                            </div>
                        </td>
                        <td>{{ $abs->waktu_masuk ? $abs->waktu_masuk->format('H:i') : '-' }}</td>
                        <td>{{ $abs->waktu_pulang ? $abs->waktu_pulang->format('H:i') : '-' }}</td>
                        <td><small class="text-muted">{{ $abs->keterangan ?: '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-clipboard fa-2x d-block mb-2" style="opacity:.3;"></i>
                            Belum ada riwayat absensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif
@endsection