# StartPlace.com

Marketplace multi-vendedor para emprendimientos, tiendas y compradores, desarrollado en Laravel.

Este README describe el estado real del software en este repositorio: arquitectura, módulos, reglas de negocio, seguridad y cómo ejecutarlo.

## Resumen funcional

StartPlace permite:
- Publicar y vender productos de múltiples empresas.
- Gestionar roles de usuario (admin, vendedor, cliente).
- Comprar con carrito, checkout y seguimiento de envío por detalle de pedido.
- Recibir solicitudes de emprendimiento para aprobar nuevos vendedores.
- Procesar solicitudes de compra mayorista por empresa.

## Stack tecnológico

- Backend: Laravel 12, PHP 8.2+
- ORM: Eloquent
- Vistas: Blade
- Frontend build: Vite
- UI: Bootstrap + estilos personalizados
- Autorización: Spatie Laravel Permission
- PDF: barryvdh/laravel-dompdf
- Tests: PHPUnit

## Módulos del sistema

### 1) Sitio público
- Inicio, tienda, detalle de producto, preguntas, contacto y página de equipo/acerca.
- Navegación con carrito y autenticación.

### 2) Autenticación y perfil
- Registro/login, edición de perfil y avatar.
- Diferenciación por roles para mostrar paneles y accesos.

### 3) Catálogo y productos
- CRUD de productos con categoría, precio, stock e imagen.
- Relación producto-empresa-vendedor.

### 4) Carrito
- Agregar/quitar/sumar/restar productos.
- Persistencia por sesión y soporte de flujo a pedido.

### 5) Checkout y pedidos
- Creación de pedido desde carrito con datos de envío y pago.
- Métodos: efectivo, tarjeta, transferencia.
- Validaciones condicionales por método y documento.
- Edición de dirección solo en estados permitidos.
- Cancelación restringida cuando ya hay envío iniciado.

### 6) Almacén vendedor
- El vendedor gestiona entregas de sus detalles de pedido.
- Estados de envío por ítem y registro de fechas.

### 7) Solicitudes de emprendimiento
- Formulario público para aplicar como vendedor.
- Adjuntos obligatorios (imagen representativa y carta).
- Revisión en panel admin con aprobar/rechazar.
- Notificación y correos de estado.

### 8) Solicitudes mayoristas
- Clientes envían solicitudes a empresas.
- Vendedores ven, marcan vistas y actualizan estado.

### 9) Administración
- Gestión de usuarios, roles, productos y solicitudes.

## Reglas de negocio clave (estado actual)

### Solicitudes de emprendimiento
- Un usuario con solicitud pendiente no puede enviar otra.
- Si el usuario ya tiene rol vendedor, no puede volver a enviar solicitud.
- Antes de enviar, se muestra modal de confirmación.

### Formulario de solicitud (ubicación y contacto)
- País, departamento/estado y ciudad son dependientes.
- Para Colombia se usa dataset extenso de departamentos/ciudades.
- Teléfono:
  - Solo números.
  - Validación visual en tiempo real (Válido/Inválido).
  - Longitud por país (Colombia exacto 10, otros rango general).

### Pedidos y envíos
- Si un detalle ya fue enviado/entregado, el comprador no puede cancelar el pedido.
- Dirección editable solo antes de estados bloqueados.

### Pagos
- Tarjeta: número, expiración y CVV validados.
- Transferencia: proveedor y número con reglas por proveedor.
- Seguridad: CVV no se guarda en texto plano, se almacena hash.

## Modelo de datos principal

Entidades más importantes:
- User
- Empresa
- Producto
- ProductoImagen
- Carrito
- Pedido
- PedidoDetalle
- Solicitud
- SolicitudCompraMayorista
- Review
- Category

Relaciones principales:
- Un usuario puede tener empresa(s), productos, pedidos y solicitudes.
- Una empresa tiene productos y solicitudes mayoristas.
- Un pedido tiene muchos detalles y cada detalle referencia producto.

## Estructura del proyecto

