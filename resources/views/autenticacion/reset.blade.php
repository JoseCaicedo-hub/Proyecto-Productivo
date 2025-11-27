@extends('autenticacion.app')
@section('titulo', 'Sistema - Cambiar Password')
@section('contenido')

<div class="animated-background"></div>

<div class="login-box">
  <div class="login-container">
    <div class="logo-section">
      <h1>StartPlace</h1>
      <p>Cambiar Contraseña</p>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
    @endif

    <form action="{{route('password.update')}}" method="post">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

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

      <div class="form-group">
        <div class="input-wrapper position-relative">
          <input id="loginPassword" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder=" " />
          <i class="bi bi-lock-fill input-icon"></i>
          <label for="loginPassword" class="form-label">Contraseña</label>
          <button type="button" class="btn btn-sm btn-link toggle-password position-absolute" data-target="#loginPassword" style="right:10px;top:50%;transform:translateY(-50%);">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <div class="input-wrapper position-relative">
          <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder=" " />
          <i class="bi bi-lock-fill input-icon"></i>
          <label for="password_confirmation" class="form-label">Confirme su Contraseña</label>
          <button type="button" class="btn btn-sm btn-link toggle-password position-absolute" data-target="#password_confirmation" style="right:10px;top:50%;transform:translateY(-50%);">
            <i class="bi bi-eye"></i>
          </button>
        </div>
        @error('password_confirmation')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <div class="row">
        <div class="col-12">
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Actualizar password</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
          const target = document.querySelector(this.getAttribute('data-target'));
          if (!target) return;
          if (target.type === 'password') {
            target.type = 'text';
            this.querySelector('i').classList.remove('bi-eye');
            this.querySelector('i').classList.add('bi-eye-slash');
          } else {
            target.type = 'password';
            this.querySelector('i').classList.remove('bi-eye-slash');
            this.querySelector('i').classList.add('bi-eye');
          }
        });
      });
    });
  </script>
@endsection

      