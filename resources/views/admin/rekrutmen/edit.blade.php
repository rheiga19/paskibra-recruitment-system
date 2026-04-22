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

        {{-- Form Edit Rekrutmen --}}
        <div class="card">
            <div class="card-header"><h4>Edit: {{ $rekrutmen->nama }}</h4></div>
            <div class="card-body">

                {{-- FIX: action pakai route .update (PUT), bukan .edit (GET) --}}
                <form action="{{ route('admin.rekrutmen.update', $rekrutmen) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun"
                                       class="form-control @error('tahun') is-invalid @enderror"
                                       value="{{ old('tahun', $rekrutmen->tahun) }}"
                                       min="2020" max="2099" required>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nama Rekrutmen <span class="text-danger">*</span></label>
                                <input type="text" name="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $rekrutmen->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Buka <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_buka"
                                       class="form-control @error('tanggal_buka') is-invalid @enderror"
                                       value="{{ old('tanggal_buka', $rekrutmen->tanggal_buka->format('Y-m-d')) }}" required>
                                @error('tanggal_buka')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Tutup <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_tutup"
                                       class="form-control @error('tanggal_tutup') is-invalid @enderror"
                                       value="{{ old('tanggal_tutup', $rekrutmen->tanggal_tutup->format('Y-m-d')) }}" required>
                                @error('tanggal_tutup')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putra</label>
                                <input type="number" name="kuota_putra"
                                       class="form-control @error('kuota_putra') is-invalid @enderror"
                                       value="{{ old('kuota_putra', $rekrutmen->kuota_putra) }}"
                                       min="1" placeholder="Kosongkan jika tidak dibatasi">
                                @error('kuota_putra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kuota Putri</label>
                                <input type="number" name="kuota_putri"
                                       class="form-control @error('kuota_putri') is-invalid @enderror"
                                       value="{{ old('kuota_putri', $rekrutmen->kuota_putri) }}"
                                       min="1" placeholder="Kosongkan jika tidak dibatasi">
                                @error('kuota_putri')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  rows="3">{{ old('deskripsi', $rekrutmen->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Syarat &amp; Ketentuan</label>
                        <textarea name="syarat"
                                  class="form-control @error('syarat') is-invalid @enderror"
                                  rows="4">{{ old('syarat', $rekrutmen->syarat) }}</textarea>
                        @error('syarat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Catatan Internal</label>
                        <textarea name="catatan"
                                  class="form-control @error('catatan') is-invalid @enderror"
                                  rows="2">{{ old('catatan', $rekrutmen->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_aktif"
                                   name="is_aktif" value="1"
                                   {{ old('is_aktif', $rekrutmen->is_aktif) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_aktif">
                                Rekrutmen Aktif
                                <small class="text-muted d-block">Mengaktifkan akan menonaktifkan rekrutmen lain yang sedang aktif.</small>
                            </label>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.rekrutmen.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>

        {{-- Tahap Seleksi --}}
        <div class="card">
            <div class="card-header">
                <h4>Tahap Seleksi</h4>
                <div class="card-header-action">
                    <small class="text-muted">{{ $tahapList->count() }} tahap terdaftar</small>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width:80px">Urutan</th>
                            <th>Nama Tahap</th>
                            <th style="width:120px">Passing Grade</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahapList as $t)
                        <tr>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $t->urutan }}</span>
                            </td>
                            <td>{{ $t->nama }}</td>
                            <td>{{ $t->passing_grade ?? 70 }}</td>
                            <td>
                                <form action="{{ route('admin.seleksi.destroyTahap', $t) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger btn-icon"
                                            onclick="return confirm('Hapus tahap \'{{ addslashes($t->nama) }}\'?')"
                                            title="Hapus Tahap">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Belum ada tahap seleksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <form action="{{ route('admin.seleksi.storeTahap', $rekrutmen) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">Nama Tahap</label>
                            <input type="text" name="nama" class="form-control form-control-sm"
                                   placeholder="cth. Tes Kesehatan" required>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Urutan</label>
                            <input type="number" name="urutan" class="form-control form-control-sm"
                                   value="{{ $tahapList->count() + 1 }}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Passing Grade</label>
                            <input type="number" name="passing_grade" class="form-control form-control-sm"
                                   value="70" min="0" max="100">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success btn-sm btn-block">
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