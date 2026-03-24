@extends('plantilla.app')
@section('contenido')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css">
<style>
    .iti { width: 100%; }
    .iti .form-control { padding-left: 96px; }
</style>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row"> 
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Perfil del usuario</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if (session('mensaje'))
                            <div class="alert alert-success">
                                {{ session('mensaje') }}
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="border rounded p-3 bg-light">
                                    <h5 class="mb-3">Foto de perfil</h5>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        @php
                                            $avatar = $registro->avatar ?? null;
                                            $avatarSrc = $avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($registro->name ?? 'Usuario') . '&background=e6f9ff&color=0b63d6&size=128';
                                        @endphp
                                        <img src="{{ $avatarSrc }}" alt="Avatar" class="rounded-circle" style="width:90px;height:90px;object-fit:cover;border:2px solid rgba(11,99,214,0.2);">

                                        <form action="{{ route('perfil.avatar.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2 flex-wrap" id="avatar-upload-form">
                                            @csrf
                                            <input type="file" id="avatar-input" name="avatar" class="d-none" accept="image/*" required onchange="if(this.files.length){ document.getElementById('avatar-upload-form').submit(); }">
                                            <label for="avatar-input" class="btn btn-outline-primary mb-0">Actualizar foto</label>
                                        </form>
                                    </div>
                                    @error('avatar')
                                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('perfil.update')}}" method="POST" id="formRegistroUsuario">
                            @csrf
                            @method('PUT')                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombres</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                     id="name" name="name" value="{{old('name', $registro->name ??'')}}" required>
                                     @error('name')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email" value="{{old('email',  $registro->email ??'')}}" required>
                                     @error('email')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                                <input type="tel" class="form-control @error('telefono') is-invalid @enderror"
                                                                 id="telefono" name="telefono" value="{{old('telefono', $registro->telefono ?? '')}}" data-old-value="{{ old('telefono', $registro->telefono ?? '') }}" inputmode="numeric" minlength="7" maxlength="15" title="El teléfono solo debe contener números" autocomplete="tel" required>
                                     @error('telefono')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                                 <small id="telefono-warning" class="text-danger d-none">El teléfono solo debe contener números y un país válido.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ciudad" class="form-label">País</label>
                                    <select class="form-select @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" required>
                                        <option value="">Selecciona un país</option>
                                        <option value="Colombia" {{ old('ciudad', $registro->ciudad ?? '') === 'Colombia' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                                        <option value="Argentina" {{ old('ciudad', $registro->ciudad ?? '') === 'Argentina' ? 'selected' : '' }}>🇦🇷 Argentina</option>
                                        <option value="Brasil" {{ old('ciudad', $registro->ciudad ?? '') === 'Brasil' ? 'selected' : '' }}>🇧🇷 Brasil</option>
                                        <option value="Chile" {{ old('ciudad', $registro->ciudad ?? '') === 'Chile' ? 'selected' : '' }}>🇨🇱 Chile</option>
                                        <option value="Ecuador" {{ old('ciudad', $registro->ciudad ?? '') === 'Ecuador' ? 'selected' : '' }}>🇪🇨 Ecuador</option>
                                        <option value="Perú" {{ old('ciudad', $registro->ciudad ?? '') === 'Perú' ? 'selected' : '' }}>🇵🇪 Perú</option>
                                        <option value="Venezuela" {{ old('ciudad', $registro->ciudad ?? '') === 'Venezuela' ? 'selected' : '' }}>🇻🇪 Venezuela</option>
                                        <option value="México" {{ old('ciudad', $registro->ciudad ?? '') === 'México' ? 'selected' : '' }}>🇲🇽 México</option>
                                        <option value="Costa Rica" {{ old('ciudad', $registro->ciudad ?? '') === 'Costa Rica' ? 'selected' : '' }}>🇨🇷 Costa Rica</option>
                                        <option value="Panamá" {{ old('ciudad', $registro->ciudad ?? '') === 'Panamá' ? 'selected' : '' }}>🇵🇦 Panamá</option>
                                        <option value="Uruguay" {{ old('ciudad', $registro->ciudad ?? '') === 'Uruguay' ? 'selected' : '' }}>🇺🇾 Uruguay</option>
                                        <option value="Paraguay" {{ old('ciudad', $registro->ciudad ?? '') === 'Paraguay' ? 'selected' : '' }}>🇵🇾 Paraguay</option>
                                        <option value="Bolivia" {{ old('ciudad', $registro->ciudad ?? '') === 'Bolivia' ? 'selected' : '' }}>🇧🇴 Bolivia</option>
                                        <option value="Guatemala" {{ old('ciudad', $registro->ciudad ?? '') === 'Guatemala' ? 'selected' : '' }}>🇬🇹 Guatemala</option>
                                        <option value="Honduras" {{ old('ciudad', $registro->ciudad ?? '') === 'Honduras' ? 'selected' : '' }}>🇭🇳 Honduras</option>
                                    </select>
                                     @error('ciudad')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="departamento" class="form-label">Departamento / Estado</label>
                                    <select class="form-select @error('departamento') is-invalid @enderror" id="departamento" name="departamento" required>
                                        <option value="">Selecciona un departamento/estado</option>
                                    </select>
                                     @error('departamento')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="municipio" class="form-label">Ciudad/Municipio</label>
                                    <select class="form-select @error('municipio') is-invalid @enderror" id="municipio" name="municipio" required>
                                        <option value="">Selecciona una ciudad</option>
                                    </select>
                                     @error('municipio')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" rows="3" required>{{old('direccion', $registro->direccion ?? '')}}</textarea>
                                    @error('direccion')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                                     id="password" name="password" value="{{old('password')}}">
                                     @error('password')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirme el password</label>
                                    <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror"
                                     id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}">
                                     @error('password_confirmation')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary me-md-2"
                                    onclick="window.location.href='{{route('dashboard')}}'">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar datos</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">

                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js"></script>
<script>
    const colombiaDataUrl = "{{ asset('data/colombia.min.json') }}";

    // Fallback de ciudades por país (cuando no exista mapa por departamento)
    const ciudadesPorPais = {
        'Colombia': ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena', 'Santa Marta', 'Cúcuta', 'Bucaramanga', 'Pereira', 'Manizales', 'Huancayo'],
        'Argentina': ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata', 'Mar del Plata', 'Tucumán', 'Salta', 'Corrientes', 'Misiones'],
        'Brasil': ['São Paulo', 'Rio de Janeiro', 'Brasília', 'Salvador', 'Fortaleza', 'Belo Horizonte', 'Manaus', 'Recife', 'Porto Alegre', 'Curitiba'],
        'Chile': ['Santiago', 'Valparaíso', 'Concepción', 'La Serena', 'Temuco', 'Valdivia', 'Puerto Montt', 'Antofagasta', 'Iquique', 'Punta Arenas'],
        'Ecuador': ['Quito', 'Guayaquil', 'Cuenca', 'Santo Domingo', 'Ambato', 'Machala', 'Portoviejo', 'Manta', 'Ibarra', 'Latacunga'],
        'Perú': ['Lima', 'Arequipa', 'Trujillo', 'Chiclayo', 'Iquitos', 'Cusco', 'Ayacucho', 'Huancayo', 'Tacna', 'Puno'],
        'Venezuela': ['Caracas', 'Maracaibo', 'Valencia', 'Barquisimeto', 'Ciudad Guayana', 'Mérida', 'Matanzas', 'Barcelona', 'Margarita', 'Puerto La Cruz'],
        'México': ['Ciudad de México', 'Guadalajara', 'Monterrey', 'Puebla', 'Cancún', 'Acapulco', 'Veracruz', 'Mérida', 'Querétaro', 'Toluca'],
        'Costa Rica': ['San José', 'San Pedro', 'Alajuela', 'Cartago', 'Puntarenas', 'Limón', 'Heredia', 'Liberia', 'La Fortuna', 'Puerto Viejo'],
        'Panamá': ['Panamá', 'San Miguelito', 'Colón', 'La Chorrera', 'Panamá Viejo', 'San Blas', 'Chitré', 'David', 'Bocas del Toro', 'Boquete'],
        'Uruguay': ['Montevideo', 'Salto', 'Paysandú', 'Minas', 'Rivera', 'Tacuarembó', 'Melo', 'Rocha', 'Mercedes', 'Soriano'],
        'Paraguay': ['Asunción', 'Ciudad del Este', 'San Juan Bautista', 'Encarnación', 'Villarrica', 'Coronel Oviedo', 'Caaguazú', 'Caazapá', 'Pedro Juan Caballero', 'Salto del Guairá'],
        'Bolivia': ['La Paz', 'Santa Cruz', 'Cochabamba', 'Sucre', 'Oruro', 'Potosí', 'Tarija', 'Trinidad', 'Riberalta', 'Guayaramerín'],
        'Guatemala': ['Ciudad de Guatemala', 'Mixco', 'Villa Nueva', 'Puerto Barrios', 'Cobán', 'Antigua Guatemala', 'Chichicastenango', 'Quetzaltenango', 'Retalhuleu', 'Huehuetenango'],
        'Honduras': ['Tegucigalpa', 'San Pedro Sula', 'La Ceiba', 'Comayagüela', 'Choloma', 'Cortés', 'Choluteca', 'OlanChO', 'Danlí', 'Juticalpa']
    };

    // Mapa por departamento para Colombia (se rellena dinámicamente desde /public/data/colombia.min.json)
    const ciudadesPorDepartamentoColombia = {};

    const departamentosPorPais = {
        'Colombia': ['Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar', 'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca', 'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía', 'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta', 'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda', 'San Andrés y Providencia', 'Santander', 'Sucre', 'Tolima', 'Valle del Cauca', 'Vaupés', 'Vichada', 'Bogotá D.C.'],
        'Argentina': ['Buenos Aires', 'Catamarca', 'Chaco', 'Chubut', 'Córdoba', 'Corrientes', 'Entre Ríos', 'Formosa', 'Jujuy', 'La Pampa', 'La Rioja', 'Mendoza', 'Misiones', 'Neuquén', 'Río Negro', 'Salta', 'San Juan', 'San Luis', 'Santa Cruz', 'Santa Fe', 'Santiago del Estero', 'Tierra del Fuego', 'Tucumán'],
        'Brasil': ['Acre', 'Alagoas', 'Amapá', 'Amazonas', 'Bahía', 'Ceará', 'Distrito Federal', 'Espírito Santo', 'Goiás', 'Maranhão', 'Mato Grosso', 'Mato Grosso do Sul', 'Minas Gerais', 'Pará', 'Paraíba', 'Paraná', 'Pernambuco', 'Piauí', 'Rio de Janeiro', 'Rio Grande do Norte', 'Rio Grande do Sul', 'Rondônia', 'Roraima', 'Santa Catarina', 'São Paulo', 'Sergipe', 'Tocantins'],
        'Chile': ['Arica y Parinacota', 'Tarapacá', 'Antofagasta', 'Atacama', 'Coquimbo', 'Valparaíso', 'Metropolitana', 'O’Higgins', 'Maule', 'Ñuble', 'Biobío', 'La Araucanía', 'Los Ríos', 'Los Lagos', 'Aysén', 'Magallanes'],
        'Ecuador': ['Azuay', 'Bolívar', 'Cañar', 'Carchi', 'Chimborazo', 'Cotopaxi', 'El Oro', 'Esmeraldas', 'Galápagos', 'Guayas', 'Imbabura', 'Loja', 'Los Ríos', 'Manabí', 'Morona Santiago', 'Napo', 'Orellana', 'Pastaza', 'Pichincha', 'Santa Elena', 'Santo Domingo de los Tsáchilas', 'Sucumbíos', 'Tungurahua', 'Zamora Chinchipe'],
        'Perú': ['Amazonas', 'Áncash', 'Apurímac', 'Arequipa', 'Ayacucho', 'Cajamarca', 'Callao', 'Cusco', 'Huancavelica', 'Huánuco', 'Ica', 'Junín', 'La Libertad', 'Lambayeque', 'Lima', 'Loreto', 'Madre de Dios', 'Moquegua', 'Pasco', 'Piura', 'Puno', 'San Martín', 'Tacna', 'Tumbes', 'Ucayali'],
        'Venezuela': ['Amazonas', 'Anzoátegui', 'Apure', 'Aragua', 'Barinas', 'Bolívar', 'Carabobo', 'Cojedes', 'Delta Amacuro', 'Falcón', 'Guárico', 'Lara', 'Mérida', 'Miranda', 'Monagas', 'Nueva Esparta', 'Portuguesa', 'Sucre', 'Táchira', 'Trujillo', 'Yaracuy', 'Zulia', 'Distrito Capital'],
        'México': ['Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas', 'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Estado de México', 'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco', 'Michoacán', 'Morelos', 'Nayarit', 'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí', 'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas'],
        'Costa Rica': ['San José', 'Alajuela', 'Cartago', 'Heredia', 'Guanacaste', 'Puntarenas', 'Limón'],
        'Panamá': ['Bocas del Toro', 'Coclé', 'Colón', 'Chiriquí', 'Darién', 'Herrera', 'Los Santos', 'Panamá', 'Veraguas', 'Panamá Oeste'],
        'Uruguay': ['Artigas', 'Canelones', 'Cerro Largo', 'Colonia', 'Durazno', 'Flores', 'Florida', 'Lavalleja', 'Maldonado', 'Montevideo', 'Paysandú', 'Río Negro', 'Rivera', 'Rocha', 'Salto', 'San José', 'Soriano', 'Tacuarembó', 'Treinta y Tres'],
        'Paraguay': ['Asunción', 'Concepción', 'San Pedro', 'Cordillera', 'Guairá', 'Caaguazú', 'Caazapá', 'Itapúa', 'Misiones', 'Paraguarí', 'Alto Paraná', 'Central', 'Ñeembucú', 'Amambay', 'Canindeyú', 'Presidente Hayes', 'Boquerón', 'Alto Paraguay'],
        'Bolivia': ['La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro', 'Potosí', 'Chuquisaca', 'Tarija', 'Beni', 'Pando'],
        'Guatemala': ['Alta Verapaz', 'Baja Verapaz', 'Chimaltenango', 'Chiquimula', 'El Progreso', 'Escuintla', 'Guatemala', 'Huehuetenango', 'Izabal', 'Jalapa', 'Jutiapa', 'Petén', 'Quetzaltenango', 'Quiché', 'Retalhuleu', 'Sacatepéquez', 'San Marcos', 'Santa Rosa', 'Sololá', 'Suchitepéquez', 'Totonicapán', 'Zacapa'],
        'Honduras': ['Atlántida', 'Choluteca', 'Colón', 'Comayagua', 'Copán', 'Cortés', 'El Paraíso', 'Francisco Morazán', 'Gracias a Dios', 'Intibucá', 'Islas de la Bahía', 'La Paz', 'Lempira', 'Ocotepeque', 'Olancho', 'Santa Bárbara', 'Valle', 'Yoro']
    };

    // Elementos select
    const municipioSelect = document.getElementById('municipio');
    const ciudadSelect = document.getElementById('ciudad');
    const departamentoSelect = document.getElementById('departamento');

    function setSelectOptions(selectElement, items, placeholder, selectedValue = '') {
        if (!selectElement) return;
        selectElement.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.text = placeholder;
        selectElement.appendChild(defaultOption);

        (items || []).forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.text = item;
            if (selectedValue && selectedValue === item) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
    }

    function actualizarCiudades(selectedMunicipio = '') {
        const pais = ciudadSelect.value;
        const departamento = departamentoSelect.value;

        if (!pais || !departamento) {
            setSelectOptions(municipioSelect, [], 'Selecciona una ciudad', '');
            return;
        }

        let ciudades = [];
        if (pais === 'Colombia' && ciudadesPorDepartamentoColombia[departamento]) {
            ciudades = ciudadesPorDepartamentoColombia[departamento];
        } else {
            ciudades = ciudadesPorPais[pais] || [];
        }

        setSelectOptions(municipioSelect, ciudades, 'Selecciona una ciudad', selectedMunicipio);
    }

    function actualizarDepartamentos() {
        const pais = ciudadSelect.value;
        const departamentos = departamentosPorPais[pais] || [];
        const departamentoActual = '{{ old("departamento", $registro->departamento ?? "") }}';
        setSelectOptions(departamentoSelect, departamentos, 'Selecciona un departamento/estado', departamentoActual);

        const municipioActual = '{{ old("municipio", $registro->municipio ?? "") }}';
        actualizarCiudades(municipioActual);
    }

    // Event listener para cambios de país
    ciudadSelect.addEventListener('change', function () {
        actualizarDepartamentos();
    });

    departamentoSelect.addEventListener('change', function () {
        actualizarCiudades('');
    });

    async function loadColombiaData() {
        try {
            const response = await fetch(colombiaDataUrl, { cache: 'no-store' });
            if (!response.ok) return;
            const data = await response.json();
            const map = {};

            (data || []).forEach(item => {
                if (item?.departamento && Array.isArray(item?.ciudades)) {
                    map[item.departamento] = item.ciudades;
                }
            });

            if (Object.keys(map).length > 0) {
                Object.keys(ciudadesPorDepartamentoColombia).forEach(key => delete ciudadesPorDepartamentoColombia[key]);
                Object.assign(ciudadesPorDepartamentoColombia, map);
            }
        } catch (error) {
            console.warn('No se pudo cargar data de Colombia para ciudades por departamento.', error);
        }
    }

    // Inicializar ciudades cuando carga la página
    document.addEventListener('DOMContentLoaded', function () {
        loadColombiaData().finally(() => {
            actualizarDepartamentos();
        });

        const telefonoInput = document.getElementById('telefono');
        const telefonoWarning = document.getElementById('telefono-warning');
        const form = document.getElementById('formRegistroUsuario');
        let iti = null;

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
                iti.setCountry('co');
                telefonoInput.value = oldValue;
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
    });
</script>
@endsection