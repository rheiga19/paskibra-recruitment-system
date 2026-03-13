@extends('layouts.app')
@section('title', 'Edit Rekrutmen')

@section('content')
<div class="section-header">
    <h1>Edit Rekrutmen</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.rekrutmen.index') }}">Rekrutmen</a></div>
        <div class="breadcrumb-item active">Edit</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h4>Edit: {{ $rekrutmen->nama }}</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.rekrutmen.update', $rekrutmen) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                                       value="{{ old('tahun', $rekrutmen->tahun) }}" required>
                                @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nama Rekrutmen <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $rekrutmen->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Buka</label>
                                <input type="date" name="tanggal_buka" class="form-control"
                                       value="{{ old('tanggal_buka', $rekrutmen->tanggal_buka->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Tutup</label>
                                <input type="date" name="tanggal_tutup" class="form-control"
                                       value="{{ old('tanggal_tutup', $rekrutmen->tanggal_tutup->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putra</label>
                                <input type="number" name="kuota_putra" class="form-control" value="{{ old('kuota_putra', $rekrutmen->kuota_putra) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putri</label>
                                <input type="number" name="kuota_putri" class="form-control" value="{{ old('kuota_putri', $rekrutmen->kuota_putri) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $rekrutmen->deskripsi) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Syarat & Ketentuan</label>
                        <textarea name="syarat" class="form-control" rows="4">{{ old('syarat', $rekrutmen->syarat) }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_aktif" name="is_aktif" value="1"
                                   {{ old('is_aktif', $rekrutmen->is_aktif) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_aktif">Rekrutmen Aktif</label>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button>
                        <a href="{{ route('admin.rekrutmen.index') }}" class="btn btn-secondary ml-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tahap Seleksi --}}
        <div class="card">
            <div class="card-header"><h4>Tahap Seleksi</h4></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Urutan</th><th>Nama Tahap</th><th>Passing Grade</th></tr></thead>
                    <tbody>
                        @foreach($tahapList as $t)
                        <tr>
                            <td>{{ $t->urutan }}</td>
                            <td>{{ $t->nama }}</td>
                            <td>{{ $t->passing_grade ?? 70 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <form action="{{ route('admin.seleksi.storeTahap', $rekrutmen) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <input type="text" name="nama" class="form-control" placeholder="Nama tahap baru" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="urutan" class="form-control" placeholder="Urutan" value="{{ $tahapList->count() + 1 }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="passing_grade" class="form-control" placeholder="KKM" value="70">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-plus mr-1"></i> Tambah Tahap
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection