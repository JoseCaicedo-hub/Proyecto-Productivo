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
                                <div>{{ Str::limit($s->idea, 120) }}</div>
                                @if(!empty($s->detalle))
                                    <div class="text-muted small mt-2">{{ Str::limit($s->detalle, 120) }}</div>
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
                                        <form action="{{ route('admin.solicitudes.accept', $s->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            <button class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center" title="Aceptar" aria-label="Aceptar">
                                                <i class="bi bi-check" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.solicitudes.reject', $s->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            <button class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center" title="Rechazar" aria-label="Rechazar">
                                                <i class="bi bi-x" aria-hidden="true"></i>
                                            </button>
                                        </form>
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
