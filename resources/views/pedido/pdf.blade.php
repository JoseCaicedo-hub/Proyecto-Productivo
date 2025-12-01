<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Pedido #{{ $pedido->id }}</title>
    <style>
        body{ font-family: DejaVu Sans, Arial, sans-serif; color:#222; }
        .header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:20px }
        .logo{ width:120px }
        .invoice { width:100%; border-collapse:collapse; margin-top:10px }
        .invoice th, .invoice td{ border:1px solid #ddd; padding:8px; font-size:12px }
        .invoice th{ background:#f5f5f5 }
        .text-right{ text-align:right }
        .small{ font-size:11px; color:#555 }
        .totals{ margin-top:12px; width:100%; }
        .totals td{ padding:6px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="{{ public_path('images/Logo.png') }}" class="logo" alt="Logo">
            <div style="margin-top:8px">
                <strong>StartPlace S.A.S.</strong>
            </div>
        </div>
        <div class="text-right">
            <h2>Factura / Pedido</h2>
            <div class="small">Factura #: F-{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="small">Pedido #: {{ $pedido->id }}</div>
            <div class="small">Fecha: {{ $pedido->created_at->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div style="display:flex;justify-content:space-between;margin-bottom:16px">
        <div style="width:48%">
            <strong>Cliente</strong>
            <div class="small">{{ $pedido->user->name }}</div>
            <div class="small">{{ $pedido->user->email }}</div>
            <div class="small">Tipo doc: {{ $pedido->tipo_documento ?? ($pedido->user->tipo_documento ?? 'No registrado') }}</div>
            <div class="small">Nro doc: {{ $pedido->numero_documento ?? ($pedido->user->nit ?? 'No registrado') }}</div>
            <div class="small">Dirección: {{ $pedido->direccion ?? ($pedido->user->direccion ?? 'No registrada') }}</div>
            <div class="small">Tel: {{ $pedido->telefono ?? ($pedido->user->telefono ?? 'No registrado') }}</div>
        </div>
        <div style="width:48%">
            <strong>Detalles de pago</strong>
            <div class="small">Método: {{ $pedido->metodo_pago ?? 'No especificado' }}</div>
            <div class="small">Estado pago: {{ $pedido->estado ?? 'pendiente' }}</div>
            @if(!empty($pedido->referencia))
                <div class="small">Referencia: {{ $pedido->referencia }}</div>
            @endif
        </div>
    </div>

    <table class="invoice">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Talla</th>
                <th>Imagen</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $d)
            <tr>
                <td style="max-width:180px">{{ $d->producto->nombre }}</td>
                <td style="width:60px;text-align:center">{{ $d->talla ?? '-' }}</td>
                <td style="width:80px;text-align:center">
                    @php $img = public_path('uploads/productos/' . ($d->producto->imagen ?? 'no-image.png')); @endphp
                    @if(file_exists($img))
                        <img src="{{ $img }}" style="width:60px;height:60px;object-fit:cover;border-radius:4px" alt="">
                    @endif
                </td>
                <td class="text-right">{{ $d->cantidad }}</td>
                <td class="text-right">{{ number_format($d->precio,2) }}</td>
                <td class="text-right">{{ number_format($d->cantidad * $d->precio,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $subtotal = 0;
        foreach($pedido->detalles as $d) { $subtotal += ($d->cantidad * $d->precio); }
        $taxRate = 0; // modificar según necesites, p. ej. 0.19 para IVA 19%
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;
    @endphp

    <table class="totals">
        <tr>
            <td style="width:65%" class="text-right small">Subtotal</td>
            <td class="text-right">${{ number_format($subtotal,2) }}</td>
        </tr>
        <tr>
            <td class="text-right small">IVA ({{ $taxRate * 100 }}%)</td>
            <td class="text-right">${{ number_format($tax,2) }}</td>
        </tr>
        <tr>
            <td class="text-right small"><strong>Total</strong></td>
            <td class="text-right"><strong>${{ number_format($total > 0 ? $total : $pedido->total,2) }}</strong></td>
        </tr>
    </table>

    <div style="margin-top:28px" class="small">
        Gracias por su compra.
    </div>

    <div style="position:fixed; bottom:20px; font-size:10px; color:#666; width:100%; text-align:center;">
        Esta factura fue generada electrónicamente y no requiere firma física. Conserva este documento para efectos contables.
    </div>
    
</body>
</html>
