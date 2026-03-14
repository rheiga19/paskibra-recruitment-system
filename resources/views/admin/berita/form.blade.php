@extends('layouts.app')
@section('title', $berita->exists ? 'Edit Berita' : 'Tambah Berita')

@push('css')
<style>
.preview-img { max-height:200px; border-radius:10px; object-fit:cover; border:2px solid #f0f0f0; }
.ck-editor__editable { min-height: 300px; }
</style>
@endpush

@section('content')
<div class="section-header">
    <h1>{{ $berita->exists ? 'Edit Berita' : 'Tambah Berita' }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.berita.index') }}">Berita</a></div>
        <div class="breadcrumb-item active">{{ $berita->exists ? 'Edit' : 'Tambah' }}</div>
    </div>
</div>

<form action="{{ $berita->exists ? route('admin.berita.update', $berita) : route('admin.berita.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($berita->exists) @method('PUT') @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
                <div class="card-header"><h4><i class="fas fa-pen mr-2 text-primary"></i>Konten Berita</h4></div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul', $berita->judul) }}"
                               placeholder="Masukkan judul berita..." required>
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Isi Berita <span class="text-danger">*</span></label>
                        <textarea name="konten" id="konten"
                                  class="form-control @error('konten') is-invalid @enderror"
                                  rows="15" placeholder="Tulis isi berita di sini...">{{ old('konten', $berita->konten) }}</textarea>
                        @error('konten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Mendukung HTML dasar: &lt;b&gt;, &lt;i&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;p&gt;</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Publish --}}
            <div class="card mb-3" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
                <div class="card-header"><h4><i class="fas fa-cog mr-2 text-primary"></i>Pengaturan</h4></div>
                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="is_published"
                               name="is_published" value="1"
                               {{ old('is_published', $berita->is_published) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_published">
                            <strong>Publikasikan Sekarang</strong>
                            <div class="text-muted small">Berita akan tampil di halaman publik</div>
                        </label>
                    </div>

                    @if($berita->exists)
                    <div class="text-muted small border-top pt-2">
                        <div>Dibuat: {{ $berita->created_at->format('d M Y, H:i') }}</div>
                        <div>Oleh: {{ $berita->admin->name ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Thumbnail --}}
            <div class="card mb-3" style="border:none;border-radius:14px;box-shadow:0 2px 16px rgba(0,0,0,.07);">
                <div class="card-header"><h4><i class="fas fa-image mr-2 text-primary"></i>Gambar Thumbnail</h4></div>
                <div class="card-body">
                    {{-- Preview gambar existing --}}
                    @if($berita->gambar)
                    <div class="mb-3" id="preview-existing">
                        <img src="{{ $berita->gambar_url }}" class="preview-img w-100" id="existing-img">
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" id="hapus_gambar" name="hapus_gambar" value="1">
                            <label class="custom-control-label text-danger" for="hapus_gambar">Hapus gambar ini</label>
                        </div>
                    </div>
                    @endif

                    <div id="preview-new" class="mb-2" style="display:none;">
                        <img src="" class="preview-img w-100" id="preview-new-img">
                    </div>

                    <div class="form-group mb-0">
                        <label class="btn btn-outline-primary btn-block" style="border-radius:10px;cursor:pointer;">
                            <i class="fas fa-upload mr-1"></i>
                            {{ $berita->gambar ? 'Ganti Gambar' : 'Upload Gambar' }}
                            <input type="file" name="gambar" accept="image/*"
                                   style="display:none;" id="input-gambar"
                                   onchange="previewGambar(this)">
                        </label>
                        <small class="text-muted">JPG/PNG/WebP, max 2MB</small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary flex-fill" style="border-radius:10px;">
                    <i class="fas fa-arrow-left mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-danger flex-fill" style="border-radius:10px;">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
function previewGambar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-new-img').src = e.target.result;
            document.getElementById('preview-new').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Disable hapus_gambar jika ada file baru
document.getElementById('input-gambar')?.addEventListener('change', function() {
    const cb = document.getElementById('hapus_gambar');
    if (cb) cb.checked = false;
});
</script>
@endpush