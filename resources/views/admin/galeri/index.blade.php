@extends('layouts.app')
@section('title', 'Manajemen Galeri')

@push('css')
<style>
.foto-wrap {
    position:relative; border-radius:12px; overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,.08); aspect-ratio:1; background:#f0f0f0;
}
.foto-wrap img { width:100%; height:100%; object-fit:cover; transition:.3s; display:block; }
.foto-wrap:hover img { transform:scale(1.05); }
.foto-overlay {
    position:absolute; inset:0; background:rgba(0,0,0,.5);
    opacity:0; transition:.2s; display:flex; flex-direction:column;
    align-items:center; justify-content:center; gap:6px;
}
.foto-wrap:hover .foto-overlay { opacity:1; }
.upload-zone {
    border:2px dashed #dee2e6; border-radius:14px; padding:28px;
    text-align:center; cursor:pointer; transition:.2s; background:#fafafa;
}
.upload-zone:hover, .upload-zone.dragover { border-color:#cc0000; background:#fff5f5; }
.upload-zone input[type=file] { display:none; }
.preview-thumb { width:72px; height:72px; object-fit:cover; border-radius:8px; border:2px solid #e9ecef; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>Manajemen Galeri</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Galeri</div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<div class="row">
    {{-- Kiri: Form Upload --}}
    <div class="col-lg-4 mb-3">
        <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
            <div class="card-header">
                <h4><i class="fas fa-upload mr-2 text-primary"></i>Upload Foto</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 pl-3 small">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Judul <small class="text-muted">(opsional, default nama file)</small></label>
                        <input type="text" name="judul" class="form-control"
                               value="{{ old('judul') }}" placeholder="Judul foto...">
                    </div>

                    <div class="form-group">
                        <label>Keterangan <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="keterangan" class="form-control"
                               value="{{ old('keterangan') }}" placeholder="Caption foto...">
                    </div>

                    <div class="upload-zone" id="upload-zone"
                         onclick="document.getElementById('input-foto').click()">
                        <i class="fas fa-images fa-2x text-muted mb-2"></i>
                        <p class="mb-1 text-muted font-weight-600">Klik atau drag foto ke sini</p>
                        <small class="text-muted">JPG/PNG/WebP, max 3MB, bisa pilih banyak</small>
                        <input type="file" name="foto[]" id="input-foto"
                               accept="image/*" multiple onchange="previewFoto(this)">
                    </div>

                    <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                    <div id="preview-info" class="text-muted small mt-1" style="display:none;"></div>

                    <button type="submit" class="btn btn-danger btn-block mt-3" style="border-radius:10px;">
                        <i class="fas fa-upload mr-1"></i> Upload Foto
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
            <div class="card-body text-center py-3">
                <div style="font-size:32px;font-weight:900;color:#cc0000;">{{ $galeri->total() }}</div>
                <div class="text-muted small">Total Foto</div>
            </div>
        </div>
    </div>

    {{-- Kanan: Grid Foto --}}
    <div class="col-lg-8">
        <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-images mr-2 text-primary"></i>Semua Foto
                    <small class="text-muted font-weight-normal">({{ $galeri->total() }} foto)</small>
                </h4>
            </div>
            <div class="card-body">
                @if($galeri->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-images fa-3x mb-3" style="opacity:.3;"></i>
                    <p>Belum ada foto. Upload foto pertama kamu!</p>
                </div>
                @else
                <div class="row">
                    @foreach($galeri as $f)
                    <div class="col-4 col-md-3 mb-3">
                        <div class="foto-wrap">
                            <img src="{{ $f->foto_url }}" alt="{{ $f->judul }}" loading="lazy">
                            <div class="foto-overlay">
                                <button class="btn btn-sm btn-light"
                                        style="border-radius:8px;font-size:11px;"
                                        onclick="editFoto({{ $f->id }}, '{{ addslashes($f->judul) }}', '{{ addslashes($f->keterangan ?? '') }}')">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <form action="{{ route('admin.galeri.destroy', $f) }}" method="POST"
                                      onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" style="border-radius:8px;font-size:11px;">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($f->keterangan)
                        <p class="text-muted mt-1 mb-0 text-center" style="font-size:10px;">
                            {{ Str::limit($f->keterangan, 28) }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if($galeri->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-3">
                    <small class="text-muted">
                        {{ $galeri->firstItem() }}–{{ $galeri->lastItem() }} dari {{ $galeri->total() }} foto
                    </small>
                    {{ $galeri->links() }}
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Info Foto</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="form-edit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit-judul" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="edit-keterangan"
                               class="form-control" placeholder="Caption opsional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function previewFoto(input) {
    const container = document.getElementById('preview-container');
    const info      = document.getElementById('preview-info');
    container.innerHTML = '';
    if (!input.files.length) { info.style.display = 'none'; return; }
    Array.from(input.files).forEach(file => {
        const img = document.createElement('img');
        img.className = 'preview-thumb';
        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(file);
        container.appendChild(img);
    });
    info.style.display = '';
    info.textContent = input.files.length + ' foto dipilih';
}

const zone = document.getElementById('upload-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    const input = document.getElementById('input-foto');
    input.files = e.dataTransfer.files;
    previewFoto(input);
});

function editFoto(id, judul, keterangan) {
    document.getElementById('form-edit').action = '/admin/galeri/' + id;
    document.getElementById('edit-judul').value      = judul;
    document.getElementById('edit-keterangan').value = keterangan;
    $('#modalEdit').modal('show');
}
</script>
@endpush