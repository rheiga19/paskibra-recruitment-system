@extends('layouts.app')
@section('title', 'Detail Rekrutmen')

@section('content')
<div class="section-header">
    <h1>{{ $rekrutmen->nama }}</h1>
    <div class="section-header-button">
        <a href="{{ route('admin.rekrutmen.edit', $rekrutmen) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
    </div>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.rekrutmen.index') }}">Rekrutmen</a></div>
        <div class="breadcrumb-item active">Detail</div>
    </div>
</div>

<div class="row">
    {{-- Stat mini --}}
    <div class="col-md-3 col-sm-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total Pendaftar</h4></div>
                <div class="card-body">{{ $rekrutmen->pendaftaran_count }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-male"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Putra</h4></div>
                <div class="card-body">{{ $rekrutmen->pendaftar_putra }} / {{ $rekrutmen->kuota_putra ?? '∞' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i class="fas fa-female"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Putri</h4></div>
                <div class="card-body">{{ $rekrutmen->pendaftar_putri }} / {{ $rekrutmen->kuota_putri ?? '∞' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-trophy"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Lulus</h4></div>
                <div class="card-body">{{ $rekrutmen->lulus_count }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        {{-- Info rekrutmen --}}
        <div class="card">
            <div class="card-header"><h4>Informasi</h4></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Status</td><td><span class="badge badge-{{ $rekrutmen->is_aktif ? 'success':'secondary' }}">{{ $rekrutmen->is_aktif ? 'Aktif':'Nonaktif' }}</span></td></tr>
                    <tr><td class="text-muted">Periode</td><td>{{ $rekrutmen->tanggal_buka->format('d M Y') }} – {{ $rekrutmen->tanggal_tutup->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted">Tahun</td><td>{{ $rekrutmen->tahun }}</td></tr>
                    @if($rekrutmen->deskripsi)
                    <tr><td class="text-muted" colspan="2">{{ $rekrutmen->deskripsi }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
        {{-- Tahap seleksi --}}
        <div class="card">
            <div class="card-header"><h4>Tahap Seleksi</h4></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($tahapList as $t)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-primary mr-2">{{ $t->urutan }}</span>
                            {{ $t->nama }}
                        </div>
                        <small class="text-muted">KKM: {{ $t->passing_grade ?? 70 }}</small>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Daftar Pendaftar</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.pendaftaran.index', ['rekrutmen_id' => $rekrutmen->id]) }}" class="btn btn-sm btn-primary">
                        Lihat Lengkap
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr><th>No. Daftar</th><th>Nama</th><th>JK</th><th>Sekolah</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaran as $p)
                            <tr>
                                <td><small class="text-muted">{{ $p->no_pendaftaran ?? '-' }}</small></td>
                                <td>{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td>
                                <td><small>{{ $p->nama_sekolah }}</small></td>
                                <td>
                                    @php $c = ['menunggu'=>'warning','diverifikasi'=>'info','lulus'=>'success','tidak_lulus'=>'danger'][$p->status] ?? 'secondary'; @endphp
                                    <span class="badge badge-{{ $c }}">{{ $p->status_label }}</span>
                                </td>
                                <td><a href="{{ route('admin.pendaftaran.show', $p) }}" class="btn btn-sm btn-icon btn-primary"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pendaftar</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pendaftaran->hasPages())
            <div class="card-footer">{{ $pendaftaran->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection