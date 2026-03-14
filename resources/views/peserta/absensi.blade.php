@extends('layouts.peserta')
@section('title', 'Absensi Saya')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4>Absensi Latihan</h4>
    @if($pendaftaran)
    <a href="{{ route('peserta.kartu') }}" class="btn btn-primary" target="_blank">
        <i class="fas fa-id-card mr-1"></i> Kartu Anggota
    </a>
    @endif
</div>

@if(!$pendaftaran)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle mr-1"></i>
    Fitur absensi hanya tersedia untuk peserta yang dinyatakan lulus seleksi.
</div>
@else

{{-- Ringkasan kehadiran --}}
<div class="row mb-4">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white text-center">
            <div class="card-body py-3">
                <div style="font-size:28px;font-weight:900;">{{ $totalAll }}</div>
                <div style="font-size:12px;">Total Jadwal</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-success text-white text-center">
            <div class="card-body py-3">
                <div style="font-size:28px;font-weight:900;">{{ $totalHadir }}</div>
                <div style="font-size:12px;">Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-danger text-white text-center">
            <div class="card-body py-3">
                <div style="font-size:28px;font-weight:900;">{{ $totalAll - $totalHadir }}</div>
                <div style="font-size:12px;">Tidak Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-{{ $persen >= 80 ? 'success' : ($persen >= 60 ? 'warning' : 'danger') }} text-white text-center">
            <div class="card-body py-3">
                <div style="font-size:28px;font-weight:900;">{{ $persen }}%</div>
                <div style="font-size:12px;">Kehadiran</div>
            </div>
        </div>
    </div>
</div>

{{-- Info kartu --}}
{{-- gap-3 tidak support Bootstrap 4, ganti mr-3 pada icon --}}
<div class="alert alert-info d-flex align-items-center mb-4">
    <i class="fas fa-id-card fa-2x mr-3"></i>
    <div>
        <strong>Tunjukkan kartu anggota kamu saat latihan.</strong>
        Panitia akan scan QR code di kartu untuk mencatat kehadiran.
        <a href="{{ route('peserta.kartu') }}" target="_blank" class="alert-link ml-2">
            Download/Print Kartu →
        </a>
    </div>
</div>

{{-- Riwayat absensi --}}
<div class="card">
    <div class="card-header"><h4><i class="fas fa-history mr-2"></i>Riwayat Kehadiran</h4></div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
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
                    <td><strong>{{ $abs->jadwal->nama }}</strong></td>
                    <td>{{ $abs->jadwal->tanggal->translatedFormat('d F Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $abs->badgeStatus() }}">
                            {{ $abs->labelStatus() }}
                        </span>
                    </td>
                    <td>{{ $abs->waktu_masuk ? $abs->waktu_masuk->format('H:i') : '-' }}</td>
                    <td>{{ $abs->waktu_pulang ? $abs->waktu_pulang->format('H:i') : '-' }}</td>
                    <td><small class="text-muted">{{ $abs->keterangan ?: '-' }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Belum ada riwayat absensi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif
@endsection