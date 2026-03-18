@extends('web.app')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/equipo.css') }}">
@endpush

@section('contenido')

<!-- HERO: izquierda texto, derecha logo marketplace -->
<div class="equipo-hero container-fluid px-0">
  <div class="row g-0 align-items-center">
    <div class="col-lg-7 hero-left">
      <div class="hero-inner px-5 py-5">
        <h1 class="display-4">Construye tu negocio digital con StartPlace</h1>
      </div>
    </div>
    <div class="col-lg-5 hero-right d-flex align-items-center justify-content-center">
      <div class="marketplace-box text-center p-4">
        <img src="{{ asset('images/logo.png') }}" alt="MarketPlace" class="market-logo mb-3" style="max-width:300px;">
      </div>
    </div>
  </div>
</div>

<!-- IDEA section -->
<div class="container py-5">
  <div class="row justify-content-center text-center mb-5">
    <div class="col-lg-8">
      <div class="idea-box p-4">
        <h2 class="section-title">Como iniciar?</h2>
        <p class="section-sub">StartPlace es el marketplace diseñado para emprendedores, negocios locales y vendedores independientes que buscan aumentar su visibilidad sin costos exagerados ni procesos difíciles.
Publica tus productos, recibe más visitas gracias a nuestra publicidad destacada y administra tus ventas de forma sencilla y segura.</p>
        <a class="btn btn-dark mt-3" href="{{ route('web.solicitud') }}">Empezar</a>
      </div>
    </div>
  </div>

  <!-- Equipo -->
  <h3 class="text-center mb-4">Equipo Directivo</h3>
  @php
    $equipo = [
      [
        'imagen' => 1,
        'nombre' => 'Fundador',
        'cargo' => 'CEO & Estrategia de Negocio',
        'descripcion' => 'Define la visión de StartPlace, lidera el crecimiento del marketplace y establece alianzas estratégicas.',
      ],
      [
        'imagen' => 2,
        'nombre' => 'Cofundador',
        'cargo' => 'COO & Operaciones',
        'descripcion' => 'Supervisa procesos operativos, experiencia de vendedores y calidad del servicio en toda la plataforma.',
      ],
      [
        'imagen' => 3,
        'nombre' => 'Líder de Tecnología',
        'cargo' => 'CTO & Producto Digital',
        'descripcion' => 'Dirige la evolución tecnológica, seguridad de la plataforma y desarrollo continuo de nuevas funcionalidades.',
      ],
      [
        'imagen' => 4,
        'nombre' => 'Líder Comercial',
        'cargo' => 'CMO & Crecimiento de Marca',
        'descripcion' => 'Impulsa posicionamiento de marca, adquisición de usuarios y estrategias de marketing orientadas a resultados.',
      ],
    ];
  @endphp
  <div class="row gy-4">
    @foreach($equipo as $miembro)
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card team-card text-center p-3 h-100">
          <img src="{{ asset('images/team' . $miembro['imagen'] . '.svg') }}" alt="{{ $miembro['nombre'] }}" class="team-photo mb-3">
          <h5 class="mb-1">{{ $miembro['nombre'] }}</h5>
          <p class="fw-semibold mb-2">{{ $miembro['cargo'] }}</p>
          <p class="small text-muted mb-0">{{ $miembro['descripcion'] }}</p>
        </div>
      </div>
    @endforeach
  </div>
</div>

<!-- El formulario de solicitud fue movido a la página dedicada (/solicitud) -->

@endsection
