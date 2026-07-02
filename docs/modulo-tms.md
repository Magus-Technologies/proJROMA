# Módulo TMS (Transportation Management System) — Lógica requerida

Documento de diseño funcional/técnico del módulo de transporte y despacho.
Estado: **lógica definida — pendiente de implementar**. Este documento describe SOLO la
lógica y el modelo de datos; la UI se decide después.

---

## 1. Objetivo

Armar el reparto diario: el usuario **define rutas como conjuntos de mercados**, el sistema
**jala los pedidos** de los clientes de esos mercados en una fecha, **calcula el peso total**
y permite asignar un **vehículo que aguante ese peso** con su **conductor**, registrando
luego la **entrega** punto por punto.

---

## 2. Jerarquía de datos (clave del módulo)

Un **PUNTO DE ENTREGA** puede ser de dos tipos:
- **MERCADO**: un local con UNA dirección que agrupa **muchos clientes** (puestos dentro).
- **TIENDA**: un **cliente suelto** con su **propia dirección**, fuera de un mercado.

```
Producto  ──(peso_bruto)
   └─ Línea de pedido      productos_cotis (cantidad)
        └─ Pedido          cotizaciones (por fecha)
             └─ Cliente    clientes.mercado
                  └─ PUNTO DE ENTREGA
                       ├─ MERCADO  (clientes.mercado > 0)  → N clientes  ◄── FALTA maestro + CRUD
                       └─ TIENDA   (clientes.mercado = 0/null) → 1 cliente
                            └─ RUTA = conjunto de PUNTOS (mercados y/o tiendas)  ◄── FALTA maestro
                                 └─ DESPACHO (fecha)
                                      → peso total
                                      → VEHÍCULO (capacidad)  ◄── FALTA maestro
                                      → CONDUCTOR             ◄── FALTA maestro
```

Reglas de la jerarquía:
- **Un cliente está en un mercado** (`clientes.mercado > 0`) **o es una tienda suelta**
  (`clientes.mercado = 0`/null), con su propia dirección.
- **Una ruta se compone de uno o varios PUNTOS** (mercados y/o tiendas).
  - Agregar un **mercado** a la ruta arrastra a TODOS sus clientes.
  - Agregar una **tienda** a la ruta agrega a ESE cliente individual.
- **Un despacho** = una ruta + una fecha → jala TODOS los pedidos de los clientes de los
  puntos de esa ruta.

---

## 3. Flujo de negocio

```
 DÍA 1                                      DÍA 2 (mañana)
 ─────                                      ──────────────
 1. Vendedores registran PEDIDOS            4. ARMAR DESPACHO:
    (cotizaciones)                              a. elegir RUTA (ya trae sus mercados)
 2. Pueden AUMENTAR pedidos                     b. elegir FECHA de los pedidos
 3. (corte: fin del día)                        c. el sistema JALA los pedidos de los
                                                   clientes de esos mercados
                                                d. calcula PESO total
                                                e. sugiere VEHÍCULO con capacidad ≥ peso
                                                f. asigna CONDUCTOR
                                             5. CARGAR → SALIR A RUTA
                                             6. ENTREGAR por punto (mercado/cliente):
                                                ENTREGADO | RECHAZADO | PARCIAL
                                             7. CERRAR despacho (+ costos opcional)
```

---

## 4. Qué ya existe (reutilizar)

| Recurso | Tabla / campo | Uso |
|---|---|---|
| Pedidos (cabecera) | `cotizaciones` (`cotizacion_id`, `fecha`, `id_cliente`, `total`, `id_tido`, `id_empresa`, `sucursal`) | Fuente de pedidos. `id_tido` define el tipo "pedido". |
| Pedidos (detalle) | `productos_cotis` (`id_coti`, `id_producto`, `cantidad`) | Líneas del pedido para calcular peso. |
| **Peso del producto** | `productos.peso_bruto` (decimal 10,2) | ✅ Permite calcular el peso del despacho. |
| Cliente / punto | `clientes` (`id_cliente`, `datos`, `direccion`, `distrito`, `telefono`) | Punto de entrega. |
| Cliente ↔ mercado | `clientes.mercado` (int, valores 1–22) | Ya asocia cliente con un mercado… pero sin nombre. |
| Empresa/sucursal | `id_empresa`, `sucursal` | Filtrar SIEMPRE por la sesión. |

