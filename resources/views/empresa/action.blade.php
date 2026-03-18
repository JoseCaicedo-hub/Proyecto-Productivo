@extends('plantilla.app')

@section('contenido')
@php
    $authUser = auth()->user();
    $esVendedor = $authUser && $authUser->hasRole('vendedor') && !$authUser->hasRole('admin');
    $esCreacion = !isset($empresa);
    $especificacionesBasePath = 'docs/Instrucciones_Empresa';
    $especificacionesPublicPath = public_path($especificacionesBasePath);
    $especificacionesPublicPathPdf = public_path($especificacionesBasePath . '.pdf');
    $especificacionesDisponible = file_exists($especificacionesPublicPath) || file_exists($especificacionesPublicPathPdf);
    $especificacionesUrl = asset(file_exists($especificacionesPublicPath) ? $especificacionesBasePath : $especificacionesBasePath . '.pdf');
@endphp
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">{{ isset($empresa) ? 'Editar empresa' : 'Crear empresa' }}</h3>
                    </div>
                    <div class="card-body">
                        @if($esVendedor && $esCreacion)
                            <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    Para solicitar la creación de tu empresa debes adjuntar un PDF con toda la información requerida.
                                </div>
                                @if($especificacionesDisponible)
                                    <a href="{{ $especificacionesUrl }}" class="btn btn-outline-primary btn-sm" download>
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Descargar especificaciones (PDF)
                                    </a>
                                @else
                                    <span class="badge bg-warning text-dark">PDF de especificaciones pendiente de configurar</span>
                                @endif
                            </div>
                        @endif

                        <form action="{{ isset($empresa) ? route('empresas.update', $empresa->id) : route('empresas.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($empresa))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre de la empresa</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $empresa->nombre ?? '') }}" required>
                                    @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contacto" class="form-label">Contacto</label>
                                    <input type="text" name="contacto" id="contacto" class="form-control @error('contacto') is-invalid @enderror"
                                           value="{{ old('contacto', $empresa->contacto ?? '') }}" placeholder="Email, teléfono o redes">
                                    @error('contacto')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="descripcion" class="form-label">Información adicional</label>
                                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $empresa->descripcion ?? '') }}</textarea>
                                    @error('descripcion')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
                                    @error('logo')<small class="text-danger">{{ $message }}</small>@enderror
                                    @if(isset($empresa) && $empresa->logo)
                                        <img src="{{ asset($empresa->logo) }}" alt="Logo empresa" class="mt-2" style="max-width:120px;border-radius:8px;">
                                    @endif
                                </div>
                            </div>

                            @if($esVendedor && $esCreacion)
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="documento_pdf" class="form-label">Documento de empresa (PDF) <span class="text-danger">*</span></label>
                                        <input type="file" name="documento_pdf" id="documento_pdf" accept="application/pdf" class="form-control @error('documento_pdf') is-invalid @enderror" required>
                                        @error('documento_pdf')<small class="text-danger">{{ $message }}</small>@enderror
                                        <small class="text-muted">Este documento será enviado al administrador para su revisión.</small>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuAlmacen').classList.add('menu-open');
    document.getElementById('itemEmpresa').classList.add('active');
</script>
@endpush
