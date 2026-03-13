@extends('layouts.app')
@section('title', 'Input Nilai — ' . $pendaftaran->nama_lengkap)

@push('css')
<style>
.inp-card {
    border: none; border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,.09); overflow: hidden;
}
.inp-card-head {
    background: linear-gradient(135deg, #cc0000, #8b0000);
    padding: 20px 24px; color: #fff;
}
.inp-card-head h4 { margin: 0; font-weight: 800; font-size: 17px; }
.inp-card-head small { opacity: .8; font-size: 12px; }

.peserta-info {
    background: #fafbfc; border-bottom: 1px solid #f0f0f0;
    padding: 16px 24px;
    display: flex; gap: 24px; flex-wrap: wrap;
}
.peserta-info-item small { font-size: 11px; color: #aaa; letter-spacing: 1px; display: block; }
.peserta-info-item strong { font-size: 14px; color: #333; }

/* Nilai input card */
.nilai-group {
    background: #fff; border: 2px solid #f0f0f0; border-radius: 12px;
    padding: 16px; transition: border-color .2s;
}
.nilai-group:focus-within { border-color: #cc0000; }
.nilai-group label { font-weight: 700; font-size: 12px; color: #555; letter-spacing: .5px; margin-bottom: 8px; display: block; }
.nilai-group input { border: none; background: transparent; font-size: 22px; font-weight: 800;
    color: #333; width: 100%; outline: none; padding: 0; }
.nilai-group input::placeholder { color: #ddd; font-weight: 400; font-size: 18px; }
.nilai-group .sub { font-size: 11px; color: #bbb; margin-top: 4px; }

/* Preview total */
.preview-total {
    border-radius: 14px; padding: 20px 24px;
    background: linear-gradient(135deg, #f8f9fc, #f0f0f5);
    border: 2px solid #f0f0f0; transition: all .3s;
    text-align: center;
}
.preview-total.lolos { background: linear-gradient(135deg, #e8faf3, #d4f5e5); border-color: #1cc88a; }
.preview-total.gagal { background: linear-gradient(135deg, #fde8e8, #fcd4d4); border-color: #e74a3b; }
.preview-total .total-angka {
    font-size: 48px; font-weight: 900; line-height: 1;
    color: #ccc; transition: color .3s;
}
.preview-total.lolos .total-angka { color: #1cc88a; }
.preview-total.gagal .total-angka { color: #e74a3b; }
.preview-total .total-status { font-size: 14px; font-weight: 700; margin-top: 8px; color: #aaa; }
.preview-total.lolos .total-status { color: #1cc88a; }
.preview-total.gagal .total-status { color: #e74a3b; }
.preview-total .total-kkm { font-size: 11px; color: #bbb; margin-top: 4px; }

/* Progress bar nilai */
.nilai-progress { height: 5px; background: #f0f0f0; border-radius: 5px; margin-top: 8px; overflow: hidden; }
.nilai-progress-bar { height: 100%; border-radius: 5px; background: #cc0000; width: 0; transition: width .4s, background .4s; }
</style>
@endpush

@section('content')

<div class="section-header">
    <h1>Input Nilai</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">
            <a href="{{ route('admin.seleksi.index', ['rekrutmen_id' => $pendaftaran->rekrutmen_id, 'tahap_id' => $tahap->id]) }}">Seleksi</a>
        </div>
        <div class="breadcrumb-item active">Input Nilai</div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">

        <div class="inp-card">

            {{-- Header --}}
            <div class="inp-card-head">
                <h4><i class="fas fa-edit mr-2"></i>{{ $pendaftaran->nama_lengkap }}</h4>
                <small>{{ $tahap->nama }} &nbsp;·&nbsp; KKM: {{ $tahap->passing_grade ?? 70 }}</small>
            </div>

            {{-- Info Peserta --}}
            <div class="peserta-info">
                <div class="peserta-info-item">
                    <small>NO. PENDAFTARAN</small>
                    <strong>{{ $pendaftaran->no_pendaftaran ?? '—' }}</strong>
                </div>
                <div class="peserta-info-item">
                    <small>GENDER</small>
                    <strong style="color:{{ $pendaftaran->jenis_kelamin==='L'?'#667eea':'#f5576c' }}">
                        {{ $pendaftaran->jenis_kelamin === 'L' ? '♂ Putra' : '♀ Putri' }}
                    </strong>
                </div>
                <div class="peserta-info-item">
                    <small>SEKOLAH</small>
                    <strong>{{ $pendaftaran->nama_sekolah }}</strong>
                </div>
                <div class="peserta-info-item">
                    <small>TAHAP</small>
                    <strong>{{ $tahap->urutan }}. {{ $tahap->nama }}</strong>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.seleksi.simpan-nilai', [$pendaftaran, $tahap]) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body p-4">

                    {{-- Preview Total --}}
                    <div class="preview-total mb-4" id="previewBox">
                        <div class="total-angka" id="previewAngka">—</div>
                        <div class="total-status" id="previewStatus">Isi nilai untuk melihat hasil</div>
                        <div class="total-kkm">KKM: {{ $tahap->passing_grade ?? 70 }}</div>
                    </div>

                    {{-- Input Nilai 5 Komponen --}}
                    <div class="row">
                        @php
                        $komponen = [
                            'nilai_pancasila' => ['label' => 'Pancasila / Kebangsaan', 'icon' => 'fas fa-flag'],
                            'nilai_tiu'       => ['label' => 'TIU (Tes Inteligensi)', 'icon' => 'fas fa-brain'],
                            'nilai_pbb'       => ['label' => 'PBB (Baris-Berbaris)', 'icon' => 'fas fa-walking'],
                            'nilai_fisik'     => ['label' => 'Tes Fisik', 'icon' => 'fas fa-running'],
                            'nilai_wawancara' => ['label' => 'Wawancara', 'icon' => 'fas fa-comments'],
                        ];
                        @endphp

                        @foreach($komponen as $field => $info)
                        <div class="col-md-6 mb-3">
                            <div class="nilai-group">
                                <label>
                                    <i class="{{ $info['icon'] }} mr-1" style="color:#cc0000"></i>
                                    {{ $info['label'] }}
                                </label>
                                <input type="number"
                                       name="{{ $field }}"
                                       class="nilai-input"
                                       data-field="{{ $field }}"
                                       value="{{ old($field, $hasil->$field) }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="0 – 100"
                                       autocomplete="off">
                                <div class="nilai-progress">
                                    <div class="nilai-progress-bar" id="bar_{{ $field }}"></div>
                                </div>
                                <div class="sub">Maks: 100 &nbsp;·&nbsp; KKM: {{ $tahap->passing_grade ?? 70 }}</div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Catatan --}}
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-sticky-note mr-1 text-muted"></i> Catatan Penilaian
                                </label>
                                <textarea name="catatan" class="form-control" rows="3"
                                          placeholder="Catatan dari penilai (opsional)...">{{ old('catatan', $hasil->catatan) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-3 flex-wrap">
                        <button type="submit" class="btn btn-danger btn-lg px-5" style="border-radius:10px;font-weight:700;">
                            <i class="fas fa-save mr-2"></i> Simpan Nilai
                        </button>
                        <a href="{{ route('admin.seleksi.index', ['rekrutmen_id' => $pendaftaran->rekrutmen_id, 'tahap_id' => $tahap->id]) }}"
                           class="btn btn-light btn-lg px-4" style="border-radius:10px;">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('js')
<script>
const KKM = {{ $tahap->passing_grade ?? 70 }};
const fields = ['nilai_pancasila','nilai_tiu','nilai_pbb','nilai_fisik','nilai_wawancara'];

function updatePreview() {
    const vals = fields.map(f => {
        const el = document.querySelector(`[name="${f}"]`);
        const v  = parseFloat(el?.value);
        // Update bar
        const bar = document.getElementById('bar_' + f);
        if (bar) {
            bar.style.width = (isNaN(v) ? 0 : Math.min(v, 100)) + '%';
            bar.style.background = (!isNaN(v) && v >= KKM) ? '#1cc88a' : '#e74a3b';
        }
        return isNaN(v) ? null : v;
    }).filter(v => v !== null);

    const box    = document.getElementById('previewBox');
    const angka  = document.getElementById('previewAngka');
    const status = document.getElementById('previewStatus');

    if (!vals.length) {
        box.className    = 'preview-total mb-4';
        angka.textContent  = '—';
        status.textContent = 'Isi nilai untuk melihat hasil';
        return;
    }

    const total = (vals.reduce((a,b) => a+b, 0) / vals.length).toFixed(2);
    const lolos = parseFloat(total) >= KKM;

    angka.textContent  = total;
    status.textContent = lolos ? '✅ LOLOS' : '❌ TIDAK LOLOS';
    box.className      = 'preview-total mb-4 ' + (lolos ? 'lolos' : 'gagal');
}

document.querySelectorAll('.nilai-input').forEach(el => {
    el.addEventListener('input', updatePreview);
});

updatePreview();
</script>
@endpush