### Vacíos detectados (hay que crear)
1. **Maestro de MERCADOS** + CRUD. Hoy `clientes.mercado` es un número sin nombre/zona.
2. **Maestro de RUTAS** (ruta = conjunto de mercados) + CRUD.
3. **Maestro de VEHÍCULOS** (placa, capacidad, documentos) + CRUD.
4. **Maestro de CONDUCTORES** + CRUD.
5. **Edición del mercado de un cliente** (en el CRUD de clientes o desde el de mercados),
   para poder mantener la relación cliente→mercado.

---

## 5. Modelo de datos propuesto (tablas nuevas, prefijo `tms_`)

Todas con `id_empresa` + `sucursal`.

### 5.1 `tms_mercados` — maestro de mercados (pocos datos)
```
id            PK
id_empresa    int
sucursal      int
nombre        varchar(120)   -- "Mercado Central", "Mercado Caquetá"
direccion     varchar(245)   -- dirección ESPECÍFICA (para ubicar al repartir)
referencia    varchar(245)   nullable  -- "frente a la iglesia", puerta/portón, etc.
distrito      varchar(120)   nullable  -- zona/distrito
telefono      varchar(20)    nullable  -- contacto opcional (administración del mercado)
estado        tinyint        default 1
```
> Solo lo esencial: **nombre + dirección específica** son los obligatorios; lo demás es
> opcional. La dirección debe ser precisa porque es a donde llega el vehículo.
>
> Migración de datos: crear un registro por cada valor distinto de `clientes.mercado`
> (1–22) y, si se desea, convertir `clientes.mercado` en FK a `tms_mercados`.

### 5.2 `tms_rutas` — maestro de rutas
```
id            PK
id_empresa    int
sucursal      int
nombre        varchar(120)   -- "Ruta Norte", "Ruta SJL"
descripcion   varchar(245)   nullable
estado        tinyint        default 1
```

### 5.3 `tms_ruta_puntos` — qué puntos componen una ruta (mercados y/o tiendas)
```
id            PK
id_ruta       int  FK tms_rutas (onDelete cascade)
tipo          varchar(10)    -- MERCADO | TIENDA
id_mercado    int  nullable  -- si tipo=MERCADO → FK tms_mercados
id_cliente    int  nullable  -- si tipo=TIENDA  → FK clientes (cliente suelto)
orden         int  default 0 -- orden de visita del punto en la ruta
```
> Regla: exactamente uno de `id_mercado` / `id_cliente` está lleno según `tipo`.

### 5.4 `tms_vehiculos` — maestro de flota
```
id                  PK
id_empresa          int
sucursal            int
placa               varchar(15)    -- único por empresa  (ej. "ABC-123")
tipo                varchar(15)    -- CAMIONETA | FURGONETA | CAMION | MOTO | OTRO
marca               varchar(60)    nullable  (ej. "Toyota")
modelo              varchar(60)    nullable  (ej. "Hilux")
anio                smallint       nullable

-- PESO (clave para el matching con el peso del despacho)
capacidad_kg        decimal(10,2)  -- carga útil máxima en kg (lo que PUEDE llevar)
tara_kg             decimal(10,2)  nullable  -- peso del vehículo vacío (opcional)

-- TAMAÑO de la zona de carga (para volumen / "qué tan grande")
largo_m             decimal(6,2)   nullable
ancho_m             decimal(6,2)   nullable
alto_m              decimal(6,2)   nullable
capacidad_m3        decimal(8,2)   nullable  -- volumen útil (= largo×ancho×alto o manual)

-- DOCUMENTOS (para alertas de vencimiento)
soat_vence          date           nullable
rev_tecnica_vence   date           nullable

estado              tinyint        default 1
```

**Ejemplos de tipos y capacidades típicas:**

| Tipo | Capacidad (kg) | Tamaño aprox. carga | Uso |
|---|---|---|---|
| **Moto / mototaxi** | 150 – 300 | pequeño | pedidos chicos, tiendas cercanas |
| **Camioneta** (pickup) | 800 – 1,000 | mediano | rutas medianas, mixto |
| **Furgoneta** (van) | 1,000 – 1,500 | cerrado, mediano-grande | mercados con varios puestos |
| **Camión** | 3,000 – 8,000+ | grande | rutas pesadas / muchos mercados |

