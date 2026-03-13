@extends('layouts.app')
@section('title', 'Manajemen Seleksi')

@push('css')
<style>
.sel-card {
    border: none; border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow: hidden;
}
.sel-card-head {
    padding: 14px 20px; background: #fff;
    border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px;
}
.sel-card-head h5 { margin: 0; font-weight: 700; font-size: 14px; color: #333; }

/* Tahap pills */
.tahap-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
.tahap-pill {
    padding: 7px 16px; border-radius: 100px; font-size: 12px;
    font-weight: 600; border: 2px solid #e0e0e0; background: #fff;
    color: #777; cursor: pointer; text-decoration: none; transition: .2s;
}
.tahap-pill:hover { border-color: #cc0000; color: #cc0000; text-decoration: none; }
.tahap-pill.active { background: #cc0000; border-color: #cc0000; color: #fff; }
.tahap-pill .kkm-badge {
    background: rgba(255,255,255,.25); border-radius: 100px;
    padding: 1px 7px; font-size: 10px; margin-left: 5px;
}
.tahap-pill:not(.active) .kkm-badge { background: #f0f0f0; color: #999; }

/* Status badge pengumuman */
.badge-announce { font-size: 11px; padding: 5px 12px; border-radius: 100px; }

/* Tabel */
.sel-table th {
    font-size: 11px; color: #bbb; letter-spacing: 1px;
    text-transform: uppercase; font-weight: 600;
    padding: 10px 14px; background: #fafbfc;
    border-bottom: 1px solid #f0f0f0 !important;
}
.sel-table td { padding: 11px 14px; vertical-align: middle; border-color: #f7f7f7 !important; }
.sel-table tr:hover td { background: #fafbff; }

/* Nilai box */
.nilai-box {
    display: inline-block; min-width: 36px; text-align: center;
    background: #f5f5f5; border-radius: 6px;
    padding: 3px 8px; font-size: 13px; font-weight: 600; color: #555;
}
.nilai-box.lolos   { background: #e8f9f0; color: #1cc88a; }
.nilai-box.gagal   { background: #fde8e8; color: #e74a3b; }

/* Avatar inisial */
.av { width: 34px; height: 34px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #fff; flex-shrink: 0; }

/* Progress nilai */
.nilai-bar { height: 4px; border-radius: 4px; background: #f0f0f0; margin-top: 4px; }
.nilai-bar-fill { height: 100%; border-radius: 4px; transition: width .4s; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="section-header">
    <h1>Manajemen Seleksi</h1>
    <div class="section-header-button">
        <a href="{{ route('admin.seleksi.hasil-akhir', ['rekrutmen_id' => $rekrutmenId]) }}"
           class="btn btn-success">
            <i class="fas fa-trophy mr-1"></i> Hasil Akhir
        </a>
    </div>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Seleksi</div>
    </div>
</div>

{{-- Filter Rekrutmen --}}
<div class="card sel-card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-bullhorn text-danger"></i>
                <label class="mb-0 font-weight-bold text-muted" style="font-size:13px;white-space:nowrap;">Rekrutmen:</label>
                <select name="rekrutmen_id" class="form-control form-control-sm" style="min-width:220px"
                        onchange="this.form.submit()">
                    @foreach($rekrutmen as $r)
                    <option value="{{ $r->id }}" {{ $rekrutmenId == $r->id ? 'selected' : '' }}>
                        {{ $r->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary ml-auto"
                    data-toggle="modal" data-target="#modalTambahTahap">
                <i class="fas fa-plus mr-1"></i> Tambah Tahap
            </button>
        </form>
    </div>
</div>

{{-- Tahap Pills Navigation --}}
@if($tahapList->count())
<div class="tahap-nav">
    @foreach($tahapList as $t)
    <a href="{{ route('admin.seleksi.index', ['rekrutmen_id' => $rekrutmenId, 'tahap_id' => $t->id]) }}"
       class="tahap-pill {{ $tahapAktif?->id == $t->id ? 'active' : '' }}">
        <i class="fas fa-circle-notch mr-1" style="font-size:8px;"></i>
        {{ $t->urutan }}. {{ $t->nama }}
        <span class="kkm-badge">KKM {{ $t->passing_grade ?? 70 }}</span>
    </a>
    @endforeach
</div>
@endif

{{-- Tabel Peserta --}}
@if($tahapAktif)
<div class="card sel-card">
    <div class="sel-card-head">
        <h5>
            <i class="fas fa-list-ol mr-2" style="color:#cc0000"></i>
            {{ $tahapAktif->nama }}
            <small class="text-muted ml-1" style="font-size:12px;font-weight:400;">
                — KKM {{ $tahapAktif->passing_grade ?? 70 }}
            </small>
        </h5>
        <div class="d-flex align-items-center flex-wrap gap-2">
            {{-- Status pengumuman --}}
            @if($tahapAktif->is_diumumkan)
                <span class="badge badge-success badge-announce">
                    <i class="fas fa-bullhorn mr-1"></i> Diumumkan
                    @if($tahapAktif->tanggal_pengumuman)
                        · {{ $tahapAktif->tanggal_pengumuman->format('d M Y') }}
                    @endif
                </span>
            @else
                <span class="badge badge-secondary badge-announce">
                    <i class="fas fa-eye-slash mr-1"></i> Belum Diumumkan
                </span>
            @endif

            {{-- Toggle pengumuman --}}
            <form action="{{ route('admin.seleksi.togglePengumuman', $tahapAktif) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm btn-{{ $tahapAktif->is_diumumkan ? 'warning' : 'success' }}"
                        onclick="return confirm('{{ $tahapAktif->is_diumumkan ? 'Sembunyikan pengumuman ini?' : 'Umumkan hasil tahap ini ke peserta?' }}')">
                    <i class="fas fa-{{ $tahapAktif->is_diumumkan ? 'eye-slash' : 'bullhorn' }} mr-1"></i>
                    {{ $tahapAktif->is_diumumkan ? 'Sembunyikan' : 'Umumkan' }}
                </button>
            </form>

            {{-- Hapus tahap --}}
            <form action="{{ route('admin.seleksi.destroyTahap', $tahapAktif) }}" method="POST"
                  onsubmit="return confirm('Hapus tahap ini beserta semua nilainya?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash mr-1"></i> Hapus Tahap
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table sel-table mb-0">
            <thead>
                <tr>
                    <th>Peserta</th>
                    <th>JK</th>
                    <th class="text-center">Pancasila</th>
                    <th class="text-center">TIU</th>
                    <th class="text-center">PBB</th>
                    <th class="text-center">Fisik</th>
                    <th class="text-center">Wawancara</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peserta as $p)
                @php
                    $h = $p->hasilSeleksi->first();
                    $avColors = ['#667eea','#f5576c','#1cc88a','#f6c23e','#36b9cc','#cc0000'];
                    $avBg = $avColors[ord(strtoupper($p->nama_lengkap[0])) % count($avColors)];
                    $kkm = $tahapAktif->passing_grade ?? 70;
                @endphp
                <tr>
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
                    <td>
                        <span style="font-size:12px;font-weight:600;color:{{ $p->jenis_kelamin==='L'?'#667eea':'#f5576c' }}">
                            {{ $p->jenis_kelamin === 'L' ? '♂ Putra' : '♀ Putri' }}
                        </span>
                    </td>

                    @foreach(['nilai_pancasila','nilai_tiu','nilai_pbb','nilai_fisik','nilai_wawancara'] as $field)
                    <td class="text-center">
                        @if($h && $h->$field !== null)
                            <span class="nilai-box {{ $h->$field >= $kkm ? 'lolos' : 'gagal' }}">
                                {{ $h->$field }}
                            </span>
                        @else
                            <span style="color:#ddd;">—</span>
                        @endif
                    </td>
                    @endforeach

                    <td class="text-center">
                        @if($h)
                            <div style="font-weight:700;font-size:15px;color:{{ $h->nilai_total >= $kkm ? '#1cc88a' : '#e74a3b' }}">
                                {{ $h->nilai_total }}
                            </div>
                            <div class="nilai-bar" style="width:60px;margin:0 auto;">
                                <div class="nilai-bar-fill"
                                     style="width:{{ min($h->nilai_total,100) }}%;background:{{ $h->nilai_total >= $kkm ? '#1cc88a' : '#e74a3b' }}">
                                </div>
                            </div>
                        @else
                            <span style="color:#ddd;">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($h)
                            <span class="badge badge-{{ $h->status === 'lolos' ? 'success' : 'danger' }} badge-pill px-3">
                                <i class="fas fa-{{ $h->status === 'lolos' ? 'check' : 'times' }} mr-1"></i>
                                {{ $h->status === 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                            </span>
                        @else
                            <span class="badge badge-secondary badge-pill px-3">Belum Dinilai</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('admin.seleksi.input-nilai', [$p, $tahapAktif]) }}"
                           class="btn btn-sm btn-{{ $h ? 'warning' : 'primary' }}"
                           style="border-radius:8px;font-size:12px;"
                           title="{{ $h ? 'Edit Nilai' : 'Input Nilai' }}">
                            <i class="fas fa-{{ $h ? 'edit' : 'plus' }} mr-1"></i>
                            {{ $h ? 'Edit' : 'Input' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:#ccc;">
                        <i class="fas fa-users-slash fa-2x d-block mb-2"></i>
                        Belum ada peserta yang lolos administrasi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($peserta instanceof \Illuminate\Pagination\LengthAwarePaginator && $peserta->hasPages())
    <div class="card-footer">{{ $peserta->links() }}</div>
    @endif
</div>
@else
<div class="text-center py-5" style="color:#ccc;">
    <i class="fas fa-layer-group fa-3x d-block mb-3"></i>
    <p>Belum ada tahap seleksi. Klik <strong>"Tambah Tahap"</strong> untuk memulai.</p>
</div>
@endif

{{-- Modal Tambah Tahap --}}
@php $rekrutmenObj = $rekrutmen->firstWhere('id', $rekrutmenId); @endphp
@if($rekrutmenObj)
<div class="modal fade" id="modalTambahTahap" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;">
            <form action="{{ route('admin.seleksi.storeTahap', $rekrutmenObj) }}" method="POST">
                @csrf
                <div class="modal-header" style="background:#cc0000;color:#fff;border:none;">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Tahap Seleksi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Tahap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control"
                               placeholder="Contoh: Tes Tertulis, Tes Fisik..." required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Urutan <span class="text-danger">*</span></label>
                                <input type="number" name="urutan" class="form-control"
                                       value="{{ $tahapList->count() + 1 }}" min="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold">KKM / Passing Grade</label>
                                <input type="number" name="passing_grade" class="form-control"
                                       value="70" min="0" max="100" step="0.01"
                                       placeholder="0–100">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"
                                  placeholder="Keterangan tahap (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save mr-1"></i> Simpan Tahap
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection