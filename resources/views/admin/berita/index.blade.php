@extends('layouts.app')
@section('title', 'Manajemen Berita')

@section('content')
<div class="section-header">
    <h1>Manajemen Berita</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Berita</div>
    </div>
</div>

{{-- Filter --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-filter mr-2"></i>Filter</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.berita.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Berita
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.berita.index') }}">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">Status</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Published</option>
                        <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="font-weight-bold">Cari Judul</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari judul berita..."
                               value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-auto mb-3">
                    @if(request('search') || request()->has('status') && request('status') !== '')
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-newspaper mr-2"></i>Daftar Berita</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-md mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($berita as $b)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <b>{{ Str::limit($b->judul, 60) }}</b>
                            @if($b->ringkasan)
                            <br><small class="text-muted">{{ Str::limit($b->ringkasan, 80) }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="badge badge-{{ $b->is_published ? 'success' : 'secondary' }}">
                                {{ $b->is_published ? 'Published' : 'Draft' }}
                            </div>
                        </td>
                        <td>
                            <small>{{ $b->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.berita.edit', $b) }}"
                               class="btn btn-sm btn-primary btn-icon">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.berita.publish', $b) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-icon btn-{{ $b->is_published ? 'warning' : 'success' }}"
                                        title="{{ $b->is_published ? 'Jadikan Draft' : 'Publish' }}">
                                    <i class="fas fa-{{ $b->is_published ? 'eye-slash' : 'eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.berita.destroy', $b) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus berita ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-icon btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state" data-height="300">
                                <div class="empty-state-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <h2>Belum Ada Berita</h2>
                                <p class="lead">Mulai tambahkan berita untuk ditampilkan ke publik.</p>
                                <a href="{{ route('admin.berita.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus mr-1"></i> Tambah Berita
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($berita->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            {{ $berita->firstItem() }}–{{ $berita->lastItem() }} dari {{ $berita->total() }}
        </small>
        {{ $berita->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection