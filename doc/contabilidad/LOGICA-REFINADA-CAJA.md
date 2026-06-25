# Lógica Refinada del Módulo de Caja

## Problemas detectados en la versión actual

1. `cajas.tipo` con valores `GENERAL|CHICA|VENDEDOR` — no tiene sentido, una caja es solo una caja
2. La jerarquía está mal implementada: el tipo no debería determinar si tiene padre o fondo fijo
3. Caja Chica vista como un subtipo especial (rendiciones, fondo fijo) cuando debería ser solo una caja hija que "cierra" y reporta a la principal que "cuadra"
4. La lógica actual mezcla concepto de caja con concepto de flujo operativo

## Nueva definición

### Caja (tabla `cajas` — simplificada)

| Columna | Descripción |
|---------|-------------|
| `id` | PK |
| `id_empresa` | FK empresa |
| `sucursal` | Sucursal |
| `nombre` | Ej: "Caja Principal", "Caja Repartidor 1", "Caja Vendedor Juan" |
| `id_usuario_responsable` | Responsable de la caja (usuario que opera la caja) |
| `id_caja_padre` | **Opcional**. Si tiene valor, esta caja es hija de esa caja principal |
| `saldo_actual` | Saldo en tiempo real |
| `moneda` | PEN por defecto |
| `estado` | ACTIVA / INACTIVA |

Se eliminan: `tipo`, `monto_fondo_fijo`.

### Instrumentos por caja (`caja_instrumentos` — sin cambios)

**Solo las cajas hijas** (las que tienen `id_caja_padre`) necesitan instrumentos asignados. La **caja principal** no tiene instrumentos propios; su función es consolidar/ver los datos de sus hijas al hacer el cuadre.

Los instrumentos disponibles para asignar a una caja hija:
- Efectivo
- Cuenta Bancaria (con banco y número)
- Tarjeta (con banco, marca y últimos 4 dígitos)
- Billetera Digital (tipo dinámico: Yape, Plin, etc. + cuenta bancaria vinculada)

Esto permite controlar qué métodos de pago puede usar cada responsable. Ejemplos:
- **Caja de Repartidor**: solo Efectivo
- **Caja de Vendedor**: Efectivo + Billetera digital (Yape)
- **Caja de Chofer**: Efectivo + Tarjeta

**Flujo de creación de caja hija:**
1. Abrir modal "Nueva Caja" en Gestión de Cajas
2. Llenar: nombre, responsable, seleccionar **caja padre** (obligatorio para hijas)
3. Al guardar, se abre automáticamente el modal de **Asignar Instrumentos**
4. Seleccionar uno o más instrumentos del catálogo global de la empresa
5. Confirmar

Obligatorio: toda caja hija debe tener al menos un instrumento asignado.

**Nota:** La caja principal se crea sin instrumentos. Solo sirve como contenedor para el cuadre.

### Movimientos (`caja_movimientos` — sin cambios)

INGRESO / EGRESO con categorías (MANUAL, VENTA, COMPRA, APERTURA, AJUSTE, CIERRE, CUADRE).

Cada movimiento pertenece a una caja y actualiza su `saldo_actual`.

### Nueva: Cierre de Caja vs Cuadre de Caja

#### Cierre de Caja (lo hace el responsable de la caja hija)

- Acción diaria donde el responsable de una caja hija "cierra" su caja
- Genera un **movimiento de tipo CIERRE** que congela el saldo y el desglose por instrumento
- El cierre puede ser aprobado o rechazado por el responsable de la caja principal
- Tabla sugerida: `cierre_caja` (id_caja, fecha, saldo_declarado, saldo_sistema, desglose_instrumentos, estado, id_usuario_cierra)

#### Cuadre de Caja (lo hace el responsable de la caja principal)

- Acción donde el responsable de la caja principal revisa los cierres de las cajas hijas
- Puede ver el **consolidado** de todas las cajas hijas: suma de ingresos/egresos, diferencias, etc.
- Aprueba o rechaza los cierres
- La tabla `cajas.id_caja_padre` permite hacer consultas recursivas del tipo:
  ```sql
  SELECT * FROM cierre_caja WHERE id_caja IN (
    SELECT id FROM cajas WHERE id_caja_padre = ?  -- la caja principal
  );
  ```

## Flujo operativo diario

