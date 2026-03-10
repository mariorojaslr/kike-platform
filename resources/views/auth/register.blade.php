<x-guest-layout>
    <div class="ms-auto me-auto" style="max-width: 450px; width: 100%;">
        <h3 class="fw-bold mb-2">Crear nueva cuenta 🚀</h3>
        <p class="text-muted mb-4 small">Únete a la plataforma líder en gestión integral. Completa tus datos para comenzar.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-user"></i></span>
                    <input id="name" type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ej. Juan Pérez">
                </div>
                @error('name')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="tucorreo@empresa.com">
                </div>
                @error('email')
                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-check-double"></i></span>
                        <input id="password_confirmation" type="password" class="form-control border-start-0 ps-0 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                    </div>
                    @error('password_confirmation')
                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                Registrar y Continuar <i class="fas fa-user-plus"></i>
            </button>
            
            <div class="text-center mt-4">
                <p class="text-muted small">¿Ya tienes una cuenta en el sistema? <br>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: var(--secundario);">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
