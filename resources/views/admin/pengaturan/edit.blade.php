@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="section-header">
    <h1>Pengaturan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Pengaturan</div>
    </div>
</div>

@php
    $rekrutmenAktif = \App\Models\Rekrutmen::where('is_aktif', true)->first();
    $jumlahLulus    = $rekrutmenAktif
        ? \App\Models\Pendaftaran::where('rekrutmen_id', $rekrutmenAktif->id)->where('is_lulus_final', true)->count()
        : 0;
@endphp

{{-- Stat Cards --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Peserta Lulus Final</h4></div>
                <div class="card-body">{{ $jumlahLulus }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Rekrutmen Aktif</h4></div>
                <div class="card-body" style="font-size:14px;">{{ $rekrutmenAktif?->nama ?? '-' }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon {{ $pengaturan->pengumuman_aktif ? 'bg-success' : 'bg-warning' }}">
                <i class="fas fa-{{ $pengaturan->pengumuman_aktif ? 'eye' : 'eye-slash' }}"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header"><h4>Pengumuman</h4></div>
                <div class="card-body">{{ $pengaturan->pengumuman_aktif ? 'Aktif' : 'Nonaktif' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- ── Pengumuman Kelulusan ── --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-bullhorn mr-2"></i>Pengumuman Kelulusan Final</h4>
                <div class="card-header-action">
                    <a href="{{ route('pengumuman') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-external-link-alt mr-1"></i> Preview
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- Status sekarang --}}
                <div class="alert alert-{{ $pengaturan->pengumuman_aktif ? 'success' : 'warning' }}">
                    <i class="fas fa-{{ $pengaturan->pengumuman_aktif ? 'check-circle' : 'clock' }} mr-1"></i>
                    @if($pengaturan->pengumuman_aktif)
                        Pengumuman <strong>AKTIF</strong> — publik dapat melihat daftar peserta lulus.
                        @if($pengaturan->pengumuman_diaktifkan_at)
                            <small class="d-block mt-1 text-muted">
                                Diaktifkan: {{ $pengaturan->pengumuman_diaktifkan_at->format('d M Y, H:i') }}
                            </small>
                        @endif
                    @else
                        Pengumuman <strong>BELUM AKTIF</strong> — publik tidak dapat melihat hasil kelulusan.
                    @endif
                </div>

                {{-- Form toggle pengumuman --}}
                <form action="{{ route('admin.pengaturan.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="font-weight-bold">
                            Pesan Pengumuman <small class="text-muted">(opsional)</small>
                        </label>
                        <textarea name="pesan_pengumuman" class="form-control" rows="3"
                            placeholder="Contoh: Selamat kepada seluruh peserta yang lulus seleksi Paskibra 2026...">{{ $pengaturan->pesan_pengumuman }}</textarea>
                        <small class="text-muted">Pesan ini akan ditampilkan di halaman pengumuman publik.</small>
                    </div>

                    <div class="d-flex mt-3" style="gap:8px;">
                        @if(!$pengaturan->pengumuman_aktif)
                        <button type="submit" name="aksi" value="aktifkan" class="btn btn-success"
                                onclick="return confirm('Aktifkan pengumuman? Publik akan dapat melihat daftar peserta lulus.')">
                            <i class="fas fa-bullhorn mr-1"></i> Aktifkan Pengumuman
                        </button>
                        @else
                        <button type="submit" name="aksi" value="nonaktifkan" class="btn btn-warning"
                                onclick="return confirm('Nonaktifkan pengumuman? Publik tidak akan bisa melihat hasil kelulusan.')">
                            <i class="fas fa-eye-slash mr-1"></i> Nonaktifkan Pengumuman
                        </button>
                        @endif
                        <button type="submit" name="aksi" value="simpan_pesan" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Pesan
                        </button>
                    </div>
                </form>

                <hr>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pengumuman hanya menampilkan peserta dengan status <strong>Lulus Final</strong>.
                    Pastikan proses seleksi sudah selesai sebelum mengaktifkan.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Info Sistem ── --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-cog mr-2"></i>Informasi Sistem</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="aksi" value="simpan_info">

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Kecamatan</label>
                        <input type="text" name="nama_kecamatan" class="form-control"
                               value="{{ $pengaturan->nama_kecamatan }}"
                               placeholder="Kecamatan Compreng">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Alamat Sekretariat</label>
                        <input type="text" name="alamat_sekretariat" class="form-control"
                               value="{{ $pengaturan->alamat_sekretariat }}"
                               placeholder="Jl. Raya Compreng No. 1">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">No. HP Panitia</label>
                        <input type="text" name="no_hp_panitia" class="form-control"
                               value="{{ $pengaturan->no_hp_panitia }}"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Simpan Info
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection