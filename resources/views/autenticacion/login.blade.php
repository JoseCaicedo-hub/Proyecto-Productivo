@extends('autenticacion.app')
@section('titulo', 'StartPlace - Login')
@section('contenido')

<div class="animated-background"></div>

<div class="login-box">
  <div class="login-container">
    <div class="logo-section">
      <h1>StartPlace</h1>
      <p>Bienvenido de vuelta</p>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
    @endif

    @if(Session::has('mensaje'))
      <div class="alert alert-info alert-dismissible fade show">
        {{Session::get('mensaje')}}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></button>
      </div>
    @endif

    <form action="{{route('login.post')}}" method="post" id="loginForm">
      @csrf
      
      <div class="form-group">
        <div class="input-wrapper">
          <input 
            id="loginEmail" 
            type="email" 
            name="email" 
            value="{{old('email')}}" 
            class="form-control" 
            placeholder=" "
            required
            autocomplete="email"
          />
          <i class="bi bi-envelope input-icon"></i>
          <label for="loginEmail" class="form-label">Email</label>
        </div>
      </div>

      <div class="form-group">
        <div class="input-wrapper position-relative">
          <input 
            id="loginPassword" 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder=" "
            required
            autocomplete="current-password"
          />
          <i class="bi bi-lock-fill input-icon"></i>
          <label for="loginPassword" class="form-label">Contraseña</label>
          <button type="button" class="btn btn-sm btn-link toggle-password position-absolute" data-target="#loginPassword" style="right:10px;top:50%;transform:translateY(-50%);">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      
<div class="links-vertical mb-2">
  <div class="forgot-link">
    <a href="{{route('password.request')}}">¿Olvidaste tu contraseña?</a>
  </div>

  <div class="forgot-link">
    <a href="{{route('registro')}}">Eres Nuevo/a en StartPlace? Regístrate</a>
  </div>
</div>

      <div class="social-login mb-3">
        <a href="{{ url('auth/redirect/google') }}" class="btn social-google">
          <i class="bi bi-google"></i>
        </a>
        <a href="{{ url('auth/redirect/facebook') }}" class="btn social-facebook">
          <i class="bi bi-facebook"></i>
        </a>
        <a href="{{ url('auth/redirect/twitter') }}" class="btn social-twitter">
          <i class="bi bi-twitter"></i>
        </a>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn-login">
          <span>Iniciar Sesión</span>
        </button>
      </div>
      <div class="login-footer-links">
        <a href="#">Ayuda</a>
        <span>·</span>
        <a href="#">Términos</a>
        <span>·</span>
        <a href="#">Privacidad</a>
    </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para los inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach((input, index) => {
      input.style.animationDelay = `${index * 0.1}s`;
      input.style.animation = 'slideIn 0.5s ease-out forwards';
    });

    // Validación en tiempo real
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');

    emailInput.addEventListener('input', function() {
      if (this.validity.valid) {
        this.style.borderColor = '#4CAF50';
      } else {
        this.style.borderColor = '#e0e0e0';
      }
    });

    passwordInput.addEventListener('input', function() {
      if (this.value.length >= 6) {
        this.style.borderColor = '#4CAF50';
      } else if (this.value.length > 0) {
        this.style.borderColor = '#ff9800';
      } else {
        this.style.borderColor = '#e0e0e0';
      }
    });

    // Efecto de typing en el título
    const title = document.querySelector('.logo-section h1');
    const originalText = title.textContent;
    title.textContent = '';
    let i = 0;
    
    function typeWriter() {
      if (i < originalText.length) {
        title.textContent += originalText.charAt(i);
        i++;
        setTimeout(typeWriter, 150);
      }
    }

    setTimeout(typeWriter, 500);

    // Animación de carga del botón
    form.addEventListener('submit', function(e) {
      const btn = document.querySelector('.btn-login');
      btn.innerHTML = '<span><i class="bi bi-arrow-repeat spin"></i> Iniciando sesión...</span>';
      btn.disabled = true;
    });

    // Efecto de ondas en el botón
    const btn = document.querySelector('.btn-login');
    btn.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      ripple.classList.add('ripple');
      
      this.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });

    // Mostrar/ocultar contraseña (toggle)
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

      