@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <h3>Pedidos entregados</h3>

    @if($detalles->isEmpty())
        <div class="alert alert-info">No hay entregas registradas para tus productos.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>ID Detalle</th>
                        <th>Pedido ID</th>
                        <th>Producto ID</th>
                        <th>Producto Nombre</th>
                        <th>Id usuario</th>
                        <th>Cantidad</th>
                        <th>Talla</th>
                        <th>Fecha Envío</th>
                        <th>Fecha Recibido</th>
                        <th>Recibido Por</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $d)
                        <tr>
                            <td>{{ $d->id }}</td>
                            <td>{{ $d->pedido_id }}</td>
                            <td>{{ $d->producto_id }}</td>
                            <td>{{ optional($d->producto)->nombre ?? '—' }}</td>
                            <td>{{ optional($d->producto)->user_id ?? 'NULL' }}</td>
                            <td>{{ $d->cantidad }}</td>
                            <td>{{ $d->talla ?? '-' }}</td>
                            <td>{{ $d->fecha_envio ? $d->fecha_envio->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ $d->fecha_recibido ? $d->fecha_recibido->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ optional($d->entregadoPor)->name ?? '-' }}</td>
                            <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
