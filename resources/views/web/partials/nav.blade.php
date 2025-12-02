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
    /* User dropdown styles */
    .nav-user-dropdown .dropdown-toggle { gap: .5rem; }
    .user-dropdown-menu { min-width: 260px; border-radius:12px; overflow:hidden; box-shadow:0 10px 30px rgba(19,38,63,0.12); border:1px solid rgba(13,110,253,0.06); }
    /* Header neutralizado: quitar fondo azul para mejor integración con vistas */
    .user-dropdown-header { padding:14px 16px; background:#ffffff; color:#111827; display:flex; gap:12px; align-items:center; border-bottom:1px solid rgba(15,23,42,0.04); }
    .user-avatar-lg{ width:64px; height:64px; object-fit:cover; border-radius:50%; border:2px solid rgba(2,6,23,0.06); box-shadow:0 6px 18px rgba(2,6,23,0.04); }
    .user-dropdown-name{ font-weight:700; font-size:0.95rem; color:#0f1724 }
    .user-dropdown-email{ font-size:0.8rem; color:#475569 }
    .user-dropdown-body{ padding:12px 12px; background:#fff; }
    .user-action-btn{ width:100%; display:flex; align-items:center; justify-content:center; gap:.5rem; padding:.5rem .6rem; border-radius:8px; font-weight:600 }
    .user-action-btn.light{ background:#f8f9fb; border:1px solid rgba(11,99,214,0.06); color:#0b63d6 }
    .user-action-btn.ghost{ background:transparent; border:0; color:#495057 }
    .user-logout-btn{ background:transparent; color:#e55353; border:0; font-weight:700 }
    .user-links { display:flex; flex-direction:column; gap:6px; }
    .user-links a { color:#374151; padding:8px 10px; border-radius:8px; text-decoration:none; }
    .user-links a:hover{ background:#f1f5f9; }
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

                {{-- user dropdown moved to right side next to cart --}}

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

            {{-- Right-side: user dropdown and logo (moved from left) --}}
            <div class="d-flex align-items-center ms-3">
                @auth
                    @php
                        $user = auth()->user();
                        $avatar = $user->avatar ?? null;
                        $name = trim($user->name ?? '');
                        $parts = preg_split('/\s+/', $name);
                        $initials = strtoupper((isset($parts[0])?substr($parts[0],0,1):'') . (isset($parts[1])?substr($parts[1],0,1):''));
                    @endphp

                    <div class="dropdown nav-user-dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="navUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if($avatar)
                                <img src="{{ asset($avatar) }}" alt="Avatar" class="rounded-circle me-2" style="width:36px;height:36px;object-fit:cover;border:2px solid rgba(11,99,214,0.08);">
                            @else
                                <div class="rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width:36px;height:36px;background:linear-gradient(135deg,#e6f9ff,#dff3ff);color:var(--bs-primary);font-weight:700;">{{ $initials ?: 'U' }}</div>
                            @endif
                            <span class="me-3 d-none d-md-inline">{{ $user->name }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end user-dropdown-menu p-0" aria-labelledby="navUserDropdown">
                            <div class="user-dropdown-header">
                                @if($avatar)
                                    <img src="{{ asset($avatar) }}" class="user-avatar-lg" alt="Avatar">
                                @else
                                    <div class="user-avatar-lg d-inline-flex justify-content-center align-items-center" style="background:#fff;color:#0b63d6;font-weight:800;">{{ $initials ?: 'U' }}</div>
                                @endif
                                <div>
                                    <div class="user-dropdown-name">{{ $user->name }}</div>
                                    <div class="user-dropdown-email">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="user-dropdown-body">
                                <div class="user-links mb-2">
                                    <a href="{{route('perfil.pedidos')}}"><i class="bi bi-bag me-2"></i>Mis pedidos</a>
                                    <a href="{{ route('plantilla.profile') }}"><i class="bi bi-person me-2"></i>Mi perfil</a>
                                    <a href="{{ route('perfil.edit') }}"><i class="bi bi-pencil me-2"></i>Editar perfil</a>
                                </div>

                                <div class="mt-2 text-center">
                                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="user-logout-btn"> <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a class="btn btn-link me-3" href="{{ route('login') }}">Iniciar sesión</a>
                @endauth

                {{-- Brand/logo was originally on the left; removed from right. --}}
            </div>
        </div>
    </div>
</nav>
