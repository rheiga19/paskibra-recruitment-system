@extends('layouts.app')
@section('title', 'Dokumen Persyaratan')

@section('content')
<div class="section-header">
    <h1>Dokumen Persyaratan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Dokumen</div>
    </div>
</div>

@if(!$pendaftaranAktif)
<div class="alert alert-info" style="border-left:4px solid #3abaf4;">
    <div class="d-flex align-items-start gap-3">
        <i class="fas fa-exclamation-circle fa-2x text-info mr-3 mt-1"></i>
        <div>
            <strong>Perhatian sebelum mendaftar!</strong>
            <p class="mb-0 mt-1">Pastikan semua dokumen yang diupload sudah <strong>benar dan terbaca jelas</strong>.
            Setelah Anda menekan tombol <strong>Daftar</strong>, dokumen <strong>tidak bisa diubah lagi</strong>.
            Periksa kembali setiap file sebelum melanjutkan pendaftaran.</p>
        </div>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@php
$dokList = [
    'foto_4x6'        => ['label' => 'Pas Foto 4×6',            'sub' => 'Background merah, JPG. Maks 1MB.',       'icon' => 'camera',         'accept' => '.jpg,.jpeg,.png'],
    'ktp_pelajar'     => ['label' => 'Kartu Pelajar / KTP',     'sub' => 'JPG, PNG, atau PDF. Maks 2MB.',          'icon' => 'id-card',        'accept' => '.jpg,.jpeg,.png,.pdf'],
    'akta_kelahiran'  => ['label' => 'Akta Kelahiran',           'sub' => 'JPG, PNG, atau PDF. Maks 2MB.',          'icon' => 'file-alt',       'accept' => '.jpg,.jpeg,.png,.pdf'],
    'rapor'           => ['label' => 'Rapor Semester Terakhir',  'sub' => 'JPG, PNG, atau PDF. Maks 2MB.',          'icon' => 'book',           'accept' => '.jpg,.jpeg,.png,.pdf'],
    'surat_sehat'     => ['label' => 'Surat Keterangan Sehat',   'sub' => 'Dari dokter/puskesmas. Maks 2MB.',       'icon' => 'heartbeat',      'accept' => '.jpg,.jpeg,.png,.pdf'],
    'surat_izin_ortu' => ['label' => 'Surat Izin Orang Tua',     'sub' => 'Ditandatangani & bermaterai. Maks 2MB.', 'icon' => 'file-signature', 'accept' => '.jpg,.jpeg,.png,.pdf'],
];
$uploaded = $dokumen->count();
$total    = count($dokList);

// Hitung dokumen yang belum ada (belum pernah diupload sebelumnya)
$belumUpload = collect($dokList)->keys()->filter(fn($k) => !isset($dokumen[$k]))->values();
$sisaWajib   = $belumUpload->count();
@endphp

