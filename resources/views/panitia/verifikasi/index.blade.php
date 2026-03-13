@extends('layouts.app')
@section('title', 'Verifikasi Pendaftaran')

@push('css')
<style>
.stat-mini { border:none; border-radius:12px; padding:16px 20px; color:#fff; display:flex; align-items:center; gap:14px; box-shadow:0 3px 14px rgba(0,0,0,.12); }
.stat-mini .icon { width:42px; height:42px; border-radius:10px; background:rgba(255,255,255,.22); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.stat-mini .val  { font-size:22px; font-weight:800; line-height:1; }
.stat-mini .lbl  { font-size:11px; opacity:.8; }
.sm-blue   { background:linear-gradient(135deg,#667eea,#764ba2); }
.sm-yellow { background:linear-gradient(135deg,#f6d365,#fda085); }
.sm-green  { background:linear-gradient(135deg,#43e97b,#38f9d7); }
.sm-red    { background:linear-gradient(135deg,#cc0000,#8b0000); }

.table-verif th { background:#f8f9fa; font-size:12px; letter-spacing:.5px; font-weight:700; color:#555; border-top:none; }
.table-verif td { vertical-align:middle; font-size:13px; }
.badge-status { font-size:11px; padding:5px 10px; border-radius:8px; font-weight:700; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Verifikasi Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Verifikasi</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col-6 col-xl-3 mb-3">
        <div class="stat-mini sm-blue">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div><div class="lbl">TOTAL</div><div class="val">{{ $total }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="stat-mini sm-yellow">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div><div class="lbl">MENUNGGU</div><div class="val">{{ $menunggu }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="stat-mini sm-green">
            <div class="icon"><i class="fas fa-check"></i></div>
            <div><div class="lbl">DITERIMA</div><div class="val">{{ $diterima }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3 mb-3">
        <div class="stat-mini sm-red">
            <div class="icon"><i class="fas fa-times"></i></div>
            <div><div class="lbl">DITOLAK</div><div class="val">{{ $ditolak }}</div></div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="border-bottom:1px solid #f0f0f0;">
        <h4 class="mb-0"><i class="fas fa-list mr-2 text-primary"></i>Daftar Peserta</h4>
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <select name="status" class="form-control form-control-sm" style="width:auto;border-radius:8px;"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ request('status')=='menunggu'     ? 'selected':'' }}>Menunggu</option>
                <option value="diverifikasi" {{ request('status')=='diverifikasi' ? 'selected':'' }}>Diterima</option>
                <option value="ditolak"      {{ request('status')=='ditolak'      ? 'selected':'' }}>Ditolak</option>
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama / no daftar..."
                   value="{{ request('search') }}" style="width:200px;border-radius:8px;">
            <button class="btn btn-sm btn-primary" style="border-radius:8px;">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-sm btn-secondary" style="border-radius:8px;">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        @if($pendaftaran->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3" style="opacity:.3;"></i>
            <p>Tidak ada data pendaftaran{{ request('search') ? ' yang cocok' : '' }}.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-verif table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-3" style="width:50px">#</th>
                        <th>No. Daftar</th>
                        <th>Nama Peserta</th>
                        <th>Sekolah</th>
                        <th>Jenis Kelamin</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftaran as $i => $p)
                    <tr>
                        <td class="pl-3 text-muted">{{ $pendaftaran->firstItem() + $i }}</td>
                        <td><strong style="color:#cc0000;">{{ $p->no_pendaftaran }}</strong></td>
                        <td>
                            <div class="font-weight-600">{{ $p->nama_lengkap }}</div>
                            <small class="text-muted">{{ $p->no_hp }}</small>
                        </td>
                        <td>
                            <div>{{ $p->nama_sekolah }}</div>
                            <small class="text-muted">{{ $p->jenjang }} – Kelas {{ $p->kelas }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $p->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                {{ $p->jenis_kelamin == 'L' ? 'Putra' : 'Putri' }}
                            </span>
                        </td>
                        <td>
                            @php $jmlDok = $p->dokumen->count(); @endphp
                            <span class="badge badge-{{ $jmlDok == 6 ? 'success' : 'warning' }}">
                                {{ $jmlDok }}/6
                            </span>
                        </td>
                        <td>
                            @php
                                $sc=['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger'];
                                $sl=['menunggu'=>'Menunggu','diverifikasi'=>'Diterima','ditolak'=>'Ditolak'];
                            @endphp
                            <span class="badge badge-status badge-{{ $sc[$p->status] ?? 'secondary' }}">
                                {{ $sl[$p->status] ?? $p->status }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $p->created_at->format('d M Y') }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('panitia.verifikasi.show', $p) }}"
                               class="btn btn-sm btn-primary" style="border-radius:8px;">
                                <i class="fas fa-eye mr-1"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pendaftaran->hasPages())
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
            <small class="text-muted">
                Menampilkan {{ $pendaftaran->firstItem() }}–{{ $pendaftaran->lastItem() }} dari {{ $pendaftaran->total() }} data
            </small>
            {{ $pendaftaran->withQueryString()->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection