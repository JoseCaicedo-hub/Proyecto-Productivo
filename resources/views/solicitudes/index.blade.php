@extends('plantilla.app')
@php use Illuminate\Support\Str; @endphp
@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Solicitudes de Emprendimiento</h3>
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
                            <th>Idea</th>
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
                            <td style="max-width:340px">{{ Str::limit($s->idea, 120) }}</td>
                            <td>{{ ucfirst($s->estado) }}</td>
                            <td>
                                @if($s->estado === 'pendiente')
                                    <form action="{{ route('admin.solicitudes.accept', $s->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Aceptar</button>
                                    </form>
                                    <form action="{{ route('admin.solicitudes.reject', $s->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">Rechazar</button>
                                    </form>
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