```
1. Día normal
   ├── Cada caja (principal e hijas) registra sus movimientos
   └── Se actualiza saldo_actual de cada caja

2. Fin del día — CIERRE (caja hija)
   ├── Responsable hace clic en "Cerrar caja"
   ├── Ingresa saldo_declarado (lo que hay físicamente)
   ├── Sistema muestra saldo_sistema (según movimientos)
   ├── Puede hacer ajuste si hay diferencia (genera movimiento AJUSTE)
   └── Se genera registro cierre_caja con estado PENDIENTE

3. Siguiente día — CUADRE (caja principal)
   ├── Responsable principal revisa los cierres pendientes de sus hijas
   ├── Ve consolidado: total ingresos/egresos/saldos
   ├── Aprueba (valida) o rechaza (solicita corrección)
   └── Al aprobar, se genera un movimiento CUADRE en la caja principal
```

## Cambios a realizar

### Migraciones
1. Nueva migración `alter_cajas_drop_tipo`: elimina columna `tipo`, `monto_fondo_fijo` de `cajas`
2. Nueva migración `create_cierre_caja_table`: tabla para cierres de caja
3. Opcional: eliminar tabla `caja_chica_rendiciones` (o ignorarla)

### Modelos y servicios
1. `app/Services/CajaService.php` — agregar métodos:
   - `cerrarCaja(idCaja, saldoDeclarado, desglose)`
   - `aprobarCierre(idCierre)`
   - `consolidadoCajasHijas(idCajaPadre, fecha)`

### Vistas
1. `caja/gestion.blade.php`:
   - Eliminar select de tipo, fondo fijo
   - Formulario: nombre, responsable, caja padre (opcional)
   - **Al crear**: después de guardar, abrir automáticamente el modal de Asignar Instrumentos para que el usuario seleccione al menos uno
   - Si cancela el modal, la caja se crea pero queda sin instrumentos (mostrar advertencia)
2. `caja/rendiciones.blade.php` — reemplazar con vista de "Cierres de Caja"
3. `caja/movimientos.blade.php` — sin cambios (solo filtrar por caja)

### API
1. `CajaMaestroApiController` — eliminar validación de tipo, aceptar o no `id_caja_padre`
2. Eliminar `RendicionApiController` (ya no aplica)
3. Nuevo `CierreCajaApiController`

## Reglas de movimientos

### INGRESO / EGRESO

Un movimiento representa una entrada o salida de dinero en una caja. Se genera por:
- **Cobro de venta** (automático desde el módulo de ventas)
- **Cobro de deuda anterior** (automático desde cuentas por cobrar)
- **Registro manual** (desde la vista de movimientos o Mi Caja)
- **Ajuste** (diferencia detectada en cierre)
- **Apertura** (saldo inicial de la caja)

### Editar y Anular (nunca eliminar)

| Acción | Descripción |
|--------|-------------|
| **Editar** | Se puede cambiar: descripción, fecha, monto, instrumento. Al editar el monto, el sistema recalcula el `saldo_actual` de la caja y de todos los movimientos posteriores |
| **Anular** | El movimiento se marca como `ANULADO`, se restaura el `saldo_anterior` en la caja y se recalcula el saldo de movimientos posteriores |
| **Eliminar** | ❌ No existe. Los movimientos una vez creados nunca se borran de la BD por auditoría |

**¿Por qué no eliminar?** Porque un movimiento:
- Ya afectó el `saldo_actual` de la caja
- Podría estar vinculado a una venta, compra o cierre de caja
- Debe quedar registro para auditoría (quién, cuándo, por qué)

**Flujo de edición de monto:**
```
1. Usuario edita monto de movimiento (ej: pasa de S/100 a S/120)
2. Sistema calcula diferencia: +S/20
3. Actualiza saldo_actual de la caja: saldo_actual + diferencia
4. Recalcula saldo_anterior/posterior de los movimientos siguientes
```

**Flujo de anulación:**
```
1. Usuario anula movimiento
2. Sistema calcula el efecto inverso (INGRESO → resta, EGRESO → suma)
3. Restaura saldo_actual de la caja
4. Marca movimiento como ANULADO
5. Recalcula saldos de movimientos posteriores
```

## Notas
- La jerarquía padre-hijo es opcional. Una caja puede ser independiente y nunca hacer cierre
- El cierre de caja es un proceso diario opcional, no obligatorio
- El cuadre no genera movimientos contables por sí mismo; solo consolida información
- Los ajustes por diferencias se registran como movimientos tipo AJUSTE
