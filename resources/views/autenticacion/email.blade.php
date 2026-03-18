@extends('autenticacion.app')
@section('titulo', 'Sistema - Recuperar Password')
@section('contenido')

<div class="animated-background"></div>

<style>
  .reset-loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(1.5px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 3000;
  }

  .reset-loading-box {
    min-width: 250px;
    border-radius: 12px;
    padding: 16px 18px;
    background: rgba(15, 23, 42, 0.94);
    color: #f8fafc;
    box-shadow: 0 14px 34px rgba(2, 6, 23, 0.35);
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
  }

  .reset-loading-box .spinner-border {
    width: 1.1rem;
    height: 1.1rem;
    border-width: .14em;
  }

  .btn-submit-loading .spinner-border {
    width: 0.95rem;
    height: 0.95rem;
    border-width: .12em;
    margin-right: 8px;
    vertical-align: text-bottom;
  }
</style>

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

    <form action="{{route('password.send-link')}}" method="post" id="resetLinkForm">
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
            <button type="submit" class="btn btn-primary btn-submit-loading" id="resetLinkSubmitBtn">
              <span class="spinner-border spinner-border-sm d-none" id="resetLinkSpinner" role="status" aria-hidden="true"></span>
              <span id="resetLinkSubmitText">Enviar enlace de recuperación</span>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="reset-loading-overlay" id="resetLoadingOverlay" aria-hidden="true">
  <div class="reset-loading-box">
    <div class="spinner-border text-light" role="status" aria-hidden="true"></div>
    <span>Enviando enlace de recuperación...</span>
  </div>
</div>

<script>
  (function () {
    const form = document.getElementById('resetLinkForm');
    const submitBtn = document.getElementById('resetLinkSubmitBtn');
    const submitText = document.getElementById('resetLinkSubmitText');
    const spinner = document.getElementById('resetLinkSpinner');
    const overlay = document.getElementById('resetLoadingOverlay');

    if (!form || !submitBtn || !submitText || !spinner || !overlay) {
      return;
    }

    form.addEventListener('submit', function (event) {
      if (form.dataset.submitting === 'true') {
        event.preventDefault();
        return;
      }

      if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
        return;
      }

      form.dataset.submitting = 'true';
      submitBtn.disabled = true;
      spinner.classList.remove('d-none');
      submitText.textContent = 'Enviando...';
      overlay.style.display = 'flex';
      overlay.setAttribute('aria-hidden', 'false');
    });
  })();
</script>

@endsection