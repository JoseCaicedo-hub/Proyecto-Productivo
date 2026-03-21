@extends('web.app')

@push('estilos')
<link href="{{ asset('css/tienda.css') }}" rel="stylesheet">
@endpush

@section('contenido')

@php
    // Productos destacados para el carrusel
    $topProducts = \App\Http\Controllers\HeaderController::topProductos(5);
    // Lista de categorías principales desde base de datos
  $categorias = \App\Models\Category::query()
    ->orderBy('name')
    ->pluck('name');

  $empresasFiltro = \App\Models\Empresa::query()
    ->where('estado', 'activo')
    ->whereHas('productos')
    ->orderBy('nombre')
    ->get(['id', 'nombre']);

    // Productos para la lista principal (paginados)
    $search = trim((string) request('search', ''));
    $selectedCategory = request('category');
  $selectedEmpresa = request('empresa');

  $productosQuery = \App\Models\Producto::with('empresa')
    ->whereNotNull('empresa_id')
    ->whereHas('empresa', function($q){
      $q->where('estado', 'activo');
    });

    if($selectedCategory){
      $productosQuery->where('categoria', $selectedCategory);
    }

  if($selectedEmpresa){
    $productosQuery->where('empresa_id', $selectedEmpresa);
  }

    if($search !== ''){
      $productosQuery->where(function($q) use ($search){
        $q->where('nombre', 'like', "%{$search}%")
          ->orWhere('descripcion', 'like', "%{$search}%")
          ->orWhere('categoria', 'like', "%{$search}%");
      });
    }

    $productosQuery->orderBy('created_at','desc');

    $productos = $productosQuery->paginate(12)->appends(request()->only('category', 'search', 'empresa'));
    $hasFilters = filled($selectedCategory) || filled($search) || filled($selectedEmpresa);
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
        </ul>
      </div>
    </aside>

    <main class="col-md-9">
      <div class="card border-0 shadow-sm search-panel mb-4">
        <div class="card-body p-3 p-md-4">
          <form method="GET" action="{{ route('web.tienda') }}" class="row g-2 align-items-end">
            <div class="col-12 col-lg-5">
              <label for="search" class="form-label fw-semibold mb-1">Buscar producto</label>
              <div class="search-input-wrap">
                <i class="bi bi-search search-icon" aria-hidden="true"></i>
                <input
                  type="text"
                  id="search"
                  name="search"
                  class="form-control search-input"
                  placeholder="Ej: audífonos, camiseta, hogar..."
                  value="{{ $search }}"
                >
              </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
              <label for="category" class="form-label fw-semibold mb-1">Categoría</label>
              <select id="category" name="category" class="form-select search-select">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-4 col-lg-2">
              <label for="empresa" class="form-label fw-semibold mb-1">Empresa</label>
              <select id="empresa" name="empresa" class="form-select search-select">
                <option value="">Todas</option>
                @foreach($empresasFiltro as $empresaItem)
                  <option value="{{ $empresaItem->id }}" {{ (string)$selectedEmpresa === (string)$empresaItem->id ? 'selected' : '' }}>{{ $empresaItem->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-4 col-lg-2 d-flex gap-2 justify-content-md-start">
              <button type="submit" class="btn btn-search" aria-label="Buscar">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span class="visually-hidden">Buscar</span>
              </button>
              @if($hasFilters)
                <a href="{{ route('web.tienda') }}" class="btn btn-search-clear" title="Limpiar filtros">
                  <i class="bi bi-x-lg" aria-hidden="true"></i>
                </a>
              @endif
            </div>
          </form>

          <div class="search-hint mt-2">
            <i class="bi bi-lightbulb me-1"></i> Tip: prueba buscar por nombre, categoría o palabras de la descripción.
          </div>
        </div>
      </div>

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
                <div class="small text-muted d-flex align-items-center gap-2">
                  @if(optional($producto->empresa)->logo)
                    <img src="{{ asset($producto->empresa->logo) }}" alt="{{ $producto->empresa->nombre }}" style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                  @endif
                  <span>{{ optional($producto->empresa)->nombre ?? 'Sin empresa' }}</span>
                </div>
              </div>
              <div class="card-footer bg-transparent border-0 text-center pb-3">
                @php
                    $categoria = $producto->categoria ?? '';
                    $esRopa = is_string($categoria) && strtolower(trim($categoria)) === 'ropa';
                @endphp
                <div class="product-actions">
                  <form action="{{ route('carrito.agregar') }}" method="POST" class="{{ $esRopa ? 'add-with-size' : '' }}">
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

                    <button type="submit" class="btn btn-celeste btn-add-cart fw-semibold">Añadir al carrito</button>
                  </form>
                  <a href="{{ url('/producto/'.$producto->id) }}" class="btn btn-view-article fw-semibold">Ver artículo</a>
                </div>
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