- app/Http/Controllers: lógica de negocio y endpoints web
- app/Models: entidades Eloquent
- resources/views: vistas Blade (web, admin, pedidos, solicitudes)
- routes/web.php: rutas del sistema
- database/migrations: evolución de esquema
- database/seeders: carga inicial de roles/permisos/categorías
- public/images, public/data: assets e información estática

## Migraciones recientes destacadas

- Categorías en base de datos.
- Campos de dirección en users y pedidos.
- Campos de pago en pedidos (incluye card_last4 y hash de CVV).
- Ampliación de solicitud de emprendimiento con datos de negocio.
- Campo departamento en solicitudes.
- Foto opcional en reseñas (`reviews.foto_producto`).

## Actualizaciones recientes (marzo 2026)

### Solicitudes de emprendimiento
- El envío requiere sesión iniciada (ruta POST protegida).
- `nombre` y `email` de la solicitud se fuerzan desde la cuenta autenticada (no editables).
- En panel admin, aceptar/rechazar usa modales de confirmación.
- Al rechazar, la solicitud se elimina del registro junto con sus adjuntos.
- Al aceptar, se asigna rol vendedor y se crea/vincula empresa automáticamente si faltaba.

### Registro y acceso
- El registro público se simplificó: todos se crean como cliente.
- Se eliminó creación de empresa durante registro.
- Tras registrarse, el usuario vuelve al inicio con alerta de éxito.
- Mensajes de validación de registro en español (incluye email duplicado).

### Productos y vendedores
- Corrección de creación de productos para vendedores recién aprobados.
- Resolución automática de empresa para vendedor cuando `empresa_id` no estaba vinculado.

### Compras mayoristas
- Nombre, email y teléfono del solicitante se toman de su cuenta y no son editables en el formulario.
- Teléfono validado solo numérico en backend.
- Nuevo módulo en menú de Almacén: **Gran Pedido** para vendedor y admin.
- Listado de mayorista mejorado con empresa, descripción y paginación de 8 por página.
- Acciones por registro: ver, descargar documento y eliminar (con confirmación).
- Control de acceso por rol: admin ve todas; vendedor solo su empresa.

### Reseñas
- Soporte para subir foto opcional del producto recibido en la reseña.
- UI de reseña con selector visual de imagen (botón + cámara), preview y render de imagen en comentarios.

### Chatbot
- Nuevas respuestas para perfil, cambio de foto/avatar, contraseña, datos de cuenta y roles.

### UI/UX
- Carrusel principal actualizado para mostrar imagen completa de producto sin recorte (`object-fit: contain`) sin alterar dimensiones del carrusel.
- Correcciones de modales y botones en vistas administrativas.

## Seguridad y validaciones

- Validación backend obligatoria (además de validación frontend).
- Autorización por rol y permisos.
- Restricciones de estado para operaciones sensibles.
- Sanitización de campos numéricos en checkout.
- Hash de CVV para evitar exposición de datos críticos.

## Ejecución local

1. Instalar dependencias:
	- composer install
	- npm install

2. Configurar entorno:
	- copiar .env
	- php artisan key:generate
	- configurar conexión a BD

3. Base de datos:
	- php artisan migrate
	- php artisan db:seed

4. Ejecutar proyecto:
	- php artisan serve
	- npm run dev
	- php artisan storage:link

## Despliegue (referencia)

- npm run build
- php artisan migrate --force
- php artisan config:cache
- php artisan route:cache
- php artisan view:clear

Nota de producción Linux/AWS:
- Respetar mayúsculas/minúsculas en nombres de archivos (ejemplo: Logo.png vs logo.png).
- Verificar que assets de public/images estén presentes tras deploy.

## Estado actual del producto

Fortalezas actuales:
- Flujo completo marketplace (catálogo, carrito, pedidos, paneles por rol).
- Solicitudes de emprendimiento con control de duplicados y estados.
- Validaciones robustas de checkout y formulario de solicitud.

Pendientes recomendados:
- Integrar pasarela de pago real.
- Mejorar cobertura de pruebas automáticas.
- Endurecer rate limiting y auditoría de acciones administrativas.

## Licencia

Uso interno del proyecto StartPlace.
