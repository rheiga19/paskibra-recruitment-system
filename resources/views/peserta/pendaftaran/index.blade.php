@extends('layouts.app')
@section('title', 'Status Pendaftaran')

@section('content')
<div class="section-header">
    <h1>Status Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Status Pendaftaran</div>
    </div>
</div>

@forelse($pendaftaran as $p)
@php
    $sc = ['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger','lulus'=>'success','tidak_lulus'=>'danger'];
    $sl = ['menunggu'=>'Menunggu Verifikasi','diverifikasi'=>'Lolos Administrasi','ditolak'=>'Ditolak','lulus'=>'Lulus Final','tidak_lulus'=>'Tidak Lulus'];
    $si = ['menunggu'=>'clock','diverifikasi'=>'check-circle','lulus'=>'trophy','tidak_lulus'=>'times-circle','ditolak'=>'ban'];
@endphp
<div class="card mb-3" style="border-left:4px solid {{ $p->status === 'lulus' ? '#1cc88a' : ($p->status === 'menunggu' ? '#f6a821' : ($p->status === 'diverifikasi' ? '#36b9cc' : '#e74a3b')) }}">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
                <div style="font-size:11px;color:#aaa;letter-spacing:1px;">NO. PENDAFTARAN</div>
                <div style="font-size:20px;font-weight:900;color:#cc0000;">{{ $p->no_pendaftaran }}</div>
                <div class="text-muted small">{{ $p->rekrutmen->nama ?? '-' }}</div>
                <div class="text-muted small mt-1">
                    <i class="fas fa-calendar mr-1"></i>
                    Didaftarkan {{ $p->created_at->translatedFormat('d F Y, H:i') }}
                </div>
            </div>
            <span class="badge badge-{{ $sc[$p->status] ?? 'secondary' }} px-3 py-2" style="font-size:12px;border-radius:10px;">
                <i class="fas fa-{{ $si[$p->status] ?? 'circle' }} mr-1"></i>
                {{ $sl[$p->status] ?? $p->status }}
            </span>
        </div>

        {{-- Mini timeline --}}
        <div class="d-flex align-items-center mt-3" style="gap:0">
            @php
                $steps   = ['menunggu','diverifikasi','lulus'];
                $currIdx = array_search($p->status, $steps);
                $labels  = ['Mendaftar','Verifikasi','Lulus'];
            @endphp
            @foreach($steps as $i => $step)
            @php $done = $currIdx !== false && $i <= $currIdx; @endphp
            <div class="text-center" style="flex:1">
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center text-white"
                     style="width:28px;height:28px;background:{{ $done ? '#1cc88a' : '#e0e0e0' }};margin:0 auto;font-size:11px">
                    <i class="fas fa-{{ $done ? 'check' : 'circle' }} fa-xs"></i>
                </div>
                <div style="font-size:10px;color:{{ $done ? '#1cc88a' : '#aaa' }};margin-top:4px;font-weight:{{ $p->status===$step?'700':'400' }}">
                    {{ $labels[$i] }}
                </div>
            </div>
            @if(!$loop->last)
            <div style="flex:2;height:2px;background:{{ $currIdx !== false && $i < $currIdx ? '#1cc88a' : '#e0e0e0' }};margin-bottom:18px"></div>
            @endif
            @endforeach
        </div>

        @if($p->catatan_admin)
        <div class="alert alert-warning py-2 mt-3 mb-0 small">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Catatan:</strong> {{ $p->catatan_admin }}
        </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('peserta.pendaftaran.show', $p) }}"
               class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                <i class="fas fa-eye mr-1"></i> Lihat Detail
            </a>
            @if(in_array($p->status, ['diverifikasi','lulus']))
            <a href="{{ route('peserta.pendaftaran.kartu', $p) }}"
               class="btn btn-sm btn-outline-danger ml-1" style="border-radius:8px;">
                <i class="fas fa-print mr-1"></i> Cetak Kartu
            </a>
            @endif
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-clipboard fa-4x text-muted mb-3 d-block" style="opacity:.25"></i>
        <h5 class="text-muted">Belum Ada Pendaftaran</h5>
        <p class="text-muted small">Kamu belum pernah mendaftar rekrutmen apapun.</p>
        <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary" style="border-radius:10px;">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endforelse
@endsection