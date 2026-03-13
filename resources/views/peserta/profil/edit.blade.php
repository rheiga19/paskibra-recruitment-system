@extends('layouts.app')
@section('title', 'Biodata Diri')

@section('content')
<div class="section-header">
    <h1>Biodata Diri</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Biodata</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<form action="{{ route('peserta.profil.update') }}" method="POST">
@csrf @method('PUT')

{{-- Data Diri --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-user mr-2 text-primary"></i>Data Diri</h4>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap', auth()->user()->name) }}" required>
                    @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>NIK <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                           value="{{ old('nik', $profil->nik) }}" maxlength="16" required>
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih...</option>
                        <option value="L" {{ old('jenis_kelamin', $profil->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                        <option value="P" {{ old('jenis_kelamin', $profil->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                    </select>
                    @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                           value="{{ old('tempat_lahir', $profil->tempat_lahir) }}" required>
                    @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                           value="{{ old('tanggal_lahir', $profil->tanggal_lahir?->format('Y-m-d')) }}" required>
                    @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>No. HP / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $profil->no_hp) }}" required>
                    @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Golongan Darah</label>
                    <select name="golongan_darah" class="form-control">
                        <option value="">Tidak tahu</option>
                        @foreach(['A','B','AB','O'] as $gd)
                        <option value="{{ $gd }}" {{ old('golongan_darah', $profil->golongan_darah) == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tinggi Badan (cm) <span class="text-danger">*</span></label>
                    <input type="number" name="tinggi_badan" class="form-control @error('tinggi_badan') is-invalid @enderror"
                           value="{{ old('tinggi_badan', $profil->tinggi_badan) }}" min="100" max="250" required>
                    @error('tinggi_badan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Berat Badan (kg) <span class="text-danger">*</span></label>
                    <input type="number" name="berat_badan" class="form-control @error('berat_badan') is-invalid @enderror"
                           value="{{ old('berat_badan', $profil->berat_badan) }}" min="20" max="200" required>
                    @error('berat_badan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror"
                              rows="2" required>{{ old('alamat_lengkap', $profil->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Prestasi / Penghargaan</label>
                    <textarea name="prestasi" class="form-control" rows="2"
                              placeholder="Contoh: Juara 1 PBB tingkat kecamatan 2024">{{ old('prestasi', $profil->prestasi) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Sekolah --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-school mr-2 text-primary"></i>Data Sekolah</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Sekolah <span class="text-danger">*</span></label>
                    <input type="text" name="nama_sekolah" class="form-control @error('nama_sekolah') is-invalid @enderror"
                           value="{{ old('nama_sekolah', $profil->nama_sekolah) }}" required>
                    @error('nama_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Jenjang <span class="text-danger">*</span></label>
                    <select name="jenjang" class="form-control @error('jenjang') is-invalid @enderror" required>
                        <option value="">Pilih...</option>
                        @foreach(['SMP','MTs','SMA','MA','SMK'] as $j)
                        <option value="{{ $j }}" {{ old('jenjang', $profil->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jenjang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Kelas <span class="text-danger">*</span></label>
                    <select name="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
                        <option value="">Pilih...</option>
                        @foreach(['VII','VIII','IX','X','XI','XII'] as $k)
                        <option value="{{ $k }}" {{ old('kelas', $profil->kelas) == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                    @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nilai Rata-rata Rapor <span class="text-danger">*</span></label>
                    <input type="number" name="nilai_rata" class="form-control @error('nilai_rata') is-invalid @enderror"
                           value="{{ old('nilai_rata', $profil->nilai_rata) }}" min="0" max="100" step="0.01" required>
                    @error('nilai_rata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Orang Tua --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-users mr-2 text-primary"></i>Data Orang Tua / Wali</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ortu" class="form-control @error('nama_ortu') is-invalid @enderror"
                           value="{{ old('nama_ortu', $profil->nama_ortu) }}" required>
                    @error('nama_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>No. HP Orang Tua <span class="text-danger">*</span></label>
                    <input type="text" name="hp_ortu" class="form-control @error('hp_ortu') is-invalid @enderror"
                           value="{{ old('hp_ortu', $profil->hp_ortu) }}" required>
                    @error('hp_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Hubungan <span class="text-danger">*</span></label>
                    <select name="hubungan_ortu" class="form-control @error('hubungan_ortu') is-invalid @enderror" required>
                        <option value="">Pilih...</option>
                        @foreach(['Ayah','Ibu','Wali'] as $h)
                        <option value="{{ $h }}" {{ old('hubungan_ortu', $profil->hubungan_ortu) == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    @error('hubungan_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('peserta.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    <button type="submit" class="btn btn-primary px-5">
        <i class="fas fa-save mr-1"></i> Simpan Biodata
    </button>
</div>

</form>
@endsection