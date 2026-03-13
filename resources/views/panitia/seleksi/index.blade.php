@extends('layouts.app')
@section('title', 'Input Nilai Seleksi')

@section('content')
<div class="section-header">
    <h1>Input Nilai Seleksi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Input Nilai</div>
    </div>
</div>

{{-- ── STAT ── --}}
<div class="row">
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Lolos Administrasi</h4></div>
                <div class="card-body">{{ number_format($totalPeserta) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-edit"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Belum Dinilai</h4></div>
                <div class="card-body">{{ number_format($belumDinilai) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-info"><i class="fas fa-check-circle"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Sudah Dinilai</h4></div>
                <div class="card-body">{{ number_format($sudahDinilai) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-trophy"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Lolos Seleksi</h4></div>
                <div class="card-body">{{ number_format($lolosSeleksi) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTER REKRUTMEN ── --}}
<div class="card">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center flex-wrap gap-2">
            <label class="mb-0 font-weight-bold mr-2" style="font-size:13px;">Rekrutmen:</label>
            <select name="rekrutmen_id" class="form-control form-control-sm" style="width:auto"
                    onchange="this.form.submit()">
                @foreach($rekrutmenList as $r)
                <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected' : '' }}>
                    {{ $r->nama }}
                </option>
                @endforeach
            </select>
            @if(request('tahap_id'))
                <input type="hidden" name="tahap_id" value="{{ request('tahap_id') }}">
            @endif
        </form>
    </div>
</div>

{{-- ── TAHAP PILLS ── --}}
@if($tahapList->count())
<div class="d-flex flex-wrap gap-2 mb-3" style="gap:8px;">
    @foreach($tahapList as $t)
    <a href="{{ route('panitia.seleksi.index', ['rekrutmen_id' => $rekrutmenId, 'tahap_id' => $t->id]) }}"
       class="btn btn-sm btn-{{ $tahap?->id == $t->id ? 'primary' : 'outline-primary' }}"
       style="border-radius:100px;">
        {{ $t->urutan }}. {{ $t->nama }}
        <span class="ml-1" style="opacity:.7;font-size:10px;">KKM {{ $t->passing_grade ?? 70 }}</span>
    </a>
    @endforeach
</div>
@endif

{{-- ── TABEL PESERTA ── --}}
<div class="card">
    <div class="card-header">
        <h4>
            <i class="fas fa-star mr-2 text-primary"></i>
            {{ $tahap ? $tahap->nama . ' — Daftar Peserta' : 'Daftar Peserta' }}
        </h4>
        <div class="card-header-action">
            <a href="{{ route('panitia.hasil.index', ['rekrutmen_id' => $rekrutmenId]) }}"
               class="btn btn-sm btn-success">
                <i class="fas fa-trophy mr-1"></i> Lihat Hasil
            </a>
        </div>
    </div>
    <div class="card-body">

        {{-- Filter --}}
        <form method="GET" action="{{ route('panitia.seleksi.index') }}" class="mb-3">
            <input type="hidden" name="rekrutmen_id" value="{{ $rekrutmenId }}">
            @if($tahap)<input type="hidden" name="tahap_id" value="{{ $tahap->id }}">@endif
            <div class="row align-items-end">
                <div class="col-12 col-sm-4 col-lg-4 mb-2 mb-sm-0">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Cari nama..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-3 col-lg-2 mb-2 mb-sm-0">
                    <select name="nilai" class="form-control form-control-sm">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('nilai') === 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                        <option value="sudah" {{ request('nilai') === 'sudah' ? 'selected' : '' }}>Sudah Dinilai</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2 mb-2 mb-sm-0">
                    <select name="jk" class="form-control form-control-sm">
                        <option value="">Semua JK</option>
                        <option value="L" {{ request('jk') === 'L' ? 'selected' : '' }}>Putra</option>
                        <option value="P" {{ request('jk') === 'P' ? 'selected' : '' }}>Putri</option>
                    </select>
                </div>
                <div class="col-12 col-sm-2 col-lg-4">
                    <div class="d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="{{ route('panitia.seleksi.index', ['rekrutmen_id' => $rekrutmenId]) }}"
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th class="d-none d-sm-table-cell">JK</th>
                        <th class="d-none d-md-table-cell">Sekolah</th>
                        <th>Nilai Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $i => $p)
                    @php
                        $hasil = $tahap
                            ? $p->hasilSeleksi->where('seleksi_tahap_id', $tahap->id)->first()
                            : null;
                        $sudahNilai = $hasil && $hasil->nilai_total !== null;
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $pendaftaran->firstItem() + $i }}</td>
                        <td class="font-weight-bold">{{ $p->nama_lengkap }}</td>
                        <td class="d-none d-sm-table-cell">
                            {{ $p->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}
                        </td>
                        <td class="d-none d-md-table-cell">
                            <small>{{ $p->nama_sekolah }}</small>
                        </td>
                        <td>
                            @if($sudahNilai)
                                <strong class="{{ $hasil->status === 'lolos' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($hasil->nilai_total, 2) }}
                                </strong>
                            @else
                                <span class="text-muted">–</span>
                            @endif
                        </td>
                        <td>
                            @if($sudahNilai)
                                <span class="badge badge-{{ $hasil->status === 'lolos' ? 'success' : 'danger' }}">
                                    {{ $hasil->status === 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                </span>
                            @else
                                <span class="badge badge-warning">Belum Dinilai</span>
                            @endif
                        </td>
                        <td>
                            @if($tahap)
                            <a href="{{ route('panitia.seleksi.input', [$p, $tahap]) }}"
                               class="btn btn-sm btn-{{ $sudahNilai ? 'info' : 'primary' }}">
                                <i class="fas fa-{{ $sudahNilai ? 'edit' : 'plus' }} mr-1"></i>
                                {{ $sudahNilai ? 'Edit' : 'Input' }}
                            </a>
                            @else
                            <span class="text-muted" style="font-size:12px;">Pilih tahap</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada peserta</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendaftaran->hasPages())
        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
            <small class="text-muted">
                Menampilkan {{ $pendaftaran->firstItem() }}–{{ $pendaftaran->lastItem() }}
                dari {{ $pendaftaran->total() }} peserta
            </small>
            {{ $pendaftaran->appends(request()->query())->links() }}
        </div>
        @endif

    </div>
</div>
@endsection