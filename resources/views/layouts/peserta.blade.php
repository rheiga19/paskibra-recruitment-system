<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Paskibra Compreng') }}</title>

    <!-- General CSS -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries (per halaman) -->
    @stack('css-libs')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- CSS per halaman -->
    @stack('css')
</head>

<body>
<div id="app">

    {{-- ── SIDEBAR PESERTA ── --}}
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="{{ route('peserta.dashboard') }}">
                    {{ config('app.name', 'Paskibra') }}
                </a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="{{ route('peserta.dashboard') }}">PSK</a>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-header">Menu</li>

                <li class="{{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('peserta.dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                    <a href="{{ route('peserta.profil.edit') }}" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Biodata Saya</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('peserta.dokumen.*') ? 'active' : '' }}">
                    <a href="{{ route('peserta.dokumen.index') }}" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span>Dokumen</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Pendaftaran</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('peserta.hasil.*') ? 'active' : '' }}">
                    <a href="{{ route('peserta.hasil.index') }}" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Hasil Seleksi</span>
                    </a>
                </li>
            </ul>
        </aside>
    </div>

    {{-- ── NAVBAR ── --}}
    <div class="navbar-bg"></div>
    <nav class="navbar navbar-expand-lg main-navbar sticky-top">
        <div class="form-inline mr-auto">
            <ul class="navbar-nav mr-3">
                <li>
                    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                        <i data-feather="align-justify"></i>
                    </a>
                </li>
            </ul>
        </div>

        <ul class="navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" data-toggle="dropdown"
                   class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="avatar" src="{{ asset('assets/img/avatar/avatar-1.png') }}"
                         class="rounded-circle mr-1" width="30">
                    <div class="d-sm-none d-lg-inline-block">
                        Hi, {{ auth()->user()->name ?? 'Peserta' }}
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        {{ auth()->user()->name ?? '' }}<br>
                        <small class="text-muted">Peserta</small>
                    </div>
                    <a href="{{ route('peserta.profil.edit') }}" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profil Saya
                    </a>
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

    {{-- ── MAIN CONTENT ── --}}
    <div class="main-content">
        <section class="section">

            {{-- Section Header --}}
            <div class="section-header">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="section-header-subtitle">@yield('page-subtitle')</div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('peserta.dashboard') }}">Dashboard</a>
                    </div>
                    @yield('breadcrumb')
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
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
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            {{-- Content --}}
            @yield('content')

        </section>
    </div>

    {{-- ── FOOTER ── --}}
    <footer class="main-footer">
        <div class="footer-left">
            Copyright &copy; {{ date('Y') }} <b>{{ config('app.name') }}</b>
        </div>
        <div class="footer-right">
            Paskibra Kecamatan Compreng
        </div>
    </footer>

</div>

<!-- General JS -->
<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
<script src="{{ asset('assets/modules/popper.js') }}"></script>
<script src="{{ asset('assets/modules/tooltip.js') }}"></script>
<script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('assets/modules/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>

<!-- JS Libraries (per halaman) -->
@stack('js-libs')

<!-- Template JS -->
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- Feather Icons -->
<script src="{{ asset('assets/modules/feather-icons/feather.min.js') }}"></script>
<script>feather.replace()</script>

<!-- JS per halaman -->
@stack('js')
</body>
</html>