<aside class="app-sidebar custom-sidebar shadow">
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
                @canany(['producto-list', 'empresa-list'])
                @php
                    $almacenOpen = request()->routeIs('almacen.*') || request()->routeIs('productos.*') || request()->routeIs('mayorista.*');
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
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('vendedor'))
                        <li class="nav-item">
                            <a href="{{ route('mayorista.solicitudes.index') }}" class="nav-link {{ request()->routeIs('mayorista.*') ? 'active' : '' }}" id="itemGranPedido">
                                <i class="nav-icon bi bi-basket2-fill"></i>
                                <p>Gran Pedido</p>
                            </a>
                        </li>
                        @endif
                        @endcan
                    </ul>
                </li>
                @endcanany            

                @role('admin')
                @php
                    $autorizacionOpen = request()->routeIs('empresas.*') || request()->routeIs('admin.solicitudes.*') || request()->routeIs('admin.empresas.solicitudes.*');
                    $pendientes = \App\Models\Solicitud::where('estado','pendiente')->count();
                @endphp
                <li class="nav-item {{ $autorizacionOpen ? 'menu-open' : '' }}" id="mnuAutorizacion">
                    <a href="#" class="nav-link {{ $autorizacionOpen ? 'active' : '' }}">
                        <i class="nav-icon bi bi-check2-square"></i>
                        <p>
                            Autorización
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('empresas.index') }}" class="nav-link {{ request()->routeIs('empresas.*') ? 'active' : '' }}" id="itemEmpresa">
                                <i class="nav-icon bi bi-building"></i>
                                <p>Empresas</p>
                            </a>
                        </li>
                        <li class="nav-item">
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
                    </ul>
                </li>
                @endrole
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>