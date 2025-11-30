@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <h3>Debug Almacén — Detalles recientes</h3>

    <p class="small text-muted">Usuario conectado: {{ auth()->user()->id }} — Sólo visible para administradores autorizados.</p>

    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>ID Detalle</th>
                    <th>Pedido ID</th>
                    <th>Producto ID</th>
                    <th>Producto Nombre</th>
                    <th>Producto.user_id</th>
                    <th>Cantidad</th>
                    <th>Talla</th>
                    <th>Entregado</th>
                        <th>Fecha Envío</th>
                        <th>Fecha Recibido</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detalles as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->pedido_id }}</td>
                        <td>{{ $d->producto_id }}</td>
                        <td>{{ optional($d->producto)->nombre ?? '—' }}</td>
                        <td>{{ optional($d->producto)->user_id ?? 'NULL' }}</td>
                        <td>{{ $d->cantidad }}</td>
                        <td>{{ $d->talla ?? '-' }}</td>
                        <td>{{ is_null($d->entregado) ? 'NULL' : ($d->entregado ? 'Sí' : 'No') }}</td>
                        <td>{{ $d->fecha_envio ? $d->fecha_envio->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $d->fecha_recibido ? $d->fecha_recibido->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9">No hay detalles para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
