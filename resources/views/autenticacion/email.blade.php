@extends('autenticacion.app')
@section('titulo', 'Sistema - Recuperar Password')
@section('contenido')

<div class="animated-background"></div>

<div class="login-box">
  <div class="login-container">
    <div class="logo-section">
      <h1>StartPlace</h1>
      <p>Ingrese su email para recuperar su password</p>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
    @endif

    <form action="{{route('password.send-link')}}" method="post">
      @csrf
      @if(Session::has('mensaje'))
        <div class="alert alert-info alert-dismissible fade show mt-2">
          {{Session::get('mensaje')}}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
        </div>
      @endif

      <div class="form-group">
        <div class="input-wrapper">
          <input id="loginEmail" type="email" name="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" placeholder=" " />
          <i class="bi bi-envelope input-icon"></i>
          <label for="loginEmail" class="form-label">Email</label>
        </div>
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <div class="row">
        <div class="col-12">
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Enviar enlace de recuperación</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection