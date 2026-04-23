@extends('layouts.app')
@section('title', 'Dokumen Persyaratan')

@section('content')

<div class="section-header">
    <h1>Dokumen Persyaratan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item">
            <a href="{{ route('peserta.dashboard') }}">Dashboard</a>
        </div>
        <div class="breadcrumb-item active">Dokumen</div>
    </div>
</div>

{{-- ================= ALERT ================= --}}
@if(!$pendaftaranAktif)
<div class="alert alert-info" style="border-left:4px solid #3abaf4;">
    <div class="d-flex">
        <i class="fas fa-info-circle fa-2x mr-3 mt-1"></i>
        <div>
            <strong>Perhatian sebelum mendaftar!</strong><br>
            Pastikan semua dokumen benar dan terbaca jelas.
            Setelah menekan tombol <b>Daftar</b>, dokumen tidak bisa diubah lagi.
        </div>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
</div>
@endif

@php
$dokList = [
'foto_4x6'        => ['label'=>'Pas Foto 4x6','sub'=>'Background merah, JPG max 1MB','icon'=>'camera','accept'=>'.jpg,.jpeg,.png'],
'sertifikat'      => ['label'=>'Sertifikat Prestasi','sub'=>'JPG PNG PDF max 2MB','icon'=>'trophy','accept'=>'.jpg,.jpeg,.png,.pdf'],
'surat_sehat'     => ['label'=>'Surat Keterangan Sehat','sub'=>'JPG PNG PDF max 2MB','icon'=>'heartbeat','accept'=>'.jpg,.jpeg,.png,.pdf'],
'surat_izin_ortu' => ['label'=>'Surat Izin Orang Tua','sub'=>'JPG PNG PDF max 2MB','icon'=>'file-signature','accept'=>'.jpg,.jpeg,.png,.pdf'],
];

$uploaded = $dokumen->count();
$total = count($dokList);
@endphp

