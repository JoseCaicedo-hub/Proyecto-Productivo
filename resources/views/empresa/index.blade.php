@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Mi Empresa</h3>
        @if(!$user->hasRole('vendedor') || $user->hasRole('admin'))
        <a href="{{ route('empresas.create') }}" class="btn btn-primary btn-sm">Nueva empresa</a>
        @endif
    </div>

    @if(session('mensaje'))
        <div class="alert alert-info">{{ session('mensaje') }}</div>
    @endif

    @if($user->hasRole('vendedor') && !$user->hasRole('admin'))
        <div class="alert alert-warning">
            Como vendedor, las nuevas empresas requieren aprobación de un administrador antes de poder usarlas en productos.
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <strong>Empresas aprobadas</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mb-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $empresa)
                            <tr class="align-middle">
                                <td style="width:100px;">
                                    @if($empresa->logo)
                                        <img src="{{ asset($empresa->logo) }}" alt="{{ $empresa->nombre }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                                    @else
                                        <span class="text-muted">Sin logo</span>
                                    @endif
                                </td>
                                <td>{{ $empresa->nombre }}</td>
                                <td>{{ $empresa->contacto ?: '—' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($empresa->descripcion, 90) ?: '—' }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($empresa->estado) }}</span></td>
                                <td>
                                    <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-info btn-sm">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aún no tienes empresas aprobadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Solicitudes de creación</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mb-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Revisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->nombre }}</td>
                                <td>{{ $solicitud->contacto ?: '—' }}</td>
                                <td>
                                    @if($solicitud->estado === 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($solicitud->estado === 'aprobada')
                                        <span class="badge bg-success">Aprobada</span>
                                    @else
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
                                        {{ $solicitud->motivo_rechazo }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No has enviado solicitudes todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuAlmacen').classList.add('menu-open');
    const itemEmpresa = document.getElementById('itemEmpresa');
    if (itemEmpresa) {
        itemEmpresa.classList.add('active');
    }
</script>
@endpush