> El **matching** del §6 usa `capacidad_kg` vs el peso del despacho. Si además se quiere
> validar por **tamaño**, se compara el volumen estimado de la carga vs `capacidad_m3`
> (requiere volumen por producto, que hoy NO existe → ver decisión §12.5).

### 5.5 `tms_conductores` — maestro de choferes
```
id                  PK
id_empresa          int
sucursal            int
id_usuario          int            nullable  -- si también es usuario del sistema
nombres             varchar(120)
documento           varchar(15)    nullable
licencia            varchar(30)    nullable
licencia_vence      date           nullable
telefono            varchar(20)    nullable
estado              tinyint        default 1
```

### 5.6 `tms_despachos` — cabecera del despacho/viaje
```
id                  PK
id_empresa          int
sucursal            int
codigo              varchar(20)    -- correlativo DSP-000123
fecha_reparto       date
id_ruta             int  FK tms_rutas
id_vehiculo         int  FK tms_vehiculos
id_conductor        int  FK tms_conductores
peso_total          decimal(12,2)  -- calculado al armar
estado              varchar(15)    -- PLANIFICADO|CARGADO|EN_RUTA|CERRADO|ANULADO
observaciones       varchar(255)   nullable
id_usuario_creacion int
created_at / updated_at
```

### 5.7 `tms_despacho_pedidos` — pedidos jalados al despacho
```
id                  PK
id_despacho         int  FK tms_despachos (onDelete cascade)
id_cotizacion       int  -- el pedido jalado
id_cliente          int  -- denormalizado (punto de entrega)
id_mercado          int  -- denormalizado (a qué mercado pertenece)
peso                decimal(12,2)  -- peso del pedido
monto               decimal(12,2)  -- total del pedido
orden               int            -- orden de visita
estado_entrega      varchar(15)    -- PENDIENTE|ENTREGADO|RECHAZADO|PARCIAL
motivo_rechazo      varchar(255)   nullable
hora_entrega        datetime       nullable
```

---

## 6. Algoritmo de "Armar Despacho" (núcleo de la lógica)

**Entrada:** `id_ruta`, `fecha_desde`, `fecha_hasta` (o una sola fecha).

```
1. puntos = SELECT tipo, id_mercado, id_cliente
            FROM tms_ruta_puntos WHERE id_ruta = :ruta

   mercados = ids de los puntos tipo MERCADO
   tiendas  = ids de cliente de los puntos tipo TIENDA

2. clientes = (SELECT id_cliente FROM clientes
                WHERE mercado IN (mercados) AND id_empresa = :empresa)
              UNION
              (tiendas)                       -- clientes sueltos agregados directo

3. pedidos  = SELECT c.cotizacion_id, c.id_cliente, c.total, cl.mercado
              FROM cotizaciones c
              JOIN clientes cl ON cl.id_cliente = c.id_cliente
              WHERE c.id_cliente IN (clientes)
                AND c.fecha BETWEEN :desde AND :hasta
                AND c.id_tido = :tipo_pedido
                AND c.estado = activo
                AND c.cotizacion_id NOT IN (             -- aún sin despacho activo
                      SELECT id_cotizacion FROM tms_despacho_pedidos dp
                      JOIN tms_despachos d ON d.id = dp.id_despacho
                      WHERE d.estado <> 'ANULADO')

4. POR CADA pedido:
     peso_pedido = SUM(pc.cantidad * p.peso_bruto)
                   FROM productos_cotis pc
                   JOIN productos p ON p.id_producto = pc.id_producto
                   WHERE pc.id_coti = pedido.cotizacion_id

5. peso_total = SUM(peso_pedido de todos los pedidos)

6. vehiculos_sugeridos = SELECT * FROM tms_vehiculos
        WHERE id_empresa = :empresa
          AND estado = 1
          AND capacidad_kg >= peso_total
          AND id NOT IN (vehículos ya en un despacho EN_RUTA/CARGADO esa fecha)
        ORDER BY capacidad_kg ASC      -- el más ajustado primero

7. (usuario elige vehículo + conductor) → CREAR despacho:
     - tms_despachos (cabecera con peso_total)
     - tms_despacho_pedidos (un registro por pedido, con su peso/monto/mercado)
     - orden de visita = orden del mercado en la ruta (tms_ruta_mercados.orden)
```

**Salida en pantalla (resumen para decidir):**
```
Ruta: <nombre>          Fecha: <rango>
Mercados: N             Puntos/clientes: M       Pedidos: K
Peso total: X kg
Vehículos que aguantan: [placa - capacidad] ...
```

