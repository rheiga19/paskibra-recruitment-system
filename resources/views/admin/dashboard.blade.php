@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="section-header">
    <h1>Dashboard Admin</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">Dashboard</div>
    </div>
</div>

{{-- ── STATUS REKRUTMEN AKTIF ── --}}
@if($rekrutmenAktif)
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-primary mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row gap-2">
                    <div class="d-flex align-items-start align-items-md-center gap-3">
                        <div class="bg-primary rounded p-2 text-white flex-shrink-0">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ $rekrutmenAktif->nama }}</div>
                            <small class="text-muted d-block d-sm-inline">
                                {{ $rekrutmenAktif->tanggal_buka->format('d M Y') }} –
                                {{ $rekrutmenAktif->tanggal_tutup->format('d M Y') }}
                            </small>
                            <small class="text-muted d-block d-sm-inline">
                                <span class="d-none d-sm-inline">&nbsp;·&nbsp;</span>
                                Kuota Putra: <strong>{{ $rekrutmenAktif->kuota_putra ?? '–' }}</strong>
                                &nbsp;|&nbsp;
                                Putri: <strong>{{ $rekrutmenAktif->kuota_putri ?? '–' }}</strong>
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('admin.rekrutmen.show', $rekrutmenAktif) }}"
                       class="btn btn-sm btn-primary flex-shrink-0">
                        <i class="fas fa-eye mr-1"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── STAT CARDS ── --}}
<div class="row">
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total Pendaftar</h4></div>
                <div class="card-body">{{ number_format($totalPendaftar) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Menunggu Verifikasi</h4></div>
                <div class="card-body">{{ number_format($totalMenunggu) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-trophy"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Peserta Lulus</h4></div>
                <div class="card-body">{{ number_format($totalLulus) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i class="fas fa-user-graduate"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total Akun Peserta</h4></div>
                <div class="card-body">{{ number_format($totalPeserta) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── GRAFIK HARIAN + GENDER ── --}}
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-line mr-2 text-primary"></i>Tren Pendaftaran 30 Hari Terakhir</h4>
            </div>
            <div class="card-body">
                <canvas id="grafikHarian" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="row">
            <div class="col-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-male"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Putra</h4></div>
                        <div class="card-body">{{ number_format($putra) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-female"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Putri</h4></div>
                        <div class="card-body">{{ number_format($putri) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── STATUS + JENJANG ── --}}
<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-bar mr-2 text-primary"></i>Pendaftar per Status</h4>
            </div>
            <div class="card-body">
                <canvas id="chartStatus" height="160"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-school mr-2 text-primary"></i>Pendaftar per Jenjang</h4>
            </div>
            <div class="card-body">
                <canvas id="chartJenjang" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── TABEL TERBARU + REKRUTMEN ── --}}
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list mr-2 text-primary"></i>Pendaftar Terbaru</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No. Daftar</th>
                                <th>Nama</th>
                                <th class="d-none d-sm-table-cell">JK</th>
                                <th class="d-none d-md-table-cell">Sekolah</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendaftaranTerbaru as $p)
                            <tr>
                                <td><small class="text-muted">{{ $p->no_pendaftaran ?? '-' }}</small></td>
                                <td class="font-weight-bold">{{ $p->nama_lengkap }}</td>
                                <td class="d-none d-sm-table-cell">
                                    {{ $p->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small>{{ $p->nama_sekolah }}</small>
                                </td>
                                <td>
                                    @php
                                        $colors = [
                                            'menunggu'    => 'warning',
                                            'diverifikasi'=> 'info',
                                            'lulus'       => 'success',
                                            'tidak_lulus' => 'danger',
                                        ];
                                        $c = $colors[$p->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $c }}">{{ $p->status_label }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pendaftaran.show', $p) }}"
                                       class="btn btn-sm btn-icon btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada pendaftar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-bullhorn mr-2 text-primary"></i>Rekrutmen</h4>
                <div class="card-header-action">
                    <a href="{{ route('admin.rekrutmen.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($rekrutmenList as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                        <div>
                            <div class="font-weight-bold" style="font-size:.9rem">{{ $r->nama }}</div>
                            <small class="text-muted">{{ $r->pendaftaran_count }} pendaftar</small>
                        </div>
                        <span class="badge badge-{{ $r->is_aktif ? 'success' : 'secondary' }}">
                            {{ $r->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4">
                        Belum ada rekrutmen
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js-libs')
<script src="{{ asset('assets/modules/chart.js/Chart.min.js') }}"></script>
@endpush

@push('js')
<script>
// ── Grafik Harian ────────────────────────────────────────────────────
new Chart(document.getElementById('grafikHarian'), {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Pendaftar',
            data: {!! json_encode($dataGrafik) !!},
            borderColor: '#6777ef',
            backgroundColor: 'rgba(103,119,239,0.1)',
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: '#6777ef',
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { ticks: { maxTicksLimit: 10 } }
        }
    }
});

// ── Bar Status ───────────────────────────────────────────────────────
const statusMap = {
    menunggu: 'Menunggu',
    diverifikasi: 'Diverifikasi',
    lulus: 'Lulus',
    tidak_lulus: 'Tidak Lulus'
};
const statusData = @json($perStatus);
new Chart(document.getElementById('chartStatus'), {
    type: 'bar',
    data: {
        labels: Object.keys(statusData).map(k => statusMap[k] || k),
        datasets: [{
            label: 'Jumlah',
            data: Object.values(statusData),
            backgroundColor: ['#ffa426', '#3abaf4', '#54ca68', '#fc544b'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── Horizontal Jenjang ───────────────────────────────────────────────
const jenjangData = @json($perJenjang);
new Chart(document.getElementById('chartJenjang'), {
    type: 'bar',
    data: {
        labels: Object.keys(jenjangData),
        datasets: [{
            label: 'Pendaftar',
            data: Object.values(jenjangData),
            backgroundColor: '#6777ef',
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush