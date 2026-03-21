@extends('plantilla.app')
@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <a href="{{ route('mayorista.solicitudes.index') }}" class="btn btn-secondary mb-3">
                    <i class="bi bi-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
        </div>

        @if(session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Solicitud #{{ $solicitud->id }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Información del Cliente</h6>
                                <p class="mb-2">
                                    <strong>Nombre:</strong><br>
                                    {{ $solicitud->nombre_cliente }}
                                </p>
                                <p class="mb-2">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $solicitud->email_cliente }}">{{ $solicitud->email_cliente }}</a>
                                </p>
                                <p class="mb-2">
                                    <strong>Teléfono:</strong><br>
                                    <a href="tel:{{ $solicitud->telefono_cliente }}">{{ $solicitud->telefono_cliente }}</a>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted">Información de la Solicitud</h6>
                                <p class="mb-2">
                                    <strong>Fecha de Solicitud:</strong><br>
                                    {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-2">
                                    <strong>Estado:</strong><br>
                                    @if($solicitud->estado === 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($solicitud->estado === 'contactado')
                                        <span class="badge bg-info">Contactado</span>
                                    @elseif($solicitud->estado === 'rechazado')
                                        <span class="badge bg-danger">Rechazado</span>
                                    @elseif($solicitud->estado === 'completado')
                                        <span class="badge bg-success">Completado</span>
                                    @endif
                                </p>
                                @if($solicitud->visto_en)
                                <p class="mb-2">
                                    <strong>Visto:</strong><br>
                                    {{ $solicitud->visto_en->format('d/m/Y H:i') }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3">Descripción de la Solicitud</h6>
                        <p>{{ $solicitud->descripcion }}</p>

                        @if($solicitud->documento)
                        <hr>
                        <h6 class="text-muted mb-3">Documento Adjunto</h6>
                        <a href="{{ asset('storage/' . $solicitud->documento) }}" class="btn btn-secondary btn-sm" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Descargar Documento
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">Acciones</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mayorista.solicitud.updateEstado', $solicitud->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="estado" class="form-label">Cambiar Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="contactado" {{ $solicitud->estado === 'contactado' ? 'selected' : '' }}>Contactado</option>
                                    <option value="rechazado" {{ $solicitud->estado === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    <option value="completado" {{ $solicitud->estado === 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
                                <i class="bi bi-check-circle me-2"></i>Actualizar Estado
                            </button>
                        </form>

                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tip:</strong> Usa el botón de teléfono o email arriba para contactar al cliente directamente.
                        </div>

                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $solicitud->email_cliente }}" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>Enviar Email
                            </a>
                            <a href="tel:{{ $solicitud->telefono_cliente }}" class="btn btn-outline-success">
                                <i class="bi bi-telephone me-2"></i>Llamar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
