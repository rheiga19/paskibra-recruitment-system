@extends('layouts.app')
@section('title', 'Manajemen Pendaftaran')

@section('content')
<div class="section-header">
    <h1>Manajemen Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Pendaftaran</div>
    </div>
</div>

{{-- Filter --}}
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="font-weight-bold">Rekrutmen</label>
                    <select name="rekrutmen_id" class="form-control">
                        <option value="">Semua Rekrutmen</option>
                        @foreach($rekrutmenList as $r)
                        <option value="{{ $r->id }}" {{ request('rekrutmen_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="font-weight-bold">Gender</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jenis_kelamin') === 'L' ? 'selected' : '' }}>Putra</option>
                        <option value="P" {{ request('jenis_kelamin') === 'P' ? 'selected' : '' }}>Putri</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="font-weight-bold">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama / No. Daftar / NIK"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Hasil: {{ $pendaftaran->total() }} pendaftar</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>No. Daftar</th><th>Nama</th><th>JK</th>
                        <th>Sekolah / Kelas</th><th>Rekrutmen</th><th>Status</th><th>Daftar</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $p)
                    <tr>
                        <td><small class="text-muted">{{ $p->no_pendaftaran ?? '-' }}</small></td>
                        <td>
                            <strong>{{ $p->nama_lengkap }}</strong>
                            <br><small class="text-muted">{{ $p->nik }}</small>
                        </td>
                        <td>{{ $p->jenis_kelamin === 'L' ? '♂ Putra' : '♀ Putri' }}</td>
                        <td><small>{{ $p->nama_sekolah }}<br>{{ $p->jenjang }} Kelas {{ $p->kelas }}</small></td>
                        <td><small>{{ $p->rekrutmen->nama ?? '-' }}</small></td>
                        <td>
                            @php $c = ['menunggu'=>'warning','diverifikasi'=>'info','lulus'=>'success','tidak_lulus'=>'danger'][$p->status] ?? 'secondary'; @endphp
                            <span class="badge badge-{{ $c }}">{{ $p->status_label }}</span>
                            @if($p->is_lulus_final)
                                <span class="badge badge-success ml-1"><i class="fas fa-star"></i> Final</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $p->created_at->format('d M Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.pendaftaran.show', $p) }}" class="btn btn-sm btn-icon btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                            <form action="{{ route('admin.pendaftaran.destroy', $p) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon btn-danger" onclick="return confirm('Hapus data pendaftaran ini?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Tidak ada data pendaftaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pendaftaran->hasPages())
    <div class="card-footer">{{ $pendaftaran->links() }}</div>
    @endif
</div>
@endsection