<aside class="app-sidebar custom-sidebar shadow">
    <style>
    /* Scoped styles para el sidebar: paleta blanca + celeste de StartPlace */
    .custom-sidebar{
        --sp-primary: #0b63d6; /* celeste principal */
        --sp-primary-dark: #07478a;
        --sp-accent: #e6f9ff;
        background: linear-gradient(180deg,#ffffff 0%, #f8fdff 100%);
        color:var(--sp-primary-dark);
        min-height:100vh;
        border-right:1px solid rgba(11,61,145,0.06);
    }
    .custom-sidebar .sidebar-brand{
        padding:12px 16px;
        background: linear-gradient(90deg,var(--sp-accent) 0%, #dff3ff 100%);
        border-bottom:1px solid rgba(11,61,145,0.04);
    }
    .custom-sidebar .brand-link{display:flex;align-items:center;gap:12px;text-decoration:none}
    .custom-sidebar .brand-image{width:56px;height:56px;object-fit:contain;border-radius:10px;box-shadow:0 6px 18px rgba(11,63,150,0.08)}
    .custom-sidebar .brand-text{font-weight:700;color:var(--sp-primary-dark);margin-left:6px;display:inline-block;opacity:0;transform:translateX(-6px);transition:opacity .18s ease, transform .18s ease}
    .custom-sidebar .brand-link:hover .brand-text, .custom-sidebar:hover .brand-text{opacity:1;transform:none}
    .custom-sidebar .sidebar-wrapper{padding-top:10px}
    .custom-sidebar .nav-link{color:var(--sp-primary-dark);border-radius:8px;margin:6px 8px;padding:10px 12px;display:flex;align-items:center}
    .custom-sidebar .nav-link .nav-icon{font-size:1.05rem;color:var(--sp-primary)}
    .custom-sidebar .nav-link:hover{background:rgba(11,99,214,0.06);color:var(--sp-primary-dark)}
    .custom-sidebar .nav-item .nav-treeview .nav-link{padding-left:36px}
    .custom-sidebar .nav-arrow{float:right;color:#6c757d}
    .custom-sidebar .nav-link.active, .custom-sidebar .nav-link.active:hover{background:linear-gradient(90deg,#dbeeff,#eaf6ff);color:var(--sp-primary-dark);font-weight:600}
    /* pequeño ajuste para iconos y texto en dispositivos pequeños */
        @media (max-width:767px){
            .custom-sidebar{position:relative}
            .custom-sidebar .brand-text{display:none}
            .custom-sidebar .brand-image{width:48px;height:48px}
        }
    </style>
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{ route('web.index') }}" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{ asset('images/Logo.png') }}" alt="StartPlace" class="brand-image shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text">StartPlace</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link" id="mnuDashboard">
                        <i class="nav-icon bi bi-grid"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('perfil.pedidos')}}" class="nav-link" id="mnuPedidos">
                        <i class="nav-icon bi bi-bag-fill"></i>
                        <p>
                            Pedidos
                        </p>
                    </a>
                </li>
                @canany(['user-list', 'rol-list'])
                <li class="nav-item" id="mnuSeguridad">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>
                            Seguridad
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('user-list')
                        <li class="nav-item">
                            <a href="{{route('usuarios.index')}}" class="nav-link" id="itemUsuario">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        @endcan
                        @can('rol-list')
                        <li class="nav-item">
                            <a href="{{route('roles.index')}}" class="nav-link" id="itemRole">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['producto-list'])
                @php
                    $almacenOpen = request()->routeIs('almacen.*') || request()->routeIs('productos.*');
                @endphp
                <li class="nav-item {{ $almacenOpen ? 'menu-open' : '' }}" id="mnuAlmacen">
                    <a href="#" class="nav-link {{ $almacenOpen ? 'active' : '' }}">
                        <i class="nav-icon bi bi-archive-fill"></i>
                        <p>
                            Almacén
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('producto-list')
                        <li class="nav-item">
                            <a href="{{route('productos.index')}}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" id="itemProducto">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Mis Productos</p>
                            </a>
                        </li>
                        @endcan
                        @can('producto-list')
                        <li class="nav-item">
                            <a href="{{ route('almacen.index') }}" class="nav-link {{ request()->routeIs('almacen.index') ? 'active' : '' }}" id="itemEntregar">
                                <i class="nav-icon bi bi-truck"></i>
                                <p>Entregar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('almacen.entregados') }}" class="nav-link {{ request()->routeIs('almacen.entregados') ? 'active' : '' }}" id="itemEntregados">
                                <i class="nav-icon bi bi-check2-square"></i>
                                <p>Entregados</p>
                            </a>
                        </li>
                        @role('admin')
                        <li class="nav-item">
                            @php $pendientes = \App\Models\Solicitud::where('estado','pendiente')->count(); @endphp
                            <a href="{{ route('admin.solicitudes.index') }}" class="nav-link {{ request()->routeIs('admin.solicitudes.*') ? 'active' : '' }}" id="itemSolicitudes">
                                <i class="nav-icon bi bi-list-check"></i>
                                <p>
                                    Solicitudes
                                    @if($pendientes > 0)
                                        <span class="badge bg-danger ms-2">{{ $pendientes }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>
                        @endrole
                        @endcan
                    </ul>
                </li>
                @endcanany            
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>