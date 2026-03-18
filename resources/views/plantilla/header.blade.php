<nav class="app-header navbar navbar-expand bg-body" style="background:linear-gradient(180deg,#ffffff 0%, #fbfdff 100%);border-bottom:1px solid rgba(0,0,0,0.04);">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('web.index') }}" class="nav-link">Inicio</a></li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('web.tienda') }}" class="nav-link">Tienda</a></li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('web.preguntas') }}" class="nav-link">Preguntas</a></li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('web.equipo') }}" class="nav-link">Acerca</a></li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('web.contactanos') }}" class="nav-link">Contáctanos</a></li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            @if(Auth::check())
            <?php
                $user = Auth::user();
                $name = trim($user->name ?? '');
                $parts = preg_split('/\s+/', $name);
                $initials = strtoupper((isset($parts[0])?substr($parts[0],0,1):'') . (isset($parts[1])?substr($parts[1],0,1):''));
                $avatar = $user->avatar ?? null; // expected path relative to storage/public or url
            ?>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    @if($avatar)
                        <img src="{{ asset($avatar) }}" class="user-image rounded-circle shadow-sm me-2" alt="Avatar" style="width:40px;height:40px;object-fit:cover;border:2px solid rgba(11,99,214,0.12);">
                    @else
                        <div class="user-initials rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width:40px;height:40px;background:linear-gradient(135deg,#e6f9ff,#dff3ff);color:var(--bs-primary);font-weight:700;">{{ $initials ?: 'U' }}</div>
                    @endif
                    <span class="d-none d-md-inline">{{ $user->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" style="min-width:220px;">
                    <li>
                        <div class="plantilla-user-header">
                            @if($avatar)
                                <img src="{{ asset($avatar) }}" class="plantilla-avatar-lg" alt="Avatar">
                            @else
                                <div class="plantilla-avatar-placeholder">{{ $initials ?: 'U' }}</div>
                            @endif
                            <div>
                                <div class="plantilla-user-name">{{ $user->name }}</div>
                                <div class="plantilla-user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </li>
                    <li class="plantilla-user-actions">
                        <a href="{{ route('plantilla.profile') }}" class="btn btn-primary plantilla-btn">Ver perfil</a>
                        <a href="{{ route('perfil.edit') }}" class="btn btn-outline-primary plantilla-btn">Editar perfil</a>

                        {{-- Formulario de subida de avatar --}}
                        <form action="{{ route('perfil.avatar.upload', []) }}" method="post" enctype="multipart/form-data" id="avatar-upload-form">
                            @csrf
                            <label class="btn btn-light plantilla-btn" style="border:1px dashed rgba(0,0,0,0.08);">
                                Cambiar foto
                                <input type="file" name="avatar" accept="image/*" onchange="document.getElementById('avatar-upload-form').submit()" style="display:none">
                            </label>
                        </form>

                        <a href="#" onclick="document.getElementById('logout-form').submit();" class="btn btn-outline-secondary plantilla-btn">Cerrar sesión</a>
                        <form action="{{route('logout')}}" id="logout-form" method="post" class="d-none">@csrf</form>
                    </li>
                </ul>
            </li>
            @endif
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>