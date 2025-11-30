@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Entregas pendientes</h3>
        <div>
            <a href="{{ route('almacen.entregados') }}" class="btn btn-outline-secondary btn-sm">Ver pedidos entregados</a>
            <a href="{{ route('almacen.debug') }}" class="btn btn-outline-info btn-sm ms-2">Debug</a>
        </div>
    </div>

    @if(session('mensaje'))
        <div class="alert alert-success">{{ session('mensaje') }}</div>
    @endif

    @if($detalles->isEmpty())
        <div class="alert alert-info">No hay entregas pendientes para tus productos.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Pedido #</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                                    <th>Talla</th>
                                    <th>Estado Envío</th>
                                    <th>Fecha Envío</th>
                                    <th>Fecha Recibido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->pedido_id }}</td>
                        <td>{{ optional($detalle->pedido->user)->name ?? '—' }}</td>
                        <td>{{ optional($detalle->producto)->nombre ?? 'Producto eliminado' }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->talla ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $detalle->envio_estado === 'enviado' ? 'bg-info' : ($detalle->envio_estado === 'entregado' ? 'bg-success' : 'bg-warning') }}">{{ ucfirst($detalle->envio_estado ?? 'pendiente') }}</span>
                        </td>
                        <td>{{ $detalle->fecha_envio ? $detalle->fecha_envio->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $detalle->fecha_recibido ? $detalle->fecha_recibido->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            @if($detalle->envio_estado === 'pendiente')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-enviar-{{ $detalle->id }}">Marcar Enviado</button>
                            @else
                                <span class="small text-muted">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                    <!-- Modal confirmar envío -->
                    <div class="modal fade" id="modal-enviar-{{ $detalle->id }}" tabindex="-1" aria-labelledby="modalEnviarLabel{{ $detalle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalEnviarLabel{{ $detalle->id }}">Confirmar envío</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <form action="{{ route('almacen.entregar', $detalle->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>¿Marcar el producto <strong>{{ optional($detalle->producto)->nombre ?? '—' }}</strong> del pedido <strong>#{{ $detalle->pedido_id }}</strong> como <strong>Enviado</strong>?</p>
                                        <p class="small text-muted">Esto registrará la fecha de envío y notificará al comprador que puede confirmar recepción.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Confirmar Enviado</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
