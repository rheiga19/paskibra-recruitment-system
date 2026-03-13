@extends('layouts.app')
@section('title', 'Hasil Akhir Seleksi')

@push('css')
<style>
.ha-card {
    border: none; border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow: hidden;
}
.ha-card-head {
    padding: 14px 20px; background: #fff;
    border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.ha-card-head h5 { margin: 0; font-size: 14px; font-weight: 700; color: #333; }

/* Tabel */
.ha-table th {
    font-size: 11px; color: #bbb; letter-spacing: 1px; text-transform: uppercase;
    font-weight: 600; padding: 10px 14px; background: #fafbfc;
    border-bottom: 1px solid #f0f0f0 !important;
}
.ha-table td { padding: 12px 14px; vertical-align: middle; border-color: #f5f5f5 !important; }
.ha-table tr.lulus-row td { background: #f0fdf8; }
.ha-table tr:hover td { background: #fafbff; }
.ha-table tr.lulus-row:hover td { background: #e8f9f0; }

/* Rank badge */
.rank-badge {
    width: 32px; height: 32px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 13px; color: #fff;
}
.rank-1 { background: linear-gradient(135deg, #f7c948, #e8a000); }
.rank-2 { background: linear-gradient(135deg, #b0c4de, #8899aa); }
.rank-3 { background: linear-gradient(135deg, #cd7f32, #a05c1a); }
.rank-n { background: #e0e0e0; color: #888; }

/* Nilai chip */
.nilai-chip {
    display: inline-block; padding: 2px 10px; border-radius: 100px;
    font-size: 11px; font-weight: 700; margin: 1px;
}
.nilai-chip.lolos { background: #e8faf3; color: #1cc88a; }
.nilai-chip.gagal  { background: #fde8e8; color: #e74a3b; }

/* Avatar */
.av { width: 34px; height: 34px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px; color: #fff; flex-shrink: 0; }

/* Checkbox custom */
.cb-wrap input[type="checkbox"] {
    width: 18px; height: 18px; cursor: pointer; accent-color: #cc0000;
}

/* Summary strip */
.summary-strip {
    display: flex; gap: 16px; flex-wrap: wrap;
    padding: 14px 20px; background: #fafbfc; border-bottom: 1px solid #f0f0f0;
}
.summary-item { text-align: center; }
.summary-item .val { font-size: 20px; font-weight: 800; }
.summary-item .lbl { font-size: 11px; color: #aaa; letter-spacing: 1px; text-transform: uppercase; }
</style>
@endpush

@section('content')

<div class="section-header">
    <h1>Hasil Akhir Seleksi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.seleksi.index') }}">Seleksi</a></div>
        <div class="breadcrumb-item active">Hasil Akhir</div>
    </div>
</div>

{{-- Filter Rekrutmen --}}
<div class="card ha-card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <i class="fas fa-bullhorn text-danger"></i>
            <label class="mb-0 font-weight-bold text-muted" style="font-size:13px;">Rekrutmen:</label>
            <select name="rekrutmen_id" class="form-control form-control-sm" style="min-width:220px"
                    onchange="this.form.submit()">
                @foreach($rekrutmenList as $r)
                <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected':'' }}>
                    {{ $r->nama }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($rekrutmen)
@php
    $jumlahLulus     = $peserta->where('is_lulus_final', true)->count();
    $jumlahTidakLulus = $peserta->where('is_lulus_final', false)->count();
    $rataRata        = $peserta->avg('nilai_akhir');
@endphp

<form action="{{ route('admin.seleksi.proses-kelulusan') }}" method="POST" id="formLulus">
    @csrf
    <input type="hidden" name="rekrutmen_id" value="{{ $rekrutmen->id }}">

    <div class="card ha-card">
        {{-- Header --}}
        <div class="ha-card-head">
            <h5>
                <i class="fas fa-trophy mr-2" style="color:#cc0000"></i>
                {{ $rekrutmen->nama }} — Peringkat Peserta
            </h5>
            <button type="submit" class="btn btn-success" style="border-radius:8px;"
                    onclick="return confirm('Tetapkan kelulusan sesuai pilihan?')">
                <i class="fas fa-check-double mr-1"></i> Tetapkan Kelulusan
            </button>
        </div>

        {{-- Summary --}}
        <div class="summary-strip">
            <div class="summary-item">
                <div class="val" style="color:#333;">{{ $peserta->total() }}</div>
                <div class="lbl">Total Peserta</div>
            </div>
            <div class="summary-item">
                <div class="val" style="color:#1cc88a;">{{ $jumlahLulus }}</div>
                <div class="lbl">Lulus Final</div>
            </div>
            <div class="summary-item">
                <div class="val" style="color:#e74a3b;">{{ $jumlahTidakLulus }}</div>
                <div class="lbl">Tidak Lulus</div>
            </div>
            <div class="summary-item">
                <div class="val" style="color:#667eea;">{{ $rataRata ? number_format($rataRata, 1) : '—' }}</div>
                <div class="lbl">Rata-rata Nilai</div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table ha-table mb-0">
                <thead>
                    <tr>
                        <th class="cb-wrap"><input type="checkbox" id="checkAll"></th>
                        <th>#</th>
                        <th>Peserta</th>
                        <th>JK</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th>Nilai per Tahap</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peserta as $i => $p)
                    @php
                        $rank    = $peserta->firstItem() + $i;
                        $rankCls = $rank <= 3 ? "rank-{$rank}" : 'rank-n';
                        $avColors = ['#667eea','#f5576c','#1cc88a','#f6c23e','#36b9cc','#cc0000'];
                        $avBg = $avColors[ord(strtoupper($p->nama_lengkap[0])) % count($avColors)];
                    @endphp
                    <tr class="{{ $p->is_lulus_final ? 'lulus-row' : '' }}">
                        <td class="cb-wrap">
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}"
                                   {{ $p->is_lulus_final ? 'checked' : '' }}>
                        </td>
                        <td>
                            <div class="rank-badge {{ $rankCls }}">{{ $rank }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="gap:10px;">
                                <div class="av" style="background:{{ $avBg }}">
                                    {{ strtoupper(substr($p->nama_lengkap,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $p->nama_lengkap }}</div>
                                    <div style="font-size:11px;color:#bbb;">{{ $p->no_pendaftaran }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;font-weight:600;color:{{ $p->jenis_kelamin==='L'?'#667eea':'#f5576c' }}">
                            {{ $p->jenis_kelamin === 'L' ? '♂ Putra' : '♀ Putri' }}
                        </td>
                        <td class="text-center">
                            @if($p->nilai_akhir)
                                <div style="font-size:20px;font-weight:800;color:{{ $p->nilai_akhir>=70?'#1cc88a':'#e74a3b' }}">
                                    {{ number_format($p->nilai_akhir, 1) }}
                                </div>
                            @else
                                <span style="color:#ddd;">—</span>
                            @endif
                        </td>
                        <td>
                            @forelse($p->hasilSeleksi as $h)
                                <span class="nilai-chip {{ $h->status==='lolos'?'lolos':'gagal' }}">
                                    {{ $h->tahap->nama ?? 'Tahap' }}: {{ $h->nilai_total }}
                                </span>
                            @empty
                                <span style="color:#ddd;font-size:12px;">Belum ada nilai</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            @if($p->is_lulus_final)
                                <span class="badge badge-success badge-pill px-3">
                                    <i class="fas fa-star mr-1"></i> Lulus Final
                                </span>
                            @else
                                @php $sc = ['menunggu'=>'warning','diverifikasi'=>'info','tidak_lulus'=>'danger'][$p->status] ?? 'secondary'; @endphp
                                <span class="badge badge-{{ $sc }} badge-pill px-3">{{ $p->status_label }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color:#ccc;">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            Belum ada data peserta
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peserta->hasPages())
        <div class="card-footer">{{ $peserta->links() }}</div>
        @endif
    </div>
</form>
@endif

@endsection

@push('js')
<script>
// Check all
const checkAll = document.getElementById('checkAll');
if (checkAll) {
    checkAll.addEventListener('change', function () {
        document.querySelectorAll('[name="ids[]"]')
            .forEach(cb => cb.checked = this.checked);
    });
}
</script>
@endpush