@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<div class="section-header">
    <h1>Rekap Absensi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.absensi.index') }}">Absensi</a></div>
        <div class="breadcrumb-item active">Rekap</div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.absensi.rekap') }}" class="d-flex flex-wrap align-items-end" style="gap:12px;">
            <input type="hidden" name="rekrutmen_id" value="{{ $rekrutmenId }}">

            {{-- Mode filter --}}
            <div class="form-group mb-0">
                <label class="font-weight-bold d-block" style="font-size:12px;">Tampilkan</label>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'semua']) }}"
                       class="btn btn-{{ $mode === 'semua' ? 'primary' : 'outline-primary' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'harian', 'tanggal' => $tanggalFilter]) }}"
                       class="btn btn-{{ $mode === 'harian' ? 'primary' : 'outline-primary' }}">
                        Harian
                    </a>
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'mingguan', 'minggu' => $mingguFilter]) }}"
                       class="btn btn-{{ $mode === 'mingguan' ? 'primary' : 'outline-primary' }}">
                        Mingguan
                    </a>
                    <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'bulanan', 'bulan' => $bulanFilter]) }}"
                       class="btn btn-{{ $mode === 'bulanan' ? 'primary' : 'outline-primary' }}">
                        Bulanan
                    </a>
                </div>
            </div>

            {{-- Filter harian --}}
            @if($mode === 'harian')
            <div class="form-group mb-0">
                <label class="font-weight-bold d-block" style="font-size:12px;">Tanggal</label>
                <input type="date" name="tanggal" class="form-control form-control-sm"
                       value="{{ $tanggalFilter }}"
                       onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=harian&tanggal='+this.value; this.form.submit()">
            </div>
            @endif

            {{-- Filter mingguan --}}
            @if($mode === 'mingguan')
            <div class="form-group mb-0">
                <label class="font-weight-bold d-block" style="font-size:12px;">Minggu</label>
                <input type="week" name="minggu" class="form-control form-control-sm"
                       value="{{ $mingguFilter }}"
                       onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=mingguan&minggu='+this.value; this.form.submit()">
            </div>
            @endif

            {{-- Filter bulanan --}}
            @if($mode === 'bulanan')
            <div class="form-group mb-0">
                <label class="font-weight-bold d-block" style="font-size:12px;">Bulan</label>
                <input type="month" name="bulan" class="form-control form-control-sm"
                       value="{{ $bulanFilter }}"
                       onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=bulanan&bulan='+this.value; this.form.submit()">
            </div>
            @endif

            {{-- Export --}}
            <div class="form-group mb-0 ml-auto">
                <label class="font-weight-bold d-block" style="font-size:12px;">Export</label>
                <div class="d-flex" style="gap:6px;">
                    <a href="{{ route('admin.absensi.export-excel', request()->query()) }}"
                       class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('admin.absensi.export-pdf', request()->query()) }}"
                       target="_blank" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Info periode --}}
<div class="alert alert-light py-2 mb-3" style="font-size:13px;">
    <i class="fas fa-calendar-alt mr-1"></i>
    <strong>{{ $rekrutmen?->nama }}</strong> &nbsp;·&nbsp;
    @if($mode === 'semua')
        Semua jadwal ({{ $jadwalList->count() }} sesi)
    @elseif($mode === 'harian')
        Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('l, d F Y') }}</strong>
        ({{ $jadwalList->count() }} sesi)
    @elseif($mode === 'mingguan')
        Minggu: <strong>{{ $mingguLabel }}</strong>
        ({{ $jadwalList->count() }} sesi)
    @elseif($mode === 'bulanan')
        Bulan: <strong>{{ \Carbon\Carbon::parse($bulanFilter . '-01')->translatedFormat('F Y') }}</strong>
        ({{ $jadwalList->count() }} sesi)
    @endif
</div>

{{-- Tabel rekap --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Rekap Kehadiran</h4>
        <div>
            <span class="badge badge-success mr-1">H = Hadir</span>
            <span class="badge badge-warning mr-1">I = Izin</span>
            <span class="badge badge-info mr-1">S = Sakit</span>
            <span class="badge badge-danger">A = Alpha</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0" style="font-size:13px;">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2" style="vertical-align:middle;">#</th>
                    <th rowspan="2" style="vertical-align:middle;">Nama Peserta</th>
                    <th rowspan="2" style="vertical-align:middle;text-align:center;">L/P</th>
                    @foreach($jadwalList as $j)
                    <th class="text-center" style="min-width:70px;font-size:11px;">
                        {{ $j->tanggal->format('d/m') }}<br>
                        <span style="font-weight:400;">{{ Str::limit($j->nama, 10) }}</span>
                    </th>
                    @endforeach
                    <th class="text-center" style="min-width:50px;">H</th>
                    <th class="text-center" style="min-width:50px;">I</th>
                    <th class="text-center" style="min-width:50px;">S</th>
                    <th class="text-center" style="min-width:50px;">A</th>
                    <th class="text-center" style="min-width:60px;">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peserta as $i => $p)
                @php
                    $totalHadir = 0;
                    $totalIzin  = 0;
                    $totalSakit = 0;
                    $totalAlpha = 0;
                    $total      = $jadwalList->count();
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="font-weight-bold">{{ $p->nama_lengkap }}</div>
                        <small class="text-muted">{{ $p->no_pendaftaran }}</small>
                    </td>
                    <td class="text-center">{{ $p->jenis_kelamin }}</td>

                    @foreach($jadwalList as $j)
                    @php
                        $abs    = $p->absensi->firstWhere('jadwal_latihan_id', $j->id);
                        $status = $abs ? $abs->status : 'alpha';
                        $badge  = match($status) {
                            'hadir' => 'success',
                            'izin'  => 'warning',
                            'sakit' => 'info',
                            default => 'danger'
                        };
                        $label  = match($status) {
                            'hadir' => 'H',
                            'izin'  => 'I',
                            'sakit' => 'S',
                            default => 'A'
                        };
                        match($status) {
                            'hadir' => $totalHadir++,
                            'izin'  => $totalIzin++,
                            'sakit' => $totalSakit++,
                            default => $totalAlpha++,
                        };
                    @endphp
                    <td class="text-center">
                        <span class="badge badge-{{ $badge }}">{{ $label }}</span>
                        @if($abs?->waktu_masuk)
                        <div style="font-size:10px;color:#888;">
                            {{ $abs->waktu_masuk->format('H:i') }}
                        </div>
                        @endif
                    </td>
                    @endforeach

                    @php $persen = $total > 0 ? round($totalHadir / $total * 100) : 0; @endphp
                    <td class="text-center font-weight-bold text-success">{{ $totalHadir }}</td>
                    <td class="text-center font-weight-bold text-warning">{{ $totalIzin }}</td>
                    <td class="text-center font-weight-bold text-info">{{ $totalSakit }}</td>
                    <td class="text-center font-weight-bold text-danger">{{ $totalAlpha }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $persen >= 80 ? 'success' : ($persen >= 60 ? 'warning' : 'danger') }}">
                            {{ $persen }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + $jadwalList->count() + 5 }}" class="text-center py-4 text-muted">
                        Tidak ada data untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection