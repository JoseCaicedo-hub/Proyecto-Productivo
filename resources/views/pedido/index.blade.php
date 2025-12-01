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
                                                @if(auth()->check() && auth()->id() === $reg->user_id && $reg->estado === 'pendiente')
                                                    <button type="button" class="btn btn-danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#modal-cancelar-{{$reg->id}}" title="Cancelar pedido">
                                                        <i class="bi bi-x-circle"></i>
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
                                            <td>${{ number_format($reg->total, 2) }}</td>
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
                                                            <td>{{ number_format($detalle->precio, 2) }}</td>
                                                            <td>{{ number_format($detalle->cantidad * $detalle->precio, 2) }}
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
});
</script>
@endpush