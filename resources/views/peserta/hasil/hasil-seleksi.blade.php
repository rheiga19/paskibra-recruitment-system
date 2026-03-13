@extends('layouts.app')
@section('title', 'Hasil Seleksi')

@push('css')
<style>
.hasil-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.07); overflow:hidden; }
.nilai-badge {
    display:inline-flex; flex-direction:column; align-items:center;
    padding:10px 20px; border-radius:12px; font-weight:800;
}
.nilai-lolos    { background:#e8faf3; color:#1cc88a; }
.nilai-tidak    { background:#fff5f5; color:#e74a3b; }
.nilai-menunggu { background:#f8f9fa; color:#aaa; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Hasil Seleksi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Hasil Seleksi</div>
    </div>
</div>

@if(!$pendaftaran)
<div class="card hasil-card text-center">
    <div class="card-body py-5">
        <i class="fas fa-clipboard fa-4x text-muted mb-3" style="opacity:.3;"></i>
        <h4 class="text-muted">Belum Ada Data Pendaftaran</h4>
        <p class="text-muted">Kamu belum mendaftar di rekrutmen manapun.</p>
        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</div>
@elseif($hasilList->isEmpty())
<div class="card hasil-card text-center">
    <div class="card-body py-5">
        <i class="fas fa-hourglass-half fa-4x mb-3" style="opacity:.3;color:#f6a821;"></i>
        <h4 class="text-muted">Hasil Belum Diumumkan</h4>
        <p class="text-muted">Panitia belum mengumumkan hasil seleksi. Pantau terus halaman ini.</p>
        <div class="badge badge-info px-3 py-2">No. Pendaftaran: {{ $pendaftaran->no_pendaftaran }}</div>
    </div>
</div>
@else
<div class="row">
    <div class="col-lg-8">
        {{-- Hasil Per Tahap --}}
        @foreach($hasilList as $hasil)
        @php
            $lolos = $hasil->status === 'lolos';
            $tahap = $hasil->tahap; // relasi tahap sesuai model SeleksiHasil
        @endphp
        <div class="card hasil-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background:{{ $lolos ? '#f0fff8' : '#fff5f5' }};">
                <div>
                    <h4 class="mb-0">{{ $tahap->nama ?? 'Tahap Seleksi' }}</h4>
                    <small class="text-muted">
                        @if($tahap?->tanggal_pengumuman)
                            Diumumkan: {{ \Carbon\Carbon::parse($tahap->tanggal_pengumuman)->format('d M Y') }}
                        @endif
                    </small>
                </div>
                <span class="badge badge-{{ $lolos ? 'success' : 'danger' }} px-3 py-2"
                      style="font-size:13px;border-radius:10px;">
                    <i class="fas fa-{{ $lolos ? 'check' : 'times' }} mr-1"></i>
                    {{ $lolos ? 'LOLOS' : 'TIDAK LOLOS' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @if($hasil->nilai_total)
                    <div class="col-4">
                        <div class="nilai-badge {{ $lolos ? 'nilai-lolos' : 'nilai-tidak' }} w-100">
                            <span style="font-size:28px;">{{ number_format($hasil->nilai_total, 1) }}</span>
                            <span style="font-size:10px;opacity:.7;">NILAI TOTAL</span>
                        </div>
                    </div>
                    @endif
                    @if($hasil->nilai_fisik)
                    <div class="col-4">
                        <div class="nilai-badge nilai-menunggu w-100">
                            <span style="font-size:24px;">{{ number_format($hasil->nilai_fisik, 1) }}</span>
                            <span style="font-size:10px;">FISIK</span>
                        </div>
                    </div>
                    @endif
                    @if($hasil->nilai_wawancara)
                    <div class="col-4">
                        <div class="nilai-badge nilai-menunggu w-100">
                            <span style="font-size:24px;">{{ number_format($hasil->nilai_wawancara, 1) }}</span>
                            <span style="font-size:10px;">WAWANCARA</span>
                        </div>
                    </div>
                    @endif
                </div>
                @if($hasil->catatan)
                <div class="alert alert-light mt-3 mb-0 py-2">
                    <i class="fas fa-comment-alt mr-1 text-muted"></i>
                    <small>{{ $hasil->catatan }}</small>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="col-lg-4">
        {{-- Ringkasan --}}
        <div class="card hasil-card mb-3">
            <div class="card-header"><h4><i class="fas fa-trophy mr-2 text-warning"></i>Ringkasan</h4></div>
            <div class="card-body">
                <div class="mb-3 text-center">
                    <div style="font-size:11px;color:#aaa;letter-spacing:1px;">NO. PENDAFTARAN</div>
                    <div style="font-size:18px;font-weight:800;color:#cc0000;">{{ $pendaftaran->no_pendaftaran }}</div>
                </div>
                @if($pendaftaran->nilai_akhir)
                <div class="text-center mb-3">
                    <div style="font-size:11px;color:#aaa;letter-spacing:1px;">NILAI AKHIR</div>
                    <div style="font-size:40px;font-weight:900;color:#cc0000;">{{ number_format($pendaftaran->nilai_akhir, 2) }}</div>
                </div>
                @endif
                <div class="text-center">
                    @if($pendaftaran->is_lulus_final)
                    <div class="alert alert-success py-2 mb-0">
                        <i class="fas fa-star mr-1"></i>
                        <strong>SELAMAT! Kamu Lulus!</strong>
                    </div>
                    @elseif($pendaftaran->status === 'tidak_lulus')
                    <div class="alert alert-danger py-2 mb-0">
                        <i class="fas fa-times-circle mr-1"></i>
                        Belum berhasil. Semangat!
                    </div>
                    @else
                    <div class="alert alert-warning py-2 mb-0">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        Proses seleksi masih berlangsung
                    </div>
                    @endif
                </div>
                <hr>
                <div style="font-size:12px;color:#aaa;text-align:center;">
                    Tahap ditampilkan: {{ $hasilList->count() }} dari total tahap seleksi
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection