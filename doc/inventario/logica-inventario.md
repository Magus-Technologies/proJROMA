# Módulo Inventario — Lógica y Requerimientos

> Documento de diseño. Define cómo debe funcionar el inventario (sucursales,
> almacenes, stock, movimientos) y por qué. Sirve como requerimiento antes de
> implementar.

---

## 1. Estado actual (lo que hay hoy)

### Sucursales
- **No existe modelo `Sucursal`** usable. La tabla `sucursales` existe pero está **vacía**.
- `sucursal` es un **INT hardcodeado** presente en muchas tablas: `usuarios`, `productos`, `ventas`, `compras`, `cotizaciones`, `guias_remision`, `arqueo_diario`, etc.
- Se elige en el **login** con un selector fijo de 3 opciones (1, 2, 3) y se guarda en `session('sucursal')` **sin validar**.
- Cada usuario tiene `sucursal` (su sucursal por defecto) y un flag `rotativo` (puede acceder a cualquier sucursal).
- Archivos clave:
  - `app/Http/Controllers/Auth/LoginController.php:73` — guarda `sucursal` en sesión.
  - `resources/views/auth/login.blade.php` — selector hardcodeado con 3 opciones.
  - `app/Models/User.php` — `sucursal` y `rotativo` son columnas del usuario.

### Almacenes
- **No existe modelo ni tabla `almacenes`.**
- Es solo un INT, columna `almacen` en `productos` (default `'1'`).
- 3 valores fijos (Almacén 1, 2, 3) **hardcodeados** en el `<select>` de `productos/index.blade.php`.
- Se filtra con `where('productos.almacen', $almacenId)` en `ProductosApiController`.

### Stock
- El stock vive en `productos.cantidad`, pero **el mismo producto se duplica como una fila por almacén** (mismo `codigo` repetido con distinto `almacen` y su propia `cantidad`).
- Consecuencia: los datos del catálogo (descripción, precio, categoría, marca) se **duplican** y se pueden **desincronizar**.

### Problemas principales
1. Almacenes y sucursales son **números mágicos**: no se pueden crear/editar/eliminar.
2. **No hay relación** modelada entre sucursal y almacén.
3. El **stock duplica el producto** en lugar de tener un producto único con stock por almacén.
4. Vistas `Almacén`, `Traslado`, `Kardex`, `Préstamos` son **placeholders**.

---

## 2. Decisiones de diseño (cómo debe ser)

### 2.1 Jerarquía
```
Empresa
 └─ Sucursales            (maestro real, CRUD)
     └─ Almacenes         (maestro real, CRUD; cada almacén pertenece a una sucursal)
         └─ Stock por producto (cantidad por almacén)
```
> **Los almacenes se crean desde la app, NO en el código.** Se acabó el `<select>` fijo 1/2/3.

### 2.2 El stock NO se maneja en "Registro de Producto"
- **Registro de Producto = solo catálogo** (código, descripción, precios, categoría, marca, etc.).
- El producto **nace con stock 0** en todos los almacenes.
- **Todo el stock se mueve por movimientos** (ingresos / salidas), nunca editando un campo "cantidad" a mano.
- *(Opción UX permitida)*: al crear un producto se puede capturar un **"stock inicial" opcional**; internamente eso **genera un movimiento de Ingreso con motivo "Carga inicial"** en el almacén elegido. Así es cómodo pero queda **trazable en el Kardex**. El campo "Stock" del formulario de producto deja de escribir `productos.cantidad` directamente.

#### Columna "Stock" en la tabla de Registro de Producto
- En el **listado del catálogo**, la columna **Stock = SUMA del stock de todos los almacenes** del producto: `SUM(producto_stock.cantidad)`.
- Es un valor **calculado y de solo lectura** (no se edita ahí).
- El **detalle por almacén** (cuánto hay en cada uno) se ve en la vista **Almacén** y en el **Kardex**.
- Ejemplo: producto X con 50 en Almacén 1 y 30 en Almacén 2 → la columna Stock del catálogo muestra **80**.

### 2.3 Modelo de stock (sin duplicar productos)
Nueva tabla **`producto_stock`**:

| Campo | Tipo | Nota |
|---|---|---|
| id_producto | FK | |
| id_almacen | FK | |
| cantidad | int | stock actual en ese almacén |
| costo_promedio | decimal | costo actual/valorización |
| | | **UNIQUE (id_producto, id_almacen)** |

Resultado: **un solo producto** en el catálogo, con **stock por almacén** en `producto_stock`.

### 2.4 Movimientos = el Kardex
Nueva tabla **`inventario_movimientos`** (fuente de verdad del Kardex):

| Campo | Nota |
|---|---|
| id_movimiento | PK |
| id_empresa, id_almacen, id_producto | |
| tipo | `I` = ingreso, `S` = salida |
| id_motivo | FK → motivos_movimiento |
| cantidad | |
| stock_anterior, stock_nuevo | foto del stock antes/después |
| costo | costo unitario del movimiento |
| tipo_referencia, id_referencia | de dónde viene (compra, venta, traslado, ajuste…) |
| id_usuario, fecha, observacion | trazabilidad |

