<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <style>
    /* Scoped navbar styles */
    .nav-logout-btn{
        --sp:#0b63d6;
        background:linear-gradient(90deg,var(--sp),#0a58c7);
        color:#fff;border:0;padding:.45rem .75rem;border-radius:.45rem;font-weight:600;display:inline-flex;align-items:center;gap:.5rem}
    .nav-logout-btn:hover{filter:brightness(.95);text-decoration:none;color:#fff}
    .nav-logout-form{display:inline-block;margin-left:.75rem}
    @media (max-width:767px){.nav-logout-btn{padding:.4rem .6rem;font-size:.95rem}}
    </style>
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="{{ route('web.index') }}">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" style="max-height:75px;" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('web.index') ? 'fw-bold' : '' }}" href="{{ route('web.index') }}" @if(request()->routeIs('web.index')) aria-current="page" @endif>Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('web.equipo') ? 'fw-bold' : '' }}" href="{{ route('web.equipo') }}">Acerca</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('web.tienda') ? 'fw-bold' : '' }}" href="{{ route('web.tienda') }}">Tienda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('web.preguntas') ? 'fw-bold' : '' }}" href="{{ route('web.preguntas') }}">Preguntas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('web.contactanos') ? 'fw-bold' : '' }}" href="{{ route('web.contactanos') }}">Contáctanos</a>
                </li>

                <li class="nav-item dropdown">
                    @auth
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">{{auth()->user()->name}}</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{route('perfil.pedidos')}}">Mis pedidos</a></li>
                        <li><a class="dropdown-item" href="{{ route('plantilla.profile') }}">Mi perfil</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="px-3 mb-0">
                                @csrf
                                <button type="submit" class="btn btn-link dropdown-item text-danger d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                    @endauth
                </li>

            </ul>

            {{-- Logout moved into user dropdown menu --}}

            <a href="{{route('carrito.mostrar')}}" class="btn btn-outline-dark">
                <i class="bi-cart-fill me-1"></i>
                Carrito
                <span class="badge bg-dark text-white ms-1 rounded-pill">
                    @php
                        $carrito = \App\Http\Controllers\CarritoController::getCartStatic();
                        echo $carrito ? array_sum(array_column($carrito, 'cantidad')) : 0;
                    @endphp
                </span>
            </a>

        </div>
    </div>
</nav>
