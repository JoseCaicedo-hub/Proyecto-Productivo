@extends('plantilla.app')
@section('contenido')
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Productos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="{{ isset($registro)?route('productos.update', $registro->id) : route('productos.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($registro))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                     id="codigo" name="codigo" value="{{old('codigo', $registro->codigo ??'')}}" required>
                                     @error('codigo')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-3 mb-4">
                                    <label for="nombre" class="form-label">nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                     id="nombre" name="nombre" value="{{old('nombre',  $registro->nombre ??'')}}" required>
                                     @error('nombre')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-3 mb-4">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="text" class="form-control @error('precio') is-invalid @enderror"
                                     id="precio" name="precio" value="{{old('precio',  $registro->precio ??'')}}" required>
                                     @error('precio')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label for="cantidad_almacen" class="form-label">Cantidad en almacén</label>
                                    <input type="number" class="form-control @error('cantidad_almacen') is-invalid @enderror"
                                     id="cantidad_almacen" name="cantidad_almacen" value="{{old('cantidad_almacen',  $registro->cantidad_almacen ?? 0)}}" min="0" required>
                                     @error('cantidad_almacen')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="categoria" class="form-label">Categoria</label>
                                    <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror">
                                        @if(isset($categorias) && count($categorias))
                                            <option value="">-- Seleccione --</option>
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat }}" {{ old('categoria', $registro->categoria ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                            @endforeach
                                        @else
                                            <option value="">-- Seleccione --</option>
                                            <option value="Electrónica" {{ old('categoria', $registro->categoria ?? '') == 'Electrónica' ? 'selected' : '' }}>Electrónica</option>
                                            <option value="Ropa" {{ old('categoria', $registro->categoria ?? '') == 'Ropa' ? 'selected' : '' }}>Ropa</option>
                                            <option value="Hogar" {{ old('categoria', $registro->categoria ?? '') == 'Hogar' ? 'selected' : '' }}>Hogar</option>
                                            <option value="Accesorios" {{ old('categoria', $registro->categoria ?? '') == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                                            <option value="Alimentos" {{ old('categoria', $registro->categoria ?? '') == 'Alimentos' ? 'selected' : '' }}>Alimentos</option>
                                            <option value="Otros" {{ old('categoria', $registro->categoria ?? '') == 'Otros' ? 'selected' : '' }}>Otros</option>
                                        @endif
                                    </select>
                                    @error('categoria')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" id="descripcion" 
                                    rows="4">{{ old('descripcion', $registro->descripcion ?? '') }}</textarea>
                                     @error('descripcion')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="imagen" class="form-label">Imagen</label>
                                    <input type="file" class="form-control @error('imagen') is-invalid @enderror"
                                     id="imagen" name="imagen" value="{{old('imagen')}}">
                                     @error('imagen')
                                        <small class="text-danger">{{$message}}</small>
                                     @enderror
                                     @if(isset($registro) && $registro->imagen)
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/productos/' . $registro->imagen) }}" 
                                            alt="Imagen actual" style="max-width: 150px; height: auto; border-radius: 8px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary me-md-2"
                                    onclick="window.location.href='{{route('productos.index')}}'">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">

                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('mnuAlmacen').classList.add('menu-open');
    document.getElementById('itemProducto').classList.add('active');
</script>
@endpush