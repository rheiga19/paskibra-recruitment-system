@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
<div class="section-header">
    <h1>Input Nilai Seleksi</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('panitia.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">
            <a href="{{ route('panitia.seleksi.index', ['rekrutmen_id' => $pendaftaran->rekrutmen_id, 'tahap_id' => $tahap->id]) }}">
                Seleksi
            </a>
        </div>
        <div class="breadcrumb-item active">Input Nilai</div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4>{{ $pendaftaran->nama_lengkap }} — {{ $tahap->nama }}</h4>
            </div>
            <div class="card-body">

                {{-- Info peserta --}}
                <div class="alert alert-light border mb-4">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <small class="text-muted d-block">No. Pendaftaran</small>
                            <strong>{{ $pendaftaran->no_pendaftaran ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <small class="text-muted d-block">Gender</small>
                            <strong>{{ $pendaftaran->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <small class="text-muted d-block">Sekolah</small>
                            <strong>{{ $pendaftaran->nama_sekolah }}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">KKM</small>
                            <strong>{{ $tahap->passing_grade ?? 70 }}</strong>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                @endif

                <form action="{{ route('panitia.seleksi.simpan', [$pendaftaran, $tahap]) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Pancasila / Kebangsaan</label>
                                <input type="number" name="nilai_pancasila"
                                       class="form-control nilai-input"
                                       value="{{ old('nilai_pancasila', $hasil->nilai_pancasila) }}"
                                       min="0" max="100" step="0.01" placeholder="0 – 100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai TIU</label>
                                <input type="number" name="nilai_tiu"
                                       class="form-control nilai-input"
                                       value="{{ old('nilai_tiu', $hasil->nilai_tiu) }}"
                                       min="0" max="100" step="0.01" placeholder="0 – 100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai PBB</label>
                                <input type="number" name="nilai_pbb"
                                       class="form-control nilai-input"
                                       value="{{ old('nilai_pbb', $hasil->nilai_pbb) }}"
                                       min="0" max="100" step="0.01" placeholder="0 – 100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Fisik</label>
                                <input type="number" name="nilai_fisik"
                                       class="form-control nilai-input"
                                       value="{{ old('nilai_fisik', $hasil->nilai_fisik) }}"
                                       min="0" max="100" step="0.01" placeholder="0 – 100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Wawancara</label>
                                <input type="number" name="nilai_wawancara"
                                       class="form-control nilai-input"
                                       value="{{ old('nilai_wawancara', $hasil->nilai_wawancara) }}"
                                       min="0" max="100" step="0.01" placeholder="0 – 100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nilai Total <small class="text-muted">(otomatis)</small></label>
                                <input type="text" id="nilaiTotal"
                                       class="form-control bg-light" readonly
                                       placeholder="Akan dihitung otomatis">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan <small class="text-muted">(opsional)</small></label>
                        <textarea name="catatan" class="form-control" rows="3"
                                  placeholder="Catatan penilaian...">{{ old('catatan', $hasil->catatan) }}</textarea>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('panitia.seleksi.index', ['rekrutmen_id' => $pendaftaran->rekrutmen_id, 'tahap_id' => $tahap->id]) }}"
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Nilai
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const kkm    = {{ $tahap->passing_grade ?? 70 }};
    const fields = ['nilai_pancasila', 'nilai_tiu', 'nilai_pbb', 'nilai_fisik', 'nilai_wawancara'];

    function hitungTotal() {
        const vals = fields
            .map(f => parseFloat(document.querySelector(`[name="${f}"]`).value))
            .filter(v => !isNaN(v));

        const el = document.getElementById('nilaiTotal');

        if (!vals.length) {
            el.value     = '';
            el.className = 'form-control bg-light';
            return;
        }

        const total = (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(2);
        const lolos = parseFloat(total) >= kkm;

        el.value     = total + ' — ' + (lolos ? '✅ Lolos' : '❌ Tidak Lolos');
        el.className = 'form-control bg-light font-weight-bold ' + (lolos ? 'text-success' : 'text-danger');
    }

    document.querySelectorAll('.nilai-input').forEach(el => el.addEventListener('input', hitungTotal));
    hitungTotal();
</script>
@endpush