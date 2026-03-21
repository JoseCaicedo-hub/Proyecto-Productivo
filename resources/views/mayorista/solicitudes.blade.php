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
                                    <th>Nombre Cliente</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td>#{{ $solicitud->id }}</td>
                                    <td>
                                        <strong>{{ $solicitud->nombre_cliente }}</strong>
                                        @if(!$solicitud->visto_en)
                                            <span class="badge bg-warning ms-2">Nueva</span>
                                        @endif
                                    </td>
                                    <td>{{ $solicitud->email_cliente }}</td>
                                    <td>{{ $solicitud->telefono_cliente }}</td>
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
                                        <a href="{{ route('mayorista.solicitud.show', $solicitud->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>Ver
                                        </a>
                                        @if($solicitud->documento)
                                            <a href="{{ asset('storage/' . $solicitud->documento) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                <i class="bi bi-file-earmark-pdf me-1"></i>Descargar
                                            </a>
                                        @endif
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
