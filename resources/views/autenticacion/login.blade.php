@extends('autenticacion.app')
@section('titulo', 'StartPlace - Login')
@section('contenido')

<div class="animated-background">
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
</div>

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
        <div class="input-wrapper">
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
        </div>
      </div>

      <div class="forgot-link">
        <a href="{{route('password.request')}}">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="btn-login">
        <span>Iniciar Sesión</span>
      </button>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Efecto de partículas adicionales con mouse
    const container = document.querySelector('.animated-background');
    let particles = [];

    function createParticle(e) {
      const particle = document.createElement('div');
      particle.className = 'bubble';
      particle.style.width = Math.random() * 30 + 20 + 'px';
      particle.style.height = particle.style.width;
      particle.style.left = e.clientX + 'px';
      particle.style.top = e.clientY + 'px';
      particle.style.animation = 'float 3s ease-out forwards';
      container.appendChild(particle);

      setTimeout(() => {
        particle.remove();
      }, 3000);
    }

    // Crear partículas al mover el mouse
    let mouseMoveTimer;
    document.addEventListener('mousemove', function(e) {
      clearTimeout(mouseMoveTimer);
      mouseMoveTimer = setTimeout(() => {
        if (Math.random() > 0.7) {
          createParticle(e);
        }
      }, 100);
    });

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

    // Efecto parallax suave
    document.addEventListener('mousemove', function(e) {
      const bubbles = document.querySelectorAll('.bubble');
      const mouseX = e.clientX / window.innerWidth;
      const mouseY = e.clientY / window.innerHeight;

      bubbles.forEach((bubble, index) => {
        const speed = (index + 1) * 0.5;
        const x = (mouseX - 0.5) * speed;
        const y = (mouseY - 0.5) * speed;
        bubble.style.transform += ` translate(${x}px, ${y}px)`;
      });
    });

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
  });
</script>

@endsection

      