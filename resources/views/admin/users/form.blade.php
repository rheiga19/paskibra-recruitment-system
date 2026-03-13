@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="section-header">
    <h1>{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manajemen User</a></div>
        <div class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Tambah' }}</div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>
                    <i class="fas fa-{{ isset($user) ? 'user-edit' : 'user-plus' }} mr-2 text-primary"></i>
                    {{ isset($user) ? 'Edit User: ' . $user->name : 'Tambah User Baru' }}
                </h4>
            </div>
            <div class="card-body">

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

                <form method="POST"
                      action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    {{-- Nama --}}
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name ?? '') }}"
                               placeholder="Masukkan nama lengkap" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email ?? '') }}"
                               placeholder="contoh@email.com" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin"   {{ old('role', $user->role ?? '') === 'admin'   ? 'selected' : '' }}>Admin</option>
                            <option value="peserta" {{ old('role', $user->role ?? '') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password (hanya saat tambah / opsional saat edit) --}}
                    <div class="form-group">
                        <label>
                            Password
                            @if(isset($user))
                            <small class="text-muted font-weight-normal">(kosongkan jika tidak ingin mengubah)</small>
                            @else
                            <span class="text-danger">*</span>
                            @endif
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="inputPassword"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="{{ isset($user) ? 'Password baru (opsional)' : 'Minimal 8 karakter' }}"
                                   {{ isset($user) ? '' : 'required' }}>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="iconPassword"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group">
                        <label>Konfirmasi Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Ulangi password"
                               {{ isset($user) ? '' : 'required' }}>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            {{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}
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
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('inputPassword');
        const icon  = document.getElementById('iconPassword');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    });
</script>
@endpush