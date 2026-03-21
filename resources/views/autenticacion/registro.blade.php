@extends('autenticacion.app')
@section('titulo', 'Sistema - Registro')
@section('contenido')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css">
<style>
/* Scoped styles para la tarjeta de registro: centrar y limitar ancho */
.animated-background{position:fixed;inset:0;background-size:cover;background-position:center;filter:brightness(0.9);z-index:0}
.login-box{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;padding:24px 12px}
.login-box .login-container{width:100%;max-width:640px;background:#fff;border-radius:12px;padding:28px;box-shadow:0 12px 40px rgba(16,24,40,0.12)}
.login-box .logo-section h1{font-weight:800;margin:0 0 6px;text-align:center}
.login-box .logo-section p{margin:0 0 18px;text-align:center;color:#6c757d}
.login-box .form-group{margin-bottom:14px}
.login-box .input-wrapper .form-control{height:52px;padding:14px 42px 14px 44px}
.login-box .input-wrapper select.form-control{appearance:none;background-image:url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"%3e%3cpath fill="none" stroke="%23343a40" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 5l6 6 6-6"/%3e%3c/svg%3e');background-repeat:no-repeat;background-position:right 14px center;background-size:16px 12px;padding-right:36px !important}
.login-box .input-wrapper .input-icon{left:14px}
.login-box .iti{width:100%}
.login-box .iti .form-control{padding-left:96px}
.login-box .phone-input-wrapper .input-icon{display:none}
.login-box .phone-input-wrapper .iti{margin-top:8px}
.login-box .phone-input-wrapper .form-label{left:110px !important;position:absolute;top:14px;transition:all 0.2s ease;background:#fff;padding:0 2px}
.iti--allow-dropdown .iti__flag-container{padding-right:4px}
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
          <select id="tipo_usuario" name="tipo_usuario" class="form-control @error('tipo_usuario') is-invalid @enderror">
            <option value="">Selecciona tipo de usuario</option>
            <option value="cliente" {{ old('tipo_usuario') == 'cliente' ? 'selected' : '' }}>Cliente</option>
            <option value="vendedor" {{ old('tipo_usuario') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
          </select>
          <i class="bi bi-person-check input-icon"></i>
          <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
        </div>
        @error('tipo_usuario')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Campos para vendedores (ocultos inicialmente) -->
      <div id="vendedor-fields" class="d-none">
        <div class="form-group">
          <div class="input-wrapper">
            <input id="empresa_nombre" type="text" name="empresa_nombre" value="{{old('empresa_nombre')}}" class="form-control @error('empresa_nombre') is-invalid @enderror" placeholder=" " />
            <i class="bi bi-building input-icon"></i>
            <label for="empresa_nombre" class="form-label">Nombre de la Empresa</label>
          </div>
          @error('empresa_nombre')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <div class="input-wrapper">
            <input id="empresa_logo" type="file" name="empresa_logo" class="form-control @error('empresa_logo') is-invalid @enderror" accept="image/*" />
            <i class="bi bi-image input-icon"></i>
            <label for="empresa_logo" class="form-label">Logo de la Empresa</label>
          </div>
          <small class="text-muted">Formatos: JPEG, PNG, JPG, GIF (máx. 2MB)</small>
          @error('empresa_logo')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>

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
        <div class="input-wrapper phone-input-wrapper">
           <input id="telefono" type="tel" name="telefono" value="{{old('telefono')}}" data-old-value="{{ old('telefono') }}" class="form-control @error('telefono') is-invalid @enderror" placeholder=" " inputmode="numeric" minlength="7" maxlength="15" title="El teléfono solo debe contener números" autocomplete="tel" />
           <i class="bi bi-telephone input-icon"></i>
           <label for="telefono" class="form-label">Teléfono</label>
        </div>
        @error('telefono')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small id="telefono-warning" class="text-danger d-none">El teléfono solo debe contener números y un país válido.</small>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-wrapper">
              <select id="pais" name="pais" class="form-control @error('pais') is-invalid @enderror">
                <option value="">Selecciona un país</option>
                <option value="Colombia" {{ old('pais') == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                <option value="Argentina" {{ old('pais') == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                <option value="Brasil" {{ old('pais') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                <option value="Chile" {{ old('pais') == 'Chile' ? 'selected' : '' }}>Chile</option>
                <option value="Ecuador" {{ old('pais') == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                <option value="Perú" {{ old('pais') == 'Perú' ? 'selected' : '' }}>Perú</option>
                <option value="Venezuela" {{ old('pais') == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                <option value="Bolivia" {{ old('pais') == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                <option value="Paraguay" {{ old('pais') == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                <option value="Uruguay" {{ old('pais') == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                <option value="México" {{ old('pais') == 'México' ? 'selected' : '' }}>México</option>
                <option value="Guatemala" {{ old('pais') == 'Guatemala' ? 'selected' : '' }}>Guatemala</option>
                <option value="Honduras" {{ old('pais') == 'Honduras' ? 'selected' : '' }}>Honduras</option>
                <option value="Costa Rica" {{ old('pais') == 'Costa Rica' ? 'selected' : '' }}>Costa Rica</option>
                <option value="Panamá" {{ old('pais') == 'Panamá' ? 'selected' : '' }}>Panamá</option>
              </select>
              <i class="bi bi-globe input-icon"></i>
              <label for="pais" class="form-label">País</label>
            </div>
            @error('pais')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-wrapper">
              <select id="ciudad" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror">
                <option value="">Selecciona una ciudad</option>
              </select>
              <i class="bi bi-building input-icon"></i>
              <label for="ciudad" class="form-label">Ciudad</label>
            </div>
            @error('ciudad')
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

  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js"></script>
  <script>
    // Ciudades por país
    const ciudadesPorPais = {
      'Colombia': ['Bogotá D.C.', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena', 'Santa Marta', 'Bucaramanga', 'Cúcuta', 'Pereira', 'Manizales', 'Armenia', 'Ibagué', 'Villavicencio', 'Pasto', 'Popayán', 'Quibdó', 'Montería', 'Valledupar'],
      'Argentina': ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata', 'San Miguel de Tucumán', 'Mar del Plata', 'Salta', 'San Juan', 'Santa Fe'],
      'Brasil': ['São Paulo', 'Rio de Janeiro', 'Brasília', 'Salvador', 'Fortaleza', 'Belo Horizonte', 'Manaus', 'Curitiba', 'Recife', 'Porto Alegre'],
      'Chile': ['Santiago', 'Valparaíso', 'Puerta Montt', 'Concepción', 'La Serena', 'Temuco', 'Valdivia', 'Puerto Varas', 'Coquimbo', 'Antofagasta'],
      'Ecuador': ['Quito', 'Guayaquil', 'Cuenca', 'Santo Domingo', 'Ambato', 'Manta', 'Portoviejo', 'Esmeraldas', 'Latacunga', 'Ibarra'],
      'Perú': ['Lima', 'Arequipa', 'Trujillo', 'Chiclayo', 'Cajamarca', 'Huancayo', 'Iquitos', 'Cusco', 'Piura', 'Tacna'],
      'Venezuela': ['Caracas', 'Maracaibo', 'Valencia', 'Barquisimeto', 'Maracay', 'Ciudad Guayana', 'Mérida', 'Puerto La Cruz', 'Coro', 'San Cristóbal'],
      'Bolivia': ['La Paz', 'Santa Cruz de la Sierra', 'Cochabamba', 'Oruro', 'Potosí', 'Sucre', 'Tarija', 'Trinidad', 'Riberalta', 'Guanay'],
      'Paraguay': ['Asunción', 'Ciudad del Este', 'Encarnación', 'Villarrica', 'Concepción', 'Coronel Oviedo', 'Pedro Juan Caballero', 'Salto del Guairá', 'Caaguazú', 'Caazapá'],
      'Uruguay': ['Montevideo', 'Salto', 'Paysandú', 'Ciudad de la Costa', 'Maldonado', 'Minas', 'Rivera', 'Tacuarembó', 'Treinta y Tres', 'Young'],
      'México': ['Ciudad de México', 'Guadalajara', 'Monterrey', 'Cancún', 'Playa del Carmen', 'Mérida', 'Veracruz', 'Puebla', 'León', 'Querétaro'],
      'Guatemala': ['Ciudad de Guatemala', 'Chichicastenango', 'Antigua', 'Quetzaltenango', 'Huehuetenango', 'Cobán', 'Retalhuleu', 'Escuintla', 'Chiquimula', 'Petén'],
      'Honduras': ['Tegucigalpa', 'San Pedro Sula', 'La Ceiba', 'Choloma', 'Comayagua', 'Puerto Cortés', 'Juticalpa', 'Siguatepeque', 'El Progreso', 'Cortés'],
      'Costa Rica': ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Limón', 'Puntarenas', 'San Isidro de El General', 'Liberia', 'Monteverde', 'Puerto Viejo'],
      'Panamá': ['Panamá', 'San Miguelito', 'Colón', 'David', 'Chitré', 'Santiago', 'Las Tablas', 'Antón', 'Aguadulce', 'Penonomé']
    };

    // Handle país and ciudad selects
    const paisSelect = document.getElementById('pais');
    const ciudadSelect = document.getElementById('ciudad');

    if (paisSelect && ciudadSelect) {
      const updateCiudades = () => {
        const paisSeleccionado = paisSelect.value;
        ciudadSelect.innerHTML = '<option value="">Selecciona una ciudad</option>';

        if (paisSeleccionado && ciudadesPorPais[paisSeleccionado]) {
          ciudadesPorPais[paisSeleccionado].forEach(ciudad => {
            const option = document.createElement('option');
            option.value = ciudad;
            option.textContent = ciudad;
            if (ciudad === "{{ old('ciudad') }}") {
              option.selected = true;
            }
            ciudadSelect.appendChild(option);
          });
        }
      };

      paisSelect.addEventListener('change', updateCiudades);
      
      // Initialize ciudades on page load if there's a selected pais
      if (paisSelect.value) {
        updateCiudades();
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const telefonoInput = document.getElementById('telefono');
      const telefonoWarning = document.getElementById('telefono-warning');
      const telefonoLabel = document.querySelector('label[for="telefono"]');
      const form = telefonoInput ? telefonoInput.closest('form') : null;
      let iti = null;

      // Handle label floating on focus
      if (telefonoInput && telefonoLabel) {
        const floatLabel = () => {
          telefonoLabel.style.top = '-8px';
          telefonoLabel.style.fontSize = '12px';
          telefonoLabel.style.color = '#0b63d6';
        };

        const unfloatLabel = () => {
          if (telefonoInput.value.trim() === '') {
            telefonoLabel.style.top = '14px';
            telefonoLabel.style.fontSize = '14px';
            telefonoLabel.style.color = '#6c757d';
          }
        };

        telefonoInput.addEventListener('focus', floatLabel);
        telefonoInput.addEventListener('blur', unfloatLabel);
      }

      if (telefonoInput && window.intlTelInput) {
        iti = window.intlTelInput(telefonoInput, {
          initialCountry: 'co',
          preferredCountries: ['co', 'us', 'mx', 'es', 'ar', 'cl', 'pe'],
          separateDialCode: true,
          nationalMode: true,
          autoPlaceholder: 'polite',
          utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js',
        });

        const oldValue = (telefonoInput.dataset.oldValue || '').replace(/\D/g, '');
        if (oldValue) {
          iti.setNumber('+' + oldValue);
        } else {
          iti.setCountry('co');
        }
      }

      if (telefonoInput && telefonoWarning) {
        const validarTelefono = () => {
          const valor = telefonoInput.value;
          const contieneLetras = /[A-Za-z]/.test(valor);
          const hayValor = valor.trim() !== '';
          const numeroCompleto = iti ? iti.getNumber() : valor;
          const digitos = numeroCompleto.replace(/\D/g, '');

          if (contieneLetras) {
            telefonoInput.setCustomValidity('El teléfono solo debe contener números.');
            telefonoWarning.classList.remove('d-none');
            return;
          }

          if (hayValor && iti && !iti.isValidNumber()) {
            telefonoInput.setCustomValidity('Ingresa un número de teléfono válido para el país seleccionado.');
            telefonoWarning.classList.remove('d-none');
            return;
          }

          if (hayValor && (digitos.length < 7 || digitos.length > 15)) {
            telefonoInput.setCustomValidity('El teléfono debe tener entre 7 y 15 dígitos.');
            telefonoWarning.classList.remove('d-none');
          } else {
            telefonoInput.setCustomValidity('');
            telefonoWarning.classList.add('d-none');
          }
        };

        telefonoInput.addEventListener('input', validarTelefono);
        telefonoInput.addEventListener('blur', validarTelefono);
      }

      if (form && telefonoInput) {
        form.addEventListener('submit', function (event) {
          const valor = telefonoInput.value.trim();

          if (valor === '') {
            telefonoInput.value = '';
            return;
          }

          if (iti) {
            const numeroCompleto = iti.getNumber();
            telefonoInput.value = numeroCompleto.replace(/\D/g, '');
          } else {
            telefonoInput.value = valor.replace(/\D/g, '');
          }

          if (!telefonoInput.value) {
            telefonoInput.setCustomValidity('El teléfono solo debe contener números.');
            telefonoWarning.classList.remove('d-none');
            event.preventDefault();
            telefonoInput.reportValidity();
          }
        });
      }

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

      // Mostrar/ocultar campos de vendedor
      const tipoUsuarioSelect = document.getElementById('tipo_usuario');
      const vendedorFields = document.getElementById('vendedor-fields');

      if (tipoUsuarioSelect && vendedorFields) {
        const toggleVendedorFields = () => {
          if (tipoUsuarioSelect.value === 'vendedor') {
            vendedorFields.classList.remove('d-none');
            document.getElementById('empresa_nombre').setAttribute('required', 'required');
            document.getElementById('empresa_logo').setAttribute('required', 'required');
          } else {
            vendedorFields.classList.add('d-none');
            document.getElementById('empresa_nombre').removeAttribute('required');
            document.getElementById('empresa_logo').removeAttribute('required');
          }
        };

        tipoUsuarioSelect.addEventListener('change', toggleVendedorFields);
        
        // Inicializar al cargar
        toggleVendedorFields();
      }
    });
  </script>
@endsection

      