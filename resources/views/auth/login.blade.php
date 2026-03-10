<x-guest-layout>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Masukkan email"
                   required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-small text-muted">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <input id="password"
                       type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Masukkan password"
                       required autocomplete="current-password">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                       name="remember"
                       id="remember_me"
                       class="custom-control-input">
                <label class="custom-control-label" for="remember_me">Ingat saya</label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-sign-in-alt mr-1"></i> Masuk
            </button>
        </div>

    </form>

    {{-- Register Link --}}
    <div class="text-center mt-2">
        <span class="text-muted text-sm">Belum punya akun?</span>
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

    @push('js')
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                const icon = this.querySelector('i');
                if (target.type === 'password') {
                    target.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    target.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    </script>
    @endpush

</x-guest-layout>