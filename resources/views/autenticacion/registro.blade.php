@extends('autenticacion.app')
@section('titulo', 'Sistema - Registro')
@section('contenido')
<style>
/* Scoped styles para la tarjeta de registro: centrar y limitar ancho */
.animated-background{position:fixed;inset:0;background-size:cover;background-position:center;filter:brightness(0.9);z-index:0}
.login-box{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;padding:24px 12px}
.login-box .login-container{width:100%;max-width:460px;background:#fff;border-radius:12px;padding:28px;box-shadow:0 12px 40px rgba(16,24,40,0.12)}
.login-box .logo-section h1{font-weight:800;margin:0 0 6px;text-align:center}
.login-box .logo-section p{margin:0 0 18px;text-align:center;color:#6c757d}
.login-box .form-group{margin-bottom:14px}
.login-box .input-wrapper .form-control{height:52px;padding:14px 42px 14px 44px}
.login-box .input-wrapper .input-icon{left:14px}
@media (max-width:575px){
  .login-box{padding:18px}
  .login-box .login-container{padding:20px}
}
</style>

<div class="animated-background"></div>

<div class="login-box">
  <div class="login-container">
    <div class="logo-section">
      <h1>StartPlace</h1>
      <p>Registro</p>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
    @endif

    <form action="{{route('registro.store')}}" method="post">
      @csrf

      <div class="form-group">
        <div class="input-wrapper">
          <input id="name" type="text" name="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" placeholder=" " />
          <i class="bi bi-person input-icon"></i>
          <label for="name" class="form-label">Nombre</label>
        </div>
        @error('name')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

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
        <div class="input-wrapper">
          <input id="telefono" type="text" name="telefono" value="{{old('telefono')}}" class="form-control @error('telefono') is-invalid @enderror" placeholder=" " />
          <i class="bi bi-telephone input-icon"></i>
          <label for="telefono" class="form-label">Teléfono</label>
        </div>
        @error('telefono')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-wrapper">
              <input id="ciudad" type="text" name="ciudad" value="{{old('ciudad')}}" class="form-control @error('ciudad') is-invalid @enderror" placeholder=" " />
              <i class="bi bi-building input-icon"></i>
              <label for="ciudad" class="form-label">Ciudad</label>
            </div>
            @error('ciudad')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-wrapper">
              <input id="municipio" type="text" name="municipio" value="{{old('municipio')}}" class="form-control @error('municipio') is-invalid @enderror" placeholder=" " />
              <i class="bi bi-geo-alt input-icon"></i>
              <label for="municipio" class="form-label">Municipio</label>
            </div>
            @error('municipio')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
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
            <button type="submit" class="btn btn-primary">Registrar</button>
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

      