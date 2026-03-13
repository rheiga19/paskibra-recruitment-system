@extends('layouts.app')
@section('title', 'Hasil Seleksi')

@push('css')
<style>
/* ── Cards ── */
.hcard { border: none; border-radius: 14px; box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow: hidden; }
.hcard-head {
    padding: 14px 20px; background: #fff;
    border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.hcard-head h5 { margin: 0; font-size: 14px; font-weight: 700; color: #333; }
.hcard-head .ic {
    width: 26px; height: 26px; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; color: #fff; margin-right: 8px;
}

/* ── Stat strip ── */
.stat-strip {
    display: flex; flex-wrap: wrap;
    border-bottom: 1px solid #f0f0f0; background: #fafbfc;
}
.stat-strip-item {
    flex: 1; min-width: 120px; padding: 14px 20px;
    border-right: 1px solid #f0f0f0; text-align: center;
}
.stat-strip-item:last-child { border-right: none; }
.stat-strip-item .val { font-size: 22px; font-weight: 800; line-height: 1; }
.stat-strip-item .lbl { font-size: 11px; color: #aaa; letter-spacing: 1px; text-transform: uppercase; margin-top: 3px; }

/* ── Tabel ── */
.htable th {
    font-size: 11px; color: #bbb; letter-spacing: 1px; text-transform: uppercase;
    font-weight: 600; padding: 10px 14px; background: #fafbfc;
    border-bottom: 1px solid #f0f0f0 !important;
}
.htable td { padding: 11px 14px; vertical-align: middle; border-color: #f7f7f7 !important; }
.htable tr:hover td { background: #fafbff; }
.htable tr.row-lolos td { background: #f0fdf8; }
.htable tr.row-lolos:hover td { background: #e4f9ef; }

/* ── Rank ── */
.rank-badge {
    width: 30px; height: 30px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 12px; color: #fff;
}
.r1 { background: linear-gradient(135deg,#f7c948,#e8a000); }
.r2 { background: linear-gradient(135deg,#b0c4de,#8899aa); }
.r3 { background: linear-gradient(135deg,#cd7f32,#a05c1a); }
.rn { background: #e8e8e8; color: #888; }

/* ── Avatar ── */
.av { width: 32px; height: 32px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px; color: #fff; flex-shrink: 0; }

/* ── Nilai bar ── */
.mini-bar { height: 4px; background: #f0f0f0; border-radius: 4px; margin-top: 5px; width: 80px; }
.mini-bar-fill { height: 100%; border-radius: 4px; }

/* ── Komponen nilai ── */
.nilai-chip {
    display: inline-block; padding: 1px 8px; border-radius: 100px;
    font-size: 10px; font-weight: 700; margin: 1px;
}
.chip-ok  { background: #e8faf3; color: #1cc88a; }
.chip-bad { background: #fde8e8; color: #e74a3b; }

/* ── Filter bar ── */
.filter-bar {
    padding: 12px 20px; background: #fff;
    border-bottom: 1px solid #f0f0f0;
    display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
}
.filter-bar label { font-size: 12px; font-weight: 700; color: #888; margin: 0; }

/* ── Tahap pills ── */
.tahap-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.tahap-tab {
    padding: 6px 16px; border-radius: 100px; font-size: 12px; font-weight: 600;
    border: 2px solid #e0e0e0; background: #fff; color: #888;
    cursor: pointer; transition: .2s; text-decoration: none;
}
.tahap-tab:hover { border-color: #cc0000; color: #cc0000; text-decoration: none; }
.tahap-tab.active { background: #cc0000; border-color: #cc0000; color: #fff; }
</style>
@endpush

@section('content')

<div class="section-header">
    <h1>Hasil Seleksi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Hasil Seleksi</div>
    </div>
</div>

{{-- Pilih Rekrutmen --}}
<div class="card hcard mb-3">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <i class="fas fa-bullhorn text-danger"></i>
            <label class="mb-0 font-weight-bold text-muted" style="font-size:13px;">Rekrutmen:</label>
            <select name="rekrutmen_id" class="form-control form-control-sm" style="min-width:220px"
                    onchange="this.form.submit()">
                @foreach($rekrutmenList as $r)
                <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected' : '' }}>
                    {{ $r->nama }}
                </option>
                @endforeach
            </select>
            @if(request('tahap_id'))
                <input type="hidden" name="tahap_id" value="{{ request('tahap_id') }}">
            @endif
        </form>
    </div>
</div>

@if($rekrutmen)

{{-- Tahap Navigation --}}
@if($tahapList->count())
<div class="tahap-tabs">
    <a href="{{ route('panitia.hasil.index', ['rekrutmen_id' => $rekrutmenId]) }}"
       class="tahap-tab {{ !$tahapAktif ? 'active' : '' }}">
        <i class="fas fa-layer-group mr-1"></i> Semua Tahap
    </a>
    @foreach($tahapList as $t)
    <a href="{{ route('panitia.hasil.index', ['rekrutmen_id' => $rekrutmenId, 'tahap_id' => $t->id]) }}"
       class="tahap-tab {{ $tahapAktif?->id == $t->id ? 'active' : '' }}">
        {{ $t->urutan }}. {{ $t->nama }}
        @if($t->is_diumumkan)
            <i class="fas fa-bullhorn ml-1" style="font-size:9px;opacity:.7;"></i>
        @endif
    </a>
    @endforeach
</div>
@endif

{{-- Main Card --}}
<div class="card hcard">

    {{-- Header --}}
    <div class="hcard-head">
        <h5>
            <span class="ic" style="background:#cc0000"><i class="fas fa-trophy"></i></span>
            {{ $tahapAktif ? $tahapAktif->nama . ' — Rekap Nilai' : 'Rekap Nilai Semua Tahap' }}
            @if($tahapAktif)
                <small class="text-muted ml-1" style="font-size:11px;font-weight:400;">
                    KKM: {{ $tahapAktif->passing_grade ?? 70 }}
                    @if($tahapAktif->is_diumumkan)
                        &nbsp;·&nbsp;<span class="badge badge-success" style="font-size:10px;">Sudah Diumumkan</span>
                    @else
                        &nbsp;·&nbsp;<span class="badge badge-secondary" style="font-size:10px;">Belum Diumumkan</span>
                    @endif
                </small>
            @endif
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <select id="filterHasil" class="form-control form-control-sm" style="border-radius:8px;width:auto;">
                <option value="">Semua Hasil</option>
                <option value="lolos">✅ Lolos</option>
                <option value="tidak_lolos">❌ Tidak Lolos</option>
            </select>
            <select id="filterJK" class="form-control form-control-sm" style="border-radius:8px;width:auto;">
                <option value="">Semua JK</option>
                <option value="L">♂ Putra</option>
                <option value="P">♀ Putri</option>
            </select>
            <input type="text" id="filterNama" class="form-control form-control-sm"
                   style="border-radius:8px;width:160px;" placeholder="🔍 Cari nama...">
        </div>
    </div>

    {{-- Stat Strip --}}
    <div class="stat-strip">
        <div class="stat-strip-item">
            <div class="val" style="color:#333;">{{ $totalDinilai }}</div>
            <div class="lbl">Total Dinilai</div>
        </div>
        <div class="stat-strip-item">
            <div class="val" style="color:#1cc88a;">{{ $totalLolos }}</div>
            <div class="lbl">Lolos</div>
        </div>
        <div class="stat-strip-item">
            <div class="val" style="color:#e74a3b;">{{ $totalTidakLolos }}</div>
            <div class="lbl">Tidak Lolos</div>
        </div>
        <div class="stat-strip-item">
            <div class="val" style="color:#667eea;">
                {{ $nilaiTertinggi ? number_format($nilaiTertinggi, 1) : '—' }}
            </div>
            <div class="lbl">Nilai Tertinggi</div>
        </div>
        <div class="stat-strip-item">
            <div class="val" style="color:#f6c23e;">
                {{ $nilaiRataRata ? number_format($nilaiRataRata, 1) : '—' }}
            </div>
            <div class="lbl">Rata-rata</div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table htable mb-0" id="tabelHasil">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Peserta</th>
                    <th>JK</th>
                    <th class="text-center d-none d-md-table-cell">Pancasila</th>
                    <th class="text-center d-none d-md-table-cell">TIU</th>
                    <th class="text-center d-none d-md-table-cell">PBB</th>
                    <th class="text-center d-none d-md-table-cell">Fisik</th>
                    <th class="text-center d-none d-md-table-cell">Wawancara</th>
                    <th class="text-center">Nilai Total</th>
                    <th class="text-center">Hasil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hasil as $i => $h)
                @php
                    $rank = $i + 1;
                    $rankCls = match($rank) { 1=>'r1', 2=>'r2', 3=>'r3', default=>'rn' };
                    $avColors = ['#667eea','#f5576c','#1cc88a','#f6c23e','#36b9cc','#cc0000'];
                    $avBg = $avColors[ord(strtoupper($h->pendaftaran->nama_lengkap[0])) % count($avColors)];
                    $kkm = $tahapAktif?->passing_grade ?? 70;
                    $lolos = $h->status === 'lolos';
                @endphp
                <tr class="{{ $lolos ? 'row-lolos' : '' }}"
                    data-status="{{ $h->status }}"
                    data-jk="{{ $h->pendaftaran->jenis_kelamin }}"
                    data-nama="{{ strtolower($h->pendaftaran->nama_lengkap) }}">

                    <td>
                        <div class="rank-badge {{ $rankCls }}">{{ $rank }}</div>
                    </td>

                    <td>
                        <div class="d-flex align-items-center" style="gap:10px;">
                            <div class="av" style="background:{{ $avBg }}">
                                {{ strtoupper(substr($h->pendaftaran->nama_lengkap,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">
                                    {{ $h->pendaftaran->nama_lengkap }}
                                </div>
                                <div style="font-size:11px;color:#bbb;">{{ $h->pendaftaran->no_pendaftaran }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span style="font-size:12px;font-weight:600;color:{{ $h->pendaftaran->jenis_kelamin==='L'?'#667eea':'#f5576c' }}">
                            {{ $h->pendaftaran->jenis_kelamin === 'L' ? '♂ Putra' : '♀ Putri' }}
                        </span>
                    </td>

                    @foreach(['nilai_pancasila','nilai_tiu','nilai_pbb','nilai_fisik','nilai_wawancara'] as $field)
                    <td class="text-center d-none d-md-table-cell">
                        @if($h->$field !== null)
                            <span style="font-size:13px;font-weight:600;color:{{ $h->$field >= $kkm ? '#1cc88a':'#e74a3b' }}">
                                {{ $h->$field }}
                            </span>
                        @else
                            <span style="color:#ddd;">—</span>
                        @endif
                    </td>
                    @endforeach

                    <td class="text-center">
                        <div style="font-size:18px;font-weight:800;color:{{ $lolos?'#1cc88a':'#e74a3b' }};">
                            {{ number_format($h->nilai_total, 1) }}
                        </div>
                        <div class="mini-bar" style="margin:0 auto;">
                            <div class="mini-bar-fill"
                                 style="width:{{ min($h->nilai_total,100) }}%;background:{{ $lolos?'#1cc88a':'#e74a3b' }};">
                            </div>
                        </div>
                    </td>

                    <td class="text-center">
                        @if($lolos)
                            <span class="badge badge-success badge-pill px-3">
                                <i class="fas fa-check mr-1"></i> Lolos
                            </span>
                        @else
                            <span class="badge badge-danger badge-pill px-3">
                                <i class="fas fa-times mr-1"></i> Tidak Lolos
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:#ccc;">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        Belum ada data nilai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($hasil, 'hasPages') && $hasil->hasPages())
    <div class="card-footer">{{ $hasil->links() }}</div>
    @endif
</div>

@endif

@endsection

@push('js')
<script>
function filterTabel() {
    const status = document.getElementById('filterHasil').value;
    const jk     = document.getElementById('filterJK').value;
    const nama   = document.getElementById('filterNama').value.toLowerCase();

    document.querySelectorAll('#tabelHasil tbody tr').forEach(row => {
        const okStatus = !status || row.dataset.status === status;
        const okJk     = !jk     || row.dataset.jk     === jk;
        const okNama   = !nama   || (row.dataset.nama   || '').includes(nama);
        row.style.display = (okStatus && okJk && okNama) ? '' : 'none';
    });
}

['filterHasil','filterJK'].forEach(id =>
    document.getElementById(id)?.addEventListener('change', filterTabel)
);
document.getElementById('filterNama')?.addEventListener('input', filterTabel);
</script>
@endpush