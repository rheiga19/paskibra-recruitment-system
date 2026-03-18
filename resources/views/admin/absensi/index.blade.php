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

{{-- Macro select jam --}}
@php
    $jamOptions   = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));
    $menitOptions = ['00', '15', '30', '45'];
@endphp

<div class="row">

    {{-- ── KOLOM KIRI ── --}}
    <div class="col-lg-4">

        {{-- Form tambah satu jadwal --}}
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle mr-2"></i>Tambah Jadwal</h4>
            </div>
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
        <div class="card">
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

    {{-- ── KOLOM KANAN: Daftar jadwal ── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-calendar-alt mr-2"></i>Jadwal Latihan</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId]) }}"
                       class="btn btn-sm btn-info">
                        <i class="fas fa-chart-bar mr-1"></i> Rekap
                    </a>
                    <a href="{{ route('admin.absensi.export-excel', ['rekrutmen_id' => $rekrutmenId]) }}"
                       class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('admin.absensi.export-pdf', ['rekrutmen_id' => $rekrutmenId]) }}"
                       target="_blank" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md mb-0">
                        <thead>
                            <tr>
                                <th>Sesi</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Kehadiran</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalList as $j)
                            <tr>
                                <td>
                                    <b>{{ $j->nama }}</b>
                                    @if($j->isHariIni())
                                    <div class="badge badge-primary ml-1">Hari Ini</div>
                                    @endif
                                    @if($j->lokasi)
                                    <br><small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $j->lokasi }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $j->tanggal->translatedFormat('l, d F Y') }}</small>
                                </td>
                                <td>
                                    <small>{{ $j->jam_masuk }} – {{ $j->jam_pulang }}</small>
                                </td>
                                <td>
                                    <div class="badge badge-success">{{ $j->jumlahHadir() }} Hadir</div>
                                    <div class="badge badge-danger">{{ $j->jumlahAlpha() }} Alpha</div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.absensi.scan', $j) }}"
                                       class="btn btn-sm btn-{{ $j->isHariIni() ? 'primary' : 'outline-primary' }}">
                                        <i class="fas fa-qrcode mr-1"></i>
                                        {{ $j->isHariIni() ? 'Scan' : 'Buka' }}
                                    </a>
                                    <form action="{{ route('admin.absensi.destroy', $j) }}" method="POST"
                                          onsubmit="return confirm('Hapus jadwal ini?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-2x d-block mb-2" style="opacity:.3;"></i>
                                    Belum ada jadwal latihan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection