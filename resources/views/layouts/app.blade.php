<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Paskibra Compreng') }}</title>

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

    <style>
        /* Tombol hamburger mobile — hanya muncul di layar kecil */
        #mobile-hamburger {
            display: none;
        }

        @media (max-width: 1024px) {
            #mobile-hamburger {
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 12px;
                left: 12px;
                z-index: 999;
                width: 42px;
                height: 42px;
                background-color: #6777ef;
                border-radius: 8px;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                border: none;
                color: #fff;
                font-size: 18px;
            }

            /* Overlay gelap saat sidebar terbuka */
            #mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 890;
            }

            body.sidebar-show #mobile-overlay {
                display: block;
            }

            /* Sidebar muncul saat sidebar-show */
            body.sidebar-show .main-sidebar {
                left: 0 !important;
                z-index: 995 !important;
            }

            body.sidebar-gone .main-sidebar {
                left: -250px !important;
            }
        }
    </style>
</head>

<body>
<div id="app">

    {{-- ── SIDEBAR ── --}}
    @include('partials.sidebar')

    {{-- Overlay mobile --}}
    <div id="mobile-overlay"></div>

    {{-- Tombol hamburger mobile (fixed, selalu tampil di mobile) --}}
    <button id="mobile-hamburger" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars" id="hamburger-icon"></i>
    </button>

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
                <li>
                    <a href="#" class="nav-link nav-link-lg fullscreen-btn">
                        <i data-feather="maximize"></i>
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
                        Hi, {{ auth()->user()->name ?? 'Pengguna' }}
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        {{ auth()->user()->name ?? '' }}<br>
                        <small class="text-muted">{{ ucfirst(auth()->user()->role ?? '') }}</small>
                    </div>
                    @auth
                    @if(auth()->user()->isPeserta())
                    <a href="#" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profil Saya
                    </a>
                    @endif
                    @endauth
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

<script>
function toggleMobileSidebar() {
    var body    = document.body;
    var icon    = document.getElementById('hamburger-icon');
    var isOpen  = body.classList.contains('sidebar-show');

    if (isOpen) {
        // Tutup
        body.classList.remove('sidebar-show');
        body.classList.add('sidebar-gone');
        icon.className = 'fas fa-bars';
    } else {
        // Buka
        body.classList.remove('sidebar-gone');
        body.classList.add('sidebar-show');
        icon.className = 'fas fa-times';
    }
}

// Tutup sidebar saat klik overlay
document.getElementById('mobile-overlay').addEventListener('click', function () {
    document.body.classList.remove('sidebar-show');
    document.body.classList.add('sidebar-gone');
    document.getElementById('hamburger-icon').className = 'fas fa-bars';
});

// Tutup sidebar saat klik link menu
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.main-sidebar .nav-link:not([data-toggle])').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.innerWidth <= 1024) {
                document.body.classList.remove('sidebar-show');
                document.body.classList.add('sidebar-gone');
                document.getElementById('hamburger-icon').className = 'fas fa-bars';
            }
        });
    });
});
</script>
</body>
</html>