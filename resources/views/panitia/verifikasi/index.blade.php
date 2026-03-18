@extends('layouts.app')
@section('title', 'Verifikasi Pendaftaran')

@section('content')
<div class="section-header">
    <h1>Verifikasi Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Verifikasi</div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total</h4></div>
                <div class="card-body">{{ $total }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Menunggu</h4></div>
                <div class="card-body">{{ $menunggu }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-check"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Diterima</h4></div>
                <div class="card-body">{{ $diterima }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
                <i class="fas fa-times"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Ditolak</h4></div>
                <div class="card-body">{{ $ditolak }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel + Filter --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-list mr-2"></i>Daftar Peserta</h4>
        <div class="card-header-action">
            <form method="GET" action="{{ route('panitia.verifikasi.index') }}"
                  class="d-flex align-items-center" style="gap:6px;">
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="menunggu"     {{ request('status')=='menunggu'     ? 'selected':'' }}>Menunggu</option>
                    <option value="diverifikasi" {{ request('status')=='diverifikasi' ? 'selected':'' }}>Diterima</option>
                    <option value="ditolak"      {{ request('status')=='ditolak'      ? 'selected':'' }}>Ditolak</option>
                </select>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / no daftar..."
                       value="{{ request('search') }}" style="width:180px;">
                <button class="btn btn-sm btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-md mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Daftar</th>
                        <th>Nama Peserta</th>
                        <th>Sekolah</th>
                        <th class="text-center">JK</th>
                        <th class="text-center">Dokumen</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $i => $p)
                    <tr>
                        <td>{{ $pendaftaran->firstItem() + $i }}</td>
                        <td><b class="text-danger">{{ $p->no_pendaftaran }}</b></td>
                        <td>
                            <b>{{ $p->nama_lengkap }}</b>
                            <br><small class="text-muted">{{ $p->no_hp }}</small>
                        </td>
                        <td>
                            {{ $p->nama_sekolah }}
                            <br><small class="text-muted">{{ $p->jenjang }} – Kelas {{ $p->kelas }}</small>
                        </td>
                        <td class="text-center">
                            <div class="badge badge-{{ $p->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                {{ $p->jenis_kelamin == 'L' ? 'Putra' : 'Putri' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @php $jmlDok = $p->dokumen->count(); @endphp
                            <div class="badge badge-{{ $jmlDok == 6 ? 'success' : 'warning' }}">
                                {{ $jmlDok }}/6
                            </div>
                        </td>
                        <td>
                            @php
                                $sc = ['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger'];
                                $sl = ['menunggu'=>'Menunggu','diverifikasi'=>'Diterima','ditolak'=>'Ditolak'];
                            @endphp
                            <div class="badge badge-{{ $sc[$p->status] ?? 'secondary' }}">
                                {{ $sl[$p->status] ?? $p->status }}
                            </div>
                        </td>
                        <td><small class="text-muted">{{ $p->created_at->format('d M Y') }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('panitia.verifikasi.show', $p) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye mr-1"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state" data-height="250">
                                <div class="empty-state-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h2>Tidak Ada Data</h2>
                                <p class="lead">Tidak ada data pendaftaran{{ request('search') ? ' yang cocok' : '' }}.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pendaftaran->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $pendaftaran->firstItem() }}–{{ $pendaftaran->lastItem() }}
            dari {{ $pendaftaran->total() }} data
        </small>
        {{ $pendaftaran->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection