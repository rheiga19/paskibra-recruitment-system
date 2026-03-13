@extends('layouts.app')
@section('title', 'Manajemen Rekrutmen')

@section('content')
<div class="section-header">
    <h1>Manajemen Rekrutmen</h1>
    <div class="section-header-button">
        <a href="{{ route('admin.rekrutmen.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Buat Rekrutmen
        </a>
    </div>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Rekrutmen</div>
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tahun</th><th>Nama</th><th>Periode</th>
                        <th>Kuota</th><th>Pendaftar</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekrutmen as $r)
                    <tr>
                        <td><strong>{{ $r->tahun }}</strong></td>
                        <td>{{ $r->nama }}</td>
                        <td><small>{{ $r->tanggal_buka->format('d M Y') }}<br>s/d {{ $r->tanggal_tutup->format('d M Y') }}</small></td>
                        <td><small>
                            <i class="fas fa-male text-primary"></i> {{ $r->kuota_putra ?? '∞' }}
                            &nbsp;
                            <i class="fas fa-female text-danger"></i> {{ $r->kuota_putri ?? '∞' }}
                        </small></td>
                        <td><span class="badge badge-primary">{{ $r->pendaftaran_count }}</span></td>
                        <td><span class="badge badge-{{ $r->is_aktif ? 'success' : 'secondary' }}">{{ $r->is_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <a href="{{ route('admin.rekrutmen.show', $r) }}" class="btn btn-sm btn-info btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.rekrutmen.edit', $r) }}" class="btn btn-sm btn-warning btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.rekrutmen.toggle', $r) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-{{ $r->is_aktif ? 'secondary' : 'success' }} btn-icon"
                                        onclick="return confirm('Ubah status rekrutmen ini?')"
                                        title="{{ $r->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.rekrutmen.destroy', $r) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon"
                                        onclick="return confirm('Hapus rekrutmen \'{{ addslashes($r->nama) }}\'? Tindakan ini tidak bisa dibatalkan.')"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            Belum ada rekrutmen.
                            <a href="{{ route('admin.rekrutmen.create') }}">Buat sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rekrutmen->hasPages())
    <div class="card-footer">{{ $rekrutmen->links() }}</div>
    @endif
</div>
@endsection