{{-- Progress --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold">Kelengkapan Dokumen</span>
            <span class="font-weight-bold {{ $uploaded == $total ? 'text-success' : 'text-warning' }}">
                {{ $uploaded }}/{{ $total }}
            </span>
        </div>
        <div class="progress" style="height:8px;border-radius:10px;">
            <div class="progress-bar bg-{{ $uploaded == $total ? 'success' : 'warning' }}"
                 style="width:{{ ($uploaded/$total)*100 }}%;border-radius:10px;transition:width .5s;"></div>
        </div>
        @if($uploaded == $total)
        <small class="text-success mt-1 d-block"><i class="fas fa-check-circle mr-1"></i>Semua dokumen lengkap!</small>
        @else
        <small class="text-muted mt-1 d-block">{{ $total - $uploaded }} dokumen belum diupload</small>
        @endif
    </div>
</div>

@if($pendaftaranAktif)
<div class="alert alert-warning">
    <i class="fas fa-lock mr-1"></i>
    <strong>Dokumen dikunci.</strong> Anda sudah mendaftar — dokumen tidak bisa diubah.
</div>
@endif

@if(!$pendaftaranAktif)
<form action="{{ route('peserta.dokumen.upload') }}" method="POST" enctype="multipart/form-data" id="formDokumen">
    @csrf
@endif

<div class="row">
    @foreach($dokList as $key => $info)
    @php
        $dok    = $dokumen[$key] ?? null;
        $isImg  = $dok ? preg_match('/\.(jpg|jpeg|png|webp)$/i', $dok->path) : false;
        $urlDok = $dok ? route('peserta.dokumen.lihat', $key) : null;
        $sudahAda = !is_null($dok); // sudah pernah diupload sebelumnya
    @endphp
    <div class="col-12 col-md-6 mb-3">
        <div class="card h-100 {{ $dok ? 'border-success' : '' }}" style="{{ $dok ? 'border-width:2px;' : '' }}">
            <div class="card-header">
                <h4>
                    <i class="fas fa-{{ $info['icon'] }} mr-2 text-primary"></i>
                    {{ $info['label'] }}
                </h4>
                @if($dok)
                <div class="card-header-action">
                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Uploaded</span>
                </div>
                @endif
            </div>
            <div class="card-body d-flex flex-column">

                {{-- Preview --}}
                @if($dok)
                <div class="mb-3 text-center">
                    @if($isImg)
                    <a href="{{ $urlDok }}" target="_blank">
                        <img src="{{ $urlDok }}"
                             alt="{{ $info['label'] }}"
                             class="img-fluid rounded border"
                             style="max-height:160px;object-fit:cover;cursor:zoom-in;"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div style="display:none;align-items:center;justify-content:center;height:100px;"
                             class="bg-light rounded border text-muted flex-column">
                            <i class="fas fa-image fa-2x mb-1"></i>
                            <small><a href="{{ $urlDok }}" target="_blank">Buka file</a></small>
                        </div>
                    </a>
                    @else
                    <a href="{{ $urlDok }}" target="_blank" class="text-decoration-none">
                        <div class="p-4 bg-light rounded border">
                            <i class="fas fa-file-pdf fa-3x text-danger d-block mb-2"></i>
                            <small class="text-muted">{{ $dok->nama_file }}</small>
                            <div class="mt-2">
                                <span class="badge badge-light border">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka PDF
                                </span>
                            </div>
                        </div>
                    </a>
                    @endif
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-clock mr-1"></i>{{ $dok->created_at->diffForHumans() }}
                    </small>
                </div>
                @else
                <div class="mb-3 text-center p-3 border rounded bg-light flex-grow-1 d-flex align-items-center justify-content-center flex-column"
                     id="preview_{{ $key }}">
                    <i class="fas fa-{{ $info['icon'] }} fa-3x text-muted mb-2"></i>
                    <small class="text-muted">Belum diupload</small>
                </div>
                @endif

                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle mr-1"></i>{{ $info['sub'] }}
                </p>

                @if(!$pendaftaranAktif)
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input dok-input"
                               id="file_{{ $key }}"
                               name="files[{{ $key }}]"
                               accept="{{ $info['accept'] }}"
                               data-key="{{ $key }}"
                               data-sudah="{{ $sudahAda ? '1' : '0' }}"
                               onchange="previewFile(this, '{{ $key }}')">
                        <label class="custom-file-label" for="file_{{ $key }}" id="label_{{ $key }}">
                            {{ $dok ? 'Ganti file...' : 'Pilih file...' }}
                        </label>
                    </div>
                </div>
                @error('files.'.$key)
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror

                @if($dok)
                <form action="{{ route('peserta.dokumen.hapus', $key) }}" method="POST" class="mt-2"
                      onsubmit="return confirm('Hapus dokumen ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger btn-block">
                        Hapus Dokumen
                    </button>
                </form>
                @endif
                @endif

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tombol Simpan --}}
@if(!$pendaftaranAktif)
<div class="text-center mt-3 mb-2">
    {{-- Pesan sisa dokumen yang belum dipilih --}}
    <div id="infoSimpan" class="mb-2">
        @if($sisaWajib > 0)
        <small class="text-danger" id="pesanSisa">
            Pilih file untuk <strong id="jumlahSisa">{{ $sisaWajib }}</strong> dokumen yang belum diupload untuk mengaktifkan tombol simpan.
        </small>
        @endif
    </div>
    <button type="submit" form="formDokumen"
            class="btn btn-primary btn-lg px-5"
            id="btnSimpan"
            {{ $sisaWajib > 0 ? 'disabled' : '' }}>
        Simpan Semua Dokumen
    </button>
</div>
</form>
@endif

<div class="text-center mt-2 mb-4">
    <a href="{{ route('peserta.dashboard') }}" class="btn btn-secondary">
        Kembali ke Dashboard
    </a>
</div>
@endsection

@push('js')
<script>
// Dokumen yang belum pernah diupload — wajib dipilih file baru
const wajibDipilih = @json($belumUpload);
const dipilih      = {}; // key => true/false

function previewFile(input, key) {
    const label = document.getElementById('label_' + key);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        label.textContent = file.name;

        // Preview
        const previewBox = document.getElementById('preview_' + key);
        if (previewBox) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewBox.innerHTML = `<img src="${e.target.result}"
                        class="img-fluid rounded border"
                        style="max-height:160px;object-fit:cover;">`;
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.innerHTML = `
                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                    <small class="text-muted d-block mt-1">${file.name}</small>`;
            }
        }

        // Tandai sudah dipilih
        if (wajibDipilih.includes(key)) {
            dipilih[key] = true;
        }
    } else {
        if (wajibDipilih.includes(key)) {
            dipilih[key] = false;
        }
    }

    cekSimpan();
}

function cekSimpan() {
    const btnSimpan  = document.getElementById('btnSimpan');
    const pesanSisa  = document.getElementById('pesanSisa');
    const jumlahSisa = document.getElementById('jumlahSisa');

    // Hitung berapa dari wajibDipilih yang belum dipilih
    const belumDipilih = wajibDipilih.filter(k => !dipilih[k]);
    const sisa         = belumDipilih.length;

    if (sisa === 0) {
        btnSimpan.disabled = false;
        if (pesanSisa) pesanSisa.style.display = 'none';
    } else {
        btnSimpan.disabled = true;
        if (pesanSisa) {
            pesanSisa.style.display = '';
            jumlahSisa.textContent  = sisa;
        }
    }
}

// Jalankan saat load untuk kondisi awal
cekSimpan();
</script>
@endpush