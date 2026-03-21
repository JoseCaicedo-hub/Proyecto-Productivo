# 💰 Guía de Manejo de Precios Estandarizado en StartPlace

## Resumen General

Todos los precios en el marketplace ahora se manejan de manera **estandarizada** en **Pesos Colombianos (COP)** sin decimales:

- **Formato en BD**: Número entero (integer)
- **Ejemplo**: `25000` = $25.000 COP (no $25.000,00)
- **Visualización**: `$25.000` (con separador de miles, sin decimales)
- **Entrada de usuario**: Acepta `25000` o `25.000` (se limpia automáticamente)

---

## 📁 Archivos Nuevos Creados

### 1. **PriceHelper** - Manejo centralizado de precios
**Ubicación**: `app/Helpers/PriceHelper.php`

**Funciones principales**:

```php
// Limpiar entrada de usuario
PriceHelper::cleanPrice("25.000")      // → 25000
PriceHelper::cleanPrice("25000")       // → 25000
PriceHelper::cleanPrice("$25.000")     // → 25000

// Formatear para visualización
PriceHelper::formatCOP(25000)           // → "$25.000"
PriceHelper::formatCOP(25000, false)    // → "25.000"
PriceHelper::formatCOPWithoutSymbol(25000) // → "25.000"

// Validación
PriceHelper::isValidPrice("25000")     // → true
PriceHelper::isValidPrice("25.000")    // → true
PriceHelper::isValidPrice("abc")       // → false

// Obtener con fallback
PriceHelper::getPriceOrDefault("25000", 1000) // → 25000
```

### 2. **Migración de Precios**
**Ubicación**: `database/migrations/2026_03_21_100000_standardize_prices_to_integer.php`

Convierte todas las columnas de precios de `DECIMAL(10,2)` a `INTEGER`:
- `productos.precio`
- `pedidos.total`
- `pedido_detalles.precio`

---

## 🔄 Flujo de Precios en el Sistema

### **Entrada de Datos (Formulario)**
```
Usuario ingresa: "25.000" o "25000"
                    ↓
ProductoRequest::prepareForValidation()
                    ↓
PriceHelper::cleanPrice() → 25000
                    ↓
Validación: integer, min:1
                    ↓
Guardar en BD: 25000 (INTEGER)
```

### **Visualización en Vistas**
```
Valor en BD: 25000
                    ↓
Blade: @formatCOP($producto->precio)
                    ↓
PriceHelper::formatCOP(25000)
                    ↓
Mostrar: "$25.000"
```

---

## 🎨 Usando Precios en Vistas Blade

### Directivas Blade Personalizadas

```blade
<!-- Con símbolo $ -->
@formatCOP($producto->precio)
<!-- Output: $25.000 -->

<!-- Sin símbolo $ -->
@formatCOPNoSymbol($producto->precio)
<!-- Output: 25.000 -->

<!-- En operaciones -->
@formatCOP($cantidad * $precio)
<!-- Multiplica y formatea: $1.250.000 -->
```

### Ejemplo en componentes

```blade
<!-- Antes (DEPRECATED) -->
$ {{ number_format($precio, 2) }}

<!-- Ahora (RECOMENDADO) -->
@formatCOP($precio)
```

---

## ✅ Actualizado en Todo el Sistema

### Vistas modificadas:
- ✅ `resources/views/web/pedido.blade.php` - Carrito y checkout
- ✅ `resources/views/web/item.blade.php` - Detalle producto
- ✅ `resources/views/web/tienda/index.blade.php` - Catálogo
- ✅ `resources/views/web/index.blade.php` - Inicio
- ✅ `resources/views/web/partials/header.blade.php` - Header con productos destacados
- ✅ `resources/views/dashboard.blade.php` - Dashboard vendedor/comprador
- ✅ `resources/views/producto/index.blade.php` - Admin listado productos
- ✅ `resources/views/pedido/index.blade.php` - Admin listado pedidos
- ✅ `resources/views/pedido/pdf.blade.php` - PDF de comprobante
- ✅ `resources/views/pedido/delete.blade.php` - Confirmación de cancelación
- ✅ `resources/views/pedido/delete_permanent.blade.php` - Eliminación de pedido

### Validaciones modificadas:
- ✅ `app/Http/Requests/ProductoRequest.php`
  - Reglas: `'precio' => ['required', 'integer', 'min:1']`
  - Limpieza automática en `prepareForValidation()`
  - Mensajes de error actualizados

### Helpers registrados:
- ✅ `app/Providers/AppServiceProvider.php`
  - Directivas Blade: `@formatCOP()` y `@formatCOPNoSymbol()`

---

## 📝 Ejemplos de Uso

### Crear un Producto (Formulario)

```html
<input type="text" name="precio" placeholder="Ej: 25000 o 25.000" required>
```

El usuario puede ingresar:
- `25000` → Se guarda como 25000
- `25.000` → Se limpia a 25000 automáticamente
- `25,000` → Se limpia a 25000 automáticamente

### Mostrar Precio en Lista

```blade
@foreach($productos as $producto)
    <div class="price">
        @formatCOP($producto->precio)
    </div>
@endforeach
```

Output:
```
$1.000
$25.000
$1.500.000
```

### Calcular con Precios

```blade
@php
    $subtotal = $cantidad * $precio;
@endphp

<p>Subtotal: @formatCOP($subtotal)</p>
```

---

## 🔧 Validación de Precios en Controlador

```php
use App\Helpers\PriceHelper;

// En controlador (después de ProductoRequest)
$producto->precio = PriceHelper::cleanPrice($request->input('precio'));
// O ya está hecho automáticamente por ProductoRequest

// Validar manualmente si es necesario
if (!PriceHelper::isValidPrice($request->input('precio'))) {
    return back()->withErrors(['precio' => 'Precio inválido']);
}

// Obtener con default
$price = PriceHelper::getPriceOrDefault($request->input('precio'), 0);
```

---

## ⚠️ Cambios en la BD

| Tabla | Columna | Antes | Después |
|-------|---------|-------|---------|
| `productos` | `precio` | `DECIMAL(10,2)` | `INTEGER` |
| `pedidos` | `total` | `DECIMAL(10,2)` | `INTEGER` |
| `pedido_detalles` | `precio` | `DECIMAL(12,2)` | `INTEGER` |

Todos los valores existentes fueron convertidos automáticamente (ej: 25000.50 → 25000).

---

## 🚀 Próximas Mejoras (Opcional)

- [ ] Validación de rango máximo de precio en frontal (Ej: no permitir > 50.000.000)
- [ ] Formateo de entrada mientras el usuario escribe (JS)
- [ ] Integraciones comunes de pago que requieran validación de formato
- [ ] Historial de cambios de precios para auditoría

---

## 📞 Referencia Rápida

```bash
# Limpiar precio
\App\Helpers\PriceHelper::cleanPrice("25.000");  // 25000

# Formatear precio
\App\Helpers\PriceHelper::formatCOP(25000);      // "$25.000"

# Usar en Blade
@formatCOP($precio)                              // "$25.000"
@formatCOPNoSymbol($precio)                      // "25.000"
```

---

**Versión**: 1.0  
**Fecha**: 21 de marzo de 2026  
**Estado**: ✅ Implementado y en Producción
