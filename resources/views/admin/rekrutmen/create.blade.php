@extends('layouts.app')
@section('title', 'Buat Rekrutmen')

@section('content')
<div class="section-header">
    <h1>Buat Rekrutmen Baru</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.rekrutmen.index') }}">Rekrutmen</a></div>
        <div class="breadcrumb-item active">Buat</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h4>Informasi Rekrutmen</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.rekrutmen.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                                       value="{{ old('tahun', date('Y')) }}" min="2020" max="2099" required>
                                @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nama Rekrutmen <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', 'Rekrutmen Paskibra ' . date('Y')) }}"
                                       placeholder="contoh: Rekrutmen Paskibra 2025" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Buka <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_buka" class="form-control @error('tanggal_buka') is-invalid @enderror"
                                       value="{{ old('tanggal_buka') }}" required>
                                @error('tanggal_buka')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Tutup <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_tutup" class="form-control @error('tanggal_tutup') is-invalid @enderror"
                                       value="{{ old('tanggal_tutup') }}" required>
                                @error('tanggal_tutup')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putra</label>
                                <input type="number" name="kuota_putra" class="form-control" value="{{ old('kuota_putra', 8) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putri</label>
                                <input type="number" name="kuota_putri" class="form-control" value="{{ old('kuota_putri', 8) }}" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat rekrutmen...">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Syarat & Ketentuan</label>
                        <textarea name="syarat" class="form-control" rows="4" placeholder="Tuliskan syarat pendaftaran...">{{ old('syarat') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_aktif">
                                Aktifkan rekrutmen ini sekarang
                                <small class="text-muted d-block">Rekrutmen lain akan otomatis dinonaktifkan</small>
                            </label>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <a href="{{ route('admin.rekrutmen.index') }}" class="btn btn-secondary ml-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-primary">
            <div class="card-header"><h4>Info</h4></div>
            <div class="card-body">
                <p class="text-muted" style="font-size:.9rem">
                    Setelah rekrutmen dibuat, sistem akan otomatis membuat <strong>3 tahap seleksi default</strong>:
                </p>
                <ul class="text-muted" style="font-size:.9rem">
                    <li>Seleksi Administrasi</li>
                    <li>Seleksi Fisik & PBB</li>
                    <li>Wawancara & TIU</li>
                </ul>
                <p class="text-muted" style="font-size:.9rem">Tahap dapat diedit setelah rekrutmen dibuat.</p>
            </div>
        </div>
    </div>
</div>
@endsection