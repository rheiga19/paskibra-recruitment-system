@extends('layouts.app')
@section('title', 'Detail Verifikasi')

@section('content')
<div class="section-header">
    <h1>Detail Verifikasi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('panitia.verifikasi.index') }}">Verifikasi</a></div>
        <div class="breadcrumb-item active">{{ $pendaftaran->nama_lengkap }}</div>
    </div>
</div>

<div class="row">

    {{-- ── INFO PESERTA ── --}}
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-user mr-2 text-primary"></i>Info Peserta</h4>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">No. Daftar</td>
                        <td><strong>{{ $pendaftaran->no_pendaftaran ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $pendaftaran->nama_lengkap }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gender</td>
                        <td>{{ $pendaftaran->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">TTL</td>
                        <td>{{ $pendaftaran->tempat_lahir }}, {{ $pendaftaran->tanggal_lahir?->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sekolah</td>
                        <td>{{ $pendaftaran->nama_sekolah }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenjang</td>
                        <td>{{ strtoupper($pendaftaran->jenjang) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tinggi</td>
                        <td>{{ $pendaftaran->tinggi_badan ?? '-' }} cm</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Berat</td>
                        <td>{{ $pendaftaran->berat_badan ?? '-' }} kg</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php
                                $colors = ['menunggu'=>'warning','diverifikasi'=>'success','ditolak'=>'danger'];
                                $labels = ['menunggu'=>'Menunggu','diverifikasi'=>'Diterima','ditolak'=>'Ditolak'];
                            @endphp
                            <span class="badge badge-{{ $colors[$pendaftaran->status] ?? 'secondary' }}">
                                {{ $labels[$pendaftaran->status] ?? $pendaftaran->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Navigasi peserta --}}
        <div class="d-flex justify-content-between mb-3">
            @if($prev)
            <a href="{{ route('panitia.verifikasi.show', $prev) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
            </a>
            @else
            <span></span>
            @endif
            @if($next)
            <a href="{{ route('panitia.verifikasi.show', $next) }}" class="btn btn-sm btn-outline-secondary">
                Berikutnya <i class="fas fa-chevron-right ml-1"></i>
            </a>
            @endif
        </div>
    </div>

    {{-- ── DOKUMEN & VERIFIKASI ── --}}
    <div class="col-12 col-lg-8">

        {{-- Dokumen --}}
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-file-alt mr-2 text-primary"></i>Dokumen Persyaratan</h4>
            </div>
            <div class="card-body">
                @php
                    $dokList = [
                        'foto_4x6'        => 'Pas Foto 4×6',
                        'ktp_pelajar'     => 'Kartu Pelajar / KTP',
                        'akta_kelahiran'  => 'Akta Kelahiran',
                        'rapor'           => 'Rapor',
                        'surat_sehat'     => 'Surat Sehat',
                        'surat_izin_ortu' => 'Surat Izin Ortu',
                    ];
                @endphp
                <div class="row">
                    @foreach($dokList as $jenis => $label)
                    @php
                        $dok     = $dokumen->get($jenis) ?? null;
                        // PERBAIKAN: file disimpan private, wajib lewat route controller
                        // bukan asset('storage/...') yang hanya bisa baca disk public
                        $fileUrl = $dok ? route('panitia.dokumen.lihat', $dok) : null;
                        $isImage = $dok
                            ? in_array(strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'])
                            : false;
                    @endphp
                    <div class="col-6 col-md-4 mb-3">
                        <div class="border rounded p-2 text-center" style="min-height:120px;">
                            <small class="d-block text-muted mb-2 font-weight-bold">{{ $label }}</small>

                            @if($dok && $fileUrl)
                                @if($isImage)
                                    {{-- Gambar ditampilkan via <img> dengan src dari route controller --}}
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <img src="{{ $fileUrl }}"
                                             alt="{{ $label }}"
                                             class="img-fluid rounded"
                                             style="max-height:80px; object-fit:cover; cursor:zoom-in;">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger mt-2">
                                        <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                                    </a>
                                @endif
                                <div class="mt-1">
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i>Ada
                                    </span>
                                </div>
                            @else
                                <div class="mt-3">
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                </div>
                                <div class="mt-1">
                                    <span class="badge badge-danger">Tidak Ada</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Form Verifikasi --}}
        @if($pendaftaran->status === 'menunggu')
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-clipboard-check mr-2 text-primary"></i>Keputusan Verifikasi</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('panitia.verifikasi.proses', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label>Catatan <small class="text-muted">(opsional)</small></label>
                        <textarea name="catatan" class="form-control" rows="3"
                                  placeholder="Catatan untuk peserta...">{{ old('catatan') }}</textarea>
                    </div>
                    <div class="d-flex" style="gap:8px;">
                        <button type="submit" name="keputusan" value="diverifikasi"
                                class="btn btn-success"
                                onclick="return confirm('Terima pendaftaran ini?')">
                            <i class="fas fa-check mr-1"></i> Terima
                        </button>
                        <button type="submit" name="keputusan" value="ditolak"
                                class="btn btn-danger"
                                onclick="return confirm('Tolak pendaftaran ini?')">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                        <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-secondary ml-auto">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body">
                <div class="alert alert-{{ $pendaftaran->status === 'diverifikasi' ? 'success' : 'danger' }} mb-0">
                    <i class="fas fa-{{ $pendaftaran->status === 'diverifikasi' ? 'check' : 'times' }}-circle mr-2"></i>
                    Pendaftaran ini sudah
                    <strong>{{ $pendaftaran->status === 'diverifikasi' ? 'diterima' : 'ditolak' }}</strong>.
                    @if($pendaftaran->catatan_verifikasi)
                    <br><small>Catatan: {{ $pendaftaran->catatan_verifikasi }}</small>
                    @endif
                </div>
                <div class="mt-3">
                    <a href="{{ route('panitia.verifikasi.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection