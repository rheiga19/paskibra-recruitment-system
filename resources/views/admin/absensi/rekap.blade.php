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
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-filter mr-2"></i>Filter</h4>
        <div class="card-header-action">
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
    <div class="card-body">
        <form method="GET" action="{{ route('admin.absensi.rekap') }}">
            <input type="hidden" name="rekrutmen_id" value="{{ $rekrutmenId }}">
            <div class="row align-items-end">

                <div class="col-auto mb-3">
                    <label class="font-weight-bold d-block">Tampilkan</label>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'semua']) }}"
                           class="btn btn-{{ $mode === 'semua' ? 'primary' : 'outline-primary' }}">Semua</a>
                        <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'harian', 'tanggal' => $tanggalFilter]) }}"
                           class="btn btn-{{ $mode === 'harian' ? 'primary' : 'outline-primary' }}">Harian</a>
                        <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'mingguan', 'minggu' => $mingguFilter]) }}"
                           class="btn btn-{{ $mode === 'mingguan' ? 'primary' : 'outline-primary' }}">Mingguan</a>
                        <a href="{{ route('admin.absensi.rekap', ['rekrutmen_id' => $rekrutmenId, 'mode' => 'bulanan', 'bulan' => $bulanFilter]) }}"
                           class="btn btn-{{ $mode === 'bulanan' ? 'primary' : 'outline-primary' }}">Bulanan</a>
                    </div>
                </div>

                @if($mode === 'harian')
                <div class="col-auto mb-3">
                    <label class="font-weight-bold d-block">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm"
                           value="{{ $tanggalFilter }}"
                           onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=harian&tanggal='+this.value; this.form.submit()">
                </div>
                @endif

                @if($mode === 'mingguan')
                <div class="col-auto mb-3">
                    <label class="font-weight-bold d-block">Minggu</label>
                    <input type="week" name="minggu" class="form-control form-control-sm"
                           value="{{ $mingguFilter }}"
                           onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=mingguan&minggu='+this.value; this.form.submit()">
                </div>
                @endif

                @if($mode === 'bulanan')
                <div class="col-auto mb-3">
                    <label class="font-weight-bold d-block">Bulan</label>
                    <input type="month" name="bulan" class="form-control form-control-sm"
                           value="{{ $bulanFilter }}"
                           onchange="this.form.action='{{ route('admin.absensi.rekap') }}?rekrutmen_id={{ $rekrutmenId }}&mode=bulanan&bulan='+this.value; this.form.submit()">
                </div>
                @endif

            </div>
        </form>
    </div>
</div>

{{-- Info periode --}}
<div class="card">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                <strong>{{ $rekrutmen?->nama }}</strong>
                <span class="text-muted mx-2">·</span>
                @if($mode === 'semua')
                    Semua jadwal
                @elseif($mode === 'harian')
                    Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('l, d F Y') }}</strong>
                @elseif($mode === 'mingguan')
                    Minggu: <strong>{{ $mingguLabel }}</strong>
                @elseif($mode === 'bulanan')
                    Bulan: <strong>{{ \Carbon\Carbon::parse($bulanFilter . '-01')->translatedFormat('F Y') }}</strong>
                @endif
                <span class="text-muted ml-2">({{ $jadwalList->count() }} sesi)</span>
            </div>
            <div>
                <div class="badge badge-success mr-1">H = Hadir</div>
                <div class="badge badge-warning mr-1">I = Izin</div>
                <div class="badge badge-info mr-1">S = Sakit</div>
                <div class="badge badge-danger">A = Alpha</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel rekap --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-table mr-2"></i>Rekap Kehadiran</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-md mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th class="text-center">L/P</th>
                        @foreach($jadwalList as $j)
                        <th class="text-center" style="min-width:70px;font-size:12px;">
                            {{ $j->tanggal->format('d/m') }}<br>
                            <span class="font-weight-normal text-muted">{{ Str::limit($j->nama, 10) }}</span>
                        </th>
                        @endforeach
                        <th class="text-center">H</th>
                        <th class="text-center">I</th>
                        <th class="text-center">S</th>
                        <th class="text-center">A</th>
                        <th class="text-center">%</th>
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
                            <b>{{ $p->nama_lengkap }}</b>
                            <br><small class="text-muted">{{ $p->no_pendaftaran }}</small>
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
                            <div class="badge badge-{{ $badge }}">{{ $label }}</div>
                            @if($abs?->waktu_masuk)
                            <div class="text-muted" style="font-size:10px;">
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
                            <div class="badge badge-{{ $persen >= 80 ? 'success' : ($persen >= 60 ? 'warning' : 'danger') }}">
                                {{ $persen }}%
                            </div>
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
</div>

@endsection 