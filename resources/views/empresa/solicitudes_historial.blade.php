@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Historial de Solicitudes de Empresas</h3>
        <a href="{{ route('admin.empresas.solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">Volver a pendientes</a>
    </div>

    @if(session('mensaje'))
        <div class="alert alert-info">{{ session('mensaje') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive mb-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Empresa</th>
                            <th>Estado</th>
                            <th>Revisado por</th>
                            <th>Fecha revisión</th>
                            <th>Documento PDF</th>
                            <th>Motivo rechazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->user->name ?? '—' }}</td>
                                <td>{{ $solicitud->nombre }}</td>
                                <td>
                                    @if($solicitud->estado === 'aprobada')
                                        <span class="badge bg-success">Aprobada</span>
                                    @else
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td>{{ $solicitud->admin->name ?? '—' }}</td>
                                <td>{{ $solicitud->revisado_en ? $solicitud->revisado_en->format('Y-m-d H:i') : '—' }}</td>
                                <td>
                                    @if($solicitud->documento_pdf)
                                        <a href="{{ route('admin.empresas.solicitudes.documento', $solicitud->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-file-earmark-arrow-down me-1"></i>Descargar
                                        </a>
                                    @else
                                        <span class="text-muted">Sin adjunto</span>
                                    @endif
                                </td>
                                <td>{{ $solicitud->motivo_rechazo ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay solicitudes procesadas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $solicitudes->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuAlmacen').classList.add('menu-open');
    document.getElementById('itemHistorialSolicitudesEmpresas').classList.add('active');
</script>
@endpush
