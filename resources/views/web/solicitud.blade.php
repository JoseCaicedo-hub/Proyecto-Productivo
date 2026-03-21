@extends('web.app')

@section('contenido')
<section class="py-5">
  <div class="container px-4 px-lg-6">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card p-4">
          <h2 class="mb-3">Enviar solicitud de emprendimiento</h2>
          <p class="small text-muted">Cuéntanos tu idea y tus datos. Nuestro equipo la revisará y te contactaremos.</p>
          @php
            $solicitudPendiente = null;
            $esVendedor = auth()->check() && auth()->user()->hasRole('vendedor');
            if (auth()->check()) {
              $solicitudPendiente = \App\Models\Solicitud::where('user_id', auth()->id())
                ->where('estado', 'pendiente')
                ->latest()
                ->first();
            }
          @endphp

          @if(session('mensaje'))
            <div class="alert alert-success">{{ session('mensaje') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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

          @if($esVendedor)
            <div class="alert alert-success mb-0">
              Tu solicitud ya fue <strong>aprobada</strong> y actualmente tienes el rol de vendedor.
            </div>
          @elseif($solicitudPendiente)
            <div class="alert alert-warning mb-0">
              Ya enviaste una solicitud y está en estado <strong>pendiente</strong>. Debes esperar la respuesta del administrador para volver a enviar otra.
            </div>
          @else

          <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h5 class="mt-2 mb-3">Datos personales</h5>
            <div class="row g-3 mb-2">
              <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input name="nombre" id="nombre" class="form-control" value="{{ auth()->user()->name ?? old('nombre') }}" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email ?? old('email') }}" required>
              </div>
            </div>

            <hr>
            <h5 class="mt-3 mb-3">Información del emprendimiento</h5>
            <div class="row g-3 mb-2">
              <div class="col-md-6">
                <label for="nombre_emprendimiento" class="form-label">Nombre del emprendimiento / empresa</label>
                <input name="nombre_emprendimiento" id="nombre_emprendimiento" class="form-control" value="{{ old('nombre_emprendimiento') }}" required>
              </div>
              <div class="col-md-6">
                <label for="tipo_negocio" class="form-label">Tipo de negocio</label>
                <input name="tipo_negocio" id="tipo_negocio" class="form-control" placeholder="Ej: tienda, servicios, digital" value="{{ old('tipo_negocio') }}">
              </div>
              <div class="col-md-6">
                <label for="categoria_negocio" class="form-label">Categoría del negocio (opcional)</label>
                <select name="categoria_negocio" id="categoria_negocio" class="form-select">
                  <option value="">Selecciona una categoría</option>
                  @foreach(['Alimentos','Moda','Tecnología','Hogar','Belleza','Servicios','Educación','Salud','Digital','Otro'] as $categoria)
                    <option value="{{ $categoria }}" {{ old('categoria_negocio') === $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="empresa_registrada_legalmente" class="form-label">¿Tu empresa ya está registrada legalmente?</label>
                <select name="empresa_registrada_legalmente" id="empresa_registrada_legalmente" class="form-select">
                  <option value="">Selecciona una opción</option>
                  <option value="si" {{ old('empresa_registrada_legalmente') === 'si' ? 'selected' : '' }}>Sí</option>
                  <option value="no" {{ old('empresa_registrada_legalmente') === 'no' ? 'selected' : '' }}>No</option>
                </select>
              </div>
            </div>

            <hr>
            <h5 class="mt-3 mb-3">Información comercial</h5>
            <div class="mb-3">
              <label for="productos_servicios" class="form-label">¿Qué productos o servicios ofrece?</label>
              <textarea name="productos_servicios" id="productos_servicios" rows="5" class="form-control" required>{{ old('productos_servicios') }}</textarea>
            </div>
            <div class="mb-3">
              <label for="publico_objetivo" class="form-label">Público objetivo (opcional)</label>
              <input name="publico_objetivo" id="publico_objetivo" class="form-control" value="{{ old('publico_objetivo') }}">
            </div>
            <div class="mb-3">
              <label for="diferenciador" class="form-label">Diferenciador (¿qué lo hace único?)</label>
              <textarea name="diferenciador" id="diferenciador" rows="4" class="form-control">{{ old('diferenciador') }}</textarea>
            </div>

            <hr>
            <h5 class="mt-3 mb-3">Información adicional</h5>
            <div class="row g-3 mb-2">
              <div class="col-md-4">
                <label for="pais" class="form-label">País</label>
                @php
                  $paises = ['Colombia','Argentina','Brasil','Chile','Ecuador','Perú','Venezuela','México','Costa Rica','Panamá','Uruguay','Paraguay','Bolivia','Guatemala','Honduras'];
                @endphp
                <select name="pais" id="pais" class="form-select" required>
                  <option value="">Selecciona un país</option>
                  @foreach($paises as $pais)
                    <option value="{{ $pais }}" {{ old('pais') === $pais ? 'selected' : '' }}>{{ $pais }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label for="departamento" class="form-label">Departamento / Estado</label>
                <select name="departamento" id="departamento" class="form-select" required>
                  <option value="">Selecciona un departamento/estado</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="ciudad" class="form-label">Ciudad</label>
                <select name="ciudad" id="ciudad" class="form-select" required>
                  <option value="">Selecciona una ciudad</option>
                </select>
              </div>
            </div>
            <div class="row g-3 mb-2">
              <div class="col-md-12">
                <label for="direccion" class="form-label">Dirección (opcional)</label>
                <input name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}">
              </div>
            </div>

            <hr>
            <h5 class="mt-3 mb-3">Contacto del emprendimiento</h5>
            <div class="row g-3 mb-2">
              <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input name="telefono" id="telefono" class="form-control" placeholder="Ej: 3001234567" value="{{ old('telefono') }}" inputmode="numeric" required>
                <small id="telefonoFeedback" class="text-danger d-none"></small>
                <small id="telefonoStatus" class="d-block mt-1 text-muted">Inválido</small>
              </div>
              <div class="col-md-6">
                <label for="redes_sociales_web" class="form-label">Redes sociales o página web (opcional)</label>
                <input name="redes_sociales_web" id="redes_sociales_web" class="form-control" placeholder="Ej: https://instagram.com/tuemprendimiento" value="{{ old('redes_sociales_web') }}">
              </div>
            </div>

            <hr>
            <h5 class="mt-3 mb-3">Documentos de soporte</h5>

            <div class="mb-3">
              <label for="producto_img" class="form-label">Imagen representativa del producto (obligatorio)</label>
              <input type="file" name="producto_img" id="producto_img" accept="image/*" class="form-control @error('producto_img') is-invalid @enderror" required>
              <small class="form-text text-muted">JPG, PNG. Máx. recomendado 2MB.</small>
              @error('producto_img')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <div class="mt-2">
                <img id="previewImage" src="#" alt="Vista previa" style="max-width:220px; max-height:160px; display:none; object-fit:cover; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
              </div>
            </div>

            <div class="mb-3">
              <label for="carta" class="form-label">Carta de intención / por qué iniciar con nosotros (PDF o DOC) <span class="text-muted small">(obligatorio)</span></label>
              <input type="file" name="carta" id="carta" accept=".pdf,.doc,.docx" class="form-control @error('carta') is-invalid @enderror" required>
              @error('carta')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small id="cartaName" class="form-text text-muted">No se ha seleccionado ningún archivo.</small>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmarEnvioSolicitudModal">Enviar solicitud</button>
              <a class="btn btn-outline-secondary" href="{{ route('web.equipo') }}">Volver</a>
            </div>

            <div class="modal fade" id="confirmarEnvioSolicitudModal" tabindex="-1" aria-labelledby="confirmarEnvioSolicitudLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="confirmarEnvioSolicitudLabel">¿Estás seguro de enviar?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    Después de enviar esta solicitud no podrás enviar otra hasta recibir respuesta del administrador.
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Sí, enviar solicitud</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  (function(){
    // Preview de imagen seleccionada
    var inputImg = document.getElementById('producto_img');
    var preview = document.getElementById('previewImage');
    if(inputImg){
      inputImg.addEventListener('change', function(e){
        var file = e.target.files && e.target.files[0];
        if(file && file.type.startsWith('image/')){
          var reader = new FileReader();
          reader.onload = function(ev){
            preview.src = ev.target.result;
            preview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          preview.src = '#';
        }
      });
    }

    // Mostrar nombre del archivo de la carta
    var cartaInput = document.getElementById('carta');
    var cartaName = document.getElementById('cartaName');
    if(cartaInput && cartaName){
      cartaInput.addEventListener('change', function(e){
        var file = e.target.files && e.target.files[0];
        cartaName.textContent = file ? (file.name + ' (' + Math.round(file.size/1024) + ' KB)') : 'No se ha seleccionado ningún archivo.';
      });
    }

    var paisInput = document.getElementById('pais');
    var departamentoInput = document.getElementById('departamento');
    var ciudadInput = document.getElementById('ciudad');
    var telefonoInput = document.getElementById('telefono');
    var telefonoFeedback = document.getElementById('telefonoFeedback');
    var telefonoStatus = document.getElementById('telefonoStatus');

    var ubicacionesPorPais = {
      'Colombia': {
        'Antioquia': ['Medellín', 'Bello', 'Envigado', 'Itagüí', 'Rionegro'],
        'Atlántico': ['Barranquilla', 'Soledad', 'Malambo', 'Puerto Colombia'],
        'Bogotá D.C.': ['Bogotá D.C.'],
        'Bolívar': ['Cartagena', 'Magangué', 'Turbaco'],
        'Boyacá': ['Tunja', 'Duitama', 'Sogamoso'],
        'Caldas': ['Manizales', 'La Dorada'],
        'Cauca': ['Popayán', 'Santander de Quilichao'],
        'Cesar': ['Valledupar', 'Aguachica'],
        'Córdoba': ['Montería', 'Lorica'],
        'Cundinamarca': ['Soacha', 'Chía', 'Zipaquirá', 'Facatativá'],
        'Huila': ['Neiva', 'Pitalito'],
        'La Guajira': ['Riohacha', 'Maicao'],
        'Magdalena': ['Santa Marta', 'Ciénaga'],
        'Meta': ['Villavicencio', 'Acacías'],
        'Nariño': ['Pasto', 'Ipiales'],
        'Norte de Santander': ['Cúcuta', 'Ocaña'],
        'Quindío': ['Armenia', 'Calarcá'],
        'Risaralda': ['Pereira', 'Dosquebradas'],
        'Santander': ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta'],
        'Sucre': ['Sincelejo', 'Corozal'],
        'Tolima': ['Ibagué', 'Espinal'],
        'Valle del Cauca': ['Cali', 'Palmira', 'Buenaventura', 'Tuluá', 'Cartago']
      },
      'Argentina': {
        'Buenos Aires': ['Buenos Aires', 'La Plata', 'Mar del Plata'],
        'Córdoba': ['Córdoba'],
        'Santa Fe': ['Rosario', 'Santa Fe']
      },
      'Brasil': {
        'São Paulo': ['São Paulo', 'Campinas'],
        'Rio de Janeiro': ['Rio de Janeiro'],
        'Minas Gerais': ['Belo Horizonte']
      },
      'Chile': {
        'Región Metropolitana': ['Santiago'],
        'Valparaíso': ['Valparaíso', 'Viña del Mar']
      },
      'Ecuador': {
        'Pichincha': ['Quito'],
        'Guayas': ['Guayaquil'],
        'Azuay': ['Cuenca']
      },
      'Perú': {
        'Lima': ['Lima'],
        'Arequipa': ['Arequipa'],
        'La Libertad': ['Trujillo']
      },
      'Venezuela': {
        'Distrito Capital': ['Caracas'],
        'Zulia': ['Maracaibo'],
        'Carabobo': ['Valencia']
      },
      'México': {
        'Ciudad de México': ['Ciudad de México'],
        'Jalisco': ['Guadalajara'],
        'Nuevo León': ['Monterrey']
      },
      'Costa Rica': {
        'San José': ['San José'],
        'Alajuela': ['Alajuela'],
        'Heredia': ['Heredia']
      },
      'Panamá': {
        'Panamá': ['Ciudad de Panamá', 'San Miguelito'],
        'Chiriquí': ['David']
      },
      'Uruguay': {
        'Montevideo': ['Montevideo'],
        'Salto': ['Salto']
      },
      'Paraguay': {
        'Asunción': ['Asunción'],
        'Alto Paraná': ['Ciudad del Este']
      },
      'Bolivia': {
        'La Paz': ['La Paz'],
        'Santa Cruz': ['Santa Cruz de la Sierra'],
        'Cochabamba': ['Cochabamba']
      },
      'Guatemala': {
        'Guatemala': ['Ciudad de Guatemala'],
        'Quetzaltenango': ['Quetzaltenango']
      },
      'Honduras': {
        'Francisco Morazán': ['Tegucigalpa'],
        'Cortés': ['San Pedro Sula']
      }
    };
    var colombiaDataUrl = "{{ asset('data/colombia.min.json') }}";

    var oldDepartamento = @json(old('departamento', ''));
    var oldCiudad = @json(old('ciudad', ''));

    function setSelectOptions(select, options, placeholder, selected){
      if(!select) return;
      select.innerHTML = '';

      var defaultOpt = document.createElement('option');
      defaultOpt.value = '';
      defaultOpt.textContent = placeholder;
      select.appendChild(defaultOpt);

      options.forEach(function(item){
        var opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        if(selected && selected === item){
          opt.selected = true;
        }
        select.appendChild(opt);
      });
    }

    function updateDepartamentos(selectedDepartamento){
      if(!paisInput || !departamentoInput) return;
      var pais = paisInput.value || '';
      var departamentos = Object.keys(ubicacionesPorPais[pais] || {});
      setSelectOptions(departamentoInput, departamentos, 'Selecciona un departamento/estado', selectedDepartamento || '');
    }

    function updateCiudades(selectedCiudad){
      if(!paisInput || !departamentoInput || !ciudadInput) return;
      var pais = paisInput.value || '';
      var departamento = departamentoInput.value || '';
      var ciudades = (ubicacionesPorPais[pais] && ubicacionesPorPais[pais][departamento]) ? ubicacionesPorPais[pais][departamento] : [];
      setSelectOptions(ciudadInput, ciudades, 'Selecciona una ciudad', selectedCiudad || '');
    }

    function loadColombiaData(){
      return fetch(colombiaDataUrl, { cache: 'no-store' })
        .then(function(response){
          if(!response.ok) return null;
          return response.json();
        })
        .then(function(data){
          if(!Array.isArray(data)) return;

          var mapa = {};
          data.forEach(function(item){
            if(item && item.departamento && Array.isArray(item.ciudades)){
              mapa[item.departamento] = item.ciudades;
            }
          });

          if(Object.keys(mapa).length){
            ubicacionesPorPais['Colombia'] = mapa;
            if(paisInput && paisInput.value === 'Colombia'){
              updateDepartamentos(oldDepartamento || departamentoInput.value || '');
              updateCiudades(oldCiudad || ciudadInput.value || '');
            }
          }
        })
        .catch(function(){
          if (paisInput && paisInput.value === 'Colombia') {
            updateDepartamentos(oldDepartamento || departamentoInput.value || '');
            updateCiudades(oldCiudad || ciudadInput.value || '');
          }
        });
    }

    function getPhoneRule(country){
      var key = (country || '').toLowerCase();
      if(key === 'colombia') return { exact: 10 };
      return { min: 10, max: 15 };
    }

    function setPhoneState(valid, message){
      if(!telefonoInput) return;
      telefonoInput.classList.remove('is-valid', 'is-invalid');
      telefonoInput.classList.add(valid ? 'is-valid' : 'is-invalid');

      if(telefonoFeedback){
        if(valid){
          telefonoFeedback.classList.add('d-none');
          telefonoFeedback.textContent = '';
        } else {
          telefonoFeedback.classList.remove('d-none');
          telefonoFeedback.textContent = message;
        }
      }

      if(telefonoStatus){
        telefonoStatus.textContent = valid ? 'Válido' : 'Inválido';
        telefonoStatus.className = valid ? 'd-block mt-1 text-success' : 'd-block mt-1 text-danger';
      }
    }

    function validatePhone(){
      if(!telefonoInput) return true;
      var value = (telefonoInput.value || '').trim();
      var pais = paisInput ? paisInput.value : '';
      var rule = getPhoneRule(pais);

      if(!/^\d+$/.test(value)){
        setPhoneState(false, 'El teléfono solo debe contener números.');
        return false;
      }

      if(typeof rule.exact === 'number'){
        if(value.length !== rule.exact){
          setPhoneState(false, 'Para ' + pais + ' el teléfono debe tener ' + rule.exact + ' dígitos.');
          return false;
        }
        setPhoneState(true, '');
        return true;
      }

      var min = rule.min || 10;
      var max = rule.max || 15;
      if(value.length < min || value.length > max){
        setPhoneState(false, 'El teléfono debe tener entre ' + min + ' y ' + max + ' dígitos.');
        return false;
      }

      setPhoneState(true, '');
      return true;
    }

    if(paisInput){
      paisInput.addEventListener('change', function(){
        updateDepartamentos('');
        updateCiudades('');
        validatePhone();
      });
      updateDepartamentos(oldDepartamento);
      updateCiudades(oldCiudad);
      loadColombiaData();
    }

    if(departamentoInput){
      departamentoInput.addEventListener('change', function(){
        updateCiudades('');
      });
    }

    if(telefonoInput){
      telefonoInput.addEventListener('input', function(){
        this.value = this.value.replace(/\D+/g, '');
        validatePhone();
      });
      telefonoInput.addEventListener('blur', validatePhone);

      var form = telefonoInput.closest('form');
      if(form){
        form.addEventListener('submit', function(e){
          telefonoInput.value = (telefonoInput.value || '').replace(/\D+/g, '');
          if(!validatePhone()){
            e.preventDefault();
            telefonoInput.focus();
          }
        });
      }

      validatePhone();
    }
  })();
</script>
@endpush
