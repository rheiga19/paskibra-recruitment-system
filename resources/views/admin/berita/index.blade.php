@extends('layouts.app')
@section('title', 'Manajemen Berita')

@push('css')
<style>
.berita-card { border:none; border-radius:14px; box-shadow:0 2px 16px rgba(0,0,0,.07); overflow:hidden; transition:.2s; }
.berita-card:hover { transform:translateY(-3px); box-shadow:0 6px 24px rgba(0,0,0,.12); }
.berita-thumb { width:100%; height:160px; object-fit:cover; }
.berita-thumb-placeholder { width:100%; height:160px; background:linear-gradient(135deg,#f0f0f0,#e0e0e0); display:flex; align-items:center; justify-content:center; color:#ccc; font-size:32px; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Manajemen Berita</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Berita</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="fas fa-newspaper mr-2 text-primary"></i>Daftar Berita</h4>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-control form-control-sm" style="width:auto;border-radius:8px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status')==='1'?'selected':'' }}>Published</option>
                    <option value="0" {{ request('status')==='0'?'selected':'' }}>Draft</option>
                </select>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari judul..." value="{{ request('search') }}"
                       style="width:180px;border-radius:8px;">
                <button class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="fas fa-search"></i></button>
                @if(request('search') || request()->has('status'))
                <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-secondary" style="border-radius:8px;"><i class="fas fa-times"></i></a>
                @endif
            </form>
            <a href="{{ route('admin.berita.create') }}" class="btn btn-danger btn-sm" style="border-radius:8px;">
                <i class="fas fa-plus mr-1"></i> Tambah Berita
            </a>
        </div>
    </div>

    <div class="card-body">
        @if($berita->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="fas fa-newspaper fa-3x mb-3" style="opacity:.3;"></i>
            <p>Belum ada berita. <a href="{{ route('admin.berita.create') }}">Tambah sekarang</a>.</p>
        </div>
        @else
        <div class="row">
            @foreach($berita as $b)
            <div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
                <div class="berita-card card h-100">
                    @if($b->gambar)
                        <img src="{{ $b->gambar_url }}" alt="{{ $b->judul }}" class="berita-thumb">
                    @else
                        <div class="berita-thumb-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-{{ $b->is_published ? 'success' : 'secondary' }}" style="font-size:10px;">
                                {{ $b->is_published ? 'Published' : 'Draft' }}
                            </span>
                            <small class="text-muted" style="font-size:10px;">{{ $b->created_at->format('d M Y') }}</small>
                        </div>
                        <p class="font-weight-bold mb-1" style="font-size:13px;line-height:1.4;">
                            {{ Str::limit($b->judul, 55) }}
                        </p>
                        <p class="text-muted mb-0" style="font-size:11px;">{{ $b->ringkasan }}</p>
                    </div>
                    <div class="card-footer bg-white p-2 border-top">
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.berita.edit', $b) }}"
                               class="btn btn-sm btn-primary flex-fill" style="border-radius:8px;font-size:11px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.berita.publish', $b) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-{{ $b->is_published ? 'warning':'success' }}"
                                        style="border-radius:8px;font-size:11px;" title="{{ $b->is_published ? 'Jadikan Draft':'Publish' }}">
                                    <i class="fas fa-{{ $b->is_published ? 'eye-slash':'eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.berita.destroy', $b) }}" method="POST"
                                  onsubmit="return confirm('Hapus berita ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" style="border-radius:8px;font-size:11px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($berita->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-3">
            <small class="text-muted">{{ $berita->firstItem() }}–{{ $berita->lastItem() }} dari {{ $berita->total() }}</small>
            {{ $berita->withQueryString()->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection