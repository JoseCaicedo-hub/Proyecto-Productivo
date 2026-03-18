@extends('plantilla.app')

@section('contenido')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Solicitudes de Empresas</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.empresas.solicitudes.historial') }}" class="btn btn-outline-dark btn-sm">Ver historial</a>
            <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm">Mi Empresa</a>
        </div>
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
                            <th>Nombre empresa</th>
                            <th>Logo</th>
                            <th>Contacto</th>
                            <th>Descripción</th>
                            <th>Documento PDF</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr class="align-middle">
                                <td>{{ $solicitud->user->name ?? '—' }}</td>
                                <td>{{ $solicitud->nombre }}</td>
                                <td style="width:100px;">
                                    @if($solicitud->logo)
                                        <img src="{{ asset($solicitud->logo) }}" alt="Logo" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                                    @else
                                        <span class="text-muted">Sin logo</span>
                                    @endif
                                </td>
                                <td>{{ $solicitud->contacto ?: '—' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($solicitud->descripcion, 80) ?: '—' }}</td>
                                <td>
                                    @if($solicitud->documento_pdf)
                                        <a href="{{ route('admin.empresas.solicitudes.documento', $solicitud->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-file-earmark-arrow-down me-1"></i>Descargar
                                        </a>
                                    @else
                                        <span class="text-muted">Sin adjunto</span>
                                    @endif
                                </td>
                                <td style="min-width:260px;">
                                    <form action="{{ route('admin.empresas.solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                                    </form>

                                    <form action="{{ route('admin.empresas.solicitudes.rechazar', $solicitud->id) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        <input type="text" name="motivo_rechazo" class="form-control form-control-sm d-inline-block" style="width:140px" placeholder="Motivo (opcional)">
                                        <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay solicitudes pendientes.</td>
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
    document.getElementById('itemSolicitudesEmpresas').classList.add('active');
</script>
@endpush
