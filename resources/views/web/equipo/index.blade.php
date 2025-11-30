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
        <h1 class="display-4">Encabezado referente a la historia de tu negocio</h1>
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
        <h2 class="section-title">Idea</h2>
        <p class="section-sub">Cómo iniciar en StartPlace: crea tu cuenta, sube 3 fotos por producto con buena iluminación, escribe descripciones claras y precios competitivos. Activa envío y atención al cliente; publicamos tu producto en minutos.</p>
        <a class="btn btn-dark mt-3" href="#">Empezar</a>
      </div>
    </div>
  </div>

  <!-- Equipo -->
  <h3 class="text-center mb-4">Tu Equipo</h3>
  <div class="row gy-4">
    @foreach([1,2,3,4] as $i)
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card team-card text-center p-3 h-100">
          <img src="{{ asset('images/team' . $i . '.svg') }}" alt="Integrante {{$i}}" class="team-photo mb-3">
          <h5 class="mb-1">Integrante {{$i}}</h5>
          <p class="small text-muted mb-0">Resumen breve del aporte del integrante en el proyecto y sus responsabilidades.</p>
        </div>
      </div>
    @endforeach
  </div>
</div>

@endsection
