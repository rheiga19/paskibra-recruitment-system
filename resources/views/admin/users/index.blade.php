@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="section-header">
    <h1>Manajemen User</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item active">Manajemen User</div>
    </div>
</div>

{{-- ── STAT CARDS ── --}}
<div class="row">
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Total User</h4></div>
                <div class="card-body">{{ number_format($totalUser) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i class="fas fa-user-shield"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Admin</h4></div>
                <div class="card-body">{{ number_format($totalAdmin) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-user-graduate"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Peserta</h4></div>
                <div class="card-body">{{ number_format($totalPeserta) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-user-check"></i></div>
            <div class="card-wrap">
                <div class="card-header"><h4>Aktif Bulan Ini</h4></div>
                <div class="card-body">{{ number_format($aktifBulanIni) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABEL USER ── --}}
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-users mr-2 text-primary"></i>Daftar User</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </a>
        </div>
    </div>
    <div class="card-body">

        {{-- Filter & Search --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
            <div class="row align-items-end">
                <div class="col-12 col-sm-5 col-lg-4 mb-2 mb-sm-0">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Cari nama atau email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-3 col-lg-2 mb-2 mb-sm-0">
                    <select name="role" class="form-control form-control-sm">
                        <option value="">Semua Role</option>
                        <option value="admin"   {{ request('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                        <option value="peserta" {{ request('role') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-lg-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" title="Reset filter">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Flash Messages --}}
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

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th>Role</th>
                        <th class="d-none d-lg-table-cell">Terdaftar</th>
                        <th class="d-none d-lg-table-cell">Login Terakhir</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="font-weight-bold" style="font-size:.9rem">{{ $user->name }}</div>
                            <small class="text-muted d-md-none">{{ $user->email }}</small>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <small>{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <small class="text-muted">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '–' }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-icon btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Reset Password --}}
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-warning btn-reset"
                                        title="Reset Password"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-url="{{ route('admin.users.reset-password', $user) }}">
                                    <i class="fas fa-key"></i>
                                </button>
                                {{-- Hapus (tidak bisa hapus diri sendiri) --}}
                                @if($user->id !== auth()->id())
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-danger btn-hapus"
                                        title="Hapus"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-url="{{ route('admin.users.destroy', $user) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            Tidak ada user ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
            </small>
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif

    </div>
</div>

{{-- ── MODAL HAPUS ── --}}
<div class="modal fade" id="modalHapus" tabindex="-1" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Yakin hapus user <strong id="namaHapus"></strong>?
                <br><small class="text-danger">Tindakan ini tidak bisa dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL RESET PASSWORD ── --}}
<div class="modal fade" id="modalReset" tabindex="-1" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Reset password <strong id="namaReset"></strong> ke password default?</p>
                <div class="alert alert-info py-2 mb-0">
                    <small><i class="fas fa-info-circle mr-1"></i> Password baru: <strong>paskibra123</strong></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <form id="formReset" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fas fa-key mr-1"></i> Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    // ── Modal Hapus ──
    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('namaHapus').textContent = this.dataset.name;
            document.getElementById('formHapus').action     = this.dataset.url;
            $('#modalHapus').modal({ backdrop: false, keyboard: true });
            $('#modalHapus').modal('show');
        });
    });

    // ── Modal Reset Password ──
    document.querySelectorAll('.btn-reset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('namaReset').textContent = this.dataset.name;
            document.getElementById('formReset').action     = this.dataset.url;
            $('#modalReset').modal({ backdrop: false, keyboard: true });
            $('#modalReset').modal('show');
        });
    });
</script>
@endpush