{{-- ================= PROGRESS ================= --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <b>Kelengkapan Dokumen</b>
            <b>{{ $uploaded }}/{{ $total }}</b>
        </div>

        <div class="progress" style="height:10px;">
            <div class="progress-bar bg-success"
                 style="width:{{ ($uploaded/$total)*100 }}%">
            </div>
        </div>

        <small class="text-muted d-block mt-2">
            {{ $total - $uploaded }} dokumen belum diupload
        </small>
    </div>
</div>

{{-- ================= FORM ================= --}}
<form action="{{ route('peserta.dokumen.uploadSemua') }}"
      method="POST"
      enctype="multipart/form-data"
      id="formDokumen">
@csrf

<div class="row">

@foreach($dokList as $key => $info)

@php
$dok = $dokumen[$key] ?? null;
$isImg = $dok ? preg_match('/\.(jpg|jpeg|png)$/i',$dok->path) : false;
$urlDok = $dok ? route('peserta.dokumen.lihat',$key) : null;
@endphp

<div class="col-md-6 mb-4">
<div class="card dokumen-card {{ $dok ? 'uploaded' : '' }} h-100">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>
            <i class="fas fa-{{ $info['icon'] }} text-primary mr-2"></i>
            {{ $info['label'] }}
        </h4>

        @if($dok)
        <span class="badge badge-success badge-upload">
            <i class="fas fa-check-circle mr-1"></i> Uploaded
        </span>
        @endif
    </div>

    {{-- BODY --}}
    <div class="card-body d-flex flex-column">

        {{-- PREVIEW --}}
        @if($dok)

            @if($isImg)
            <div class="mb-3 text-center">
                <a href="{{ $urlDok }}" target="_blank">
                    <img src="{{ $urlDok }}" class="preview-img">
                </a>
            </div>
            @else
            <div class="preview-file text-center mb-3">
                <a href="{{ $urlDok }}" target="_blank">
                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                    <div>{{ $dok->nama_file }}</div>
                </a>
            </div>
            @endif

            <small class="text-muted d-block mb-3">
                Upload {{ $dok->created_at->diffForHumans() }}
            </small>

        @else

            <div class="placeholder-upload text-center mb-3">
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                <div>Belum diupload</div>
            </div>

        @endif

        {{-- INFO --}}
        <small class="text-muted mb-3">
            <i class="fas fa-info-circle mr-1"></i>
            {{ $info['sub'] }}
        </small>

        {{-- INPUT --}}
        @if(!$pendaftaranAktif)
        <div class="mt-auto">

            <div class="custom-file mb-2">
                <input type="file"
                       class="custom-file-input"
                       name="dokumen[{{ $key }}]"
                       id="file_{{ $key }}"
                       accept="{{ $info['accept'] }}"
                       onchange="previewNama(this,'{{ $key }}')">

                <label class="custom-file-label"
                       id="label_{{ $key }}">
                    {{ $dok ? 'Ganti file...' : 'Pilih file...' }}
                </label>
            </div>

            @if($dok)
            <button type="button"
                    class="btn btn-outline-danger btn-block btn-hapus"
                    onclick="hapusDokumen('{{ $key }}','{{ $info['label'] }}')">
                <i class="fas fa-trash mr-1"></i> Hapus Dokumen
            </button>
            @endif

        </div>
        @endif

    </div>
</div>
</div>

@endforeach

</div>

{{-- BUTTON --}}
@if(!$pendaftaranAktif)
<div class="card mt-2">
    <div class="card-body d-flex justify-content-between flex-wrap">
        <div class="text-muted small mb-2">
            Pilih file lalu klik <b>Simpan Dokumen</b>
        </div>

        <div>
            <a href="{{ route('peserta.dashboard') }}"
               class="btn btn-secondary mr-2">
               Kembali
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Simpan Dokumen
            </button>
        </div>
    </div>
</div>
@endif

</form>

{{-- FORM DELETE --}}
@foreach($dokList as $key => $info)
@if(isset($dokumen[$key]))
<form id="hapus_{{ $key }}"
      action="{{ route('peserta.dokumen.hapus',$key) }}"
      method="POST"
      style="display:none;">
@csrf
@method('DELETE')
</form>
@endif
@endforeach

@endsection

{{-- ================= CSS ================= --}}
@push('css')
<style>

.dokumen-card{
    border:0;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 4px 14px rgba(0,0,0,.05);
    transition:.25s;
}

.dokumen-card:hover{
    transform:translateY(-4px);
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

.dokumen-card.uploaded{
    border:1px solid #d4edda;
}

.dokumen-card .card-header{
    background:#fff;
    border-bottom:1px solid #f1f1f1;
    padding:18px 20px;
}

.dokumen-card .card-header h4{
    font-size:17px;
    margin:0;
    font-weight:700;
}

.dokumen-card .card-body{
    padding:20px;
}

.preview-img{
    width:100%;
    height:230px;
    object-fit:contain;
    border:1px solid #eee;
    border-radius:12px;
    background:#fafafa;
    padding:10px;
}

.preview-file{
    border:2px dashed #ddd;
    border-radius:14px;
    padding:40px 20px;
    background:#fafafa;
}

.placeholder-upload{
    border:2px dashed #ddd;
    border-radius:14px;
    padding:50px 20px;
    background:#f8f9fa;
}

.badge-upload{
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
}

.custom-file-label{
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
}

.btn-hapus{
    border-radius:10px;
}

@media(max-width:768px){
    .preview-img{
        height:180px;
    }
}

</style>
@endpush

{{-- ================= JS ================= --}}
@push('js')
<script>

function previewNama(input,key){
    if(input.files.length){
        document.getElementById('label_'+key).innerHTML=input.files[0].name;
    }
}

function hapusDokumen(key,label){
    if(confirm('Hapus dokumen '+label+' ?')){
        document.getElementById('hapus_'+key).submit();
    }
}

</script>
@endpush