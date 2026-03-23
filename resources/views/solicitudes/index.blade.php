@extends('plantilla.app')
@php use Illuminate\Support\Str; use Illuminate\Support\Facades\Storage; @endphp
@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Solicitudes de Emprendimiento</h3>
                <div class="mt-2">
                    <span class="badge bg-success me-2" style="font-size:.9rem;">✓ Aceptar</span>
                    <span class="badge bg-danger me-2" style="font-size:.9rem;">✕ Rechazar</span>
                    <small class="text-muted">Nota: el botón verde acepta la solicitud y el botón rojo la rechaza.</small>
                </div>
            </div>
            <div class="card-body">
                @if(session('mensaje'))
                    <div class="alert alert-success">{{ session('mensaje') }}</div>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Título</th>
                            <th>Idea / Detalles</th>
                            <th>Archivos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->nombre }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->titulo }}</td>
                            <td style="max-width:340px">
                                <div class="small mb-1"><strong>Emprendimiento:</strong> {{ $s->nombre_emprendimiento ?? '—' }}</div>
                                <div class="small mb-1"><strong>Tipo/Categoría:</strong> {{ $s->tipo_negocio ?? '—' }} / {{ $s->categoria_negocio ?? '—' }}</div>
                                <div class="small mb-1"><strong>Ubicación:</strong> {{ $s->pais ?? '—' }} - {{ $s->ciudad ?? '—' }}</div>
                                <div class="small mb-1"><strong>Teléfono:</strong> {{ $s->telefono ?? '—' }}</div>
                                <div class="small mb-1"><strong>Legalmente registrada:</strong> {{ $s->empresa_registrada_legalmente ? strtoupper($s->empresa_registrada_legalmente) : '—' }}</div>
                                <div>{{ Str::limit($s->idea, 120) }}</div>
                                @if(!empty($s->detalle))
                                    <div class="text-muted small mt-2">{{ Str::limit($s->detalle, 120) }}</div>
                                @endif
                                @if(!empty($s->redes_sociales_web))
                                    <div class="small mt-2"><strong>Web/Redes:</strong> {{ $s->redes_sociales_web }}</div>
                                @endif
                            </td>
                            <td>
                                @if(!empty($s->producto_img) || !empty($s->carta))
                                    <div class="d-flex gap-2 align-items-center">
                                        @if(!empty($s->producto_img))
                                            <a href="{{ Storage::url($s->producto_img) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center" title="Ver imagen" aria-label="Ver imagen">
                                                <i class="bi bi-image" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if(!empty($s->carta))
                                            <a href="{{ Storage::url($s->carta) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Descargar carta" aria-label="Descargar carta">
                                                <i class="bi bi-download" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">Sin archivos</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($s->estado) }}</td>
                            <td>
                                @if($s->estado === 'pendiente')
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center" title="Aceptar" aria-label="Aceptar" data-bs-toggle="modal" data-bs-target="#modal-aceptar-{{ $s->id }}">
                                            <i class="bi bi-check" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center" title="Rechazar" aria-label="Rechazar" data-bs-toggle="modal" data-bs-target="#modal-rechazar-{{ $s->id }}">
                                                <i class="bi bi-x" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="modal fade" id="modal-aceptar-{{ $s->id }}" tabindex="-1" aria-labelledby="modalAceptarLabel-{{ $s->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalAceptarLabel-{{ $s->id }}">Confirmar aprobación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de convertir a este usuario en vendedor?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('admin.solicitudes.accept', $s->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">Sí, convertir en vendedor</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modal-rechazar-{{ $s->id }}" tabindex="-1" aria-labelledby="modalRechazarLabel-{{ $s->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalRechazarLabel-{{ $s->id }}">Confirmar rechazo</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de borrar esta solicitud? Esta acción rechazará y eliminará el registro.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('admin.solicitudes.reject', $s->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Sí, borrar solicitud</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Procesada</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
