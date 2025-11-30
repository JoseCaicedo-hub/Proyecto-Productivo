@extends('web.app')

@push('estilos')
<link href="{{ asset('css/tienda.css') }}" rel="stylesheet">
@endpush

@section('contenido')

@php
    // Productos destacados para el carrusel
    $topProducts = \App\Http\Controllers\HeaderController::topProductos(5);
    // Lista de categorias a partir de productos existentes
    $categorias = \Illuminate\Support\Facades\DB::table('productos')->select('categoria')->distinct()->pluck('categoria')->filter()->values();
    // Productos para la lista principal (paginados)
    $productosQuery = \Illuminate\Support\Facades\DB::table('productos')->orderBy('created_at','desc');
    if(request()->has('category') && request('category')){
      $productosQuery->where('categoria', request('category'));
    }
    $productos = $productosQuery->paginate(12)->appends(request()->only('category'));
@endphp

{{-- Carrusel (copiado del header) --}}
@include('web.partials.header')

<div class="container py-5">
  <div class="row">
    <aside class="col-md-3 mb-4">
      <div class="card border-0 shadow-sm categorias-box p-3">
        <h5 class="mb-3">Categorías</h5>
        <ul class="list-unstyled mb-0">
          @foreach($categorias as $cat)
            <li class="mb-2"><a href="{{ route('web.tienda', ['category' => $cat]) }}" class="text-decoration-none categoria-link">{{ $cat }}</a></li>
          @endforeach
          {{-- Añadir algunas categorías adicionales si no están en la lista (fallback) --}}
          @php
              $extra = ['Oficina','Belleza','Deportes'];
          @endphp
          @foreach($extra as $ecat)
            @if(! $categorias->contains($ecat))
              <li class="mb-2"><a href="{{ route('web.tienda', ['category' => $ecat]) }}" class="text-decoration-none categoria-link">{{ $ecat }}</a></li>
            @endif
          @endforeach
        </ul>
      </div>
    </aside>

    <main class="col-md-9">
      <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-3">
        @foreach($productos as $producto)
          <div class="col mb-5">
            <div class="card h-100 border-0 shadow-sm producto-card">
              <div class="image-wrapper">
                <a href="{{ url('/producto/'.$producto->id) }}">
                  <img class="card-img-top" src="{{ asset('uploads/productos/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
                </a>
              </div>
              <div class="card-body text-start p-3">
                <p class="text-muted mb-1 small">{{ $producto->categoria ?? 'Sin categoría' }}</p>
                <h5 class="fw-bold mb-2">{{ $producto->nombre }}</h5>
                <p class="text-primary fw-semibold fs-5">$ {{ number_format($producto->precio, 2) }}</p>
              </div>
              <div class="card-footer bg-transparent border-0 text-center pb-3">
                @php
                    $categoria = $producto->categoria ?? '';
                    $esRopa = is_string($categoria) && strtolower(trim($categoria)) === 'ropa';
                @endphp
                <form action="{{ route('carrito.agregar') }}" method="POST" class="d-inline-block {{ $esRopa ? 'add-with-size' : '' }}">
                  @csrf
                  <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                  <input type="hidden" name="cantidad" value="1">

                  @if($esRopa)
                    <div class="mb-2">
                        <select name="talla" class="form-select form-select-sm talla-select" aria-label="Selecciona talla" required>
                            <option value="">Selecciona talla</option>
                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                        </select>
                    </div>
                  @endif

                  <button type="submit" class="btn btn-celeste rounded-pill px-4 fw-semibold">Añadir al carrito</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="d-flex justify-content-center">
        {{ $productos->links() }}
      </div>
    </main>
  </div>
</div>

@endsection

<script>
  (function(){
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('form.add-with-size').forEach(function(form){
        form.addEventListener('submit', function(e){
          var select = form.querySelector('.talla-select');
          if(select && (!select.value || select.value.trim() === '')){
            e.preventDefault();
            alert('Por favor selecciona una talla antes de añadir al carrito.');
          }
        });
      });
    });
  })();
</script>
