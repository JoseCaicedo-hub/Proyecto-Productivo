@extends('web.app')

@section('contenido')
<section class="py-5">
  <div class="container px-4 px-lg-6">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card p-4">
          <h2 class="mb-3">Enviar solicitud de emprendimiento</h2>
          <p class="small text-muted">Cuéntanos tu idea y tus datos. Nuestro equipo la revisará y te contactaremos.</p>

          @if(session('mensaje'))
            <div class="alert alert-success">{{ session('mensaje') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('solicitudes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input name="nombre" id="nombre" class="form-control" value="{{ auth()->user()->name ?? old('nombre') }}" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email ?? old('email') }}" required>
            </div>

            <div class="mb-3">
              <label for="telefono" class="form-label">Teléfono (opcional)</label>
              <input name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>

            <div class="mb-3">
              <label for="titulo" class="form-label">Título / Nombre del proyecto (opcional)</label>
              <input name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}">
            </div>

            <div class="mb-3">
              <label for="idea" class="form-label">Describe tu idea (qué vendes, público, diferencial)</label>
              <textarea name="idea" id="idea" rows="6" class="form-control" required>{{ old('idea') }}</textarea>
            </div>

            <div class="mb-3">
              <label for="detalle" class="form-label">Detalles adicionales (opcional)</label>
              <textarea name="detalle" id="detalle" rows="3" class="form-control">{{ old('detalle') }}</textarea>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-primary">Enviar solicitud</button>
              <a class="btn btn-outline-secondary" href="{{ route('web.equipo') }}">Volver</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
