<x-guest-layout>
    <div class="ms-auto me-auto" style="max-width: 400px; width: 100%;">
        <h3 class="fw-bold mb-2">Bienvenido de nuevo 👋</h3>
        <p class="text-muted mb-4 small">Ingresa tus credenciales para acceder al panel de control.</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-success small fw-bold" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="tucorreo@empresa.com">
                </div>
                @error('email')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="password" class="form-label mb-0">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color: var(--secundario);">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>
                <div class="input-group mt-2">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                </div>
                @error('password')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-4 form-check">
                <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small text-muted user-select-none" for="remember_me">
                    Recordar mi sesión
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                Ingresar al Sistema <i class="fas fa-arrow-right"></i>
            </button>
            
            <div class="text-center mt-4">
                <p class="text-muted small">¿Tú o tu empresa aún no tienen cuenta? <br>
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: var(--secundario);">Regístrate aquí</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
