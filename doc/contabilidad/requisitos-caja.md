# Módulo Caja — Requisitos

## 1. Maestro de Cajas

Se debe poder crear **Cajas** como entidad principal. Cada caja pertenece a una empresa y sucursal.

### Tabla `cajas`
| Campo | Tipo | Descripción |
|---|---|---|
| id | PK | |
| id_empresa | FK | |
| sucursal | int | |
| nombre | varchar(100) | Ej: "Caja Principal", "Caja Sucursal 1" |
| tipo | enum('PRINCIPAL','CHICA') | |
| responsable_id | FK->usuarios (nullable) | Solo para cajas chicas |
| fondo_fijo | decimal(12,2) | Solo para cajas chicas (ej. 500.00) |
| saldo_actual | decimal(12,2) | |
| estado | char(2) | |

### Reglas
- Una **Caja Principal** puede tener muchas **Cajas Chicas** asignadas
- Una **Caja Chica** tiene un **responsable** (usuario vendedor)
- Cada vendedor puede tener **máximo una** caja chica activa

---

## 2. Movimientos de Caja

### Tabla `caja_movimientos`
| Campo | Tipo | Descripción |
|---|---|---|
| id | PK | |
| id_caja | FK->cajas | |
| fecha | datetime | |
| tipo | enum('INGRESO','EGRESO','CIERRE') | |
| origen | varchar(50) | 'MANUAL', 'VENTA', 'COBRANZA', 'COMPRA', 'RENDICION_CHICA' |
| referencia_id | int nullable | ID de la venta/cobro/compra que originó el movimiento |
| descripcion | varchar(245) | |
| monto | decimal(12,2) | |
| instrumento_tipo | enum('EFECTIVO','CUENTA_BANCARIA','TARJETA','BILLETERA_DIGITAL') | |
| instrumento_id | int nullable | FK al registro del instrumento |
| id_usuario | FK | Quién registró |
| id_empresa | FK | |
| sucursal | int | |

### Reglas
- Todo movimiento debe estar vinculado a una **caja** (id_caja)
- El **origen** indica si fue manual o generado automáticamente (venta, cobranza, etc.)
- Al crear un movimiento con instrumento, se debe **actualizar el saldo** de la caja
- Los movimientos de **RENDICION_CHICA** se generan automáticamente cuando una caja chica rinde a principal

---

## 3. Caja Chica (fondo fijo)

### Flujo de operación

#### a) Asignación de fondo
- Se crea una caja de tipo `CHICA` con un `fondo_fijo` (ej. S/ 500)
- Se genera un movimiento de INGRESO (origen: ASIGNACION) desde Caja Principal a la Caja Chica
- `saldo_actual` = `fondo_fijo`

#### b) Cobros en ruta
- El vendedor sale a cobrar pedidos
- Al registrar un cobro, el sistema pregunta a qué **caja chica** se asigna
- El monto cobrado incrementa `saldo_actual` de la caja chica

#### c) Gastos menores
- El vendedor puede hacer gastos menores desde su caja chica
- Disminuye `saldo_actual`

#### d) Rendición a Caja Principal
- El vendedor entrega el dinero recolectado a Caja Principal
- Se genera movimiento de EGRESO (origen: RENDICION) en la caja chica
- Se genera movimiento de INGRESO (origen: RENDICION_CHICA) en Caja Principal
- La caja chica queda con `saldo_actual = fondo_fijo` (o 0 si se reintegra el fondo)

### Tabla `caja_movimientos` (la misma de arriba, con origen RENDICION_CHICA)

---

## 4. Instrumentos de Pago por Caja

Actualmente los instrumentos (bancos, cuentas, tarjetas, billeteras) son **globales por empresa**.

### Nueva tabla `caja_instrumentos`
| Campo | Tipo |
|---|---|
| id | PK |
| id_caja | FK->cajas |
| instrumento_tipo | enum |
| instrumento_id | int |

### Reglas
- Se asigna qué instrumentos usa cada caja
- Ej: "Caja Principal" usa BCP-CC-12345 y Efectivo
- "Caja Chica Juan" solo usa Efectivo
- Al registrar un movimiento, el select de instrumentos solo muestra los asignados a esa caja

---

## 5. Arqueo Diario

### Estado actual
Funciona, pero muestra "Efectivo" vs "Bancos" sin desglose.

### Mejora necesaria
Desglosar por **instrumento específico**:

| Vendedor | Efectivo | BCP CC 12345 | Visa *1234 | Yape | Total |
|---|---|---|---|---|---|
| Juan Pérez | 200 | 150 | 50 | 30 | 430 |

Esto requiere filtrar `caja_movimientos` por `id_caja`, agrupar por `instrumento_tipo` + `instrumento_id`.

---

## 6. Integraciones

### Desde Ventas
- Al registrar una venta al contado, se debe crear automáticamente un movimiento de INGRESO en la caja del vendedor
- Campos: origen='VENTA', referencia_id=id_venta, monto, instrumento según el pago

### Desde Cobranzas
- Al registrar un cobro de cuota, se genera INGRESO automático
- origen='COBRANZA', referencia_id=id_cobro

### Desde Compras
- Al registrar una compra al contado, se genera EGRESO automático
- origen='COMPRA', referencia_id=id_compra

---

## 7. Reportes

### Movimientos por Caja
- Filtro por caja, fechas, tipo, instrumento
- Exportable a Excel

### Saldos por Caja
- Reporte tipo estado de cuenta: saldo anterior + ingresos - egresos = saldo actual

### Rendiciones Pendientes
- Cajas chicas con saldo_actual > fondo_fijo (tienen dinero por rendir)

---

## Resumen de Tablas Necesarias

| Tabla | Tipo | Estado |
|---|---|---|
| cajas | NUEVA | No existe |
| caja_movimientos | REEMPLAZA a caja_empresa e ingreso_egreso | Por crear |
| caja_instrumentos | NUEVA | No existe |
| bancos | EXISTE | OK |
| cuentas_bancarias | EXISTE | OK |
| tarjetas | EXISTE | OK |
| billeteras_digitales | EXISTE | OK |
| billetera_tipos | EXISTE | OK |
| arqueos_diarios | EXISTE | Mejorar desglose |

## Sidebar Propuesto

```
Cajas
├── Gestión de Cajas         (CRUD maestro)
├── Movimientos              (movimientos manuales)
├── Cajas Chicas             (fondo fijo, asignación, rendición)
├── Arqueo Diario
└── Métodos de Pago
```
