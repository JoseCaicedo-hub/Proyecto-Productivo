@extends('plantilla.app')
@section('contenido')
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Pedidos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <form action="{{route('productos.index')}}" method="get">
                                <div class="input-group">
                                    <input name="texto" type="text" class="form-control" value="{{$texto}}"
                                        placeholder="Ingrese texto a buscar">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i>
                                            Buscar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(Session::has('mensaje'))
                        <div class="alert alert-info alert-dismissible fade show mt-2">
                            {{Session::get('mensaje')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                        @endif
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 150px">Opciones</th>
                                        <th style="width: 20px">ID</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Detalles</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($registros) <= 0)
                                        <tr>
                                            <td colspan="7">No hay registros que coincidan con la búsqueda</td>
                                        </tr>
                                    @else
                                        @foreach($registros as $reg)
                                        <tr class="align-middle">
                                            <td>
                                                {{-- Botón amarillo (cambio de estado) solo para administradores con permiso --}}
                                                @can('pedido-anulate')
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#modal-estado-{{$reg->id}}" title="Cambiar estado"><i
                                                            class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                @endcan

                                                {{-- Botón papelera para cancelar pedido: sólo visible para el propietario cuando está pendiente --}}
                                                @if(auth()->check() && auth()->id() === $reg->user_id && $reg->estado === 'pendiente' && !$reg->detalles->contains(function($d){ return in_array($d->envio_estado, ['enviado', 'entregado']); }))
                                                    <button type="button" class="btn btn-danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modal-cancelar-{{$reg->id}}" title="Cancelar pedido">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-info btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modal-direccion-{{$reg->id}}" title="Editar dirección">
                                                        <i class="bi bi-geo-alt"></i>
                                                    </button>
                                                @endif

                                                {{-- Si el pedido está cancelado, permitir eliminarlo (propietario o admin) --}}
                                                @if($reg->estado === 'cancelado' && (auth()->check() && (auth()->id() === $reg->user_id || auth()->user()->can('pedido-anulate'))))
                                                    <button type="button" class="btn btn-outline-danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modal-eliminar-{{$reg->id}}" title="Eliminar pedido">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>{{$reg->id}}</td>
                                            <td>{{$reg->created_at->format('d/m/Y')}}</td>
                                            <td>{{$reg->user->name}}</td>
                                            <td>@formatCOP($reg->total)</td>
                                            <td>
                                                @php
                                                    $colores = [
                                                        'pendiente' => 'bg-warning',
                                                        'enviado' => 'bg-success',
                                                        'anulado' => 'bg-danger',
                                                        'cancelado' => 'bg-secondary',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $colores[$reg->estado] ?? 'bg-dark' }}">
                                                    {{ ucfirst($reg->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#detalles-{{ $reg->id }}">
                                                    Ver detalles
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary ms-1 btn-download" title="Descargar PDF" data-route="{{ route('pedido.pdf', $reg->id) }}" data-pedido-id="{{ $reg->id }}">
                                                    <i class="bi bi-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="detalles-{{ $reg->id }}">
                                            <td colspan="7">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Talla</th>
                                                            <th>Imagen</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio Unitario</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($reg->detalles as $detalle)
                                                        <tr>
                                                            <td>{{ $detalle->producto->nombre }}</td>
                                                            <td>{{ $detalle->talla ?? '-' }}</td>
                                                            <td>
                                                                <img src="{{ asset('uploads/productos/' . $detalle->producto->imagen ) }}"
                                                                    class="img-fluid rounded"
                                                                    style="width: 80px; height: 80px; object-fit: cover;"
                                                                    alt="{{ $detalle->producto->nombre}}">
                                                            </td>
                                                            <td>{{ $detalle->cantidad}}</td>
                                                            <td>@formatCOPNoSymbol($detalle->precio)</td>
                                                            <td>@formatCOPNoSymbol($detalle->cantidad * $detalle->precio)</td>
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $detalle->envio_estado === 'enviado' ? 'bg-info' : ($detalle->envio_estado === 'entregado' ? 'bg-success' : 'bg-warning') }}">{{ ucfirst($detalle->envio_estado ?? 'pendiente') }}</span>
                                                            </td>
                                                            <td>
                                                                @if(auth()->check() && auth()->id() === $reg->user_id && ($detalle->envio_estado === 'enviado'))
                                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-recibir-{{ $detalle->id }}">Recibido</button>
                                                                    <!-- Modal confirmar recibido -->
                                                                    <div class="modal fade" id="modal-recibir-{{ $detalle->id }}" tabindex="-1" aria-labelledby="modalRecibirLabel{{ $detalle->id }}" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-success text-white">
                                                                                    <h5 class="modal-title" id="modalRecibirLabel{{ $detalle->id }}">Confirmar recibido</h5>
                                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                                </div>
                                                                                <form action="{{ route('pedido.detalle.recibir', $detalle->id) }}" method="POST">
                                                                                    @csrf
                                                                                    <div class="modal-body">
                                                                                        <p>¿Confirmas que recibiste el producto <strong>{{ $detalle->producto->nombre }}</strong> (Pedido #{{ $reg->id }})?</p>
                                                                                        <p class="small text-muted">Se registrará la fecha de recepción y se actualizará el estado.</p>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                                        <button type="submit" class="btn btn-success">Confirmar recibido</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <small class="text-muted">{{ $detalle->fecha_recibido ? $detalle->fecha_recibido->format('Y-m-d H:i') : '-' }}</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        @include('pedido.state')
                                        @include('pedido.delete')
                                        @include('pedido.delete_permanent')

                                        @if(auth()->check() && auth()->id() === $reg->user_id && $reg->estado === 'pendiente' && !$reg->detalles->contains(function($d){ return in_array($d->envio_estado, ['enviado', 'entregado']); }))
                                            <div class="modal fade" id="modal-direccion-{{$reg->id}}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Editar dirección del pedido #{{$reg->id}}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <form action="{{ route('pedido.actualizar.direccion', $reg->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="mb-2">
                                                                    <label class="form-label">País</label>
                                                                    @php
                                                                        $paisesEditar = ['Colombia','Argentina','Brasil','Chile','Ecuador','Perú','Venezuela','México','Costa Rica','Panamá','Uruguay','Paraguay','Bolivia','Guatemala','Honduras'];
                                                                        $currentPais = old('pais', $reg->pais ?? '');
                                                                        $currentDepartamento = old('departamento', $reg->departamento ?? '');
                                                                        $currentCiudad = old('ciudad', $reg->ciudad ?? '');
                                                                    @endphp
                                                                    <select
                                                                        name="pais"
                                                                        id="pais_{{ $reg->id }}"
                                                                        class="form-select js-pais-edit"
                                                                        data-pedido-id="{{ $reg->id }}"
                                                                        data-current-pais="{{ $currentPais }}"
                                                                        data-current-departamento="{{ $currentDepartamento }}"
                                                                        data-current-ciudad="{{ $currentCiudad }}"
                                                                        required
                                                                    >
                                                                        <option value="">Selecciona un país</option>
                                                                        @foreach($paisesEditar as $pais)
                                                                            <option value="{{ $pais }}" {{ $currentPais === $pais ? 'selected' : '' }}>{{ $pais }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Departamento / Estado</label>
                                                                    <select
                                                                        name="departamento"
                                                                        id="departamento_{{ $reg->id }}"
                                                                        class="form-select js-departamento-edit"
                                                                        data-pedido-id="{{ $reg->id }}"
                                                                        required
                                                                    >
                                                                        <option value="">Selecciona un departamento/estado</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Municipio / Ciudad</label>
                                                                    <select
                                                                        name="ciudad"
                                                                        id="ciudad_{{ $reg->id }}"
                                                                        class="form-select js-ciudad-edit"
                                                                        data-pedido-id="{{ $reg->id }}"
                                                                        required
                                                                    >
                                                                        <option value="">Selecciona un municipio/ciudad</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Dirección completa</label>
                                                                    <textarea name="direccion" class="form-control" rows="3" required>{{ old('direccion', $reg->direccion ?? '') }}</textarea>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Referencia</label>
                                                                    <input type="text" name="referencia" class="form-control" value="{{ old('referencia', $reg->referencia ?? '') }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <!-- Modal de confirmación de descarga -->
                            <div class="modal fade" id="confirmDownloadModal" tabindex="-1" aria-labelledby="confirmDownloadLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDownloadLabel">Descargar factura electrónica</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Estás a punto de descargar la factura electrónica del pedido <strong id="modalPedidoId">#</strong>.</p>
                                            <p class="small text-muted">Se generará un archivo PDF con los detalles de la compra. ¿Deseas continuar?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary" id="confirmDownloadBtn">Descargar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{$registros->appends(["texto"=>$texto])}}
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
@endsection
@push('scripts')
<script>
document.getElementById('mnuPedidos').classList.add('active');

document.addEventListener('DOMContentLoaded', function(){
    var confirmModalEl = document.getElementById('confirmDownloadModal');
    var confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    var selectedRoute = null;
    var selectedPedidoId = null;

    document.querySelectorAll('.btn-download').forEach(function(btn){
        btn.addEventListener('click', function(){
            selectedRoute = btn.getAttribute('data-route');
            selectedPedidoId = btn.getAttribute('data-pedido-id');
            var modalIdEl = document.getElementById('modalPedidoId');
            if (modalIdEl) modalIdEl.textContent = '#' + selectedPedidoId;
            if (confirmModal) confirmModal.show();
        });
    });

    var confirmBtn = document.getElementById('confirmDownloadBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(){
            if (selectedRoute) {
                window.open(selectedRoute, '_blank');
                if (confirmModal) confirmModal.hide();
            }
        });
    }

    const colombiaDataUrl = "{{ asset('data/colombia.min.json') }}";
    const locationData = {
        'Colombia': {},
        'Argentina': {'Buenos Aires': ['Buenos Aires', 'La Plata'], 'Córdoba': ['Córdoba']},
        'Brasil': {'São Paulo': ['São Paulo'], 'Rio de Janeiro': ['Rio de Janeiro']},
        'Chile': {'Región Metropolitana': ['Santiago']},
        'Ecuador': {'Pichincha': ['Quito']},
        'Perú': {'Lima': ['Lima']},
        'Venezuela': {'Distrito Capital': ['Caracas']},
        'México': {'Ciudad de México': ['Ciudad de México']},
        'Costa Rica': {'San José': ['San José']},
        'Panamá': {'Panamá': ['Ciudad de Panamá']},
        'Uruguay': {'Montevideo': ['Montevideo']},
        'Paraguay': {'Asunción': ['Asunción']},
        'Bolivia': {'La Paz': ['La Paz']},
        'Guatemala': {'Guatemala': ['Ciudad de Guatemala']},
        'Honduras': {'Francisco Morazán': ['Tegucigalpa']}
    };

    function setSelectOptions(select, values, placeholder, selectedValue) {
        if (!select) return;
        select.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = placeholder;
        select.appendChild(defaultOption);

        values.forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            if ((selectedValue || '') === value) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    function populateDepartamentosFor(pedidoId, selectedDepartamento = '') {
        const paisSelect = document.getElementById(`pais_${pedidoId}`);
        const departamentoSelect = document.getElementById(`departamento_${pedidoId}`);
        if (!paisSelect || !departamentoSelect) return;

        const pais = (paisSelect.value || '').trim();
        const departamentos = Object.keys(locationData[pais] || {});
        setSelectOptions(departamentoSelect, departamentos, 'Selecciona un departamento/estado', selectedDepartamento);
    }

    function populateCiudadesFor(pedidoId, selectedCiudad = '') {
        const paisSelect = document.getElementById(`pais_${pedidoId}`);
        const departamentoSelect = document.getElementById(`departamento_${pedidoId}`);
        const ciudadSelect = document.getElementById(`ciudad_${pedidoId}`);
        if (!paisSelect || !departamentoSelect || !ciudadSelect) return;

        const pais = (paisSelect.value || '').trim();
        const departamento = (departamentoSelect.value || '').trim();
        const ciudades = (locationData[pais] && locationData[pais][departamento]) ? locationData[pais][departamento] : [];
        setSelectOptions(ciudadSelect, ciudades, 'Selecciona un municipio/ciudad', selectedCiudad);
    }

    async function loadColombiaData() {
        try {
            const response = await fetch(colombiaDataUrl, { cache: 'no-store' });
            if (!response.ok) return;

            const data = await response.json();
            if (!Array.isArray(data)) return;

            const map = {};
            data.forEach(item => {
                if (item?.departamento && Array.isArray(item?.ciudades)) {
                    map[item.departamento] = item.ciudades;
                }
            });

            if (Object.keys(map).length > 0) {
                locationData['Colombia'] = map;
            }
        } catch (e) {
        }
    }

    function setupEditAddressSelectors() {
        document.querySelectorAll('.js-pais-edit').forEach(select => {
            const pedidoId = select.dataset.pedidoId;
            const selectedDepartamento = select.dataset.currentDepartamento || '';
            const selectedCiudad = select.dataset.currentCiudad || '';

            populateDepartamentosFor(pedidoId, selectedDepartamento);
            populateCiudadesFor(pedidoId, selectedCiudad);

            select.addEventListener('change', function () {
                populateDepartamentosFor(pedidoId, '');
                populateCiudadesFor(pedidoId, '');
            });
        });

        document.querySelectorAll('.js-departamento-edit').forEach(select => {
            const pedidoId = select.dataset.pedidoId;
            select.addEventListener('change', function () {
                populateCiudadesFor(pedidoId, '');
            });
        });
    }

    loadColombiaData().then(setupEditAddressSelectors);
});
</script>
@endpush