Regla: **cada cambio de stock crea un movimiento** y recalcula `producto_stock.cantidad` (`stock_nuevo = stock_anterior ± cantidad`).

### 2.5 Motivos (con CRUD)
Nueva tabla **`motivos_movimiento`**: `id_motivo, id_empresa, nombre, tipo (I/S), es_sistema, estado`.

- **Ingreso (I):** Carga inicial · Compra (recepción) · Ajuste positivo · Devolución de cliente · Traslado entrada · Préstamo recibido.
- **Salida (S):** Venta · Ajuste negativo · Merma / pérdida · Traslado salida · Préstamo entregado · Consumo interno.
- `es_sistema = 1` para los automáticos (Venta, Compra) que no se borran; el resto los administra el usuario.

---

## 3. Carga inicial (cuando el sistema pasa al cliente)

**Problema:** el cliente ya tiene su almacén **lleno**, pero **no tiene los documentos de compra** históricos, así que no puede cargar el stock "por compra".

**Solución — Movimiento de "Carga Inicial" (no requiere proveedor ni documento):**
1. **Por producto:** modal "Crear Ingreso" con `Tipo de Ingreso = Carga inicial`.
2. **Masivo (recomendado para migración):** importar un **Excel** (`producto, almacén, cantidad, costo`) que genera un movimiento de Ingreso "Carga inicial" por cada fila.

Esto deja el stock arrancado y **trazable** sin inventar compras falsas.

---

## 4. Submódulo Ingresos / Salidas (Movimientos)

### 4.1 Qué es
Registrar entradas y salidas **manuales** de stock con un **motivo**. Es la cara visible del Kardex.

### 4.2 Modal "Crear Ingreso / Salida" (vive dentro de **Almacén**)
Campos (según el formulario de referencia):
- **Fecha**
- **Almacén** (select de almacenes — del maestro)
- **Producto** (buscador)
- **Unidad derivada** (presentación)
- **Cantidad**
- **Stock** (actual, solo lectura)
- **Costo actual** (solo lectura / editable según motivo)
- **Proveedor** (solo si el motivo lo requiere, ej. compra)
- **Tipo de Ingreso/Salida** (= **motivo**)
- **Observaciones**
- **Stock Actual → Nuevo Stock** (preview calculado en vivo)

Marcar visualmente **obligatorio** (asterisco) vs **opcional** (badge) — ya soportado por los componentes `x-input-group` / `x-label`.

### 4.3 Vista de listado
Una tabla (componente `x-table`) que liste todas las notas de ingreso/salida con filtros: **almacén, tipo, motivo, fecha, producto**.

### 4.4 Nombre del módulo (propuesta)
- **Recomendado: "Movimientos de Inventario"** — engloba ingresos, salidas, ajustes y cuadres en un solo lugar.
- Alternativas: "Notas de Ingreso/Salida" · "Ajustes de Inventario" · "Kardex (Movimientos)".

> El **Kardex** sería la *vista por producto* de estos mismos movimientos (historial cronológico con saldo).

---

## 5. Flujos que alimentan el inventario

| Operación | Efecto en stock | Motivo |
|---|---|---|
| **Compra → Recepción** | Ingreso al almacén destino | Compra |
| **Venta** | Salida del almacén de la sucursal | Venta |
| **Traslado de Stock** | Salida en origen + Ingreso en destino | Traslado |
| **Préstamo de Productos** | Salida (entregado) / Ingreso (recibido) — contraparte externa | Préstamo |
| **Ajuste / Cuadre** | Ingreso o Salida tras conteo físico | Ajuste |
| **Carga inicial** | Ingreso inicial (migración) | Carga inicial |

---

## 6. Relación Sucursal ↔ Almacén (a confirmar con el cliente)

Propuesta por defecto: **cada almacén pertenece a una sucursal** (`almacenes.id_sucursal`). El usuario, según su `session('sucursal')`, ve y opera los almacenes de esa sucursal.
> ⚠️ Confirmar si en el negocio real los almacenes son **por sucursal** o **globales** de la empresa.

---

## 7. Fases de implementación sugeridas

1. **Maestro Almacenes** (tabla `almacenes` + CRUD) y usar de verdad **Sucursales**.
2. **Tabla `producto_stock`** + migración: pasar el stock de las filas duplicadas a `producto_stock` y **consolidar productos duplicados** en uno solo.
3. **`inventario_movimientos` + `motivos_movimiento`** + modal Ingreso/Salida + vista de listado.
4. **Conectar** Compra/Recepción, Venta, Traslado y Préstamo a los movimientos.
5. **Carga inicial** (modal + importación Excel) para la migración del cliente.

---

## 8. Pendientes / decisiones abiertas

- [ ] ¿Almacenes por sucursal o globales? (sección 6)
- [ ] ¿El "stock inicial" se permite en el alta de producto (como Ingreso automático) o solo desde Movimientos? (sección 2.2)
- [ ] Nombre final del módulo de movimientos. (sección 4.4)
- [ ] ¿Costeo: promedio ponderado, último costo, o FIFO?
- [ ] Migración de los productos duplicados actuales a producto único + `producto_stock`.
