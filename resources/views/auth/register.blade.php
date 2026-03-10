<x-guest-layout>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nama --}}
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input id="name"
                   type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Masukkan nama lengkap"
                   required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Masukkan email aktif"
                   required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-group">
                <input id="password"
                       type="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimal 8 karakter"
                       required autocomplete="new-password">
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

        {{-- Konfirmasi Password --}}
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="input-group">
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password"
                       required autocomplete="new-password">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password_confirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-user-plus mr-1"></i> Daftar Akun
            </button>
        </div>

    </form>

    {{-- Login Link --}}
    <div class="text-center mt-2">
        <span class="text-muted text-sm">Sudah punya akun?</span>
        <a href="{{ route('login') }}">Masuk di sini</a>
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