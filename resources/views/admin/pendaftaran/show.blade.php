@extends('layouts.app')
@section('title', 'Detail Pendaftaran')

@section('content')
<div class="section-header">
    <h1>Detail Pendaftaran</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.pendaftaran.index') }}">Pendaftaran</a></div>
        <div class="breadcrumb-item active">{{ $pendaftaran->nama_lengkap }}</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@php
$jenisList = [
    'foto_4x6'        => 'Foto 4×6',
    'ktp_pelajar'     => 'KTP / Kartu Pelajar',
    'akta_kelahiran'  => 'Akta Kelahiran',
    'rapor'           => 'Rapor',
    'surat_sehat'     => 'Surat Keterangan Sehat',
    'surat_izin_ortu' => 'Surat Izin Orang Tua',
];

// Filter dokumen yang jenis-nya null untuk mencegah error keyBy
$dokumenMap = $pendaftaran->dokumen
    ->filter(fn($d) => !is_null($d->jenis))
    ->keyBy('jenis');
@endphp

<div class="row">
    {{-- ── KIRI: Info Peserta + Dokumen ── --}}
    <div class="col-lg-4">

        {{-- Profil --}}
        <div class="card">
            <div class="card-body text-center">
                <div style="width:80px;height:80px;background:#6777ef;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:2rem;color:white;">
                    {{ strtoupper(substr($pendaftaran->nama_lengkap, 0, 1)) }}
                </div>
                <h5 class="mt-3 mb-1">{{ $pendaftaran->nama_lengkap }}</h5>
                <small class="text-muted">{{ $pendaftaran->no_pendaftaran ?? '-' }}</small>
                <div class="mt-2">
                    @php $c = ['menunggu'=>'warning','diverifikasi'=>'info','lulus'=>'success','tidak_lulus'=>'danger'][$pendaftaran->status] ?? 'secondary'; @endphp
                    <span class="badge badge-{{ $c }} badge-pill px-3 py-2">{{ $pendaftaran->status_label }}</span>
                </div>
            </div>
            <div class="card-body border-top p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted pl-3">NIK</td><td>{{ $pendaftaran->nik }}</td></tr>
                    <tr>
                        <td class="text-muted pl-3">Lahir</td>
                        <td>{{ $pendaftaran->tempat_lahir }}, {{ $pendaftaran->tanggal_lahir->format('d M Y') }}
                            ({{ $pendaftaran->umur }} th)</td>
                    </tr>
                    <tr><td class="text-muted pl-3">Gender</td><td>{{ $pendaftaran->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td></tr>
                    <tr><td class="text-muted pl-3">No. HP</td><td>{{ $pendaftaran->no_hp }}</td></tr>
                    <tr><td class="text-muted pl-3">Alamat</td><td>{{ $pendaftaran->alamat_lengkap }}</td></tr>
                    <tr><td class="text-muted pl-3">TB / BB</td><td>{{ $pendaftaran->tinggi_badan }} cm / {{ $pendaftaran->berat_badan }} kg</td></tr>
                    <tr><td class="text-muted pl-3">Sekolah</td><td>{{ $pendaftaran->nama_sekolah }}</td></tr>
                    <tr><td class="text-muted pl-3">Jenjang</td><td>{{ $pendaftaran->jenjang }} Kelas {{ $pendaftaran->kelas }}</td></tr>
                    <tr><td class="text-muted pl-3">Nilai Rata</td><td>{{ $pendaftaran->nilai_rata ?? '-' }}</td></tr>
                    <tr>
                        <td class="text-muted pl-3">Orang Tua</td>
                        <td>{{ $pendaftaran->nama_ortu }} ({{ $pendaftaran->hubungan_ortu }})
                            <br><small>{{ $pendaftaran->hp_ortu }}</small></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Dokumen --}}
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-folder-open mr-2 text-primary"></i>Dokumen Persyaratan</h4>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($jenisList as $key => $label)
                    @php $dok = $dokumenMap->get($key); @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-{{ $dok ? 'check-circle text-success' : 'times-circle text-danger' }} mr-2"></i>
                            <span style="font-size:13px;">{{ $label }}</span>
                        </div>
                        @if($dok)
                            @php $ext = strtolower(pathinfo($dok->path, PATHINFO_EXTENSION)); @endphp
                            <a href="{{ asset('storage/'.$dok->path) }}" target="_blank"
                               class="btn btn-sm btn-outline-primary" style="font-size:11px;">
                                <i class="fas fa-{{ in_array($ext, ['jpg','jpeg','png','webp']) ? 'image' : 'file-pdf' }} mr-1"></i>
                                Lihat
                            </a>
                        @else
                            <span class="badge badge-light text-muted">Tidak Ada</span>
                        @endif
                    </li>
                    @endforeach
                </ul>

                {{-- Preview foto 4x6 --}}
                @if($dokumenMap->has('foto_4x6'))
                @php $foto = $dokumenMap->get('foto_4x6'); @endphp
                <div class="p-3 text-center border-top">
                    <img src="{{ asset('storage/'.$foto->path) }}"
                         alt="Foto Peserta"
                         class="img-thumbnail"
                         style="max-height:160px;object-fit:cover;border-radius:8px;">
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── KANAN: Verifikasi + Hasil Seleksi ── --}}
    <div class="col-lg-8">

        {{-- Verifikasi --}}
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-check-circle mr-2 text-primary"></i>Verifikasi Status</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status Pendaftaran</label>
                                <select name="status" class="form-control">
                                    <option value="menunggu"     {{ $pendaftaran->status === 'menunggu'     ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                    <option value="diverifikasi" {{ $pendaftaran->status === 'diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                                    <option value="lulus"        {{ $pendaftaran->status === 'lulus'        ? 'selected' : '' }}>Lulus</option>
                                    <option value="tidak_lulus"  {{ $pendaftaran->status === 'tidak_lulus'  ? 'selected' : '' }}>Tidak Lulus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Catatan Admin</label>
                                <textarea name="catatan_admin" class="form-control" rows="3"
                                          placeholder="Catatan untuk peserta...">{{ $pendaftaran->catatan_admin }}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Verifikasi
                    </button>
                </form>
            </div>
        </div>

        {{-- Hasil Seleksi --}}
        @if($pendaftaran->hasilSeleksi->count())
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-star mr-2 text-warning"></i>Hasil Seleksi</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tahap</th>
                                <th>Pancasila</th>
                                <th>TIU</th>
                                <th>PBB</th>
                                <th>Fisik</th>
                                <th>Wawancara</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaran->hasilSeleksi as $h)
                            <tr>
                                {{-- Gunakan optional() untuk mencegah error jika relasi tahap null --}}
                                <td>{{ optional($h->tahap)->nama ?? '-' }}</td>
                                <td>{{ $h->nilai_pancasila ?? '-' }}</td>
                                <td>{{ $h->nilai_tiu ?? '-' }}</td>
                                <td>{{ $h->nilai_pbb ?? '-' }}</td>
                                <td>{{ $h->nilai_fisik ?? '-' }}</td>
                                <td>{{ $h->nilai_wawancara ?? '-' }}</td>
                                <td><strong>{{ $h->nilai_total ?? '-' }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $h->status === 'lolos' ? 'success' : ($h->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ $h->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Prestasi --}}
        @if($pendaftaran->prestasi)
        <div class="card">
            <div class="card-header"><h4>Prestasi</h4></div>
            <div class="card-body">{{ $pendaftaran->prestasi }}</div>
        </div>
        @endif

    </div>
</div>
@endsection