---

## 7. Máquina de estados del despacho

```
PLANIFICADO ──► CARGADO ──► EN_RUTA ──► CERRADO
     └───────────────► ANULADO (libera los pedidos)
```
- **PLANIFICADO → CARGADO**: se confirma la carga; los pedidos quedan bloqueados (no se
  aumentan/editan).
- **CARGADO → EN_RUTA**: sale el vehículo.
- **EN_RUTA → CERRADO**: todas las entregas tienen estado final.
- **→ ANULADO**: desde PLANIFICADO/CARGADO; libera los pedidos (vuelven al pool).

Estado de cada punto (`tms_despacho_pedidos.estado_entrega`):
`PENDIENTE → ENTREGADO | RECHAZADO | PARCIAL`.

---

## 8. Reglas de negocio (validaciones)

1. **Un pedido, un despacho activo**: no jalar pedidos que ya estén en un despacho no
   anulado.
2. **Peso vs capacidad**: advertir/bloquear si `peso_total > capacidad_kg` del vehículo.
3. **Disponibilidad**: un vehículo o conductor no puede estar en dos despachos
   CARGADO/EN_RUTA la misma `fecha_reparto`.
4. **Documentos vencidos**: avisar si SOAT / rev. técnica / licencia vencen antes de la
   `fecha_reparto`.
5. **Bloqueo por carga**: si el despacho está CARGADO/EN_RUTA no se agregan/quitan
   pedidos.
6. **Cliente sin mercado**: si un cliente no tiene `mercado`, no entra a ninguna ruta →
   reportarlo para corregir el dato.
7. **Multiempresa/sucursal**: todo filtrado por `id_empresa` + `sucursal` de la sesión.

---

## 9. CRUD / mantenimientos necesarios (lo que "falta")

| CRUD | Para qué |
|---|---|
| **Mercados** | Crear/editar mercados con nombre y zona (hoy son solo números). |
| **Rutas** | Crear rutas y **asignarles mercados** (con orden de visita). |
| **Vehículos** | Flota con capacidad y vencimientos. |
| **Conductores** | Choferes con licencia y vencimientos. |
| **Cliente → mercado** | Poder asignar/editar el mercado de cada cliente (en el CRUD de clientes o desde mercados). |

---

## 10. Integración

- **Fuente de pedidos**: `cotizaciones` (pedidos) + `productos_cotis` + `productos.peso_bruto`.
- **Puntos de entrega**: `clientes` (vía `clientes.mercado`).
- **Opcional fase 2**: generar **Guía de Remisión** (`guia_remision`) por pedido/cliente al
  entregar, reutilizando vehículo y conductor del despacho.
- **Opcional fase 2**: registrar **costos del viaje** (combustible/peajes/viáticos) como
  egresos en `caja_movimientos`.

---

## 11. Fases sugeridas

| Fase | Entrega |
|---|---|
| **1 — Maestros** | `tms_mercados`, `tms_rutas` (+ ruta_mercados), `tms_vehiculos`, `tms_conductores` y sus CRUD. Asignar mercado a clientes. |
| **2 — Armar despacho** | Algoritmo §6: jalar pedidos por ruta+fecha, calcular peso, sugerir vehículo, crear despacho. |
| **3 — Reparto/entregas** | Estados del despacho, registro de entregas por punto. |
| **4 — Integraciones** | Guía de Remisión desde entrega, costos a caja, capacidad/vencimientos. |

---

## 12. Decisiones pendientes de confirmar

1. ¿`clientes.mercado` se convierte en **FK real** a `tms_mercados` o se deja como número
   y solo se mapea por id?
2. ¿La ruta es **fija** (maestro reusable) o se **arma al vuelo** eligiendo mercados en
   cada despacho? (El modelo soporta ambos: ruta maestra reutilizable.)
3. ¿Qué `id_tido` corresponde exactamente al **"pedido"** (vs cotización)? (Hay `id_tido`
   = 1 y = 6 con datos.)
4. ¿El peso usa `peso_bruto` tal cual, o hay que considerar `presentaciones`/unidades de
   `productos_cotis` (presenta/medida)?
5. ¿Se controla **capacidad por volumen** además de peso? (hoy solo hay `peso_bruto`).
6. ¿Conductores son **usuarios** del sistema o maestro aparte?
```
