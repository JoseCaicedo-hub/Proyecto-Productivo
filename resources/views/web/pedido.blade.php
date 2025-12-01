@extends('web.app')
@section('contenido')
<!-- Section-->
<section class="py-5">
    <div class="container px-4 px-lg-12 my-5">
        <h2 class="fw-bold mb-4">Detalle de su Pedido</h2>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="row">
                            <div class="col-md-5"><strong>Producto</strong></div>
                            <div class="col-md-2 text-center"><strong>Precio</strong></div>
                            <div class="col-md-2 text-center"><strong>Cantidad</strong></div>
                            <div class="col-md-3 text-end"><strong>Subtotal</strong></div>
                        </div>
                    </div>
                    <div class="card-body" id="cartItems">
                        @forelse($carrito as $id => $item)
                        <!-- Product-->
                        <div class="row align-items-center mb-3 cart-item">
                            <!--Nombre y código-->
                            <div class="col-md-5 d-flex align-items-center">
                                <img src="{{ asset('uploads/productos/' . $item['imagen']) }}" 
                                style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $item['nombre'] }}">
                                <div class="ms-3">
                                    <h6 class="mb-0">{{ $item['nombre'] }}</h6>
                                    <small class="text-muted">{{ $item['codigo'] }}</small>
                                    @if(!empty($item['talla']))
                                        <div class="mt-1">
                                            <span class="badge bg-secondary">Talla: {{ $item['talla'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!--Precio-->
                            <div class="col-md-2 text-center">
                                <span class="fw-bold">${{ number_format($item['precio'], 2) }}</span>
                            </div>
                            <!--Cantidad-->
                            <div class="col-md-2 d-flex justify-content-center">
                                <div class="input-group input-group-sm" style="max-width: 100px;">
                                    <a class="btn btn-outline-secondary" href="{{ route('carrito.restar', ['producto_id' => $id]) }}"
                                        data-action="decrease">-</a>
                                    <input type="text" class="form-control text-center" value="{{ $item['cantidad'] }}"
                                        readonly>
                                        <a href="{{ route('carrito.sumar', ['producto_id' => $id]) }}" class="btn btn-outline-secondary btn-sm">
                                            +
                                        </a>
                                </div>
                            </div>

                            <!--Subtotal-->
                            <div class="col-md-3 d-flex align-items-center justify-content-end">
                                <div class="text-end me-3">
                                    <span
                                        class="fw-bold subtotal">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                                </div>
                                <a class="btn btn-sm btn-outline-danger" href="{{ route('carrito.eliminar', $id) }}">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                        </div>
                        <hr>
                        @empty
                        <div class="text-center">
                            <p>Tu carrito esta vacío</p>
                        </div>
                        @endforelse
                    </div>
                    @if (session('mensaje'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            {{ session('mensaje') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col text-end">
                                <a class="btn btn-outline-danger me-2" href="{{route('carrito.vaciar')}}">
                                    <i class="bi bi-x-circle me-1"></i>Vaciar carrito
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $total = 0;
                            foreach ($carrito as $item) {
                                $total += $item['precio'] * $item['cantidad'];
                            }
                        @endphp
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong id="orderTotal">${{ number_format($total, 2) }}</strong>
                        </div>
                        <!-- Checkout Button -->
                        <form id="checkoutForm" action="{{ route('pedido.realizar') }}" method="POST">
                            @csrf
                            <!-- Botón que abre el modal de envío/pago -->
                            <button type="button" class="btn btn-primary w-100" id="checkout" data-bs-toggle="modal" data-bs-target="#shippingModal">
                                <i class="bi bi-credit-card me-1"></i>Realizar pedido
                            </button>

                            <!-- Modal para datos de envío y pago -->
                            <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shippingModalLabel">Datos de envío y pago</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="direccion" class="form-label">Dirección exacta <span class="text-danger">*</span></label>
                                                <textarea name="direccion" id="direccion" class="form-control" rows="3" required>{{ old('direccion') }}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="tipo_documento" class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                                                    <select name="tipo_documento" id="tipo_documento" class="form-select" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de ciudadanía (CC)</option>
                                                        <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                                        <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de extranjería (CE)</option>
                                                        <option value="PAS" {{ old('tipo_documento') == 'PAS' ? 'selected' : '' }}>Pasaporte</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="numero_documento" class="form-label">Número de documento <span class="text-danger">*</span></label>
                                                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" value="{{ old('numero_documento') }}" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="metodo_pago" class="form-label">Método de pago <span class="text-danger">*</span></label>
                                                    <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                                        <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta débito/crédito</option>
                                                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="referencia" class="form-label">Referencia / Instrucciones (Casa, Apto, Residencia)</label>
                                                <input type="text" name="referencia" id="referencia" class="form-control" value="{{ old('referencia') }}">
                                            </div>

                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul class="mb-0">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Confirmar y pagar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Continue Shopping -->
                        <a href="/" class="btn btn-outline-secondary w-100 mt-3">
                            <i class="bi bi-arrow-left me-1"></i>Continuar comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection