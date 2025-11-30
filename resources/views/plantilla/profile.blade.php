@extends('plantilla.app')

@section('titulo', 'Perfil de usuario - StartPlace')

@section('contenido')
<style>
/* Scoped styles para la página de perfil */
.profile-page .profile-header{
  background: linear-gradient(90deg,#fff 0%, #f7f7f7 100%);
  padding: 28px;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.06);
}
.profile-card{
  display:flex;gap:20px;align-items:center;
}
.profile-avatar{
  width:120px;height:120px;border-radius:50%;overflow:hidden;flex:0 0 120px;border:6px solid #fff;box-shadow:0 6px 18px rgba(0,0,0,0.12);
}
.profile-avatar img{width:100%;height:100%;object-fit:cover}
.profile-main h2{margin:0;font-weight:700}
.profile-main .meta{color:#6c757d;margin-top:6px}
.profile-actions{margin-top:12px}
.profile-actions .btn{margin-right:8px}

/* Panels */
.info-card{border-radius:10px;padding:18px;background:#fff;box-shadow:0 6px 20px rgba(0,0,0,0.04);}
.recent-list .item{display:flex;align-items:flex-start;gap:12px;padding:12px 10px;border-bottom:1px solid #f1f1f1}
.recent-list .item:last-child{border-bottom:0}
.recent-list .item .icon{width:36px;height:36px;border-radius:8px;background:#f5f7fa;display:flex;align-items:center;justify-content:center;color:#2b6cb0}
.small-muted{color:#6c757d;font-size:0.92rem}

/* Responsivo */
@media (max-width: 767px){
  .profile-card{flex-direction:column;align-items:flex-start}
  .profile-avatar{width:96px;height:96px}
}
</style>

<section class="py-4 profile-page">
  <div class="container">
    <div class="row mb-3">
      <div class="col-12">
        <div class="profile-header info-card">
          <div class="d-flex justify-content-between align-items-center w-100">
            <div class="profile-card">
              <div class="profile-avatar">
                @php
                  $profileUser = $user ?? auth()->user();
                  $avatar = $profileUser->avatar ?? null;
                  $name = trim($profileUser->name ?? '');
                  $parts = preg_split('/\s+/', $name);
                  $initials = strtoupper((isset($parts[0])?substr($parts[0],0,1):'') . (isset($parts[1])?substr($parts[1],0,1):''));
                @endphp
                @if($avatar)
                  <img src="{{ asset($avatar) }}" alt="Avatar" />
                @else
                  @if(!empty($initials))
                    <div class="d-flex justify-content-center align-items-center h-100" style="font-weight:700;color:#0b63d6;font-size:1.25rem;background:linear-gradient(135deg,#e6f9ff,#dff3ff);border-radius:50%;">{{ $initials }}</div>
                  @else
                    <img src="{{ asset('images/Logo.png') }}" alt="Avatar" />
                  @endif
                @endif
              </div>
              <div class="profile-main">
                <h2>{{ $user->name ?? auth()->user()->name ?? 'Nombre Usuario' }}</h2>
                <div class="meta small-muted">{{ $user->email ?? auth()->user()->email ?? 'email@startplace.com' }}</div>
                <div class="profile-actions">
                  <a href="{{ route('perfil.edit') }}" class="btn btn-primary btn-sm">Gestionar cuenta</a>
                  <a href="{{ route('perfil.pedidos') }}" class="btn btn-outline-secondary btn-sm">Mis pedidos</a>
                </div>
              </div>
            </div>
            <div class="text-end d-none d-md-block">
              @php
                $profileUser = $profileUser ?? $user ?? auth()->user();
                $rating = isset($profileUser->rating) ? floatval($profileUser->rating) : 0;
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
              @endphp

              <div class="mb-2" aria-label="Calificación del usuario">
                @for($i = 0; $i < $fullStars; $i++)
                  <i class="bi bi-star-fill text-warning me-1" style="font-size:18px"></i>
                @endfor
                @if($halfStar)
                  <i class="bi bi-star-half text-warning me-1" style="font-size:18px"></i>
                @endif
                @for($i = 0; $i < $emptyStars; $i++)
                  <i class="bi bi-star text-muted me-1" style="font-size:18px"></i>
                @endfor
                <div class="small text-muted mt-1">{{ $rating > 0 ? number_format($rating,1) : 'Sin calificaciones' }}</div>
              </div>

              <div class="small-muted">Miembro desde: <strong>{{ isset($profileUser) && $profileUser->created_at ? $profileUser->created_at->format('d M Y') : (auth()->user()->created_at->format('d M Y') ?? '—') }}</strong></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="info-card mb-3">
          <h5 class="mb-3">Acerca de</h5>
          <p class="small-muted">Información pública del usuario y detalles de contacto.</p>
          <dl class="row mb-0 mt-3">
            <dt class="col-5">Rol</dt>
            <dd class="col-7">{{ $user->role ?? 'Cliente' }}</dd>
            <dt class="col-5">Teléfono</dt>
            <dd class="col-7">{{ $user->telefono ?? '—' }}</dd>
            <dt class="col-5">Ubicación</dt>
            <dd class="col-7">{{ $user->ciudad ?? '—' }}</dd>
          </dl>
        </div>

        <div class="info-card mb-3">
          <h6>Estadísticas</h6>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
              <div class="h4 mb-0">{{ $ordersCount ?? 0 }}</div>
              <div class="small-muted">Pedidos</div>
            </div>
            <div>
              <div class="h4 mb-0">{{ $reviewsCount ?? 0 }}</div>
              <div class="small-muted">Reseñas</div>
            </div>
            <div>
              <div class="h4 mb-0">{{ $spend ?? '0' }}</div>
              <div class="small-muted">Gasto</div>
            </div>
          </div>
        </div>

        <div class="info-card">
          <h6>Enlaces rápidos</h6>
          <ul class="list-unstyled mb-0 mt-2">
            <li><a href="{{ route('perfil.edit') }}">Editar perfil</a></li>
            <li><a href="{{ route('perfil.pedidos') }}">Mis pedidos</a></li>
            <li><a href="{{ route('web.contactanos') }}">Contactar soporte</a></li>
          </ul>
        </div>

      </div>

      <div class="col-lg-8">
        <div class="info-card mb-3">
          <h5 class="mb-3">Actividad reciente</h5>
          <div class="recent-list">
            @forelse($recentActivities ?? [] as $act)
              <div class="item">
                <div class="icon"><i class="bi bi-bell"></i></div>
                <div>
                  <div><strong>{{ $act['title'] }}</strong></div>
                  <div class="small-muted">{{ $act['meta'] ?? '' }}</div>
                </div>
              </div>
            @empty
              <div class="small-muted p-3">No hay actividad reciente.</div>
            @endforelse
          </div>
        </div>

        <div class="info-card">
          <h5 class="mb-3">Detalles</h5>
          <p class="mb-0 small-muted">Aquí puedes mostrar información adicional: órdenes, direcciones, métodos de pago y más. Inserta componentes o partials según tu necesidad.</p>
        </div>
      </div>
    </div>

  </div>
</section>

@endsection
