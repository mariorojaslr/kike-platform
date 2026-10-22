<nav x-data="{ open: false }" class="border-b" style="background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%); border-color: #334155 !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="no-underline d-flex align-items-center gap-2 text-decoration-none">
                        <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" style="height: 36px; width: auto; background: #ffffff; padding: 3px 8px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                        <div class="d-flex flex-column">
                            <span class="text-white fw-bold" style="font-size: 0.95rem; letter-spacing: 0.5px; line-height: 1.1;">
                                APOS <span style="color: #38bdf8;">LA RIOJA</span>
                            </span>
                            <small style="font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.5px;">INTEGRA • GESTIÓN DE INTEGRACIÓN ESCOLAR</small>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none px-2 py-1 rounded text-sm fw-bold {{ request()->routeIs('dashboard') ? 'text-info border-bottom border-info' : 'text-light opacity-75' }}">
                        <i class="fas fa-th-large me-1 text-info"></i> Panel Principal
                    </a>

                    <a href="{{ route('auditor.docentes.index') }}" class="text-decoration-none px-2 py-1 rounded text-sm fw-bold {{ request()->is('auditor*') ? 'text-warning border-bottom border-warning' : 'text-light opacity-75' }}">
                        <i class="fas fa-clipboard-check me-1 text-warning"></i> Auditoría y Resoluciones
                    </a>

                    <a href="{{ route('liquidacion.prestadores.index') }}" class="text-decoration-none px-2 py-1 rounded text-sm fw-bold {{ request()->is('liquidacion*') ? 'text-success border-bottom border-success' : 'text-light opacity-75' }}">
                        <i class="fas fa-file-invoice-dollar me-1 text-success"></i> Liquidación e-SIDIF
                    </a>

                    <a href="{{ route('directora.asistencias.demo') }}" class="text-decoration-none px-2 py-1 rounded text-sm fw-bold {{ request()->is('app-directora*') ? 'text-info border-bottom border-info' : 'text-light opacity-75' }}">
                        <i class="fas fa-school me-1 text-primary"></i> App Directora
                    </a>

                    <a href="{{ route('pwa.docente.demo') }}" class="text-decoration-none px-2 py-1 rounded text-sm fw-bold {{ request()->is('app-docente*') ? 'text-info border-bottom border-info' : 'text-light opacity-75' }}">
                        <i class="fas fa-chalkboard-teacher me-1 text-info"></i> App Docente
                    </a>

                    <a href="{{ url('/panel-accesos') }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                        <i class="fas fa-compass me-1"></i> Accesos Rápidos
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @include('partials.notificaciones_widget')

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-secondary text-sm leading-4 font-bold rounded-pill text-white hover:text-info focus:outline-none transition ease-in-out duration-150" style="background: rgba(255,255,255,0.08);">
                            <div class="font-bold"><i class="fas fa-user-circle text-info me-1"></i> {{ strtoupper(Auth::user()->name) }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Escritorio') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/config/localidades')">Localidades</x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/config/titulos')">Títulos</x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/config/diagnosticos')">Diagnósticos</x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
