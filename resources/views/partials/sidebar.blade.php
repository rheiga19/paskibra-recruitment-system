{{-- resources/views/partials/sidebar.blade.php --}}

<style>
/* ── Sidebar scrollable ── */
#sidebar-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
}

.sidebar-menu-container {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar-menu-container::-webkit-scrollbar { width: 3px; }
.sidebar-menu-container::-webkit-scrollbar-track { background: transparent; }
.sidebar-menu-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 3px; }
.sidebar-menu-container:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,.4); }

/* ── Dropdown submenu ── */
.sidebar-menu .dropdown-nav {
    display: none;
    list-style: none;
    padding: 0;
    margin: 0;
    background: rgba(0,0,0,.08);
}

.sidebar-menu li.active > .dropdown-nav,
.sidebar-menu li.dropdown.open > .dropdown-nav {
    display: block;
}

.sidebar-menu .dropdown-nav li a {
    padding: 9px 20px 9px 52px;
    font-size: 13px;
    display: block;
    color: inherit;
    text-decoration: none;
    transition: background .2s;
}

.sidebar-menu .dropdown-nav li a:hover {
    background: rgba(0,0,0,.08);
}

.sidebar-menu .dropdown-nav li.active > a,
.sidebar-menu .dropdown-nav li a.active {
    font-weight: 600;
}

/* ── Arrow rotasi saat open ── */
.sidebar-menu li.dropdown.open > a.has-dropdown::after {
    transform: rotate(90deg);
}
</style>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">

        {{-- ── LOGO (full) ── --}}
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo Paskibra"
                     class="header-logo"
                     style="height:36px; width:auto; object-fit:contain;">
                <span class="logo-name">PASKIBRA COMPRENG</span>
            </a>
        </div>

        {{-- ── LOGO (collapsed) ── --}}
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo"
                     style="height:28px; width:auto;">
            </a>
        </div>

        {{-- ── MENU (scrollable) ── --}}
        <div class="sidebar-menu-container">
            <ul class="sidebar-menu">
                @auth

                {{-- ══════════════════════
                     PESERTA
                ══════════════════════ --}}
                @if(auth()->user()->isPeserta())

                    <li class="menu-header">Menu Peserta</li>

                    <li class="{{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('peserta.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.profil.edit') }}" class="nav-link">
                            <i class="fas fa-user-edit"></i>
                            <span>Biodata Diri</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('peserta.dokumen.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.dokumen.index') }}" class="nav-link">
                            <i class="fas fa-folder-open"></i>
                            <span>Dokumen Persyaratan</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-link">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Status Pendaftaran</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('peserta.hasil.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.hasil.index') }}" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Hasil Seleksi</span>
                        </a>
                    </li>

                @endif

                {{-- ══════════════════════
                     ADMIN
                ══════════════════════ --}}
                @if(auth()->user()->isAdmin())

                    <li class="menu-header">Rekrutmen</li>

                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Dropdown: Rekrutmen --}}
                    <li class="nav-item dropdown {{ request()->routeIs('admin.rekrutmen.*') ? 'active open' : '' }}">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown-sidebar">
                            <i class="fas fa-bullhorn"></i>
                            <span>Rekrutmen</span>
                        </a>
                        <ul class="dropdown-nav">
                            <li class="{{ request()->routeIs('admin.rekrutmen.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.rekrutmen.index') }}">
                                    <i class="fas fa-list fa-xs mr-1"></i> Daftar Rekrutmen
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('admin.rekrutmen.create') ? 'active' : '' }}">
                                <a href="{{ route('admin.rekrutmen.create') }}">
                                    <i class="fas fa-plus fa-xs mr-1"></i> Buat Rekrutmen
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pendaftaran.index') }}" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Pendaftaran</span>
                        </a>
                    </li>

                    {{-- Dropdown: Seleksi --}}
                    <li class="nav-item dropdown {{ request()->routeIs('admin.seleksi.*') ? 'active open' : '' }}">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown-sidebar">
                            <i class="fas fa-star"></i>
                            <span>Seleksi</span>
                        </a>
                        <ul class="dropdown-nav">
                            <li class="{{ request()->routeIs('admin.seleksi.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.seleksi.index') }}">
                                    <i class="fas fa-edit fa-xs mr-1"></i> Input Nilai
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('admin.seleksi.hasil-akhir') ? 'active' : '' }}">
                                <a href="{{ route('admin.seleksi.hasil-akhir') }}">
                                    <i class="fas fa-trophy fa-xs mr-1"></i> Hasil Akhir
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-header">Konten</li>

                    <li class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.berita.index') }}" class="nav-link">
                            <i class="fas fa-newspaper"></i>
                            <span>Berita</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.galeri.index') }}" class="nav-link">
                            <i class="fas fa-images"></i>
                            <span>Galeri</span>
                        </a>
                    </li>

                    <li class="menu-header">Sistem</li>

                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                            <i class="fas fa-user-cog"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengaturan.edit') }}" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>

                @endif

                {{-- ══════════════════════
                     PANITIA
                ══════════════════════ --}}
                @if(auth()->user()->isPanitia())

                    <li class="menu-header">Menu Panitia</li>

                    <li class="{{ request()->routeIs('panitia.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('panitia.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('panitia.verifikasi.*') ? 'active' : '' }}">
                        <a href="{{ route('panitia.verifikasi.index') }}" class="nav-link">
                            <i class="fas fa-user-check"></i>
                            <span>Verifikasi Administrasi</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('panitia.seleksi.*') ? 'active' : '' }}">
                        <a href="{{ route('panitia.seleksi.index') }}" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span>Input Nilai Seleksi</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('panitia.hasil.*') ? 'active' : '' }}">
                        <a href="{{ route('panitia.hasil.index') }}" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Hasil Akhir</span>
                        </a>
                    </li>

                @endif

                @endauth
            </ul>
        </div>
        {{-- end .sidebar-menu-container --}}

    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle="dropdown-sidebar"]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();

            var parent = this.closest('li.dropdown');
            var isOpen = parent.classList.contains('open');

            // Tutup semua dropdown lain
            document.querySelectorAll('.sidebar-menu li.dropdown.open').forEach(function (item) {
                if (item !== parent) item.classList.remove('open');
            });

            // Toggle yang diklik
            parent.classList.toggle('open', !isOpen);
        });
    });
});
</script>