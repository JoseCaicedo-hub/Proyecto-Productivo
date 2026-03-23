@extends('plantilla.app')
@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="bi bi-shop me-2"></i>Solicitudes de Compra al Por Mayor</h1>
                </div>
            </div>
        </div>

        @if(session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($solicitudes->count() > 0)
        <style>
            .acciones-wrap {
                display: flex;
                gap: 6px;
                align-items: center;
                flex-wrap: wrap;
            }

            .acciones-wrap .btn {
                border-radius: 8px;
                padding: 4px 10px;
                font-size: .85rem;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .acciones-wrap .btn i {
                font-size: .85rem;
            }

            .empresa-col {
                min-width: 190px;
            }

            .empresa-cell {
                max-width: 240px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Listado de Solicitudes</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="empresa-col">Empresa</th>
                                    <th>Nombre Cliente</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th style="min-width: 210px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td>#{{ $solicitud->id }}</td>
                                    <td class="empresa-cell" title="{{ $solicitud->empresa->nombre ?? '—' }}">{{ $solicitud->empresa->nombre ?? '—' }}</td>
                                    <td>
                                        <strong>{{ $solicitud->nombre_cliente }}</strong>
                                        @if(!$solicitud->visto_en)
                                            <span class="badge bg-warning ms-2">Nueva</span>
                                        @endif
                                    </td>
                                    <td>{{ $solicitud->email_cliente }}</td>
                                    <td>{{ $solicitud->telefono_cliente }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($solicitud->descripcion, 80) }}</td>
                                    <td>{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($solicitud->estado === 'contactado')
                                            <span class="badge bg-info">Contactado</span>
                                        @elseif($solicitud->estado === 'rechazado')
                                            <span class="badge bg-danger">Rechazado</span>
                                        @elseif($solicitud->estado === 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="acciones-wrap">
                                            <a href="{{ route('mayorista.solicitud.show', $solicitud->id) }}" class="btn btn-primary">
                                                <i class="bi bi-eye"></i>Ver
                                            </a>
                                            @if($solicitud->documento)
                                                <a href="{{ asset('storage/' . $solicitud->documento) }}" class="btn btn-secondary" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i>PDF
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal-eliminar-solicitud-{{ $solicitud->id }}" title="Eliminar" aria-label="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <div class="modal fade" id="modal-eliminar-solicitud-{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalEliminarSolicitudLabel-{{ $solicitud->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalEliminarSolicitudLabel-{{ $solicitud->id }}">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Seguro de eliminar este registro?
                                                        <div class="small text-muted mt-2">Esta acción no se puede deshacer.</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('mayorista.solicitud.destroy', $solicitud->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $solicitudes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-md-12">
                <div class="card card-empty">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Sin solicitudes de compra mayorista</h5>
                        <p class="text-muted">Cuando los clientes soliciten compra al por mayor, aparecerán aquí.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    const mnuAlmacen = document.getElementById('mnuAlmacen');
    if (mnuAlmacen) {
        mnuAlmacen.classList.add('menu-open');
    }
    const itemGranPedido = document.getElementById('itemGranPedido');
    if (itemGranPedido) {
        itemGranPedido.classList.add('active');
    }
</script>
@endpush
