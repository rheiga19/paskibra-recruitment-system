<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Paskibra Compreng') }}</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    @stack('css-libs')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('css')
</head>

<body>
    <div id="app">

        <!-- ── SIDEBAR ──────────────────────────────────────────── -->
        <div class="main-sidebar sidebar-style-2">
            <aside id="sidebar-wrapper">
                <div class="sidebar-brand">
                    <a href="{{ route('dashboard') }}">
                        <img alt="logo" src="{{ asset('assets/img/stisla-fill.svg') }}" class="header-logo" />
                        <span class="logo-name">{{ config('app.name') }}</span>
                    </a>
                </div>

                <div class="sidebar-brand sidebar-brand-sm">
                    <a href="{{ route('dashboard') }}">PSK</a>
                </div>

                <ul class="sidebar-menu">

                    {{-- MENU PESERTA --}}
                    @auth
                    @if(auth()->user()->isPeserta())
                    <li class="menu-header">Peserta</li>

                    <li class="{{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('peserta.dashboard') }}" class="nav-link">
                            <i class="fas fa-fire"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.profil.edit') }}" class="nav-link">
                            <i class="fas fa-user-edit"></i><span>Profil Saya</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('peserta.dokumen.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.dokumen.index') }}" class="nav-link">
                            <i class="fas fa-folder-open"></i><span>Dokumen</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-link">
                            <i class="fas fa-clipboard-list"></i><span>Pendaftaran</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('peserta.hasil.*') ? 'active' : '' }}">
                        <a href="{{ route('peserta.hasil.index') }}" class="nav-link">
                            <i class="fas fa-trophy"></i><span>Hasil Seleksi</span>
                        </a>
                    </li>
                    @endif

                    {{-- MENU ADMIN --}}
                    @if(auth()->user()->isAdmin())
                    <li class="menu-header">Admin</li>

                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item dropdown {{ request()->routeIs('admin.rekrutmen.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link has-dropdown">
                            <i class="fas fa-bullhorn"></i><span>Rekrutmen</span>
                        </a>
                        <ul class="dropdown-nav">
                            <li><a href="{{ route('admin.rekrutmen.index') }}" class="nav-link">Daftar Rekrutmen</a></li>
                            <li><a href="{{ route('admin.rekrutmen.create') }}" class="nav-link">Buat Rekrutmen</a></li>
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pendaftaran.index') }}" class="nav-link">
                            <i class="fas fa-users"></i><span>Pendaftaran</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="fas fa-star"></i><span>Penilaian</span>
                        </a>
                    </li>

                    <li class="menu-header">Konten</li>

                    <li class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.berita.index') }}" class="nav-link">
                            <i class="fas fa-newspaper"></i><span>Berita</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.galeri.index') }}" class="nav-link">
                            <i class="fas fa-images"></i><span>Galeri</span>
                        </a>
                    </li>

                    <li class="menu-header">Sistem</li>

                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                            <i class="fas fa-user-cog"></i><span>Manajemen User</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengaturan.edit') }}" class="nav-link">
                            <i class="fas fa-cog"></i><span>Pengaturan</span>
                        </a>
                    </li>
                    @endif

                    {{-- MENU PANITIA --}}
                    @if(auth()->user()->isPanitia())
                    <li class="menu-header">Panitia</li>

                    <li class="{{ request()->routeIs('panitia.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('panitia.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('panitia.penilaian.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="fas fa-star-half-alt"></i><span>Input Penilaian</span>
                        </a>
                    </li>
                    @endif
                    @endauth

                </ul>
            </aside>
        </div>

        <!-- ── NAVBAR ────────────────────────────────────────────── -->
        <div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar sticky-top">
            <div class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li>
                        <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                            <i data-feather="align-justify"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link nav-link-lg fullscreen-btn">
                            <i data-feather="maximize"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                        <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1" width="30">
                        <div class="d-sm-none d-lg-inline-block">
                            Hi, {{ auth()->user()->name ?? 'Pengguna' }}
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-title">
                            {{ auth()->user()->name ?? '' }}
                            <br>
                            <small class="text-muted">{{ ucfirst(auth()->user()->role ?? '') }}</small>
                        </div>
                        @if(auth()->user()->isPeserta())
                        <a href="{{ route('peserta.profil.edit') }}" class="dropdown-item has-icon">
                            <i class="far fa-user"></i> Profil Saya
                        </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- ── MAIN CONTENT ──────────────────────────────────────── -->
        <div class="main-content">
            <section class="section">

                {{-- Page Header --}}
                @isset($header)
                <div class="section-header">
                    {{ $header }}
                </div>
                @endisset

                {{-- Flash Messages --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                @endif

                {{-- Content --}}
                {{ $slot }}

            </section>
        </div>

        <!-- ── FOOTER ────────────────────────────────────────────── -->
        <footer class="main-footer">
            <div class="footer-left">
                Copyright &copy; {{ date('Y') }} <b>{{ config('app.name') }}</b>
            </div>
            <div class="footer-right">
                Paskibra Kecamatan Compreng
            </div>
        </footer>

    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/popper.js') }}"></script>
    <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>

    <!-- JS Libraries -->
    @stack('js-libs')

    <!-- Template JS File -->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Feather Icons -->
    <script src="{{ asset('assets/modules/feather-icons/feather.min.js') }}"></script>
    <script>feather.replace()</script>

    @stack('js')
</body>
</html>