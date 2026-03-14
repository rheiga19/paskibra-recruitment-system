@extends('layouts.app')
@section('title', 'Jadwal Latihan & Absensi')

@section('content')
<div class="section-header">
    <h1>Absensi Latihan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Absensi</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

{{-- Macro select jam — dipakai ulang di dua form --}}
@php
    $jamOptions   = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));
    $menitOptions = ['00', '15', '30', '45'];
@endphp

<div class="row">

    {{-- ── KOLOM KIRI ── --}}
    <div class="col-lg-4">

        {{-- Form tambah satu jadwal --}}
        <div class="card">
            <div class="card-header"><h4><i class="fas fa-plus-circle mr-2"></i>Tambah Jadwal</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.absensi.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">Rekrutmen</label>
                        <select name="rekrutmen_id" class="form-control" required>
                            @foreach($rekrutmenList as $r)
                            <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected' : '' }}>
                                {{ $r->nama }} ({{ $r->tahun }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Sesi</label>
                        <input type="text" name="nama" class="form-control" required
                               placeholder="Latihan PBB Sesi 1">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                    {{-- Jam Masuk --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Jam Masuk</label>
                        <div class="input-group">
                            <select name="jam_masuk_h" class="form-control" required>
                                @foreach($jamOptions as $h)
                                <option value="{{ $h }}" {{ $h === '07' ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">:</span>
                            </div>
                            <select name="jam_masuk_m" class="form-control" required>
                                @foreach($menitOptions as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Jam Pulang --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Jam Pulang</label>
                        <div class="input-group">
                            <select name="jam_pulang_h" class="form-control" required>
                                @foreach($jamOptions as $h)
                                <option value="{{ $h }}" {{ $h === '15' ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">:</span>
                            </div>
                            <select name="jam_pulang_m" class="form-control" required>
                                @foreach($menitOptions as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="Lapangan Kecamatan Compreng">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Buat Jadwal
                    </button>
                </form>
            </div>
        </div>

        {{-- Form buat jadwal massal --}}
        <div class="card mt-3">
            <div class="card-header">
                <h4><i class="fas fa-calendar-week mr-2"></i>Buat Jadwal Massal</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.absensi.store-bulk') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">Rekrutmen</label>
                        <select name="rekrutmen_id" class="form-control" required>
                            @foreach($rekrutmenList as $r)
                            <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected' : '' }}>
                                {{ $r->nama }} ({{ $r->tahun }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Sesi</label>
                        <input type="text" name="nama" class="form-control" required
                               placeholder="Latihan PBB">
                        <small class="text-muted">Otomatis jadi: "Latihan PBB - Senin, 16 Mar 2026", dst.</small>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control" required
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" required
                                       value="{{ now()->addDays(6)->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Hari yang Aktif</label>
                        <div class="d-flex flex-wrap mt-1">
                            @foreach(['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6,'Minggu'=>0] as $namaHari => $val)
                            <div class="custom-control custom-checkbox mr-3 mb-1">
                                <input type="checkbox" class="custom-control-input"
                                       id="hari{{ $val }}" name="hari[]" value="{{ $val }}"
                                       {{ in_array($val, [1,2,3,4,5]) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="hari{{ $val }}">{{ $namaHari }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Jam Masuk Bulk --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Jam Masuk</label>
                        <div class="input-group">
                            <select name="jam_masuk_h" class="form-control" required>
                                @foreach($jamOptions as $h)
                                <option value="{{ $h }}" {{ $h === '07' ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">:</span>
                            </div>
                            <select name="jam_masuk_m" class="form-control" required>
                                @foreach($menitOptions as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Jam Pulang Bulk --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Jam Pulang</label>
                        <div class="input-group">
                            <select name="jam_pulang_h" class="form-control" required>
                                @foreach($jamOptions as $h)
                                <option value="{{ $h }}" {{ $h === '15' ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">:</span>
                            </div>
                            <select name="jam_pulang_m" class="form-control" required>
                                @foreach($menitOptions as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control"
                               placeholder="Lapangan Kecamatan Compreng">
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-calendar-plus mr-1"></i> Buat Semua Jadwal
                    </button>
                </form>
            </div>
        </div>

    </div>
    {{-- end col-lg-4 --}}

    {{-- ── KOLOM KANAN: Daftar jadwal ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Jadwal Latihan</h4>
                <div>
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId]) }}"
                       class="btn btn-sm btn-info mr-1">
                        <i class="fas fa-chart-bar mr-1"></i> Rekap
                    </a>
                    <a href="{{ route('admin.absensi.export-excel', ['rekrutmen_id' => $rekrutmenId]) }}"
                       class="btn btn-sm btn-success mr-1">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('admin.absensi.export-pdf', ['rekrutmen_id' => $rekrutmenId]) }}"
                       target="_blank" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($jadwalList as $j)
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="border-bottom:1px solid #f0f0f0;">
                    <div>
                        <div class="font-weight-bold">
                            {{ $j->nama }}
                            @if($j->isHariIni())
                            <span class="badge badge-primary ml-1">Hari Ini</span>
                            @endif
                        </div>
                        <div class="text-muted" style="font-size:13px;">
                            📅 {{ $j->tanggal->translatedFormat('l, d F Y') }}
                            &nbsp;·&nbsp;
                            🕐 {{ $j->jam_masuk }} – {{ $j->jam_pulang }}
                            @if($j->lokasi) &nbsp;·&nbsp; 📍 {{ $j->lokasi }} @endif
                        </div>
                        <div class="mt-1">
                            <span class="badge badge-success">{{ $j->jumlahHadir() }} Hadir</span>
                            <span class="badge badge-danger">{{ $j->jumlahAlpha() }} Alpha</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.absensi.scan', $j) }}"
                           class="btn btn-sm btn-{{ $j->isHariIni() ? 'primary' : 'outline-primary' }} mr-1">
                            <i class="fas fa-qrcode mr-1"></i>
                            {{ $j->isHariIni() ? 'Scan Sekarang' : 'Buka Scan' }}
                        </a>
                        <form action="{{ route('admin.absensi.destroy', $j) }}" method="POST"
                              onsubmit="return confirm('Hapus jadwal ini?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                    Belum ada jadwal latihan. Tambahkan jadwal di form sebelah kiri.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection